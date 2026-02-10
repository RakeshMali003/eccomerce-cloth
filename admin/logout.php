<?php
// 1. Initialize the session
session_start();
require_once __DIR__ . '/../config/config.php';

// 2. Clear all session variables
$_SESSION = array();

// 3. Destroy the session cookie on the user's browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 4. Destroy the session on the server
session_destroy();

// 5. Redirect to the index page or login page
header("Location: " . ADMIN_URL . "index.php?msg=logged_out");
exit();
?>