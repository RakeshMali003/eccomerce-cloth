<?php
require_once "../../config/database.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target = $_POST['target'];
    $type = $_POST['type'];
    $title = $_POST['title'];
    $message = $_POST['message'];
    $user_id = $_POST['user_id'] ?? null;

    try {
        if ($target === 'all') {
            // Get all customers
            $stmt = $pdo->query("SELECT user_id FROM users WHERE role = 'customer'");
            $users = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $pdo->beginTransaction();
            $insert = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type, status) VALUES (?, ?, ?, ?, 'unread')");
            foreach ($users as $uid) {
                $insert->execute([$uid, $title, $message, $type]);
            }
            $pdo->commit();
            $_SESSION['success'] = "Broadcast successfully delivered to " . count($users) . " customers.";
        } else {
            // Specific user
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type, status) VALUES (?, ?, ?, ?, 'unread')");
            $stmt->execute([$user_id, $title, $message, $type]);
            $_SESSION['success'] = "Personalized alert dispatched to recipient.";
        }
    } catch (PDOException $e) {
        if ($pdo->inTransaction())
            $pdo->rollBack();
        $_SESSION['error'] = "Delivery failure: " . $e->getMessage();
    }

    header("Location: send-notification.php");
    exit();
}
?>