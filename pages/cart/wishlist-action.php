<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login first']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$product_id = (int) ($_POST['product_id'] ?? 0);

if ($action === 'toggle' && $product_id > 0) {
    // Check if exists
    $stmt = $pdo->prepare("SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $exists = $stmt->fetch();

    if ($exists) {
        // Remove
        $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?")->execute([$user_id, $product_id]);
        echo json_encode(['status' => 'removed', 'message' => 'Removed from wishlist']);
    } else {
        // Add
        $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)")->execute([$user_id, $product_id]);
        echo json_encode(['status' => 'added', 'message' => 'Added to wishlist']);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
exit;
?>