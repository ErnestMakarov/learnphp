<?php

declare(strict_types=1);

use App\Router;

// Let PHP's development server serve real files only from the public directory.
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
$file = realpath(__DIR__ . $path);
if (
    PHP_SAPI === 'cli-server'
    && $file !== false
    && str_starts_with($file, __DIR__ . DIRECTORY_SEPARATOR)
    && is_file($file)
    && pathinfo($file, PATHINFO_EXTENSION) !== 'php'
) {
    return false;
}

require __DIR__ . '/../bootstrap.php';

ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);
session_start();

require __DIR__ . '/../routes.php';

try {
    Router::dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
} catch (Throwable $error) {
    error_log((string) $error);
    http_response_code(500);
    echo 'The application could not complete the request. Check the server log.';
}
