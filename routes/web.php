<?php

use App\Http\Controllers\CommentsController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ PostsController::class, 'index']);


Route::middleware('guest')->group(function () {
    Route::get('/login', [SessionController::class, 'create']);
    Route::post('/login', [SessionController::class, 'store']);

    Route::get('/register', [RegisteredUserController::class, 'create']);
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [SessionController::class, 'destroy']);

    Route::get('/posts/create', [PostsController::class, 'create']);
    Route::post('/posts', [PostsController::class, 'store']);

    Route::get('/posts/{post}/edit', [PostsController::class, 'edit']);
    Route::post('/posts/{post}/edit', [PostsController::class, 'update']);

    Route::post('/posts/{post}/delete', [PostsController::class, 'destroy']);

    Route::post('/comments', [CommentsController::class, 'store']);
    Route::post('/comments/{comment}/delete', [CommentsController::class, 'destroy']);
});

Route::get('/posts/{post}', [PostsController::class, 'show'])->name('posts.show');
Route::get('/comments/{post:id}', [CommentsController::class, 'index']);
