<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Database;
use App\UserRepository;

try {
    $db = Database::getInstance();
    $repository = new UserRepository($db);

    $users = $repository->findByFilters($_GET);

    var_dump($users);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}