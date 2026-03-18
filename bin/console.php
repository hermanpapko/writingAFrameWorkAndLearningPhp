<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$db = Database::getInstance()->getWrapper();

echo "Running migrations...\n";

$db->execute("
    CREATE TABLE IF NOT EXISTS migrations_history (
        id SERIAL PRIMARY KEY,
        migration_name VARCHAR(255) NOT NULL UNIQUE,
        executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

$history = $db->fetchAll("SELECT migration_name FROM migrations_history");

$executedMigrations = array_column($history, 'migration_name');

$migrationsDir = __DIR__ . '/../migrations';
if (!is_dir($migrationsDir)) {
    echo "Directory not found: {$migrationsDir}\n";
    exit(1);
}

$files = glob($migrationsDir . '/*.sql');
sort($files);

$appliedCount = 0;

foreach ($files as $file) {
    $migrationName = basename($file);

    if (!in_array($migrationName, $executedMigrations, true)) {
        echo "Applying: {$migrationName}...\n";
        $sql = file_get_contents($file);

        try {
            $db->getPdo()->exec($sql);

            $db->execute(
                "INSERT INTO migrations_history (migration_name) VALUES (:name)",
                ['name' => $migrationName]
            );

            echo "Successfully applied: {$migrationName}\n";
            $appliedCount++;
        } catch (\PDOException $e) {
            echo "Error applying migration {$migrationName}:\n" . $e->getMessage() . "\n";
            echo "Aborting.\n";
            exit(1);
        }
    }
}

if ($appliedCount === 0) {
    echo "Nothing to migrate. Database is up to date.\n";
} else {
    echo "Migrations completed successfully.\n";
}
