<?php

use App\Models\User;

function view(string $name, array $variables = []): void
{
    extract($variables, EXTR_SKIP);
    require __DIR__ . '/views/' . $name . '.php';
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path, true, 303);
    exit;
}

function abort(int $status, string $message): never
{
    http_response_code($status);
    view('errors/error', compact('status', 'message'));
    exit;
}

function auth(): ?User
{
    return isset($_SESSION['userID']) ? User::find((int) $_SESSION['userID']) : null;
}

function requireAdmin(): void
{
    $user = auth();
    if (!$user) {
        redirect('/login');
    }
    if (!$user->is_admin) {
        abort(403, 'Administrator access is required.');
    }
}

function csrfToken(): string
{
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}

function csrfField(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrfToken()) . '">';
}

function verifyCsrf(): void
{
    $token = $_POST['_token'] ?? '';
    if (!is_string($token) || !hash_equals(csrfToken(), $token)) {
        abort(403, 'The form has expired. Reload the page and try again.');
    }
}

function input(string $key): string
{
    return isset($_POST[$key]) && is_string($_POST[$key]) ? $_POST[$key] : '';
}

function requestId(): int
{
    $source = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;
    $id = filter_var($source['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id === false) {
        abort(404, 'This record does not exist.');
    }
    return $id;
}

function flash(string $message): void
{
    $_SESSION['flash'] = $message;
}
