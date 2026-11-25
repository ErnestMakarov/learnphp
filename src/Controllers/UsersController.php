<?php

namespace App\Controllers;

use App\Models\User;

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
}
