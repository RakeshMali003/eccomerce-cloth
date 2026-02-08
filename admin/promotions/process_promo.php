<?php
require_once "../../config/database.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'add';
    $discount_id = $_POST['discount_id'] ?? null;
    $code = strtoupper(trim($_POST['code']));
    $type = $_POST['type'];
    $value = $_POST['value'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $usage_limit = $_POST['usage_limit'];
    $status = $_POST['status'];

    try {
        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO discounts (code, type, value, start_date, end_date, usage_limit, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$code, $type, $value, $start_date, $end_date, $usage_limit, $status]);
            $_SESSION['success'] = "Promo campaign launched successfully!";
        } elseif ($action === 'edit' && $discount_id) {
            $stmt = $pdo->prepare("UPDATE discounts SET code = ?, type = ?, value = ?, start_date = ?, end_date = ?, usage_limit = ?, status = ? WHERE discount_id = ?");
            $stmt->execute([$code, $type, $value, $start_date, $end_date, $usage_limit, $status, $discount_id]);
            $_SESSION['success'] = "Campaign parameters refined.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Operation failed: " . $e->getMessage();
    }
    header("Location: promo-codes.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    try {
        $stmt = $pdo->prepare("DELETE FROM discounts WHERE discount_id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "Campaign terminated.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Deletion failed: " . $e->getMessage();
    }
    header("Location: promo-codes.php");
    exit();
}
