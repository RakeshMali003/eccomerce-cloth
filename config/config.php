<?php
// Prevent direct access
if (count(get_included_files()) == 1)
    exit("Direct access not permitted.");

// Start Session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Database Configuration ---
// Update these values for your LIVE server
$host = $_SERVER['HTTP_HOST'];
if ($host === 'localhost' || $host === '127.0.0.1') {
    // Localhost Configuration
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'ecommerce_clothing_store');
} else {
    // Live Server Configuration (UPDATE THESE WITH YOUR CPANEL DB DETAILS)
    define('DB_HOST', 'localhost');
    define('DB_USER', 'YOUR_LIVE_DB_USER');
    define('DB_PASS', 'YOUR_LIVE_DB_PASS');
    define('DB_NAME', 'YOUR_LIVE_DB_NAME');
}

// --- Base URL Configuration ---
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];

// Detect Admin Subdomain
$isAdminSubdomain = (strpos($host, 'admin.') === 0);

if ($host === 'localhost' || $host === '127.0.0.1') {
    // Localhost
    define('BASE_URL', 'http://localhost/ecommerce-website/');
    define('ADMIN_URL', 'http://localhost/ecommerce-website/admin/');
} else {
    // Live Server
    if ($isAdminSubdomain) {
        // e.g. admin.example.com
        // Main site is example.com (remove 'admin.' from host)
        $mainHost = str_replace('admin.', '', $host);
        define('BASE_URL', $protocol . "://" . $mainHost . "/");
        define('ADMIN_URL', $protocol . "://" . $host . "/");
    } else {
        // Main site access
        define('BASE_URL', $protocol . "://" . $host . "/");
        define('ADMIN_URL', BASE_URL . 'admin/');
    }
}

// --- Application Constants ---
// ADMIN_URL is now defined above to handle subdomain logic
define('CURRENCY', '₹');
define('APP_NAME', 'Thread & Trend');

// Define Base Path (Physical file path)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/../');
}

// Error Reporting (Turn off for live, on for local)
if ($host === 'localhost' || (isset($_GET['debug']) && $_GET['debug'] === 'true')) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>



<!-- 
<?php
// Prevent direct access
if (count(get_included_files()) == 1)
    exit("Direct access not permitted.");

// Start Session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



define('DB_HOST', 'localhost');
define('DB_NAME', 'joshiele1_joshielectrical');
define('DB_USER', 'joshiele1_rakesh');
define('DB_PASS', 'Qal%9898801505');

// --- Base URL Configuration ---
// Automatically detect if running on localhost or live server
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];

// Check if we are on localhost
if ($host === 'localhost' || $host === '127.0.0.1') {
    // Localhost configuration (Preserve existing setup)
    define('BASE_URL', 'http://localhost/ecommerce-website/');
} else {
    // Live Server Configuration
    // Assuming the website is at the root of the domain (e.g., https://example.com/)
    // If it's in a subfolder, change this to: $protocol . "://" . $host . "/subfolder/";
    define('BASE_URL', $protocol . "://" . $host . "/");
}

// --- Application Constants ---
define('ADMIN_URL', BASE_URL . 'admin/');
define('CURRENCY', '₹');
define('APP_NAME', 'Thread & Trend');

// Define Base Path (Physical file path)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/../');
}

// Error Reporting (Turn off for live, on for local)
if ($host === 'localhost') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>         -->