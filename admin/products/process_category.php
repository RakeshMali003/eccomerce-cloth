<?php
session_start();
require_once "../../config/database.php";

$action = $_REQUEST['action'] ?? '';

// --- DELETE ---
if ($action === 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE category_id = ?");
        $stmt->execute([$_GET['id']]);
        $_SESSION['toast'] = ['msg' => 'Category removed successfully.', 'type' => 'success'];
    } catch (Exception $e) {
        $_SESSION['toast'] = ['msg' => 'Error: Category is linked to products and cannot be deleted.', 'type' => 'error'];
    }
    header("Location: category.php");
    exit();
}

// --- ADD / EDIT ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $category_id = $_POST['category_id'] ?? null;

    try {
        if ($action === 'edit') {
            $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ?, updated_at = NOW() WHERE category_id = ?");
            $stmt->execute([$name, $description, $category_id]);
            $msg = "Category updated!";
        } else {
            $stmt = $pdo->prepare("INSERT INTO categories (name, description, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$name, $description]);
            $msg = "Category created successfully!";
        }
        $_SESSION['toast'] = ['msg' => $msg, 'type' => 'success'];
    } catch (Exception $e) {
        $_SESSION['toast'] = ['msg' => 'Error: ' . $e->getMessage(), 'type' => 'error'];
    }
    header("Location: category.php");
    exit();
}