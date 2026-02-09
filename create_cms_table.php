<?php
require_once "config/database.php";

try {
    $sql = "
    CREATE TABLE IF NOT EXISTS page_content (
        content_id INT AUTO_INCREMENT PRIMARY KEY,
        page_name VARCHAR(50) NOT NULL,
        section_key VARCHAR(100) NOT NULL,
        content_value TEXT,
        content_type ENUM('text', 'image', 'rich_text') DEFAULT 'text',
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_content (page_name, section_key)
    );
    ";
    $pdo->exec($sql);
    echo "Table 'page_content' created successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>