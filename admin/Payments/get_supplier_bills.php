<?php
// get_supplier_bills.php
require_once "../../config/database.php";

header('Content-Type: application/json');

$supplier_id = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : 0;

if ($supplier_id > 0) {
    try {
        // We fetch only bills that are NOT fully paid
        $stmt = $pdo->prepare("SELECT bill_id, bill_number, total_amount 
                               FROM supplier_bills 
                               WHERE supplier_id = ? 
                               AND payment_status != 'Paid' 
                               ORDER BY due_date ASC");
        $stmt->execute([$supplier_id]);
        $bills = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($bills);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode([]);
}