<?php
require_once __DIR__ . '/autoload.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$controller = new App\Controllers\UserController();
try {
    if ($requestUri === '/analyse' || $requestUri === '/analyze') {
        $controller->analyze();
    } elseif ($requestUri === '/parse') {
        $controller->parse();
    } else {
        echo "Hi! Use /analyze or /parse";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
