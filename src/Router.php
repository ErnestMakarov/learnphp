<?php

namespace App;

class Router
{
    private static array $routes = [];

    public static function get(string $path, array $action): void
    {
        self::$routes['GET'][$path] = $action;
    }

    public static function post(string $path, array $action): void
    {
        self::$routes['POST'][$path] = $action;
    }

    public static function dispatch(string $uri, string $method): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $action = self::$routes[$method][$path] ?? null;
        if ($action === null) {
            $allowed = array_keys(array_filter(self::$routes, fn (array $routes) => isset($routes[$path])));
            if ($allowed !== []) {
                header('Allow: ' . implode(', ', $allowed));
                abort(405, 'This HTTP method is not allowed.');
            }
            abort(404, 'Page not found.');
        }

        if ($method === 'POST') {
            verifyCsrf();
        }
        [$class, $actionName] = $action;
        (new $class())->$actionName();
    }
}
