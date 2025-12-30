<?php
// admin/Payments/get_pending_bills.php
header('Content-Type: application/json');
require_once "../../config/database.php";

if (isset($_GET['supplier_id'])) {
    $sid = (int)$_GET['supplier_id'];
    
    // Using your actual table: supplier_bills
    $stmt = $pdo->prepare("SELECT bill_id, bill_number, balance_amount 
                           FROM supplier_bills 
                           WHERE supplier_id = ? AND balance_amount > 0");
    $stmt->execute([$sid]);
    $bills = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($bills);
} else {
    echo json_encode([]);
}
exit();