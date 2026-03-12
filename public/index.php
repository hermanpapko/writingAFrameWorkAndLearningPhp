<?php

ini_set('display_errors', '0');
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
    'debug' => true
]);

$uri = $_SERVER['REQUEST_URI'];
$path = (string) parse_url($uri, PHP_URL_PATH);

$isApiRequest = str_starts_with($path, '/count/') || $path === '/users/generate' || $path === '/users/import';

if ($isApiRequest) {
    $render = new \App\Views\JsonRenderer();
} else {
    $render = new \App\Views\TwigRenderer($twig);
}

use App\Core\Routing\Router;
use App\Controllers\UserController;

$router = new Router();

$userController = new UserController($render);

$router->get('/', [$userController, 'index']);
$router->post('/users/generate', [$userController, 'generate']);
$router->post('/users/import', [$userController, 'import']);

$router->get('/count/(\w+)', [$userController, 'countByField']);

$router->resolve($uri, $_SERVER['REQUEST_METHOD']);
