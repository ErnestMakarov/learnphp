<?php

namespace App\Validation;

use App\Models\User;

class UserInput
{
    public static function validate(User $user, string $password, string $confirmation): array
    {
        $errors = [];
        $nameLength = preg_match_all('/./us', $user->name);
        if (!$nameLength || $nameLength > 100) {
            $errors[] = 'Enter a name of 1–100 characters.';
        }
        if (!filter_var($user->email, FILTER_VALIDATE_EMAIL) || strlen($user->email) > 255) {
            $errors[] = 'Enter a valid email address.';
        }
        $existing = User::where('email', $user->email)[0] ?? null;
        if ($existing && $existing->id !== $user->id) {
            $errors[] = 'This email address is already in use.';
        }
        if ($user->id === null || $password !== '' || $confirmation !== '') {
            if (preg_match_all('/./us', $password) < 8) {
                $errors[] = 'Use a password with at least 8 characters.';
            }
            if (strlen($password) > 72 || str_contains($password, "\0")) {
                $errors[] = 'The password is too long or contains an unsupported character.';
            }
            if ($password !== $confirmation) {
                $errors[] = 'The passwords do not match.';
            }
        }
        return $errors;
    }
}
