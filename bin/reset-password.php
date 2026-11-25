<?php

use App\Models\User;

require __DIR__ . '/../bootstrap.php';
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
$email = strtolower(trim($argv[1] ?? 'admin@example.test'));
$user = User::where('email', $email)[0] ?? null;
if (!$user) {
    fwrite(STDERR, "Account not found. Pass the account email as the first argument.\n");
    exit(1);
}
$password = bin2hex(random_bytes(12));
$user->password = password_hash($password, PASSWORD_BCRYPT);
$user->save();
echo "Password reset for $email\nNew password: $password\nSave it locally; do not commit it.\n";
