<?php
// Central Load Point for Admin Panel
// Handles path resolution for both Localhost and Subdomain

// 1. Define Physical Path to Root (One level up from admin)
define('PROJECT_ROOT', dirname(__DIR__));

// 2. Check for Config
$configPath = PROJECT_ROOT . '/config/config.php';

if (!file_exists($configPath)) {
    die("<h1>System Error</h1><p>Configuration file missing. Expected at: " . htmlspecialchars($configPath) . "</p>");
}

require_once $configPath;

// 3. Load Core Functions (which loads Database & Cache)
require_once PROJECT_ROOT . '/includes/functions.php';

// 4. Enforce Admin Session Check (Optional, usually done in header)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>