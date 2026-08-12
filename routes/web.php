<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\Admin\FaqManagementController;
use App\Http\Controllers\Admin\FaqCategoryManagementController;

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

Route::get('/users/{user}', [ProfileController::class, 'show'])->name('profile.show');

Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('faqs', FaqManagementController::class);
    Route::resource('faq-categories', FaqCategoryManagementController::class);
});

Route::get('/ideas', function () {
    $ideas = DB::table('ideas')->get();
    return view('ideas', ['ideas' => $ideas]);
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
