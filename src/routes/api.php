<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('posts', [PostController::class, 'index']);
Route::get('posts/{id}', [PostController::class, 'show']);
Route::post('posts', [PostController::class, 'store']);
Route::put('posts/{post}', [PostController::class, 'update']);
Route::delete('posts/{post}', [PostController::class, 'destroy']);

Route::get('comments/posts/{id}', [CommentController::class, 'indexByPost']);
Route::post('comments', [CommentController::class, 'store']);
Route::delete('comments/{comment}', [CommentController::class, 'destroy']);

Route::get('likes/posts/{id}', [LikeController::class, 'indexByPost']);
Route::post('likes', [LikeController::class, 'store']);
Route::delete('likes/{like}', [LikeController::class, 'destroy']);