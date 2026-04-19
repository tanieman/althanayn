<?php
/**
 * بث أحداث Pusher إلى قناة لوحة التحكم (farm-dashboard)
 */

declare(strict_types=1);

function farm_pusher_broadcast(string $eventName, array $payload = []): void
{
    $autoload = dirname(__DIR__) . '/vendor/autoload.php';
    if (!is_file($autoload)) {
        return;
    }
    $cfgPath = dirname(__DIR__) . '/farm_pusher_config.php';
    if (!is_file($cfgPath)) {
        return;
    }
    require_once $autoload;
    $cfg = require $cfgPath;
    if (!is_array($cfg) || empty($cfg['key']) || empty($cfg['secret']) || empty($cfg['app_id'])) {
        return;
    }
    try {
        $pusher = new Pusher\Pusher(
            (string) $cfg['key'],
            (string) $cfg['secret'],
            (string) $cfg['app_id'],
            [
                'cluster' => (string) ($cfg['cluster'] ?? 'ap2'),
                'useTLS' => true,
            ]
        );
        $pusher->trigger('farm-dashboard', $eventName, $payload);
    } catch (Throwable $e) {
        // لا نكسر الحفظ إذا فشل الإشعار
    }
}
