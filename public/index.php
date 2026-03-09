<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

use App\Core\Routing\Router;
use App\Controllers\UserController;

$router = new Router();

$router->get('/', [UserController::class, 'index']);
$router->post('/users/generate', [UserController::class, 'generate']);
$router->post('/users/import', [UserController::class, 'import']);

$router->resolve($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
