<?php
/**
 * جلسات العملاء للوحة التحكم (للقراءة فقط)
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$file = __DIR__ . '/data/client_sessions.json';
if (!is_file($file)) {
    echo json_encode(['success' => true, 'sessions' => []], JSON_UNESCAPED_UNICODE);
    exit;
}

$fp = fopen($file, 'rb');
if ($fp === false) {
    echo json_encode(['success' => false, 'sessions' => []], JSON_UNESCAPED_UNICODE);
    exit;
}

$content = '';
if (flock($fp, LOCK_SH)) {
    $content = stream_get_contents($fp);
    flock($fp, LOCK_UN);
}
fclose($fp);

$store = json_decode($content ?: '[]', true);
$sessions = [];
if (is_array($store) && isset($store['by_id']) && is_array($store['by_id'])) {
    $now = time();
    foreach ($store['by_id'] as $sid => $row) {
        if (!is_array($row)) {
            continue;
        }
        $ls = (int) ($row['last_seen'] ?? 0);
        $sessions[] = [
            'session_id' => (string) $sid,
            'channel_suffix' => (string) ($row['channel_suffix'] ?? ''),
            'last_seen' => $ls,
            'page' => (string) ($row['page'] ?? ''),
            'order_id' => (string) ($row['order_id'] ?? ''),
            'ip' => (string) ($row['ip'] ?? ''),
            'active' => $ls > 0 && ($now - $ls) <= 45,
            'seconds_ago' => $ls > 0 ? max(0, $now - $ls) : null,
        ];
    }
}

echo json_encode(['success' => true, 'sessions' => $sessions, 'server_time' => time()], JSON_UNESCAPED_UNICODE);
