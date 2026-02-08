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
            $address = $_POST['address'] ?? null;
            $contact = $_POST['contact_number'] ?? null;
            $bank = $_POST['bank_details'] ?? null;
            $salary = $_POST['salary'] ?? 0.00;

            // Permissions
            $permissions = isset($_POST['permissions']) ? json_encode($_POST['permissions']) : json_encode([]);

            // Handle File Uploads
            $uploaded_docs = [];
            if (isset($_FILES['documents']) && !empty($_FILES['documents']['name'][0])) {
                $target_dir = "../../assets/uploads/workers/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                foreach ($_FILES['documents']['tmp_name'] as $key => $tmp_name) {
                    $file_name = $_FILES['documents']['name'][$key];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    $unique_name = uniqid() . '.' . $file_ext;
                    $target_file = $target_dir . $unique_name;

                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $uploaded_docs[] = $unique_name;
                    }
                }
            }
            $docs_json = json_encode($uploaded_docs);

            $stmt = $pdo->prepare("INSERT INTO workers (user_id, role, assigned_section, address, contact_number, documents, bank_details, salary, permissions, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$user_id, $worker_role, $section, $address, $contact, $docs_json, $bank, $salary, $permissions]);

            $pdo->commit();
            $_SESSION['success'] = "Worker added successfully with all details.";

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