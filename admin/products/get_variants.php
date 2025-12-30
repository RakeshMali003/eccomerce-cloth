<?php
require_once "../../config/database.php";
$pid = $_GET['product_id'];
$stmt = $pdo->prepare("SELECT * FROM product_variants WHERE product_id = ?");
$stmt->execute([$pid]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));



