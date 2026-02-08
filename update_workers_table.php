<?php
require_once 'config/database.php';

try {
    // Check if columns exist to avoid errors on re-run (simple check)
    // Or just try-catch each

    $cols = [
        "address TEXT",
        "contact_number VARCHAR(20)",
        "documents TEXT",
        "bank_details TEXT",
        "salary DECIMAL(10,2) DEFAULT 0.00",
        "permissions TEXT"
    ];

    foreach ($cols as $col) {
        try {
            $pdo->exec("ALTER TABLE workers ADD COLUMN $col");
            echo "Added $col<br>";
        } catch (PDOException $e) {
            // Likely column already exists
        }
    }

    echo "Workers table schema update complete.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>