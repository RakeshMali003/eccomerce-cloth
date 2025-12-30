<?php
session_start();
require_once "../../config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id  = $_POST['supplier_id'];
    $bill_number  = $_POST['bill_number']; 
    $bill_date    = $_POST['bill_date'];
    $due_date     = $_POST['due_date'];
    $total_amount = $_POST['total_amount'];
    
    // Check if this came from an existing Purchase Order
    $po_id = isset($_POST['po_id']) ? $_POST['po_id'] : null;

    $product_ids = $_POST['product_id'];
    $qtys        = $_POST['qty'];
    $costs       = $_POST['cost'];

    try {
        $pdo->beginTransaction();

        if ($po_id) {
            // --- OPTION 1: Update existing PO status to 'received' ---
            $stmtUpdatePO = $pdo->prepare("UPDATE purchase_orders SET status = 'received', total_amount = ? WHERE po_id = ?");
            $stmtUpdatePO->execute([$total_amount, $po_id]);
        } else {
            // --- OPTION 2: Create a NEW Purchase Order record (Direct Entry) ---
            $stmtPO = $pdo->prepare("INSERT INTO purchase_orders (supplier_id, po_number, order_date, expected_delivery, total_amount, status) VALUES (?, ?, ?, ?, ?, 'received')");
            $stmtPO->execute([$supplier_id, $bill_number, $bill_date, $due_date, $total_amount]);
            $po_id = $pdo->lastInsertId();
        }

        // --- STEP B: Create Supplier Bill Record ---
        $stmtBill = $pdo->prepare("INSERT INTO supplier_bills (supplier_id, bill_number, bill_date, due_date, total_amount, balance_amount, payment_status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
        $stmtBill->execute([$supplier_id, $bill_number, $bill_date, $due_date, $total_amount, $total_amount]);

        // --- STEP C: Process Items & Update Stock ---
        // If it was a PO, we delete old items and re-insert what was actually received
        if (isset($_POST['po_id'])) {
            $pdo->prepare("DELETE FROM purchase_items WHERE po_id = ?")->execute([$po_id]);
        }

        $stmtItem = $pdo->prepare("INSERT INTO purchase_items (po_id, product_id, quantity, cost_price, total) VALUES (?, ?, ?, ?, ?)");
        $stmtStock = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE product_id = ?");

        foreach ($product_ids as $index => $p_id) {
            $qty = (int)$qtys[$index];
            $cost = (float)$costs[$index];
            $item_total = $qty * $cost;

            $stmtItem->execute([$po_id, $p_id, $qty, $cost, $item_total]);
            $stmtStock->execute([$qty, $p_id]);
        }

        $pdo->commit();
        $_SESSION['toast'] = ['msg' => 'Goods received successfully!', 'type' => 'success'];

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['toast'] = ['msg' => 'Error: ' . $e->getMessage(), 'type' => 'error'];
    }

    header("Location: pending_pos.php");
    exit();
}