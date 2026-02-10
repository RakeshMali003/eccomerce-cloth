<?php
// High Performance API Endpoint Example
// Path: api/v1/products.php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/Cache.php';
require_once __DIR__ . '/../../core/RateLimiter.php';

use Core\Database;
use Core\Cache;
use Core\RateLimiter;

header('Content-Type: application/json');

// 1. Rate Limiting (First line of defense)
$limiter = new RateLimiter();
$ip = $_SERVER['REMOTE_ADDR'];
if (!$limiter->check($ip, 100, 60)) { // 100 req per minute
    http_response_code(429);
    echo json_encode(['error' => 'Too Many Requests']);
    exit;
}

// 2. Caching (Cache-Aside Pattern)
$cache = new Cache();
$page = $_GET['page'] ?? 1;
$cacheKey = "products_page_" . $page;
$cachedData = $cache->get($cacheKey);

if ($cachedData) {
    // HIT: Return cached response immediately
    header('X-Cache-Status: HIT');
    echo json_encode($cachedData);
    exit;
}

// 3. Database Query (Optimized)
header('X-Cache-Status: MISS');
$db = Database::getInstance();
$conn = $db->getConnection();

$limit = 20;
$offset = ($page - 1) * $limit;

// Prepare Statement (Prevents SQL Injection + Pre-compiles query)
$stmt = $conn->prepare("SELECT id, name, price, image FROM products WHERE status = 'active' LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

// 4. Set Cache
// Cache for 5 minutes (300s) to reduce DB load
$cache->set($cacheKey, $products, 300);

echo json_encode($products);
?>