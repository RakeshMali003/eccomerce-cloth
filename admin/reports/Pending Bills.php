<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';

require_once "../../config/database.php";
if (isset($_GET['supplier_id'])) {
    $sid = filter_var($_GET['supplier_id'], FILTER_VALIDATE_INT);
    
    // Selecting only bills that aren't fully paid
    $stmt = $pdo->prepare("SELECT bill_id, bill_number, balance_amount 
                           FROM supplier_bills 
                           WHERE supplier_id = ? AND balance_amount > 0 AND payment_status != 'Paid'");
    $stmt->execute([$sid]);
    $bills = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($bills);
    exit();
}