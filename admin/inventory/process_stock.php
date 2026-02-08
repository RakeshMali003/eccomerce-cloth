<?php
session_start();
require_once "../../config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid = $_POST['product_id'];
    $adj = (int)$_POST['adjustment_qty'];
    $reason = $_POST['reason'] ?? 'Manual Adjustment';

    try {
        $pdo->beginTransaction();

        // 1. Get current stock before update
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE product_id = ? FOR UPDATE");
        $stmt->execute([$pid]);
        $old_qty = $stmt->fetchColumn();
        
        $new_qty = $old_qty + $adj;

        // 2. Update Product Table
        $update = $pdo->prepare("UPDATE products SET stock = ? WHERE product_id = ?");
        $update->execute([$new_qty, $pid]);

        // 3. Insert into Audit Log
        $log = $pdo->prepare("INSERT INTO stock_logs (product_id, old_qty, adjustment, new_qty, reason) VALUES (?, ?, ?, ?, ?)");
        $log->execute([$pid, $old_qty, $adj, $new_qty, $reason]);

        $pdo->commit();
        $_SESSION['toast'] = ['msg' => 'Inventory synced and logged.', 'type' => 'success'];
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['toast'] = ['msg' => 'Update failed: ' . $e->getMessage(), 'type' => 'error'];
    }
    header("Location: stock-list.php");
    exit();
}