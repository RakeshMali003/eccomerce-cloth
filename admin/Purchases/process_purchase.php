<?php
session_start();
require_once "../../config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Collect Header Data
    $supplier_id  = $_POST['supplier_id'];
    $bill_number  = $_POST['bill_number']; 
    $bill_date    = $_POST['bill_date'];
    $due_date     = $_POST['due_date'];
    $total_amount = $_POST['total_amount'];
    
    $po_id = !empty($_POST['po_id']) ? $_POST['po_id'] : null;

    // 2. Collect Repeater Data
    $product_ids   = $_POST['product_id'];   // IDs (will be empty for new items)
    $product_names = $_POST['product_name']; // Item names
    $qtys          = $_POST['qty'];
    $costs         = $_POST['cost'];

    try {
        $pdo->beginTransaction();

        // --- STEP A: HANDLE PURCHASE ORDER ---
        if ($po_id) {
            $stmtUpdatePO = $pdo->prepare("UPDATE purchase_orders SET status = 'received', total_amount = ? WHERE po_id = ?");
            $stmtUpdatePO->execute([$total_amount, $po_id]);
            $pdo->prepare("DELETE FROM purchase_items WHERE po_id = ?")->execute([$po_id]);
        } else {
            $stmtPO = $pdo->prepare("INSERT INTO purchase_orders (supplier_id, po_number, order_date, expected_delivery, total_amount, status) VALUES (?, ?, ?, ?, ?, 'received')");
            $stmtPO->execute([$supplier_id, $bill_number, $bill_date, $due_date, $total_amount]);
            $po_id = $pdo->lastInsertId();
        }

        // --- STEP B: CREATE SUPPLIER BILL ---
        $stmtBill = $pdo->prepare("INSERT INTO supplier_bills (supplier_id, bill_number, bill_date, due_date, total_amount, balance_amount, payment_status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
        $stmtBill->execute([$supplier_id, $bill_number, $bill_date, $due_date, $total_amount, $total_amount]);

      // --- STEP C: PROCESS PRODUCTS (AUTO-CREATE OR UPDATE) ---
$stmtItem = $pdo->prepare("INSERT INTO purchase_items (po_id, product_id, quantity, cost_price, total) VALUES (?, ?, ?, ?, ?)");
$stmtStock = $pdo->prepare("UPDATE products SET stock = stock + ?, price = ? WHERE product_id = ?");

foreach ($product_ids as $index => $p_id) {
    $p_name = trim($product_names[$index]);
    if (empty($p_name)) continue;

    $qty = (int)$qtys[$index];
    $cost = (float)$costs[$index];
    $total = $qty * $cost;

    if (empty($p_id)) {
        // Double check by name to avoid duplicates
        $check = $pdo->prepare("SELECT product_id FROM products WHERE name = ? LIMIT 1");
        $check->execute([$p_name]);
        $existing = $check->fetch();

        if ($existing) {
            $p_id = $existing['product_id'];
            $stmtStock->execute([$qty, $cost, $p_id]);
        } else {
            // Auto-create with minimum requirements
            $gen_sku = "AUTO-" . time() . "-" . $index;
           $ins = $pdo->prepare("
INSERT INTO products 
(
 supplier_id,
 category_id,
 preferred_supplier_id,
 name,
 sku,
 description,
 price,
 wholesale_price,
 min_wholesale_qty,
 gst_percent,
 discount_percent,
 status,
 created_at,
 stock,
 min_stock_level
)
VALUES
(
 ?, 1, ?, ?, ?, '', ?, 0, 0, 0, 0, 1, NOW(), ?, 5
)
");

$ins->execute([
    $supplier_id,      // supplier_id
    $supplier_id,      // preferred_supplier_id
    $p_name,           // name
    $gen_sku,          // sku
    $cost,             // price
    $qty               // stock
]);

            $p_id = $pdo->lastInsertId();
        }
    } else {
        $stmtStock->execute([$qty, $cost, $p_id]);
    }

    $stmtItem->execute([$po_id, $p_id, $qty, $cost, $total]);
}


        $pdo->commit();
        $_SESSION['toast'] = ['msg' => 'Inventory Updated! New products registered.', 'type' => 'success'];

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $_SESSION['toast'] = ['msg' => 'System Error: ' . $e->getMessage(), 'type' => 'error'];
    }

    header("Location: Purchase Bill.php");
    exit();
}