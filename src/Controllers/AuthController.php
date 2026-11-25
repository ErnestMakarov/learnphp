<?php

namespace App\Controllers;

use App\Models\User;

class AuthController
{
    public function loginForm(): void
    {
        view('auth/login', ['title' => 'Login', 'error' => '', 'email' => '']);
    }

    public function login(): void
    {
        $email = strtolower(trim(input('email')));
        $user = User::where('email', $email)[0] ?? null;
        if (!$user || !password_verify(input('password'), $user->password)) {
            http_response_code(422);
            view('auth/login', ['title' => 'Login', 'error' => 'Incorrect email or password.', 'email' => $email]);
            return;
        }

        session_regenerate_id(true);
        $_SESSION['userID'] = $user->id;
        unset($_SESSION['csrf']);
        redirect($user->is_admin ? '/admin/articles' : '/');
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
        redirect('/login');
    }
}
