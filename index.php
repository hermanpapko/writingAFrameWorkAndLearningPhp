<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Database;

try {
    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->query("SELECT * FROM public.users");
    $users = $stmt->fetchAll();

    echo "<h1>City:</h1>";
    echo "<ul>";
    foreach ($users as $user) {
        echo "<li>" . htmlspecialchars($user['city']) . "</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}