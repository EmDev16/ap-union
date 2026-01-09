<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('profiles', ProfileController::class)->only(['show']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('profiles', ProfileController::class)->only(['edit', 'update', 'destroy']);
});

require __DIR__.'/auth.php';
