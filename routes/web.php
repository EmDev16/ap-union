<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PromptController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('news', NewsController::class)->only(['index', 'show']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('posts', PostController::class);
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('contact', [ContactController::class, 'store'])->name('contact.store');
});

Route::prefix('admin')->middleware(['auth', 'is_admin'])->group(function () {
    Route::resource('news', NewsController::class);
    Route::resource('prompts', PromptController::class);
    Route::get('contact-messages', [ContactController::class, 'index'])->name('contact.index');
});

require __DIR__.'/auth.php';
