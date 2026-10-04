<?php
class Database
{
    private static ?Database $instance = null;
    private PDO $conn;

    private function __construct()
    {
        $host = 'localhost';
        $dbname = 'inventaris_db';
        $username = 'root';
        $password = '';

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->conn = new PDO(
                "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
                $username,
                $password,
                $options
            );
        } catch (PDOException $e) {
            die('Koneksi database gagal. Pastikan MySQL aktif dan database sudah di-import.');
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->conn;
    }
}
