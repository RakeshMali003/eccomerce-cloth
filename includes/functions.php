<?php
// Security Helper
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Cache.php';

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
    $base_dir = 'assets/images/products/';

    // If path doesn't start with assets, prepend base dir
    if (!empty($image_path) && strpos($image_path, 'assets/') === false) {
        $image_path = $base_dir . $image_path;
    }

    if (!empty($image_path) && file_exists(__DIR__ . '/../' . $image_path)) {
        return BASE_URL . $image_path;
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

// Permission Helper (Cached)
function has_permission($permission)
{
    if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id']))
        return false;

    // Super Admin Bypass
    if (isset($_SESSION['admin_id'])) {
        return true;
    }

    // Check Role
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin')
        return true;

    // Fetch Permissions if not in session (Use Singleton DB)
    if (!isset($_SESSION['permissions'])) {
        $db = \Core\Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT permissions FROM workers WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $perms = $stmt->fetchColumn();
        $_SESSION['permissions'] = $perms ? json_decode($perms, true) : [];
    }

    return in_array($permission, $_SESSION['permissions']);
}

// Cart Count Helper (Cached in Session)
function get_cart_count()
{
    if (!isset($_SESSION['user_id']))
        return 0;

    // Check Session Cache first (avoid DB on every refresh)
    if (isset($_SESSION['cart_count_cache']) && (time() - $_SESSION['cart_last_check'] < 60)) {
        return $_SESSION['cart_count_cache'];
    }

    $db = \Core\Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $count = (int) $stmt->fetchColumn();

    $_SESSION['cart_count_cache'] = $count;
    $_SESSION['cart_last_check'] = time();

    return $count;
}

// Wishlist Count Helper
function get_wishlist_count()
{
    if (!isset($_SESSION['user_id']))
        return 0;

    $db = \Core\Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return (int) $stmt->fetchColumn();
}
?>