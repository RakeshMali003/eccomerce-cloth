<?php
require_once "config/database.php";
$stmt = $pdo->query("DESCRIBE order_shipments");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
?>