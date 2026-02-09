<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once "../../config/database.php";
require_once "../../includes/functions.php";

// Check permissions
session_start();
if (($_SESSION['role'] ?? '') !== 'admin') {
    die("Access Denied");
}

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    die("Order ID is required");
}

try {
    // 1. Check if order exists
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();

    if (!$order) {
        die("Order not found");
    }

    // 2. Check if invoice already exists
    $stmt = $pdo->prepare("SELECT invoice_id FROM invoices WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        header("Location: invoices.php?id=" . $existing['invoice_id']);
        exit;
    }

    // 3. Generate Invoice Data
    $invoice_number = "INV-" . date('Y') . "-" . str_pad($order_id, 6, "0", STR_PAD_LEFT);

    // Calculate totals from order items to be sure, or use order totals
    // Using order totals for consistency
    $subtotal = $order['total_amount'] - $order['gst_amount']; // Approximate if not stored

    // Better to recalculate from items for subtotal split if needed, but for now using order data
    // The invoice table has columns: order_id, invoice_number, invoice_date, total_amount, gst_total, subtotal

    $insert = $pdo->prepare("INSERT INTO invoices (order_id, invoice_number, invoice_date, total_amount, gst_total, subtotal) VALUES (?, ?, NOW(), ?, ?, ?)");
    $insert->execute([
        $order_id,
        $invoice_number,
        $order['total_amount'],
        $order['gst_amount'],
        $subtotal
    ]);

    $invoice_id = $pdo->lastInsertId();

    header("Location: invoices.php?id=" . $invoice_id);
    exit;

} catch (PDOException $e) {
    die("Error generating invoice: " . $e->getMessage());
}
