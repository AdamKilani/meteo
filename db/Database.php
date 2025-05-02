<?php
// db/Database.php
class Database {
    private $connection;
    private $db_file = "db/weather.db";

    public function __construct() {
        try {
            $this->connection = new PDO("sqlite:" . $this->db_file);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->initializeDatabase();
            // Créer le répertoire de cache si nécessaire
            if (!is_dir('cache')) {
                mkdir('cache', 0755, true);
            }
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    private function initializeDatabase() {
        $query = "
            CREATE TABLE IF NOT EXISTS favorites (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                location TEXT NOT NULL,
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        try {
            $this->connection->exec($query);
        } catch (PDOException $e) {
            die("Error creating table: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }
}