<?php

namespace App\Controllers;

use App\Models\Article;

class ArticlesController
{
    public function __construct()
    {
        requireAdmin();
    }

    private function findArticle(): Article
    {
        return Article::find(requestId()) ?? abort(404, 'Article not found.');
    }

    public function index(): void
    {
        view('articles/index', ['title' => 'Articles', 'articles' => Article::all()]);
    }

    public function view(): void
    {
        view('articles/view', ['title' => 'Article', 'article' => $this->findArticle()]);
    }

    public function create(): void
    {
        view('articles/create', ['title' => 'New article', 'article' => new Article(), 'errors' => []]);
    }

    public function store(): void
    {
        $this->save(new Article(), 'articles/create');
    }

    public function edit(): void
    {
        view('articles/edit', ['title' => 'Edit article', 'article' => $this->findArticle(), 'errors' => []]);
    }

    public function update(): void
    {
        $this->save($this->findArticle(), 'articles/edit');
    }

    private function save(Article $article, string $form): void
    {
        $article->title = trim(input('title'));
        $article->body = trim(input('body'));
        $errors = [];
        if (!preg_match_all('/./us', $article->title) || preg_match_all('/./us', $article->title) > 255) {
            $errors[] = 'Enter a title of 1–255 characters.';
        }
        if ($errors !== []) {
            http_response_code(422);
            view($form, ['title' => 'Article form', 'article' => $article, 'errors' => $errors]);
            return;
        }
        $article->save();
        flash('Article saved.');
        redirect('/admin/articles');
    }

    public function delete(): void
    {
        $this->findArticle()->delete();
        flash('Article deleted.');
        redirect('/admin/articles');
    }
}
