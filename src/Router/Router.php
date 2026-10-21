<?php

namespace Sergi\Misterplan\Router;

class Router
{
    private array $routes = [];

    /**
     * Register a route for an HTTP method and path.
     *
     * @param string $method The HTTP method.
     * @param string $path The route path.
     * @param callable $handler The function that handles the request.
     */
    public function add(
        string $method,
        string $path,
        callable $handler
    ): void {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
        ];
    }
    /**
     * Match the current request and execute its handler.
     */
    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $method = strtoupper($method);

        foreach ($this->routes as $route) {
            $parameterNames = [];

            $quotedPath = preg_quote($route['path'], '#');

            $pattern = preg_replace_callback(
                '/\\\\\{([a-zA-Z_][a-zA-Z0-9_]*)\\\\\}/',
                static function (array $matches) use (&$parameterNames): string {
                    $parameterNames[] = $matches[1];

                    return '([^/]+)';
                },
                $quotedPath
            );

            if (
                $route['method'] !== $method
                || !preg_match('#^' . $pattern . '$#', $path, $matches)
            ) {
                continue;
            }

            // Extract route parameters for the handler.
            $parameters = array_slice($matches, 1);

            call_user_func_array($route['handler'], $parameters);

            return;
        }

        header('Content-Type: application/json; charset=utf-8');
        http_response_code(404);

        echo json_encode([
            'error' => 'Route not found.',
        ]);
    }

}
