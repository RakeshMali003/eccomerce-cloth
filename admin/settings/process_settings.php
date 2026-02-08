<?php
require_once "../../config/database.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['settings'])) {
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");

        foreach ($_POST['settings'] as $key => $value) {
            $stmt->execute([$value, $key]);
        }

        $pdo->commit();
        $_SESSION['success'] = "Global configurations synchronized.";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Synchronization failed: " . $e->getMessage();
    }

    header("Location: site-settings.php");
    exit();
}
?>