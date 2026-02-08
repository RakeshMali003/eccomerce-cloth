<?php
require_once 'config/database.php';
try {
    $sql = "CREATE TABLE IF NOT EXISTS workers (
        worker_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        role VARCHAR(50) DEFAULT 'Staff',
        permissions TEXT,
        assigned_section VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Table workers created successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>