<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login
    if (isset($_SERVER['HTTP_REFERER'])) {
        $_SESSION['error'] = "Please login to add items to cart.";
        header("Location: ../auth/login.php?redirect=" . urlencode($_SERVER['HTTP_REFERER']));
    } else {
        header("Location: ../auth/login.php");
    }
    exit();
}

$user_id = $_SESSION['user_id'];
$action = $_REQUEST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ADD TO CART
    if ($action === 'add') {
        $product_id = (int) $_POST['product_id'];
        $quantity = (int) ($_POST['quantity'] ?? 1);

        if ($quantity < 1)
            $quantity = 1;

        // Check availability
        $stmt = $pdo->prepare("SELECT price, stock FROM products WHERE product_id = ? AND status = 1");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($product && $product['stock'] >= $quantity) {
            // Check if already in cart
            $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$user_id, $product_id]);
            $existing = $stmt->fetch();

            if ($existing) {
                // Update quantity
                $new_qty = $existing['quantity'] + $quantity;
                if ($new_qty <= $product['stock']) {
                    $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?")
                        ->execute([$new_qty, $user_id, $product_id]);
                    $_SESSION['success'] = "Cart updated successfully!";
                } else {
                    $_SESSION['error'] = "Not enough stock available.";
                }
            } else {
                // Insert new
                $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)")
                    ->execute([$user_id, $product_id, $quantity]);
                $_SESSION['success'] = "Product added to cart!";
            }
        } else {
            $_SESSION['error'] = "Product out of stock or unavailable.";
        }

    }
    // UPDATE QUANTITY
    elseif ($action === 'update') {
        $product_id = (int) $_POST['product_id'];
        $quantity = (int) $_POST['quantity'];

        if ($quantity > 0) {
            // Check stock
            $stmt = $pdo->prepare("SELECT stock FROM products WHERE product_id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();

            if ($product && $quantity <= $product['stock']) {
                $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?")
                    ->execute([$quantity, $user_id, $product_id]);
                $_SESSION['success'] = "Cart updated.";
            } else {
                $_SESSION['error'] = "Requested quantity not available.";
            }
        } else {
            // Remove if quantity 0
            $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?")
                ->execute([$user_id, $product_id]);
            $_SESSION['success'] = "Item removed from cart.";
        }
    }
}

// REMOVE ITEM (GET)
if ($action === 'remove' && isset($_GET['product_id'])) {
    $product_id = (int) $_GET['product_id'];
    $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?")
        ->execute([$user_id, $product_id]);
    $_SESSION['success'] = "Item removed from cart.";
}

// CLEAR CART
if ($action === 'clear') {
    $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);
    $_SESSION['success'] = "Cart cleared.";
}

// Redirect back
$redirect = $_REQUEST['redirect'] ?? $_SERVER['HTTP_REFERER'] ?? 'cart.php';
if ($redirect === 'checkout') {
    $redirect = 'checkout.php';
}
header("Location: $redirect");
exit();
?>