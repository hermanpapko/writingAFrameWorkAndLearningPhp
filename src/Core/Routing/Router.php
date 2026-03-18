<?php

namespace App\Core\Routing;

class Router
{
    /** @var array<string, array<string, array{object, string}>> */
    private array $routes = [];
    private RouteMatcher $matcher;

    public function __construct()
    {
        $this->matcher = new RouteMatcher();
    }

    /**
     * @param array{object, string} $handler
     */
    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    /**
     * @param array{object, string} $handler
     */
    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function resolve(string $uri, string $method): void
    {
        $match = $this->matcher->match($uri, $method, $this->routes);

        if (!$match) {
            http_response_code(404);
            echo "404 Not Found";
            return;
        }

        $handler = $match['handler'];
        $params = $match['params'];

        $callable = [$handler[0], $handler[1]];

        if (is_callable($callable)) {
            call_user_func_array($callable, $params);
            return;
        }

        http_response_code(500);
        echo "Server Error. Method or controller not found.";
    }
}
