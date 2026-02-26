<?php

namespace App\Controllers;

use App\Database;
use App\UserRepository;
use App\CSVParser;
class UserController
{
    public function analyze()
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT * FROM public.users");
        $users = $stmt->fetchAll();

        echo "<h1>City:</h1>";
        echo "<ul>";
        foreach ($users as $user) {
            echo "<li>" . htmlspecialchars($user['city']) . "</li>";
        }
        echo "</ul>";
    }

    public function parse()
    {
        $db = Database::getInstance();
        $repository = new UserRepository($db);
        $parser = new CSVParser();

        $csvFile = dirname(__DIR__, 2) . '/data/users.csv';
        foreach ($parser->parse($csvFile) as $userData) {
            $repository->save($userData);
        }
        echo "Import successful";
    }
}