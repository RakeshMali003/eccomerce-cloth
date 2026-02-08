<?php
require_once "../../config/database.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];
    $strategy = $_POST['strategy']; // skip or update

    if (($handle = fopen($file, "r")) !== FALSE) {
        $headers = fgetcsv($handle, 1000, ",");
        // Expected headers: name, category, price, stock, description

        $pdo->beginTransaction();
        try {
            $count = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Simplified import logic
                $name = $data[0];
                $category_name = $data[1];
                $price = $data[2];
                $stock = $data[3];
                $desc = $data[4];

                // Get category ID or use a default
                $catStmt = $pdo->prepare("SELECT category_id FROM categories WHERE name = ?");
                $catStmt->execute([$category_name]);
                $cat = $catStmt->fetch();
                $cat_id = $cat ? $cat['category_id'] : 1;

                if ($strategy === 'update') {
                    $stmt = $pdo->prepare("INSERT INTO products (name, category_id, price, stock, description) 
                                         VALUES (?, ?, ?, ?, ?) 
                                         ON DUPLICATE KEY UPDATE price = VALUES(price), stock = VALUES(stock)");
                    $stmt->execute([$name, $cat_id, $price, $stock, $desc]);
                } else {
                    $stmt = $pdo->prepare("INSERT IGNORE INTO products (name, category_id, price, stock, description) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $cat_id, $price, $stock, $desc]);
                }
                $count++;
            }
            $pdo->commit();
            $_SESSION['success'] = "Catalog manifests synchronized: $count items processed.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['error'] = "Manifest sync failed: " . $e->getMessage();
        }
        fclose($handle);
    }
    header("Location: products-list.php");
    exit();
}
?>