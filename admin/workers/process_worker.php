<?php
session_start();
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'add') {
            // 1. Create User
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $role = 'worker';

            // Check if email exists
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                throw new Exception("Email already exists.");
            }

            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, status, created_at) VALUES (?, ?, ?, ?, ?, 'active', NOW())");
            $stmt->execute([$name, $email, $phone, $password, $role]);
            $user_id = $pdo->lastInsertId();

            // 2. Add to Workers
            $worker_role = $_POST['role'];
            $section = $_POST['section'];

            $stmt = $pdo->prepare("INSERT INTO workers (user_id, role, assigned_section, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$user_id, $worker_role, $section]);

            $pdo->commit();
            $_SESSION['success'] = "Worker added successfully.";

        } elseif ($action === 'delete') {
            $worker_id = (int) $_POST['worker_id'];
            $user_id = (int) $_POST['user_id'];

            $pdo->beginTransaction();
            // Delete from workers
            $pdo->prepare("DELETE FROM workers WHERE worker_id = ?")->execute([$worker_id]);
            // Delete from users (optional, but requested as 'remove access')
            $pdo->prepare("DELETE FROM users WHERE user_id = ?")->execute([$user_id]);

            $pdo->commit();
            $_SESSION['success'] = "Worker removed.";
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }

    header("Location: list.php");
    exit();
}
?>