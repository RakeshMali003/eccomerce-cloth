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
define('DB_HOST', 'localhost');      // Usually 'localhost' for cPanel/live hosting
define('DB_USER', 'root');           // Live Database Username
define('DB_PASS', '');               // Live Database Password
define('DB_NAME', 'ecommerce_clothing_store'); // Live Database Name

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
define('BASE_PATH', __DIR__ . '/../');

// Error Reporting (Turn off for live, on for local)
if ($host === 'localhost') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>