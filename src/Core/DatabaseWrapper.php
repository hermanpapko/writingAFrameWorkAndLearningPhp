<?php

namespace App\Core;

use PDO;
use PDOStatement;

class DatabaseWrapper
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param string $sql
     * @param array<string, mixed> $params
     * @return bool
     */
    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }


    /**
     * @param string $sql
     * @param array<string, mixed> $params
     * @return array<int, array<string, mixed>>
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @param string $sql
     * @param array<string, mixed> $params
     * @return array<string, mixed>|null
     */
    public function fetch(string $sql, array $params = []): ?array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * @param string $sql
     * @param class-string $modelClass
     * @param array<string, mixed> $params
     * @return array<int, object>
     */
    public function fetchModels(string $sql, string $modelClass, array $params = []): array
    {
        $rows = $this->fetchAll($sql, $params);
        $models = [];

        foreach ($rows as $row) {
            if (method_exists($modelClass, 'hydrate')) {
                $models[] = $modelClass::hydrate($row);
            } else {
                $models[] = new $modelClass($row);
            }
        }

        return $models;
    }

    public function lastInsertId(?string $name = null): string|false
    {
        return $this->pdo->lastInsertId($name);
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}
