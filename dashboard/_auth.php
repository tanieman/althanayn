<?php

declare(strict_types=1);

/**
 * جلسة لوحة التحكم — يُضمَّن من index.php و login.php و logout.php فقط.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

function farm_dashboard_load_auth(): array
{
    $path = dirname(__DIR__) . '/farm_dashboard_auth.php';
    if (!is_file($path)) {
        return [];
    }
    $cfg = include $path;
    return is_array($cfg) ? $cfg : [];
}

function farm_dashboard_is_logged_in(): bool
{
    return !empty($_SESSION['farm_dashboard_ok']) && $_SESSION['farm_dashboard_ok'] === true;
}

function farm_dashboard_require_login(): void
{
    if (farm_dashboard_is_logged_in()) {
        return;
    }
    $script = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
    $q = $script !== 'index.php' ? '?next=' . rawurlencode($script) : '';
    header('Location: login.php' . $q, true, 302);
    exit;
}
