<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostController::class, 'indexWeb'])->name('posts.indexWeb');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::get('/posts/{post}', [PostController::class, 'showWeb'])->name('posts.showWeb');
    Route::post('/posts', [PostController::class, 'storeWeb'])->name('posts.storeWeb');

    Route::post('likes', [LikeController::class, 'storeWeb'])->name('likes.storeWeb');
    Route::post('comments', [CommentController::class, 'storeWeb'])->name('comments.storeWeb');
});


require __DIR__.'/auth.php';
