<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GreetingController;
use App\Http\Middleware\CheckMandatoryGreeting;
use Illuminate\Support\Facades\Route;

// Halaman Informasi Akun Testing & Status Arsitektur
Route::get('/', function () {
    return view('welcome');
})->name('landing');

// Autentikasi Staf & Admin
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Terproteksi Staf
Route::middleware(['auth'])->group(function () {
    // Layar Sapaan Intersepsi Koordinator (COM-01)
    Route::get('/sapaan-koordinator', [GreetingController::class, 'show'])->name('greeting.show');
    Route::post('/sapaan-koordinator/acknowledge', [GreetingController::class, 'acknowledge'])->name('greeting.acknowledge');

    // Dashboard Utama dengan Intersepsi Sapaan Wajib
    Route::middleware([CheckMandatoryGreeting::class])->group(function () {
        Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    });
});
