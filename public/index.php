<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Routing\Router;
use App\Controllers\UserController;

$router = new Router();

$router->get('/', [UserController::class, 'analyze']);
$router->get('/parse', [UserController::class, 'parse']);
$router->post('/generate', [UserController::class, 'generate']);

$router->post('/upload-csv', [UserController::class, 'upload']);

$router->resolve($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
