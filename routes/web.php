<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\Admin\FaqManagementController;
use App\Http\Controllers\Admin\FaqCategoryManagementController;
use App\Http\Controllers\Admin\ContactManagementController;
use App\Http\Controllers\Admin\NewsManagementController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\NewsController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');

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
    Route::resource('faqs', FaqManagementController::class)->except('show');
    Route::resource('faq-categories', FaqCategoryManagementController::class)->except('show');
    Route::resource('news', NewsManagementController::class)->except('show');
    Route::get('contacts', [ContactManagementController::class, 'index'])->name('contacts.index');
    Route::patch('contacts/{contact}', [ContactManagementController::class, 'update'])->name('contacts.update');
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
