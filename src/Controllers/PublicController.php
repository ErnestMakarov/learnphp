<?php

namespace App\Controllers;

use App\Models\Article;

class PublicController
{
    public function index(): void
    {
        $posts = Article::all();
        $title = 'World';
        view('index', compact('title', 'posts'));
    }

    public function us(): void
    {
        $title = 'U.S.';
        $posts = Article::all();
        view('us', compact('title', 'posts'));
    }

    public function forms(): void
    {
        $name = $_SERVER['REQUEST_METHOD'] === 'POST' ? trim(input('name')) : '';
        $age = $_SERVER['REQUEST_METHOD'] === 'POST' ? input('age') : '';
        $message = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validAge = filter_var($age, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 130]]);
            if ($name === '' || $validAge === false) {
                http_response_code(422);
                $message = 'Enter your name and an age between 0 and 130.';
            } else {
                $message = "Hello, $name! You are $validAge years old.";
            }
        }
        view('forms', ['title' => 'Forms', 'name' => $name, 'age' => $age, 'message' => $message]);
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
        view('tech', compact('title', 'posts'));
    }
}
