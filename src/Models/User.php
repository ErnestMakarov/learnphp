<?php

namespace App\Models;

class User extends Model
{
    protected static string $table = 'users';
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public int $is_admin = 0;
}
