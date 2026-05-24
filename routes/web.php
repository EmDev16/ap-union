<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/faqpage', function () {
    return view('faqpage');
});

Route::get('/contact', function () {
    return view('contact');
});

Route::get('/latest', function () {
    return view('latest');
});

Route::get('/explore', function () {
    return view('explore');
});

Route::get('/messages', function () {
    return view('messages');
});

Route::get('/search', function () {
    return view('search');
});


Route::get('/ideas', function () {
    return view('ideas');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
