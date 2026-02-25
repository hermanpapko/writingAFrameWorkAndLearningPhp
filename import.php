<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Database;
use App\CSVParser;
use App\UserRepository;

try {
    $db = Database::getInstance();
    $repository = new UserRepository($db);
    $parser = new CSVParser();

    foreach ($parser->parse(__DIR__ . '/data/users.csv') as $userData) {
        $repository->save($userData);
    }
    echo "Import successful";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}