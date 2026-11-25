<?php

namespace App\Controllers;

use App\Models\Article;

class PublicController
{
    public function index(): void
    {
        $posts = Article::all();
        $title = 'World';
        include __DIR__ . '/../../views/index.php';
    }

    public function us(): void
    {
        $title = 'U.S.';
        $posts = [
            (object) [
                'title' => 'A student guide to community projects',
                'body' => 'A classroom example about volunteering, local libraries and sharing skills with neighbours.',
                'created_at' => '2026-10-01',
            ],
            (object) [
                'title' => 'Learning beyond the classroom',
                'body' => 'Practical projects help students connect what they study with the places where they live.',
                'created_at' => '2026-10-02',
            ],
        ];
        include __DIR__ . '/../../views/us.php';
    }
}
