<?php
require_once "config/database.php";

try {
    echo "<h2>Admin Table Columns:</h2>";
    $stmt = $pdo->query("DESCRIBE admin");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($columns);
    echo "</pre>";

    echo "<h2>Dealer Applications Table Columns:</h2>";
    $stmt = $pdo->query("DESCRIBE dealer_applications");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($columns);
    echo "</pre>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>