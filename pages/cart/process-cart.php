<?php
session_start();
require_once '../../config/database.php';

$action = $_POST['action'] ?? '';
$product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
$variant_id = isset($_POST['variant_id']) ? (int) $_POST['variant_id'] : null;
$quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;
$purchase_type = $_POST['purchase_type'] ?? 'retail';

$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;

// Helper: Get cart from DB for logged-in user
function getDbCart($pdo, $user_id)
{
    $stmt = $pdo->prepare("
        SELECT c.*, p.name, p.price, p.wholesale_price, p.min_wholesale_qty, p.stock, p.main_image 
        FROM cart c 
        JOIN products p ON c.product_id = p.product_id 
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Helper: Sync session cart to DB on login
function syncSessionCartToDb($pdo, $user_id)
{
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $pid => $item) {
            $variant_id = $item['variant_id'] ?? null;
            $qty = $item['quantity'];
            $type = $item['purchase_type'] ?? 'retail';

            // Check if already in DB
            $check = $pdo->prepare("SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
            $check->execute([$user_id, $pid]);
            $existing = $check->fetch();

            if ($existing) {
                $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE cart_id = ?")->execute([$qty, $existing['cart_id']]);
            } else {
                $pdo->prepare("INSERT INTO cart (user_id, product_id, variant_id, purchase_type, quantity) VALUES (?, ?, ?, ?, ?)")
                    ->execute([$user_id, $pid, $variant_id, $type, $qty]);
            }
        }
        // Clear session cart after sync
        $_SESSION['cart'] = [];
    }
}

// ADD TO CART
if ($action === 'add') {
    $stmt = $pdo->prepare("SELECT product_id, name, price, wholesale_price, min_wholesale_qty, stock, main_image FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if ($product) {
        if ($is_logged_in) {
            // DB-based cart for logged-in users
            $check = $pdo->prepare("SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
            $check->execute([$user_id, $product_id]);
            $existing = $check->fetch();

            if ($existing) {
                $new_qty = min($existing['quantity'] + $quantity, $product['stock']);
                $pdo->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?")->execute([$new_qty, $existing['cart_id']]);
            } else {
                $qty = min($quantity, $product['stock']);
                $pdo->prepare("INSERT INTO cart (user_id, product_id, variant_id, purchase_type, quantity) VALUES (?, ?, ?, ?, ?)")
                    ->execute([$user_id, $product_id, $variant_id, $purchase_type, $qty]);
            }
        } else {
            // Session-based cart for guests
            if (!isset($_SESSION['cart']))
                $_SESSION['cart'] = [];

            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'name' => $product['name'],
                    'price' => (float) $product['price'],
                    'wholesale_price' => (float) $product['wholesale_price'],
                    'min_wholesale_qty' => (int) $product['min_wholesale_qty'],
                    'stock' => (int) $product['stock'],
                    'main_image' => $product['main_image'],
                    'variant_id' => $variant_id,
                    'purchase_type' => $purchase_type,
                    'quantity' => $quantity
                ];
            }

            // Ensure quantity doesn't exceed stock
            if ($_SESSION['cart'][$product_id]['quantity'] > $product['stock']) {
                $_SESSION['cart'][$product_id]['quantity'] = $product['stock'];
            }
        }

        if (isset($_POST['buy_now'])) {
            header("Location: checkout.php");
        } else {
            header("Location: cart.php?msg=Product added to cart!");
        }
        exit();
    }
}

// UPDATE QUANTITY
if ($action === 'update') {
    if ($is_logged_in) {
        $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?")
            ->execute([max(1, $quantity), $user_id, $product_id]);
    } else {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] = max(1, $quantity);
        }
    }
    header("Location: cart.php");
    exit();
}

// REMOVE FROM CART
if ($action === 'remove') {
    if ($is_logged_in) {
        $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?")->execute([$user_id, $product_id]);
    } else {
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    header("Location: cart.php");
    exit();
}

// CLEAR ENTIRE CART
if ($action === 'clear') {
    if ($is_logged_in) {
        $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);
    } else {
        $_SESSION['cart'] = [];
    }
    header("Location: cart.php");
    exit();
}

header("Location: ../products/product-list.php");
exit();
