<?php
require_once "../../config/database.php";

// admin/inventory/low-stock-alerts.php (Line 9)
$query = "SELECT p.name, p.stock, p.min_stock_level, s.name as supplier 
          FROM products p 
          LEFT JOIN suppliers s ON p.preferred_supplier_id = s.supplier_id 
          WHERE p.stock <= p.min_stock_level";

try {
    $items = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // This will tell us if 'supplier_id' in suppliers table is actually named something else
    die("Database Error: " . $e->getMessage());
}

// 2. Prepare Email Content
$to = "admin@yourwebsite.com"; // CHANGE THIS
$subject = "⚠️ Low Stock Alert: " . date('d M Y');

$message = "
<html>
<body style='font-family: Arial, sans-serif; color: #333;'>
    <h2 style='color: #e63946;'>Inventory Shortage Report</h2>
    <p>The following items have fallen below their minimum stock levels:</p>
    <table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%;'>
        <thead>
            <tr style='background: #f8f9fa;'>
                <th>Product Name</th>
                <th>Current Stock</th>
                <th>Min Level</th>
                <th>Suggested Supplier</th>
            </tr>
        </thead>
        <tbody>";

foreach ($items as $item) {
    $message .= "
        <tr>
            <td>{$item['name']}</td>
            <td style='color: red; font-weight: bold;'>{$item['stock']}</td>
            <td>{$item['min_stock_level']}</td>
            <td>" . ($item['supplier'] ?? 'Not Assigned') . "</td>
        </tr>";
}

$message .= "
        </tbody>
    </table>
    <p><a href='http://yourdomain.com/admin/Purchases/Purchase%20Order.php' 
          style='display: inline-block; padding: 10px 20px; background: #1d1d1d; color: #fff; text-decoration: none; border-radius: 5px;'>
          Create Purchase Orders Now
       </a></p>
</body>
</html>";

// 3. Set Headers
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: system@yourwebsite.com" . "\r\n";

// 4. Send Email
mail($to, $subject, $message, $headers);