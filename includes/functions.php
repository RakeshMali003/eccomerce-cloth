<?php
// Security Helper
function e($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Generate CSRF Token
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrf_token()
{
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Image Helper
function get_product_image($image_path)
{
    if (!empty($image_path) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/' . $image_path)) {
        return 'http://localhost/ecommerce-website/' . $image_path;
    }
    return 'https://via.placeholder.com/600x800?text=No+Image';
}

// Format Price
function format_price($amount)
{
    return '₹' . number_format((float) $amount, 2);
}

// Get User Name Initial
function get_user_initial()
{
    return isset($_SESSION['user_name']) ? strtoupper(substr($_SESSION['user_name'], 0, 1)) : 'U';
}

// Permission Helper
function has_permission($permission)
{
    global $pdo;

    if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id']))
        return false;

    // Super Admin Bypass
    if (isset($_SESSION['admin_id'])) {
        return true;
    }

    // Check Role
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin')
        return true;

    // Fetch Permissions if not in session
    if (!isset($_SESSION['permissions'])) {
        $stmt = $pdo->prepare("SELECT permissions FROM workers WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $perms = $stmt->fetchColumn();
        if ($perms) {
            $_SESSION['permissions'] = json_decode($perms, true) ?? [];
        } else {
            $_SESSION['permissions'] = [];
        }
    }

    return in_array($permission, $_SESSION['permissions']);
}
?>