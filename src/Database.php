<?php

namespace App;

use App\Core\DatabaseWrapper;
use PDO;

class Database
{
    private static ?self $instance = null;
    private DatabaseWrapper $wrapper;

    private function __construct()
    {
        $host =  $_ENV['DB_HOST'] ?? 'localhost';
        $db = $_ENV['DB_NAME'] ?? 'postgres';
        $user = (string) ($_ENV['DB_USER'] ?? 'postgres');
        $password = (string) ($_ENV['DB_PASSWORD'] ?? '');
        $port = $_ENV['DB_PORT'] ?? '5432';

        $pdo = new PDO(
            "pgsql:host=$host;port=$port;dbname=$db",
            $user,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $this->wrapper = new DatabaseWrapper($pdo);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getWrapper(): DatabaseWrapper
    {
        return $this->wrapper;
    }

    public function getConnection(): PDO
    {
        return $this->wrapper->getPdo();
    }
}
