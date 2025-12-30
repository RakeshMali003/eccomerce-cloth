<?php
require_once "../../config/database.php";
if($_POST) {
    $sql = "INSERT INTO product_variants (product_id, size, color, stock_qty) VALUES (?, ?, ?, ?)";
    $pdo->prepare($sql)->execute([
        $_POST['product_id'], $_POST['size'], $_POST['color'], $_POST['stock_qty']
    ]);
    
    // Auto-update parent total stock
    $pdo->prepare("UPDATE products SET stock = (SELECT SUM(stock_qty) FROM product_variants WHERE product_id = ?) WHERE product_id = ?")
        ->execute([$_POST['product_id'], $_POST['product_id']]);
}