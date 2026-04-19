<?php

declare(strict_types=1);

require __DIR__ . '/_auth.php';
farm_dashboard_require_login();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

$raw = file_get_contents('php://input');
$body = json_decode($raw ?: '{}', true);
$orderId = '';
if (is_array($body) && isset($body['order_id'])) {
    $orderId = trim((string) $body['order_id']);
}

if ($orderId === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'رقم الطلب مطلوب'], JSON_UNESCAPED_UNICODE);
    exit;
}

$ordersFile = dirname(__DIR__) . '/data/orders.json';

if (!is_file($ordersFile)) {
    echo json_encode(['success' => false, 'message' => 'لا يوجد ملف طلبات'], JSON_UNESCAPED_UNICODE);
    exit;
}

$fp = fopen($ordersFile, 'c+');
if ($fp === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'تعذّر فتح ملف الطلبات'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!flock($fp, LOCK_EX)) {
    fclose($fp);
    http_response_code(503);
    echo json_encode(['success' => false, 'message' => 'الملف مشغول، أعد المحاولة'], JSON_UNESCAPED_UNICODE);
    exit;
}

rewind($fp);
$content = stream_get_contents($fp);
$orders = [];
if ($content !== false && $content !== '') {
    $decoded = json_decode($content, true);
    $orders = is_array($decoded) ? $decoded : [];
}

$before = count($orders);
$orders = array_values(array_filter($orders, static function ($o) use ($orderId) {
    if (!is_array($o)) {
        return false;
    }
    $id = isset($o['id']) ? (string) $o['id'] : '';
    return $id !== $orderId;
}));

if (count($orders) === $before) {
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
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'فشل تجهيز الحفظ'], JSON_UNESCAPED_UNICODE);
    exit;
}

rewind($fp);
if (!ftruncate($fp, 0)) {
    flock($fp, LOCK_UN);
    fclose($fp);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'فشل تجهيز الملف'], JSON_UNESCAPED_UNICODE);
    exit;
}

$written = fwrite($fp, $json);
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

if ($written === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'فشل الحفظ'], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(['success' => true, 'message' => 'تم الحذف'], JSON_UNESCAPED_UNICODE);
