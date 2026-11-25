<?php

declare(strict_types=1);

$autoload = __DIR__ . '/vendor/autoload.php';
if (!is_file($autoload)) {
    if (PHP_SAPI !== 'cli') {
        http_response_code(503);
    }
    exit("Dependencies are missing. Run composer install in the project folder.\n");
}
require $autoload;
require __DIR__ . '/helpers.php';
