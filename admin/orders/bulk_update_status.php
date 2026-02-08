<?php
require_once "../../config/database.php";
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['ids']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'error' => 'Missing data.']);
    exit();
}

try {
    $ids = $data['ids'];
    $status = $data['status'];

    // Create placeholders for the IN clause
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE order_id IN ($placeholders)");
    $stmt->execute(array_merge([$status], $ids));

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
