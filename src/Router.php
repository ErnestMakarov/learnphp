<?php

namespace App;

class Router
{
    /** @var Route[] */
    private static array $routes = [];

    public static function addRoute(string $method, string $path, callable|array $action): void
    {
        self::$routes[] = new Route($method, $path, $action);
    }

    public static function dispatch(string $uri, string $method): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $allowed = [];
        foreach (self::$routes as $route) {
            if ($route->getPath() !== $path) {
                continue;
            }
            $allowed[] = $route->getMethod();
            if ($route->getMethod() !== $method) {
                continue;
            }
            if ($method === 'POST') {
                verifyCsrf();
            }
            $action = $route->getAction();
            if (is_array($action) && is_string($action[0])) {
                $action = [new $action[0](), $action[1]];
            }
            call_user_func($action);
            return;
        }
        if ($allowed !== []) {
            header('Allow: ' . implode(', ', array_unique($allowed)));
            abort(405, 'This HTTP method is not allowed.');
        }
        abort(404, 'Page not found.');
    }
}
