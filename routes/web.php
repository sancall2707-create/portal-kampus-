<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AnnouncementController;
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

    // Dashboard Utama & Modul Operasional dengan Intersepsi Sapaan Wajib
    Route::middleware([CheckMandatoryGreeting::class])->group(function () {
        Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

        // Modul Siaran & Pengumuman (COM-03 s.d. COM-05)
        Route::prefix('announcements')->name('announcements.')->group(function () {
            Route::get('/', [AnnouncementController::class, 'index'])->name('index');
            Route::get('/create', [AnnouncementController::class, 'create'])->name('create');
            Route::post('/', [AnnouncementController::class, 'store'])->name('store');
            Route::post('/{id}/read', [AnnouncementController::class, 'markAsRead'])->name('read');
            Route::get('/{id}/receipts', [AnnouncementController::class, 'readReceipts'])->name('receipts');
        });

        // Modul Kalender & Agenda Kampus (AG-01 s.d. AG-04)
        Route::prefix('agendas')->name('agendas.')->group(function () {
            Route::get('/', [AgendaController::class, 'index'])->name('index');
            Route::get('/create', [AgendaController::class, 'create'])->name('create');
            Route::post('/', [AgendaController::class, 'store'])->name('store');
        });
    });
});
