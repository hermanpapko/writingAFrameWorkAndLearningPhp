<?php

namespace App\Core\Routing;

class Router
{
    /** @var array<string, array<string, array<int, string>>> */
    private array $routes = [];

    /**
     * @param array{string, string} $handler
     */
    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }
    /**
     * @param array{string, string} $handler
     */
    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    /**
     * @param string $uri
     * @param string $method
     * @return void
     */
    public function resolve(string $uri, string $method): void
    {
        $path = parse_url($uri, PHP_URL_PATH);

        $handler = $this->routes[$method][$path] ?? null;

        if (!$handler) {
            http_response_code(404);
            echo "404 Not Found";
            return;
        }

        [$controllerClass, $methodName] = $handler;

        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();
            if (method_exists($controller, $methodName)) {
                $controller->$methodName();
                return;
            }
        }

        http_response_code(404);
        echo "Server Error. Method or controller not found.";
    }

}
