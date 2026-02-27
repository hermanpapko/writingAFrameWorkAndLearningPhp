<?php

namespace App;

use PDO;

class Database
{
    private static ?self $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $host =  getenv('DB_HOST');
        $db = getenv('DB_NAME');
        $user = (string) getenv('DB_USER');
        $password = (string) getenv('DB_PASSWORD');
        $port = getenv('DB_PORT');

        $this->pdo = new PDO(
            "pgsql:host=$host;port=$port;dbname=$db",
            $user,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}
