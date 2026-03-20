<?php

use App\Core\Routing\Router;

$twig = require_once __DIR__ . '/../src/Core/bootstrap.php';
require_once __DIR__ . '/../src/Core/dependencies.php';


$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$render = getTwigRenderer($uri, $twig);

$router = new Router();

$registerRoutes = require_once __DIR__ . '/../src/Core/Routing/web.php';
$registerRoutes($router, $render);

$router->resolve($uri, $method);
