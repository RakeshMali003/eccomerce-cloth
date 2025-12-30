<?php
session_start();
require_once "../../config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Collect PO Header Data
    $supplier_id       = $_POST['supplier_id'];
    $po_number         = $_POST['po_number'];
    $expected_delivery = $_POST['expected_delivery'];
    $total_amount      = $_POST['total_amount'];
    $order_date        = date('Y-m-d H:i:s');

    // 2. Collect Items Arrays
    $product_ids = $_POST['product_id'];
    $qtys        = $_POST['qty'];
    $rates       = $_POST['rate']; // Use 'rate' to distinguish from 'cost' in Bills

    try {
        $pdo->beginTransaction();

        // --- STEP A: Create the Purchase Order ---
        // Status is set to 'pending' because goods haven't arrived.
        // No entry is made in supplier_bills here.
        $stmtPO = $pdo->prepare("INSERT INTO purchase_orders 
            (supplier_id, po_number, order_date, expected_delivery, total_amount, status) 
            VALUES (?, ?, ?, ?, ?, 'pending')");
        
        $stmtPO->execute([$supplier_id, $po_number, $order_date, $expected_delivery, $total_amount]);
        $po_id = $pdo->lastInsertId();

        // --- STEP B: Save Ordered Items ---
        // Note: We DO NOT update the 'products' table stock here.
        $stmtItem = $pdo->prepare("INSERT INTO purchase_items 
            (po_id, product_id, quantity, cost_price, total) 
            VALUES (?, ?, ?, ?, ?)");

        foreach ($product_ids as $index => $p_id) {
            $qty = (int)$qtys[$index];
            $rate = (float)$rates[$index];
            $item_total = $qty * $rate;

            $stmtItem->execute([$po_id, $p_id, $qty, $rate, $item_total]);
        }

        $pdo->commit();
        $_SESSION['toast'] = ['msg' => 'Purchase Order Issued successfully!', 'type' => 'success'];

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['toast'] = ['msg' => 'PO Error: ' . $e->getMessage(), 'type' => 'error'];
    }

    // Redirect to the list of pending orders
    header("Location: pending_pos.php");
    exit();
}