<?php
// 1. Silent Errors for clean JSON
error_reporting(0);
ini_set('display_errors', 0);

require_once "../../config/database.php";
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;
$raw_status = $_GET['status'] ?? null;

if (!$id || !$raw_status) {
    echo json_encode(['success' => false, 'error' => 'Missing ID or Status']);
    exit;
}

// 2. Correction Map for typos
$status_map = [
    'conform' => 'confirmed',
    'called' => 'cancelled',
    'pack' => 'packed'
];
$status = $status_map[strtolower(trim($raw_status))] ?? strtolower(trim($raw_status));

try {
    $pdo->beginTransaction();

    // --- TABLE 1: UPDATE ORDERS ---
    $stmt1 = $pdo->prepare("UPDATE orders SET order_status = ?, updated_at = NOW() WHERE order_id = ?");
    $stmt1->execute([$status, $id]);

    // --- TABLE 2: SYNC SHIPMENTS ---
    // Remove old shipment record for this order to prevent UNIQUE constraint errors
    $pdo->prepare("DELETE FROM order_shipments WHERE order_id = ?")->execute([$id]);

    // Insert fresh record with timestamp logic
    $shipSql = "INSERT INTO order_shipments (order_id, status, shipped_at, delivered_at) 
                VALUES (?, ?, 
                IF(? = 'shipped', NOW(), NULL), 
                IF(? = 'delivered', NOW(), NULL))";
    $pdo->prepare($shipSql)->execute([$id, $status, $status, $status]);

    // --- TABLE 3: INVENTORY LOGGING & STOCK SYNC ---

    // Check if we already have a record for this order to avoid double-deduction or missing replenishment
    $checkInv = $pdo->prepare("SELECT COUNT(*) FROM inventory WHERE reference_id = ? AND transaction_type = 'sale'");
    $checkInv->execute([$id]);
    $alreadySold = $checkInv->fetchColumn() > 0;

    $checkRestock = $pdo->prepare("SELECT COUNT(*) FROM inventory WHERE reference_id = ? AND transaction_type = 'restock'");
    $checkRestock->execute([$id]);
    $alreadyRestocked = $checkRestock->fetchColumn() > 0;

    if ($status === 'confirmed' && !$alreadySold) {
        // DEDUCT STOCK
        $items = $pdo->prepare("SELECT product_id, variant_id, quantity FROM order_items WHERE order_id = ?");
        $items->execute([$id]);
        $order_items = $items->fetchAll();

        foreach ($order_items as $item) {
            $st = $pdo->prepare("SELECT stock FROM products WHERE product_id = ?");
            $st->execute([$item['product_id']]);
            $current_stock = $st->fetchColumn();
            $new_balance = $current_stock - $item['quantity'];

            $invSql = "INSERT INTO inventory (product_id, variant_id, transaction_type, quantity, balance_qty, reference_id) 
                       VALUES (?, ?, 'sale', ?, ?, ?)";
            $pdo->prepare($invSql)->execute([$item['product_id'], $item['variant_id'], $item['quantity'], $new_balance, $id]);

            $pdo->prepare("UPDATE products SET stock = ? WHERE product_id = ?")->execute([$new_balance, $item['product_id']]);
        }
    } elseif (($status === 'cancelled' || $status === 'returned') && $alreadySold && !$alreadyRestocked) {
        // REPLENISH STOCK
        $items = $pdo->prepare("SELECT product_id, variant_id, quantity FROM order_items WHERE order_id = ?");
        $items->execute([$id]);
        $order_items = $items->fetchAll();

        foreach ($order_items as $item) {
            $st = $pdo->prepare("SELECT stock FROM products WHERE product_id = ?");
            $st->execute([$item['product_id']]);
            $current_stock = $st->fetchColumn();
            $new_balance = $current_stock + $item['quantity'];

            $invSql = "INSERT INTO inventory (product_id, variant_id, transaction_type, quantity, balance_qty, reference_id) 
                       VALUES (?, ?, 'restock', ?, ?, ?)";
            $pdo->prepare($invSql)->execute([$item['product_id'], $item['variant_id'], $item['quantity'], $new_balance, $id]);

            $pdo->prepare("UPDATE products SET stock = ? WHERE product_id = ?")->execute([$new_balance, $item['product_id']]);
        }
    }

    $pdo->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if ($pdo->inTransaction())
        $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}