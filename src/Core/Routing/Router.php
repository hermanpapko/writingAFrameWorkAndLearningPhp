<?php

namespace App\Core\Routing;

class Router
{
    /** @var array<string, array<string, array{object, string}>> */
    private array $routes = [];

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
        $path = (string) parse_url($uri, PHP_URL_PATH);

        $handler = null;
        $params = [];

        // Direct match
        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path];
        } else {
            // Regex match
            foreach ($this->routes[$method] ?? [] as $routePath => $routeHandler) {
                $pattern = "#^" . $routePath . "$#";

                if (preg_match($pattern, $path, $matches)) {
                    $handler = $routeHandler;
                    array_shift($matches); // Remove full match
                    $params = $matches;
                    break;
                }
            }
        }

        if (!$handler) {
            http_response_code(404);
            echo "404 Not Found";
            return;
        }

        $callable = [$handler[0], $handler[1]];

        if (is_callable($callable)) {
            call_user_func_array($callable, $params);
            return;
        }

        http_response_code(500);
        echo "Server Error. Method or controller not found.";
    }
}
