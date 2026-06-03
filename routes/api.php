<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SantriController;
use App\Http\Controllers\Api\WaliSantriController;
use App\Http\Controllers\Api\KesehatanSantriController;
use App\Http\Controllers\Api\ObatController;
use App\Http\Controllers\Api\KunjunganController;
use App\Http\Controllers\Api\PemberianObatController;
use App\Http\Controllers\Api\RawatInapController;
use App\Http\Controllers\Api\MonitoringController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MedicalHistoryController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\ApprovalController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\BackupController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\JurusanController;
use App\Http\Controllers\Api\KamarController;
use App\Http\Controllers\Api\ReferralController;

/**
 * DEI Health - Mobile API Routes (v1)
 * Dirancang untuk konektivitas aplikasi Android.
 * Semua endpoint mengembalikan JSON response.
 */

Route::prefix('v1')->name('api.v1.')->group(function () {

    // ═══════════════════════════════════════════════════════════════
    // PUBLIC / GUEST ROUTES
    // ═══════════════════════════════════════════════════════════════
    Route::post('/auth/login', [AuthController::class, 'login'])->name('login');
    Route::post('/auth/register', [AuthController::class, 'register'])->name('register');

    // ═══════════════════════════════════════════════════════════════
    // PROTECTED ROUTES (Sanctum Token Auth)
    // ═══════════════════════════════════════════════════════════════
    Route::middleware(['auth:sanctum'])->group(function () {

        // ─────────────────────────────────────────────────────────
        // AUTH & PROFILE
        // ─────────────────────────────────────────────────────────
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::get('/me', [AuthController::class, 'profile'])->name('me');
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        });

        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::post('/', [ProfileController::class, 'update'])->name('update');
        });

        // ─────────────────────────────────────────────────────────
        // DASHBOARD & SEARCH
        // ─────────────────────────────────────────────────────────
        Route::get('/dashboard/summary', [DashboardController::class, 'index'])->name('dashboard.summary');
        Route::get('/search', SearchController::class)->name('search');

        // ─────────────────────────────────────────────────────────
        // MODULE: MASTER DATA (Kelas, Jurusan, Kamar)
        // ─────────────────────────────────────────────────────────
        Route::prefix('master')->name('master.')->group(function () {
            Route::apiResource('kelas', KelasController::class);
            Route::apiResource('jurusan', JurusanController::class);
            Route::apiResource('kamar', KamarController::class);
        });

        // ─────────────────────────────────────────────────────────
        // MODULE: SANTRI (Full CRUD + Kesehatan)
        // ─────────────────────────────────────────────────────────
        Route::get('santri/lookups', [SantriController::class, 'lookups'])->name('santri.lookups');
        Route::apiResource('santri', SantriController::class);
        Route::get('santri/{id}/history', [MedicalHistoryController::class, 'show'])->name('santri.history');

        // Wali Santri
        Route::apiResource('wali-santri', WaliSantriController::class);

        // Kesehatan Santri (standalone CRUD)
        Route::apiResource('kesehatan-santri', KesehatanSantriController::class);

        // ─────────────────────────────────────────────────────────
        // MODULE: OBAT (Inventory Control)
        // ─────────────────────────────────────────────────────────
        Route::apiResource('obat', ObatController::class);
        Route::post('obat/mutasi', [ObatController::class, 'mutasi'])->name('obat.mutasi');

        // Obat Alerts
        Route::prefix('obat-alerts')->name('obat.alerts.')->group(function () {
            Route::get('/stok-menipis', [LaporanController::class, 'statusObat'])->name('stok');
            Route::get('/kadaluarsa', [LaporanController::class, 'statusObat'])->name('expired');
        });

        // ─────────────────────────────────────────────────────────
        // MODULE: LAYANAN KESEHATAN
        // ─────────────────────────────────────────────────────────

        // Kunjungan / Pemeriksaan (Full CRUD + Workflow)
        Route::apiResource('kunjungan', KunjunganController::class);
        Route::get('kunjungan-form-data', [KunjunganController::class, 'formData'])->name('kunjungan.form-data');

        // Pemberian Obat (Distribusi langsung ke santri)
        Route::apiResource('pemberian-obat', PemberianObatController::class)->only(['index', 'store', 'show']);

        // Rawat Inap (Legacy - Monitoring bed)
        Route::apiResource('rawat-inap', RawatInapController::class)->only(['index', 'store', 'show']);
        Route::post('rawat-inap/{id}/checkout', [RawatInapController::class, 'keluar'])->name('rawat-inap.checkout');

        // Monitoring Santri Sakit (KasusSakit workflow - padanan Web RawatInapController)
        Route::prefix('monitoring')->name('monitoring.')->group(function () {
            Route::get('/', [MonitoringController::class, 'index'])->name('index');
            Route::get('/{id}', [MonitoringController::class, 'show'])->name('show');
            Route::post('/{id}/pindah', [MonitoringController::class, 'pindah'])->name('pindah');
            Route::post('/{id}/selesai', [MonitoringController::class, 'selesai'])->name('selesai');
            Route::post('/{id}/update-riwayat', [MonitoringController::class, 'updateRiwayat'])->name('update-riwayat');
        });

        // ─────────────────────────────────────────────────────────
        // MODULE: NOTIFICATIONS
        // ─────────────────────────────────────────────────────────
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', function () {
                return response()->json(auth()->user()->notifications()->paginate(15));
            })->name('index');

            Route::post('/{id}/read', function (string $id) {
                auth()->user()->notifications()->findOrFail($id)->markAsRead();
                return response()->json(['success' => true]);
            })->name('read');

            Route::post('/read-all', function () {
                auth()->user()->unreadNotifications->markAsRead();
                return response()->json(['success' => true]);
            })->name('read-all');
        });

        // ─────────────────────────────────────────────────────────
        // MODULE: LAPORAN & REPORTS
        // ─────────────────────────────────────────────────────────
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/daily-summary', [LaporanController::class, 'kunjungan'])->name('daily');
            Route::get('/kunjungan', [LaporanController::class, 'kunjungan'])->name('kunjungan');
            Route::get('/penggunaan-obat', [LaporanController::class, 'penggunaanObat'])->name('penggunaan-obat');
            Route::get('/status-obat', [LaporanController::class, 'statusObat'])->name('status-obat');
            Route::get('/rawat-inap', [LaporanController::class, 'rawatInap'])->name('rawat-inap');
            Route::get('/rekap-demografi', [LaporanController::class, 'rekapDemografi'])->name('rekap-demografi');
            Route::get('/sickness', [LaporanController::class, 'sicknessReport'])->name('sickness');
            Route::get('/medicine', [LaporanController::class, 'medicineReport'])->name('medicine');
        });

        // Hospital Referrals (Rujukan RS)
        Route::apiResource('rujukan', ReferralController::class);
        Route::post('rujukan/{id}/notify-guardian', [ReferralController::class, 'notifyGuardian'])->name('rujukan.notify');

        // Export
        Route::prefix('export')->name('export.')->group(function () {
            Route::get('/kunjungan/csv', [ExportController::class, 'exportKunjunganCsv'])->name('kunjungan.csv');
            Route::get('/kunjungan/pdf', [ExportController::class, 'exportKunjunganPdf'])->name('kunjungan.pdf');
        });

        // ─────────────────────────────────────────────────────────
        // MODULE: AUDIT LOG
        // ─────────────────────────────────────────────────────────
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

        // ─────────────────────────────────────────────────────────
        // MODULE: SYSTEM (Admin Only)
        // ─────────────────────────────────────────────────────────

        // Approvals
        Route::prefix('approvals')->name('approvals.')->group(function () {
            Route::get('/', [ApprovalController::class, 'index'])->name('index');
            Route::get('/{approval}', [ApprovalController::class, 'show'])->name('show');
            Route::post('/{approval}/approve', [ApprovalController::class, 'approve'])->name('approve');
            Route::post('/{approval}/reject', [ApprovalController::class, 'reject'])->name('reject');
        });

        // Backups
        Route::prefix('backups')->name('backups.')->group(function () {
            Route::get('/', [BackupController::class, 'index'])->name('index');
            Route::post('/', [BackupController::class, 'store'])->name('store');
        });

        // Settings (Full CRUD)
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('index');
            Route::get('/{key}', [SettingController::class, 'show'])->name('show');
            Route::put('/{key}', [SettingController::class, 'update'])->name('update');
            Route::post('/', [SettingController::class, 'bulkUpdate'])->name('bulkUpdate');
            Route::post('/single', [SettingController::class, 'store'])->name('store');
        });
    });
});

// Fallback for API
Route::fallback(function () {
    return response()->json([
        'message' => 'API Endpoint tidak ditemukan.',
        'status'  => 404
    ], 404);
});
