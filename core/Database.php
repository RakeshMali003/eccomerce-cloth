<?php

namespace Core;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $pdo;

    // DB Config Properties
    private $host;
    private $db;
    private $user;
    private $pass;
    private $charset = 'utf8mb4';

    private function __construct()
    {
        // Initialize logic moved to constructor
        $this->host = defined('DB_HOST') ? \DB_HOST : 'localhost';
        $this->db = defined('DB_NAME') ? \DB_NAME : '';
        $this->user = defined('DB_USER') ? \DB_USER : 'root';
        $this->pass = defined('DB_PASS') ? \DB_PASS : '';

        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => true, // Key for high traffic
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            // Log error silently, show generic message
            error_log($e->getMessage());
            die(json_encode(["error" => "Service Unavailable"]));
        }
    }

    // Singleton Pattern to reuse connection
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->pdo;
    }

    // Helper for quick select
    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
?>