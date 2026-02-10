<?php
session_start();
require_once __DIR__ . '/../config/database.php';


if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // 🛡️ Security Check: Minimum 8 Characters
    if (strlen($password) < 8) {
        header("Location: register.php?error=Password must be at least 8 digits");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, role, status, created_at) VALUES (?, ?, ?, ?, 'retail', 'active', NOW())");

    try {
        $stmt->execute([$name, $email, $hashed_password, $phone]);
        header("Location: login.php?success=Account created");
    } catch (Exception $e) {
        header("Location: register.php?error=Email already exists");
    }
}
?>