<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function admin_is_logged_in(): bool
{
    return !empty($_SESSION['isLoggedIn']) || !empty($_SESSION['admin_id']);
}

function admin_login_url(?string $returnTo = null): string
{
    $url = 'adminLogin.php';
    if ($returnTo) {
        $url .= '?return_to=' . rawurlencode($returnTo);
    }
    return $url;
}

function admin_require_login(?string $returnTo = null): void
{
    if (admin_is_logged_in()) {
        return;
    }

    $target = $returnTo ?: ($_SERVER['REQUEST_URI'] ?? 'adminHome.php');

    echo "<script>alert('Please log in as an admin to make changes.'); window.location.href = '" . admin_login_url($target) . "';</script>";
    exit();
}
?>
