<?php

namespace App\Controllers;

use App\Models\User;
use PDOException;

class UsersController
{
    public function __construct()
    {
        requireAdmin();
    }

    private function findUser(): User
    {
        return User::find(requestId()) ?? abort(404, 'User not found.');
    }

    public function index(): void
    {
        view('users/index', ['title' => 'Users', 'users' => User::all()]);
    }

    public function view(): void
    {
        view('users/view', ['title' => 'User details', 'user' => $this->findUser()]);
    }

    public function create(): void
    {
        view('users/create', ['title' => 'New user', 'user' => new User(), 'errors' => []]);
    }

    public function store(): void
    {
        $this->save(new User(), 'users/create');
    }

    public function edit(): void
    {
        view('users/edit', ['title' => 'Edit user', 'user' => $this->findUser(), 'errors' => []]);
    }

    public function update(): void
    {
        $this->save($this->findUser(), 'users/edit');
    }

    public function delete(): void
    {
        $user = $this->findUser();
        if ($user->id === (int) $_SESSION['userID']) {
            abort(422, 'You cannot delete the account you are currently using.');
        }
        $user->delete();
        flash('User deleted.');
        redirect('/admin/users');
    }

    private function save(User $user, string $form): void
    {
        $user->name = trim(input('name'));
        $user->email = strtolower(trim(input('email')));
        $password = input('password');
        $confirmation = input('password_confirm');
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
            // Bcrypt accepts at most 72 bytes. Reject longer input instead of truncating it.
            if (strlen($password) > 72 || str_contains($password, "\0")) {
                $errors[] = 'The password is too long or contains an unsupported character.';
            }
            if ($password !== $confirmation) {
                $errors[] = 'The passwords do not match.';
            }
        }

        if ($errors === []) {
            if ($password !== '') {
                $user->password = password_hash($password, PASSWORD_BCRYPT);
            }
            try {
                $user->save();
                flash('User saved.');
                redirect('/admin/users');
            } catch (PDOException $error) {
                // The database also enforces unique emails if concurrent submissions race.
                if (!in_array((string) $error->getCode(), ['23000', '23505'], true)) {
                    throw $error;
                }
                $errors[] = 'This email address is already in use.';
            }
        }

        http_response_code(422);
        view($form, ['title' => 'User form', 'user' => $user, 'errors' => $errors]);
    }
}
