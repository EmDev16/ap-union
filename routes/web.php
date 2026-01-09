<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FeedController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/users/{user}', [ProfileController::class, 'show'])
    ->name('profile.show');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Eigen profiel beheren
    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // Feed
    Route::get('/feed', [FeedController::class, 'index'])
        ->name('feed');
});

require __DIR__ . '/auth.php';
