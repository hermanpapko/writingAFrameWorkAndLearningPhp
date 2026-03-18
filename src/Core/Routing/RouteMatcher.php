<?php

namespace App\Core\Routing;

class RouteMatcher
{
    /**
     * @param string $uri
     * @param string $method
     * @param array<string, array<string, array{object, string}>> $routes
     * @return array{handler: array{object, string}, params: array<string>}|null
     */
    public function match(string $uri, string $method, array $routes): ?array
    {
        $path = (string) parse_url($uri, PHP_URL_PATH);

        // Direct match
        if (isset($routes[$method][$path])) {
            return [
                'handler' => $routes[$method][$path],
                'params' => []
            ];
        }

        // Regex match
        foreach ($routes[$method] ?? [] as $routePath => $routeHandler) {
            $pattern = "#^" . $routePath . "$#";

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches);
                return [
                    'handler' => $routeHandler,
                    'params' => $matches
                ];
            }
        }

        return null;
    }
}
