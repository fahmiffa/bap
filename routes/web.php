<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Home;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('home');
});

Route::get('/link/{id}', [Home::class, 'showLink'])->name('link.show'); 
Route::post('/sign/{id}', [Home::class, 'signLink'])->name('sign.store'); 


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [Home::class, 'index'])->name('dashboard');
    Route::get('/preview/doc/{id}', [Home::class, 'preview'])->name('document.preview');
    Route::get('/document-tambah', [Home::class, 'create'])->name('document.create');
    Route::post('/document', [Home::class, 'store'])->name('document.store');
    Route::post('/document-link/{id}', [Home::class, 'storeLink'])->name('link.store');
});

require __DIR__ . '/auth.php';
