<?php

namespace App\Controllers;

use App\Models\User;
use App\Validation\UserInput;
use PDOException;

class AuthController
{
    public function registerForm(): void
    {
        view('auth/register', ['title' => 'Register', 'user' => new User(), 'errors' => []]);
    }

    public function register(): void
    {
        $user = new User();
        $user->name = trim(input('name'));
        $user->email = strtolower(trim(input('email')));
        $password = input('password');
        $errors = UserInput::validate($user, $password, input('password_confirm'));
        if ($errors === []) {
            $user->password = password_hash($password, PASSWORD_BCRYPT);
            // Public registration can never grant administrator privileges.
            $user->is_admin = 0;
            try {
                $user->save();
                session_regenerate_id(true);
                $_SESSION['userID'] = $user->id;
                unset($_SESSION['csrf']);
                flash('Account created. You are now logged in.');
                redirect('/');
            } catch (PDOException $error) {
                if (!in_array((string) $error->getCode(), ['23000', '23505'], true)) {
                    throw $error;
                }
                $errors[] = 'This email address is already in use.';
            }
        }
        http_response_code(422);
        view('auth/register', ['title' => 'Register', 'user' => $user, 'errors' => $errors]);
    }

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
