<?php

declare(strict_types=1);

require __DIR__ . '/_auth.php';

$auth = farm_dashboard_load_auth();
$hash = isset($auth['password_hash']) ? (string) $auth['password_hash'] : '';
$userCfg = isset($auth['username']) ? (string) $auth['username'] : '';

$error = '';
$next = 'index.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['next'])) {
    $next = (string) $_POST['next'];
} elseif (isset($_GET['next'])) {
    $next = (string) $_GET['next'];
}
$next = basename($next);
if ($next !== 'index.php') {
    $next = 'index.php';
}

if (farm_dashboard_is_logged_in()) {
    header('Location: ' . $next, true, 302);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = isset($_POST['username']) ? trim((string) $_POST['username']) : '';
    $p = isset($_POST['password']) ? (string) $_POST['password'] : '';

    if ($hash === '' || $userCfg === '') {
        $error = 'ملف الإعدادات farm_dashboard_auth.php غير مكتمل.';
    } elseif ($userCfg !== $u || !password_verify($p, $hash)) {
        $error = 'اسم المستخدم أو كلمة المرور غير صحيحة.';
    } else {
        session_regenerate_id(true);
        $_SESSION['farm_dashboard_ok'] = true;
        $_SESSION['farm_dashboard_user'] = $u;
        header('Location: ' . $next, true, 302);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول — لوحة التحكم</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Tajawal', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: linear-gradient(145deg, #e8f4fc 0%, #f5f7fa 50%, #e3eef5 100%);
        }
        .card {
            width: 100%;
            max-width: 400px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 8px 32px rgba(2, 83, 128, 0.12);
            padding: 28px 26px 32px;
            border: 1px solid #e8eef2;
        }
        h1 {
            font-size: 1.25rem;
            font-weight: 800;
            color: #025380;
            margin-bottom: 6px;
            text-align: center;
        }
        .sub {
            font-size: 0.88rem;
            color: #6c757d;
            text-align: center;
            margin-bottom: 22px;
        }
        label {
            display: block;
            font-size: 0.85rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 6px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #ced4da;
            border-radius: 10px;
            font-family: inherit;
            font-size: 1rem;
            margin-bottom: 16px;
        }
        input:focus {
            outline: none;
            border-color: #0277bd;
            box-shadow: 0 0 0 3px rgba(2, 119, 189, 0.15);
        }
        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 800;
            cursor: pointer;
            background: linear-gradient(135deg, #025380 0%, #0277bd 100%);
            color: #fff;
            margin-top: 4px;
        }
        button:hover { filter: brightness(1.05); }
        .err {
            background: #f8d7da;
            color: #842029;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 0.88rem;
            margin-bottom: 16px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>لوحة التحكم</h1>
        <p class="sub">أدخل اسم المستخدم وكلمة المرور</p>
        <?php if ($error !== '') : ?>
            <div class="err"><?= htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
        <?php endif; ?>
        <form method="post" action="login.php" autocomplete="on">
            <input type="hidden" name="next" value="<?= htmlspecialchars($next, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
            <label for="username">اسم المستخدم</label>
            <input type="text" id="username" name="username" required autocomplete="username" autofocus>
            <label for="password">كلمة المرور</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">
            <button type="submit">دخول</button>
        </form>
    </div>
</body>
</html>
