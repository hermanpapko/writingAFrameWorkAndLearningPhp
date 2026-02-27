<?php

spl_autoload_register(function ($class) {
    $map = require __DIR__ . '/config/autoload_config.php';

    foreach ($map as $namespace => $path) {
        if (strpos($class, $namespace) === 0) {
            $relativeClass = str_replace('\\', '/', substr($class, strlen($namespace)));
            $file = __DIR__ . '/' . rtrim($path, '/') . '/' . ltrim($relativeClass, '/') . '.php';

            if (file_exists($file)) {
                require_once $file;
            }
        }
    }
});

// не использую по 3 заданию(написан для 2)
