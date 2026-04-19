<?php
session_start();
require_once('../dashboard/init.php');

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if ($user_id > 0) {
    $User->clearRedirectUrl($user_id);
}

echo json_encode(['success' => true]);
exit;
?>