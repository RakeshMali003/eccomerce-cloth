<?php
$env = include __DIR__ . '/.env.php';

define('BASE_URL', $env['APP_URL']);
define('ADMIN_URL', BASE_URL . 'admin/');
define('CURRENCY', '₹'); // Or $
define('APP_NAME', $env['APP_NAME']);
?>