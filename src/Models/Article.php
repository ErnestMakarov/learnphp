<?php

namespace App\Models;

class Article extends Model
{
    protected static string $table = 'articles';
    public string $title = '';
    public string $body = '';
}
