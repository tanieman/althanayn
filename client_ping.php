<?php
/**
 * نبض العميل: الصفحة الحالية، الجلسة، ربط اختياري بطلب — للوحة التحكم (نشط / غير نشط)
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

$dataDir = __DIR__ . '/data';
$file = $dataDir . '/client_sessions.json';

try {
    $raw = file_get_contents('php://input');
    $body = json_decode($raw ?: '[]', true);
    if (!is_array($body)) {
        throw new Exception('JSON غير صالح');
    }

    $sessionId = isset($body['session_id']) ? preg_replace('/[^\w\-\.]/', '', (string) $body['session_id']) : '';
    if ($sessionId === '' || strlen($sessionId) < 8) {
        throw new Exception('معرّف الجلسة ناقص');
    }

    $page = isset($body['page']) ? (string) $body['page'] : '';
    if (strlen($page) > 500) {
        $page = substr($page, 0, 500);
    }

    $orderId = isset($body['order_id']) ? preg_replace('/\D/', '', (string) $body['order_id']) : '';

    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }

    $fp = fopen($file, 'c+');
    if ($fp === false) {
        throw new Exception('تعذّر فتح التخزين');
    }
    if (!flock($fp, LOCK_EX)) {
        fclose($fp);
        throw new Exception('تعذّر القفل');
    }

    rewind($fp);
    $content = stream_get_contents($fp);
    $store = ['by_id' => []];
    if ($content !== false && $content !== '') {
        $decoded = json_decode($content, true);
        if (is_array($decoded) && isset($decoded['by_id']) && is_array($decoded['by_id'])) {
            $store = $decoded;
        }
    }

    $now = time();
    $rec = $store['by_id'][$sessionId] ?? [];
    if (!is_array($rec)) {
        $rec = [];
    }

    if (empty($rec['channel_suffix']) || !is_string($rec['channel_suffix'])) {
        $rec['channel_suffix'] = bin2hex(random_bytes(8));
    }

    $rec['last_seen'] = $now;
    $rec['page'] = $page;
    $rec['ip'] = $_SERVER['REMOTE_ADDR'] ?? '';
    if ($orderId !== '') {
        $rec['order_id'] = $orderId;
    } elseif (!isset($rec['order_id'])) {
        $rec['order_id'] = '';
    }

    $pageIsIndex = $page !== '' && (stripos($page, 'index.html') !== false || $page === '/' || preg_match('#(^|/)index\\.html#i', $page));
    $fireIndexAlert = false;
    if ($pageIsIndex && empty($rec['index_alert_sent'])) {
        $rec['index_alert_sent'] = 1;
        $fireIndexAlert = true;
    }

    $store['by_id'][$sessionId] = $rec;

    // تنظيف جلسات قديمة جداً (> 48 ساعة)
    foreach ($store['by_id'] as $sid => $row) {
        if (!is_array($row)) {
            unset($store['by_id'][$sid]);
            continue;
        }
        $ls = (int) ($row['last_seen'] ?? 0);
        if ($ls > 0 && ($now - $ls) > 172800) {
            unset($store['by_id'][$sid]);
        }
    }

    $json = json_encode($store, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        flock($fp, LOCK_UN);
        fclose($fp);
        throw new Exception('فشل الترميز');
    }

    rewind($fp);
    ftruncate($fp, 0);
    fwrite($fp, $json);
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);

    if (!empty($fireIndexAlert) && is_file(__DIR__ . '/includes/farm_pusher_broadcast.php')) {
        require_once __DIR__ . '/includes/farm_pusher_broadcast.php';
        farm_pusher_broadcast('dashboard-event', [
            'kind' => 'visitor_index',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'session_id' => $sessionId,
            'page' => $page,
        ]);
        farm_pusher_broadcast('dashboard-event', ['kind' => 'sessions_updated']);
    }

    echo json_encode([
        'ok' => true,
        'channel_suffix' => $rec['channel_suffix'],
        'session_id' => $sessionId,
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
