<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\GreetingController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\OperationalController;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\PrivateMessageController;
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

// Rute Terproteksi Staf & Admin
Route::middleware(['auth'])->group(function () {
    // Akhiri mode simulasi akun — harus di luar grup admin karena akun
    // yang sedang disimulasikan tidak memiliki izin panel admin.
    Route::post('/stop-impersonate', [AdminController::class, 'stopImpersonate'])->name('admin.stop_impersonate');

    // Layar Sapaan Intersepsi Koordinator (COM-01)
    Route::get('/sapaan-koordinator', [GreetingController::class, 'show'])->name('greeting.show');
    Route::post('/sapaan-koordinator/acknowledge', [GreetingController::class, 'acknowledge'])->name('greeting.acknowledge');

    // Dashboard Utama & Seluruh Modul Operasional dengan Intersepsi Sapaan Wajib
    Route::middleware([CheckMandatoryGreeting::class])->group(function () {
        Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

        // 1. Modul Siaran & Pengumuman (COM-03 s.d. COM-05)
        Route::prefix('announcements')->name('announcements.')->group(function () {
            Route::get('/', [AnnouncementController::class, 'index'])->name('index');
            Route::get('/create', [AnnouncementController::class, 'create'])->name('create');
            Route::post('/', [AnnouncementController::class, 'store'])->name('store');
            Route::post('/{id}/read', [AnnouncementController::class, 'markAsRead'])->name('read');
            Route::get('/{id}/receipts', [AnnouncementController::class, 'readReceipts'])->name('receipts');
        });

        // 2. Modul Kalender & Agenda Kampus (AG-01 s.d. AG-04)
        Route::prefix('agendas')->name('agendas.')->group(function () {
            Route::get('/', [AgendaController::class, 'index'])->name('index');
            Route::get('/create', [AgendaController::class, 'create'])->name('create');
            Route::post('/', [AgendaController::class, 'store'])->name('store');
        });

        // 3. Modul Perizinan Pegawai & Guru Inval (LV-01 s.d. LV-07)
        Route::prefix('leaves')->name('leaves.')->group(function () {
            Route::get('/', [LeaveController::class, 'index'])->name('index');
            Route::get('/create', [LeaveController::class, 'create'])->name('create');
            Route::post('/', [LeaveController::class, 'store'])->name('store');
            Route::get('/{id}', [LeaveController::class, 'show'])->name('show');
            Route::post('/{id}/approve-unit', [LeaveController::class, 'approveUnit'])->name('approve_unit');
            Route::post('/{id}/approve-coordinator', [LeaveController::class, 'approveCoordinator'])->name('approve_coordinator');
            Route::post('/substitutes/{id}/acknowledge', [LeaveController::class, 'acknowledgeSubstitute'])->name('acknowledge_substitute');
        });

        // 4. Modul Fasilitas & Reservasi (FC-01 s.d. FC-10)
        Route::prefix('facilities')->name('facilities.')->group(function () {
            Route::get('/', [FacilityController::class, 'index'])->name('index');
            Route::get('/create', [FacilityController::class, 'create'])->name('create');
            Route::post('/', [FacilityController::class, 'store'])->name('store');
            Route::get('/{id}', [FacilityController::class, 'show'])->name('show');
            Route::get('/{id}/letter', [FacilityController::class, 'letter'])->name('letter');
            Route::post('/{id}/screen-tu', [FacilityController::class, 'actionScreenTU'])->name('action_screening');
            Route::post('/{id}/approve-unit', [FacilityController::class, 'actionApproveUnit'])->name('action_approve_unit');
            Route::post('/{id}/approve-coordinator', [FacilityController::class, 'actionApproveCoordinator'])->name('action_approve_coordinator');
        });

        // 5. Modul Operasional Lapangan (Direct Tasking, Tiket, Panic Button, Buku Tamu)
        Route::prefix('operational')->name('operational.')->group(function () {
            Route::get('/', [OperationalController::class, 'index'])->name('index');
            Route::post('/tasks', [OperationalController::class, 'storeTask'])->name('store_task');
            Route::post('/tasks/{id}/complete', [OperationalController::class, 'completeTask'])->name('complete_task');
            Route::post('/tickets', [OperationalController::class, 'storeTicket'])->name('store_ticket');
            Route::post('/panic', [OperationalController::class, 'triggerPanic'])->name('panic');
            Route::post('/guests', [OperationalController::class, 'storeGuest'])->name('store_guest');
        });

        // 6. Modul Tata Persuratan & Penomoran Otomatis (OP-07)
        Route::prefix('letters')->name('letters.')->group(function () {
            Route::get('/', [LetterController::class, 'index'])->name('index');
            Route::post('/', [LetterController::class, 'store'])->name('store');
        });

        // 9. Modul Grup & Kepanitiaan (GR-01 s.d. GR-04)
        Route::prefix('groups')->name('groups.')->group(function () {
            Route::get('/', [GroupController::class, 'index'])->name('index');
            Route::post('/', [GroupController::class, 'store'])->name('store');
            Route::get('/{id}', [GroupController::class, 'show'])->name('show');
            Route::post('/{id}/review', [GroupController::class, 'review'])->name('review');
            Route::post('/{id}/members', [GroupController::class, 'addMember'])->name('add_member');
            Route::delete('/{id}/members/{membershipId}', [GroupController::class, 'removeMember'])->name('remove_member');
        });

        // 10. Panel Admin Koordinator (Pusat Kendali Super Admin)
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

            // Manajemen akun pegawai
            Route::get('/users', [AdminController::class, 'users'])->name('users');
            Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
            Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
            Route::post('/users/{id}/reset-password', [AdminController::class, 'resetPassword'])->name('users.reset_password');
            Route::post('/users/{id}/toggle-status', [AdminController::class, 'toggleStatus'])->name('users.toggle_status');
            Route::post('/users/{id}/impersonate', [AdminController::class, 'impersonate'])->name('users.impersonate');

            // Delegasi wewenang (Plt / Acting Role)
            Route::get('/delegations', [AdminController::class, 'delegations'])->name('delegations');
            Route::post('/delegations', [AdminController::class, 'storeDelegation'])->name('delegations.store');
            Route::post('/delegations/{id}/revoke', [AdminController::class, 'revokeDelegation'])->name('delegations.revoke');

            // Jejak audit & riwayat login
            Route::get('/audit', [AdminController::class, 'audit'])->name('audit');

            // Data induk & pengaturan sistem
            Route::get('/master', [AdminController::class, 'masterData'])->name('master');
            Route::post('/master/period', [AdminController::class, 'setActivePeriod'])->name('master.period');
            Route::post('/master/setting', [AdminController::class, 'updateSetting'])->name('master.setting');
        });
 

        // 8. Modul Pesan Satu Arah Pribadi (Direct Private Memo)
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [PrivateMessageController::class, 'index'])->name('index');
            Route::get('/create', [PrivateMessageController::class, 'create'])->name('create');
            Route::post('/', [PrivateMessageController::class, 'store'])->name('store');
            Route::get('/{id}', [PrivateMessageController::class, 'show'])->name('show');
            Route::delete('/{id}', [PrivateMessageController::class, 'destroy'])->name('destroy');
        });

        // 7. Modul Web Push Notifications ke Smartphone
        Route::prefix('push')->name('push.')->group(function () {
            Route::get('/key', [PushNotificationController::class, 'getKey'])->name('key');
            Route::post('/subscribe', [PushNotificationController::class, 'subscribe'])->name('subscribe');
            Route::post('/unsubscribe', [PushNotificationController::class, 'unsubscribe'])->name('unsubscribe');
            Route::post('/test', [PushNotificationController::class, 'sendTest'])->name('test');
        });
    });
});