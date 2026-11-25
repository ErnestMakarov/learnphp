<?php

namespace App;

class Route
{
    // PHP does not permit callable as a property type.
    public function __construct(
        private string $method,
        private string $path,
        private mixed $action
    ) {
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getAction(): mixed
    {
        return $this->action;
    }

    public static function get(string $path, callable|array $action): void
    {
        Router::addRoute('GET', $path, $action);
    }

    public static function post(string $path, callable|array $action): void
    {
        Router::addRoute('POST', $path, $action);
    }
}
