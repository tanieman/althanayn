<?php
/**
 * JSON API: إلحاق خطوة تدفق (من knet.js في المتصفح)
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

require_once __DIR__ . '/append_order_flow.php';

try {
    $input = file_get_contents('php://input');
    $body = json_decode($input, true);
    if (!$body || empty($body['order_id'])) {
        throw new Exception('معرّف الطلب مفقود');
    }
    $orderId = (string) $body['order_id'];
    $step = isset($body['step']) ? preg_replace('/[^a-z0-9_]/i', '', (string) $body['step']) : '';
    if ($step === '') {
        throw new Exception('نوع الخطوة مفقود');
    }
    $data = $body['data'] ?? [];
    if (!is_array($data)) {
        $data = [];
    }

    // تقليل حجم الحقول النصية الطويلة جداً
    foreach ($data as $k => $v) {
        if (is_string($v) && strlen($v) > 2000) {
            $data[$k] = substr($v, 0, 2000) . '…';
        }
    }

    if (!farm_append_order_flow($orderId, $step, $data)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'الطلب غير موجود'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'تم الحفظ'], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
