<?php
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

function admin_is_logged_in()
{
    return isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true;
}

function admin_require_login($returnTo = null)
{
    if (!admin_is_logged_in()) {
        $returnTo = $returnTo ?: ($_SERVER['REQUEST_URI'] ?? 'adminHome.php');
        $loginUrl = 'adminLogin.php?return_to=' . rawurlencode($returnTo);

        echo "<script>
                alert('This action is locked. Please log in as an admin to continue.');
                window.location.href = '{$loginUrl}';
              </script>";
        exit();
    }
}
