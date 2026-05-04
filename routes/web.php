<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\WebAuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\SantriController;
use App\Http\Controllers\Web\ObatController;
use App\Http\Controllers\Web\KunjunganController;
use App\Http\Controllers\Web\RawatInapController;
use App\Http\Controllers\Web\LaporanController;
use App\Http\Controllers\Web\AuditLogController;
use App\Http\Controllers\Web\MedicalHistoryController;
use App\Http\Controllers\Web\ApprovalController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\NotificationController;
use App\Http\Controllers\Web\BackupController;
use App\Http\Controllers\Web\SettingController;
use App\Http\Controllers\Web\SearchController;
use App\Http\Controllers\Web\KelasController;
use App\Http\Controllers\Web\JurusanController;
use App\Http\Controllers\Web\KamarController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Api\MedicalTagController;
use App\Http\Controllers\Web\MasterMedisController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini adalah tempat pendaftaran route untuk DEI Health.
| Seluruh route dilindungi oleh middleware auth untuk keamanan data santri.
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication
Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login'])->name('login.post');
Route::get('/register', [WebAuthController::class, 'showRegister'])->name('register');
Route::post('/register', [WebAuthController::class, 'register'])->name('register.post');
Route::get('/policy', function() { return view('auth.policy'); })->name('policy');
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Search
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Profile
    Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('profile', [ProfileController::class, 'update'])->name('profile.update');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Master Data
    Route::resource('master/kelas', KelasController::class);
    Route::resource('master/jurusan', JurusanController::class);
    Route::resource('master/kamar', KamarController::class);
    Route::resource('master/kasur', \App\Http\Controllers\Web\KasurController::class);

    // Module: Santri & History
    Route::resource('santri', SantriController::class);
    Route::get('santri/{santri}/edit-health', [SantriController::class, 'editHealth'])->name('santri.edit-health');
    Route::put('santri/{santri}/update-health', [SantriController::class, 'updateHealth'])->name('santri.update-health');
    Route::get('santri/{santri}/history', [MedicalHistoryController::class, 'show'])->name('santri.history');
    Route::get('santri/{santri}/walis', [SantriController::class, 'getWalis'])->name('santri.walis');

    // Module: Obat
    Route::resource('obat', ObatController::class);
    Route::post('obat/{obat}/update-stok', [ObatController::class, 'updateStok'])->name('obat.update-stok');

    // Module: Layanan Kesehatan
    Route::resource('kunjungan', KunjunganController::class);
    // Monitoring Santri Sakit (Legacy name Rawat Inap)
    Route::get('rawat-inap', [RawatInapController::class, 'index'])->name('rawat-inap.index');
    Route::post('rawat-inap/{id}/pindah', [RawatInapController::class, 'pindah'])->name('rawat-inap.pindah');
    Route::post('rawat-inap/{id}/update', [RawatInapController::class, 'updateRiwayat'])->name('rawat-inap.update');
    Route::post('rawat-inap/{id}/selesai', [RawatInapController::class, 'selesai'])->name('rawat-inap.selesai');

    // Module: Laporan & Audit
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    // Module: System
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::post('users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
    Route::post('users/{user}/status', [UserController::class, 'changeStatus'])->name('users.change-status');
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::post('approvals/{approval}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('approvals/{approval}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');

    Route::get('backups', [BackupController::class, 'index'])->name('backups.index');
    Route::post('backups', [BackupController::class, 'store'])->name('backups.store');
    Route::get('backups/{id}/download', [BackupController::class, 'download'])->name('backups.download');
    Route::post('backups/{id}/restore', [BackupController::class, 'restore'])->name('backups.restore');
    Route::post('backups/import', [BackupController::class, 'import'])->name('backups.import');

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

    // ── Medical Tag API (AJAX) ───────────────────────────────
    Route::prefix('api/medical-tags')->name('api.medical-tags.')->group(function () {
        // Diagnosa
        Route::get('diagnosa', [MedicalTagController::class, 'allDiagnosa'])->name('diagnosa.all');
        Route::get('diagnosa/search', [MedicalTagController::class, 'searchDiagnosa'])->name('diagnosa.search');
        Route::post('diagnosa', [MedicalTagController::class, 'storeDiagnosa'])->name('diagnosa.store');
        // Keluhan
        Route::get('keluhan', [MedicalTagController::class, 'allKeluhan'])->name('keluhan.all');
        Route::get('keluhan/search', [MedicalTagController::class, 'searchKeluhan'])->name('keluhan.search');
        Route::post('keluhan', [MedicalTagController::class, 'storeKeluhan'])->name('keluhan.store');
        // Tindakan
        Route::get('tindakan', [MedicalTagController::class, 'allTindakan'])->name('tindakan.all');
        Route::get('tindakan/search', [MedicalTagController::class, 'searchTindakan'])->name('tindakan.search');
        Route::post('tindakan', [MedicalTagController::class, 'storeTindakan'])->name('tindakan.store');
    });

    /**
     * Sesuai dengan rencana Section 4 - 8, route untuk modul lain akan ditambahkan 
     * setelah implementasi view dasar selesai. 
     * Untuk saat ini, kita fokus pada Layout dan Dashboard.
     */

    // ── Master Medis (Admin & Super Admin only) ──────────────
    Route::middleware('role:admin')->prefix('master-medis')->name('master-medis.')->group(function () {
        Route::get('/',                          [MasterMedisController::class, 'index'])->name('index');
        Route::post('diagnosa',                  [MasterMedisController::class, 'storeDiagnosa'])->name('diagnosa.store');
        Route::put('diagnosa/{diagnosa}',        [MasterMedisController::class, 'updateDiagnosa'])->name('diagnosa.update');
        Route::post('diagnosa/{diagnosa}/toggle',[MasterMedisController::class, 'toggleDiagnosa'])->name('diagnosa.toggle');
        Route::delete('diagnosa/{diagnosa}',     [MasterMedisController::class, 'destroyDiagnosa'])->name('diagnosa.destroy');
        Route::post('keluhan',                   [MasterMedisController::class, 'storeKeluhan'])->name('keluhan.store');
        Route::put('keluhan/{keluhan}',          [MasterMedisController::class, 'updateKeluhan'])->name('keluhan.update');
        Route::post('keluhan/{keluhan}/toggle',  [MasterMedisController::class, 'toggleKeluhan'])->name('keluhan.toggle');
        Route::delete('keluhan/{keluhan}',       [MasterMedisController::class, 'destroyKeluhan'])->name('keluhan.destroy');
        Route::post('tindakan',                  [MasterMedisController::class, 'storeTindakan'])->name('tindakan.store');
        Route::put('tindakan/{tindakan}',        [MasterMedisController::class, 'updateTindakan'])->name('tindakan.update');
        Route::post('tindakan/{tindakan}/toggle',[MasterMedisController::class, 'toggleTindakan'])->name('tindakan.toggle');
        Route::delete('tindakan/{tindakan}',     [MasterMedisController::class, 'destroyTindakan'])->name('tindakan.destroy');
    });
});

