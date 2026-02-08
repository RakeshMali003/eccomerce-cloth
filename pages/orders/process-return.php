<?php
require_once "../../config/database.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $order_id = $_POST['order_id'];
    $reason = $_POST['reason'];
    $comments = $_POST['comments'];
    $user_id = $_SESSION['user_id'];

    try {
        // Verify order belongs to user and is delivered
        $stmt = $pdo->prepare("SELECT order_id FROM orders WHERE order_id = ? AND user_id = ? AND order_status = 'delivered'");
        $stmt->execute([$order_id, $user_id]);
        $order = $stmt->fetch();

        if ($order) {
            // Update order status to 'returned' (simplified for now, usually needs a middle state)
            $update = $pdo->prepare("UPDATE orders SET order_status = 'returned', updated_at = CURRENT_TIMESTAMP WHERE order_id = ?");
            $update->execute([$order_id]);

            // Track the return details in notifications or a dedicated table (if existed)
            // For now, we'll send a notification to the user acknowledging the request
            $notif = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, 'Return Initiated', ?, 'order')");
            $notif->execute([$user_id, "Your return request for Order #ORD-$order_id has been received. Reason: $reason. Our partner will contact you shortly."]);

            $_SESSION['success'] = "Return request confirmed! Check your notifications for next steps.";
            header("Location: order-history.php");
        } else {
            $_SESSION['error'] = "Invalid order or not eligible for return.";
            header("Location: return-request.php");
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "System error: " . $e->getMessage();
        header("Location: return-request.php");
    }
    exit();
}
?>