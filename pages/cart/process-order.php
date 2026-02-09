<?php
session_start();
require_once '../../config/database.php';

$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;

// Fetch cart: DB for logged-in users, session for guests
if ($is_logged_in) {
    $stmt = $pdo->prepare("
        SELECT c.product_id, c.quantity, c.variant_id, c.purchase_type,
               p.name, p.price, p.wholesale_price, p.min_wholesale_qty, p.stock
        FROM cart c 
        JOIN products p ON c.product_id = p.product_id 
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $db_cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $cart = [];
    foreach ($db_cart as $item) {
        $cart[$item['product_id']] = [
            'name' => $item['name'],
            'price' => (float) $item['price'],
            'wholesale_price' => (float) $item['wholesale_price'],
            'min_wholesale_qty' => (int) $item['min_wholesale_qty'],
            'stock' => (int) $item['stock'],
            'variant_id' => $item['variant_id'],
            'purchase_type' => $item['purchase_type'],
            'quantity' => (int) $item['quantity']
        ];
    }
} else {
    $cart = $_SESSION['cart'] ?? [];
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($cart)) {
    header("Location: ../products/product-list.php");
    exit();
}

$customer_name = $_POST['customer_name'] ?? '';
$customer_phone = $_POST['customer_phone'] ?? '';
$shipping_address = $_POST['shipping_address'] ?? '';
$city = $_POST['city'] ?? '';
$pincode = $_POST['pincode'] ?? '';
$payment_method = $_POST['payment_method'] ?? 'upi';

try {
    $pdo->beginTransaction();

    // 1. Handle User Information
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    } else {
        // Create a guest user account if not logged in
        $dummy_email = "guest_" . time() . "@joshielectricals.com";
        $dummy_pass = password_hash(bin2hex(random_bytes(8)), PASSWORD_BCRYPT);

        $user_stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, status, created_at) VALUES (?, ?, ?, ?, 'user', 'active', NOW())");
        $user_stmt->execute([$customer_name, $dummy_email, $customer_phone, $dummy_pass]);
        $user_id = $pdo->lastInsertId();
        $_SESSION['user_id'] = $user_id; // Log them in as guest
    }

    // 2. Calculate Totals
    $subtotal = 0;
    foreach ($cart as $item) {
        $price = ($item['quantity'] >= $item['min_wholesale_qty']) ? $item['wholesale_price'] : $item['price'];
        $subtotal += $price * $item['quantity'];
    }

    $gst_amount = round($subtotal * 0.12);
    $total_amount = $subtotal + $gst_amount;

    // Adjust for payment method fees/discounts
    if ($payment_method === 'cod') {
        $total_amount += 150;
    } elseif ($payment_method === 'bank') {
        $total_amount -= ($subtotal * 0.01);
    }

    // 3. Update User Address with shipping info (if logged in or just created)
    $update_addr = $pdo->prepare("UPDATE users SET address = ?, city = ?, pincode = ?, phone = ? WHERE user_id = ?");
    $update_addr->execute([$shipping_address, $city, $pincode, $customer_phone, $user_id]);

    // 4. Insert Order (without shipping_address since column doesn't exist)
    $order_stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, gst_amount, order_status, payment_method, payment_status, created_at) VALUES (?, ?, ?, 'pending', ?, 'pending', NOW())");
    $order_stmt->execute([$user_id, $total_amount, $gst_amount, $payment_method]);
    $order_id = $pdo->lastInsertId();

    // 5. Insert Order Items & Update Stock
    $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
    $stock_stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?");

    foreach ($cart as $pid => $item) {
        $price = ($item['quantity'] >= $item['min_wholesale_qty']) ? $item['wholesale_price'] : $item['price'];
        $item_stmt->execute([$order_id, $pid, $item['quantity'], $price]);

        // Update stock
        $stock_stmt->execute([$item['quantity'], $pid]);
    }

    $pdo->commit();

    // 5. Clear Cart and Redirect
    if ($is_logged_in) {
        // Clear database cart for logged-in users
        $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);
    }
    unset($_SESSION['cart']);
    header("Location: order-confirmation.php?id=" . $order_id);
    exit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Error processing order: " . $e->getMessage());
}
