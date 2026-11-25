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

    public function tech(): void
    {
        $title = 'Technology';
        $posts = [
            (object) [
                'title' => 'What happens after you submit a form?',
                'body' => 'The browser sends an HTTP request to the server. A route selects a controller, the controller checks the input, and a model saves the data. A redirect then takes the browser back to the list.',
                'created_at' => '2026-10-01',
            ],
            (object) [
                'title' => 'Why Git commits should describe one change',
                'body' => 'A small commit is easier to review and explain. For this journal, a new page and a user editing form can be separate changes with messages that say what was added.',
                'created_at' => '2026-10-02',
            ],
        ];
        include __DIR__ . '/../../views/tech.php';
    }
}
