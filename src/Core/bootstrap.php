<?php

require_once __DIR__ . '/../../autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
    'debug' => true
]);

$currentLang = $_COOKIE['lang'] ?? 'en';

if (!in_array($currentLang, ['en', 'ru'])) {
    $currentLang = 'en';
}

$langFile = __DIR__ . "/../../lang/{$currentLang}.php";
$translations = file_exists($langFile) ? require $langFile : [];

$twig->addFunction(new \Twig\TwigFunction('__', function (string $key, ...$params) use ($translations) {
    $message = $translations[$key] ?? $key;
    if (!empty($params)) {
        return sprintf($message, ...$params);
    }
    return $message;
}));

$twig->addGlobal('currentLang', $currentLang);

return $twig;
