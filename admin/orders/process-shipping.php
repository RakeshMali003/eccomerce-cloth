<?php
require_once "../../config/database.php";
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $courier = $_POST['courier_name'];
    $tracking_id = $_POST['tracking_id'];
    $notes = $_POST['notes'] ?? '';

    try {
        $pdo->beginTransaction();

        // 1. Update Order Status to 'shipped'
        $stmtOrder = $pdo->prepare("UPDATE orders SET order_status = 'shipped', updated_at = NOW() WHERE order_id = ?");
        $stmtOrder->execute([$order_id]);

        // 2. Clear old shipment record and insert fresh one with tracking details
        $pdo->prepare("DELETE FROM order_shipments WHERE order_id = ?")->execute([$order_id]);

        $shipSql = "INSERT INTO order_shipments (order_id, status, tracking_number, courier_name, notes, shipped_at) 
                    VALUES (?, 'shipped', ?, ?, ?, NOW())";
        $pdo->prepare($shipSql)->execute([$order_id, $tracking_id, $courier, $notes]);

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        if ($pdo->inTransaction())
            $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>