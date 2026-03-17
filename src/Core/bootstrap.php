<?php

require_once __DIR__ . '/../../autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
    'debug' => true
]);

return $twig;
