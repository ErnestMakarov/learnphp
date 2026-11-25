<?php

use App\DB;
use App\Models\Article;
use App\Models\User;

require __DIR__ . '/../bootstrap.php';

if (PHP_SAPI !== 'cli') {
    exit('Run this script from the terminal.');
}

$email = strtolower(trim(getenv('ADMIN_EMAIL') ?: 'admin@example.test'));
$password = getenv('ADMIN_PASSWORD') ?: bin2hex(random_bytes(12));
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
    fwrite(STDERR, "ADMIN_EMAIL must be a valid email address.\n");
    exit(1);
}
if (strlen($password) < 8 || strlen($password) > 72 || str_contains($password, "\0")) {
    fwrite(STDERR, "ADMIN_PASSWORD must contain 8–72 bytes and no null characters.\n");
    exit(1);
}

$db = DB::connection();
$driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
$schema = $driver === 'sqlite' ? '/database/schema.sqlite.sql' : '/tables.sql';
$db->exec(file_get_contents(dirname(__DIR__) . $schema));

if (User::where('is_admin', 1) === []) {
    if (User::where('email', $email) !== []) {
        fwrite(STDERR, "That email already belongs to a user. Choose another ADMIN_EMAIL.\n");
        exit(1);
    }
    $admin = new User();
    $admin->name = 'Administrator';
    $admin->email = $email;
    $admin->password = password_hash($password, PASSWORD_BCRYPT);
    $admin->is_admin = 1;
    $admin->save();
    echo "Administrator created.\nEmail: $email\nPassword: $password\nSave these credentials locally.\n";
} else {
    echo "Existing administrator kept; password unchanged.\n";
}

if (Article::all() === []) {
    $article = new Article();
    $article->title = 'Small projects, useful skills';
    $article->body = 'Building a journal is a way to practise routes, forms and databases. Each small feature connects a browser request to PHP code and stored data.';
    $article->save();
}
echo "Database ready. Start with: php -S localhost:9999 -t public public/index.php\n";
