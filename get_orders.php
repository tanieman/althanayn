<?php
/**
 * قراءة الطلبات من ملف JSON (مع إعادة محاولة لتفادي قراءة لحظية أثناء الكتابة)
 */

header('Content-Type: application/json; charset=utf-8');

try {
    $ordersFile = __DIR__ . '/data/orders.json';

    if (!file_exists($ordersFile)) {
        echo json_encode([
            'success' => true,
            'orders' => [],
            'message' => 'لا توجد طلبات بعد',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $maxAttempts = 8;
    $lastJsonError = JSON_ERROR_NONE;
    $orders = null;

    for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
        $content = false;
        $fp = @fopen($ordersFile, 'rb');
        if ($fp !== false) {
            if (flock($fp, LOCK_SH)) {
                $content = stream_get_contents($fp);
                flock($fp, LOCK_UN);
                fclose($fp);
            } else {
                fclose($fp);
                $content = @file_get_contents($ordersFile);
            }
        } else {
            $content = @file_get_contents($ordersFile);
        }

        if ($content === false) {
            usleep(60000);
            continue;
        }

        // أثناء الكتابة قد يكون الملف مؤقتاً فارغاً — نعيد المحاولة
        $trimmed = trim($content);
        if ($trimmed === '') {
            if ($attempt < $maxAttempts) {
                usleep(80000 * $attempt);
                continue;
            }
            $orders = [];
            break;
        }

        $decoded = json_decode($content, true);
        $lastJsonError = json_last_error();
        if (is_array($decoded)) {
            $orders = $decoded;
            break;
        }

        if ($attempt < $maxAttempts) {
            usleep(80000 * $attempt);
            continue;
        }
    }

    if ($orders === null) {
        http_response_code(503);
        echo json_encode([
            'success' => false,
            'message' => 'تعذّر قراءة الطلبات (الملف قيد التحديث). أعد المحاولة.',
            'json_error' => $lastJsonError,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode([
        'success' => true,
        'orders' => $orders,
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
