<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\MobileAdminController;
use App\Http\Controllers\Api\MobileMasterDataController;
use App\Http\Controllers\Api\SicknessCaseApiController;
use App\Http\Controllers\Api\MedicineApiController;
use App\Http\Controllers\Api\HospitalReferralApiController;
use App\Http\Controllers\Api\SantriApiController;
use App\Http\Controllers\Api\WhatsAppApiController;
use App\Http\Controllers\Api\SettingsApiController;
use Illuminate\Support\Facades\Route;

// ─── Public Routes ──────────────────────────────────────────────────────────
Route::post('auth/login',    [ApiAuthController::class, 'login']);
Route::post('auth/register', [ApiAuthController::class, 'register']);

// ─── Protected Routes ────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // ── Auth ─────────────────────────────────────────────────────────────────
    Route::post('auth/logout',      [ApiAuthController::class, 'logout']);
    Route::get('auth/me',           [ApiAuthController::class, 'me']);

    // ── Dashboard ─────────────────────────────────────────────────────────────
    Route::get('dashboard/summary', [DashboardApiController::class, 'index']);

    // ── Kunjungan (Kasus Sakit) ───────────────────────────────────────────────
    Route::get('kunjungan-form-data',           [SicknessCaseApiController::class, 'lookups']);
    Route::get('kunjungan',                     [SicknessCaseApiController::class, 'index']);
    Route::get('kunjungan/{id}',                [SicknessCaseApiController::class, 'show']);
    Route::post('kunjungan',                    [SicknessCaseApiController::class, 'store']);
    Route::put('kunjungan/{id}',                [SicknessCaseApiController::class, 'update']);
    Route::delete('kunjungan/{id}',             [SicknessCaseApiController::class, 'destroy'])
        ->middleware('role:super_admin,admin');

    // Kunjungan special actions
    Route::post('monitoring/{id}/selesai',          [SicknessCaseApiController::class, 'markRecovered']);
    Route::post('kunjungan/{id}/notify-guardian',   [SicknessCaseApiController::class, 'notifyGuardian']);
    Route::post('kunjungan/{id}/discharge',         [SicknessCaseApiController::class, 'discharge']);
    Route::post('kunjungan/{id}/refer',             [SicknessCaseApiController::class, 'refer']);


    // ── Santri ────────────────────────────────────────────────────────────────
    Route::get('santri/lookups',    [SantriApiController::class, 'lookups']);
    Route::get('santri',            [SantriApiController::class, 'index']);
    Route::get('santri/{id}',       [SantriApiController::class, 'show']);
    Route::post('santri',           [SantriApiController::class, 'store'])
        ->middleware('role:super_admin,admin');
    Route::put('santri/{id}',       [SantriApiController::class, 'update'])
        ->middleware('role:super_admin,admin');
    Route::delete('santri/{id}',    [SantriApiController::class, 'destroy'])
        ->middleware('role:super_admin,admin');

    // Santri Guardians
    Route::get('santri/{id}/guardians',                                 [SantriApiController::class, 'guardians']);
    Route::post('santri/{id}/guardians',                                [SantriApiController::class, 'addGuardian']);
    Route::put('santri/{santriId}/guardians/{guardianId}',              [SantriApiController::class, 'updateGuardian']);
    Route::delete('santri/{santriId}/guardians/{guardianId}',           [SantriApiController::class, 'destroyGuardian']);
    Route::post('santri/{santriId}/guardians/{guardianId}/notify',      [SantriApiController::class, 'notifyGuardian']);

    // ── Obat ──────────────────────────────────────────────────────────────────
    Route::get('obat',          [MedicineApiController::class, 'index']);
    Route::get('obat/{id}',     [MedicineApiController::class, 'show']);
    Route::post('obat',         [MedicineApiController::class, 'store'])
        ->middleware('role:super_admin,admin');
    Route::put('obat/{id}',     [MedicineApiController::class, 'update'])
        ->middleware('role:super_admin,admin');
    Route::delete('obat/{id}',  [MedicineApiController::class, 'destroy'])
        ->middleware('role:super_admin,admin');
    Route::post('obat/mutasi',  [MedicineApiController::class, 'recordMutation']);



    // ── Master Data ───────────────────────────────────────────────────────────
    Route::get('master/kelas',              [MobileMasterDataController::class, 'classes']);
    Route::post('master/kelas',             [MobileMasterDataController::class, 'storeClass'])
        ->middleware('role:super_admin,admin');
    Route::put('master/kelas/{id}',         [MobileMasterDataController::class, 'updateClass'])
        ->middleware('role:super_admin,admin');
    Route::delete('master/kelas/{id}',      [MobileMasterDataController::class, 'destroyClass'])
        ->middleware('role:super_admin,admin');

    Route::get('master/jurusan',            [MobileMasterDataController::class, 'majors']);
    Route::post('master/jurusan',           [MobileMasterDataController::class, 'storeMajor'])
        ->middleware('role:super_admin,admin');
    Route::put('master/jurusan/{id}',       [MobileMasterDataController::class, 'updateMajor'])
        ->middleware('role:super_admin,admin');
    Route::delete('master/jurusan/{id}',    [MobileMasterDataController::class, 'destroyMajor'])
        ->middleware('role:super_admin,admin');



    // ── Rujukan RS ────────────────────────────────────────────────────────────
    Route::get('rujukan',           [HospitalReferralApiController::class, 'index']);
    Route::get('rujukan/{id}',      [HospitalReferralApiController::class, 'show']);
    Route::post('rujukan',          [HospitalReferralApiController::class, 'store']);
    Route::put('rujukan/{id}',      [HospitalReferralApiController::class, 'update']);
    Route::patch('rujukan/{id}/status', [HospitalReferralApiController::class, 'updateStatus']);
    Route::delete('rujukan/{id}',   [HospitalReferralApiController::class, 'destroy'])
        ->middleware('role:super_admin,admin');
    Route::post('rujukan/{id}/notify-guardian', [HospitalReferralApiController::class, 'notifyGuardian']);

    // ── Reports ───────────────────────────────────────────────────────────────
    Route::get('reports/daily-summary', [DashboardApiController::class, 'reportSummary']);
    Route::get('reports/sickness',      [DashboardApiController::class, 'reportSummary']);
    Route::get('reports/medicine',      [DashboardApiController::class, 'medicineReport']);

    // ── Settings ──────────────────────────────────────────────────────────────
    Route::get('settings',  [SettingsApiController::class, 'index']);
    Route::post('settings', [SettingsApiController::class, 'update']);

    // ── Approvals / Admin ─────────────────────────────────────────────────────
    Route::middleware('role:super_admin')->group(function () {
        Route::get('approvals',                         [MobileAdminController::class, 'users']);
        Route::post('approvals/{user}/approve',         [MobileAdminController::class, 'approve']);
        Route::post('approvals/{user}/reject',          [MobileAdminController::class, 'reject']);
        Route::post('auth/change-role',                 [MobileAdminController::class, 'changeRole']);
        Route::post('auth/quick-reset',                 [MobileAdminController::class, 'quickResetPassword']);
        Route::delete('auth/users/{user}',              [MobileAdminController::class, 'destroy']);
    });
});

// Legacy mobile prefix – keep backward compatibility
Route::prefix('mobile')->middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [DashboardApiController::class, 'index']);
    Route::get('/me',        [ApiAuthController::class, 'me']);
});
