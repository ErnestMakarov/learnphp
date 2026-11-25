<?php

use App\Controllers\ArticlesController;
use App\Controllers\AuthController;
use App\Controllers\PublicController;
use App\Controllers\UsersController;
use App\Route;

Route::get('/', [PublicController::class, 'index']);
Route::get('/us', [PublicController::class, 'us']);
Route::get('/tech', [PublicController::class, 'tech']);

Route::get('/forms', [PublicController::class, 'forms']);
Route::post('/forms', [PublicController::class, 'forms']);

Route::get('/register', [AuthController::class, 'registerForm']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'loginForm']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/admin/articles', [ArticlesController::class, 'index']);
Route::get('/admin/articles/view', [ArticlesController::class, 'view']);
Route::get('/admin/articles/create', [ArticlesController::class, 'create']);
Route::post('/admin/articles', [ArticlesController::class, 'store']);
Route::get('/admin/articles/edit', [ArticlesController::class, 'edit']);
Route::post('/admin/articles/edit', [ArticlesController::class, 'update']);
Route::post('/admin/articles/delete', [ArticlesController::class, 'delete']);

Route::get('/admin/users', [UsersController::class, 'index']);
Route::get('/admin/users/view', [UsersController::class, 'view']);
Route::get('/admin/users/create', [UsersController::class, 'create']);
Route::post('/admin/users', [UsersController::class, 'store']);
Route::get('/admin/users/edit', [UsersController::class, 'edit']);
Route::post('/admin/users/edit', [UsersController::class, 'update']);
Route::post('/admin/users/delete', [UsersController::class, 'delete']);

// Classroom naming; /admin/articles remains supported for existing links.
Route::get('/admin/posts', [ArticlesController::class, 'index']);
Route::get('/admin/posts/view', [ArticlesController::class, 'view']);
Route::get('/admin/posts/create', [ArticlesController::class, 'create']);
Route::post('/admin/posts', [ArticlesController::class, 'store']);
Route::get('/admin/posts/edit', [ArticlesController::class, 'edit']);
Route::post('/admin/posts/edit', [ArticlesController::class, 'update']);
Route::post('/admin/posts/delete', [ArticlesController::class, 'delete']);
