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
use App\Http\Controllers\Api\InfirmaryBedApiController;
use Illuminate\Support\Facades\Route;

// ─── Public API Routes ──────────────────────────────────────────────────────
Route::prefix('mobile')->group(function () {

    Route::post('/login', [ApiAuthController::class, 'login']);

    // ─── Protected API Routes ───────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [ApiAuthController::class, 'logout']);
        Route::get('/me',     [ApiAuthController::class, 'me']);

        // Dashboard
        Route::get('/dashboard', [DashboardApiController::class, 'index']);

        // Santri
        Route::get('/santri/lookups',  [SantriApiController::class, 'lookups']);
        Route::get('/santri',          [SantriApiController::class, 'index']);
        Route::get('/santri/{santri}', [SantriApiController::class, 'show']);
        Route::post('/santri',         [SantriApiController::class, 'store'])
            ->middleware('role:super_admin,admin');
        Route::put('/santri/{santri}', [SantriApiController::class, 'update'])
            ->middleware('role:super_admin,admin');
        Route::delete('/santri/{santri}', [SantriApiController::class, 'destroy'])
            ->middleware('role:super_admin,admin');

        // Kasus Sakit
        Route::get('/sickness-cases/lookups',      [SicknessCaseApiController::class, 'lookups']);
        Route::get('/sickness-cases',              [SicknessCaseApiController::class, 'index']);
        Route::get('/sickness-cases/{id}',         [SicknessCaseApiController::class, 'show']);
        Route::post('/sickness-cases',             [SicknessCaseApiController::class, 'store']);
        Route::put('/sickness-cases/{id}',         [SicknessCaseApiController::class, 'update']);
        Route::delete('/sickness-cases/{id}',      [SicknessCaseApiController::class, 'destroy'])
            ->middleware('role:super_admin,admin');
        Route::post('/sickness-cases/{id}/mark-recovered', [SicknessCaseApiController::class, 'markRecovered']);
        Route::post('/sickness-cases/{id}/notify-guardian', [WhatsAppApiController::class, 'notifySicknessCase']);

        // Obat
        Route::get('/medicines',           [MedicineApiController::class, 'index']);
        Route::get('/medicines/{id}',      [MedicineApiController::class, 'show']);
        Route::post('/medicines',          [MedicineApiController::class, 'store'])
            ->middleware('role:super_admin,admin');
        Route::put('/medicines/{id}',      [MedicineApiController::class, 'update'])
            ->middleware('role:super_admin,admin');
        Route::delete('/medicines/{id}',   [MedicineApiController::class, 'destroy'])
            ->middleware('role:super_admin,admin');

        // Kasur UKS
        Route::get('/beds',       [InfirmaryBedApiController::class, 'index']);
        Route::get('/beds/{id}',  [InfirmaryBedApiController::class, 'show']);
        Route::post('/beds',      [InfirmaryBedApiController::class, 'store'])
            ->middleware('role:super_admin,admin');
        Route::put('/beds/{id}',  [InfirmaryBedApiController::class, 'update'])
            ->middleware('role:super_admin,admin');
        Route::delete('/beds/{id}', [InfirmaryBedApiController::class, 'destroy'])
            ->middleware('role:super_admin,admin');

        // Rujukan RS
        Route::get('/referrals',           [HospitalReferralApiController::class, 'index']);
        Route::get('/referrals/{id}',      [HospitalReferralApiController::class, 'show']);
        Route::post('/referrals',          [HospitalReferralApiController::class, 'store']);
        Route::put('/referrals/{id}',      [HospitalReferralApiController::class, 'update']);
        Route::delete('/referrals/{id}',   [HospitalReferralApiController::class, 'destroy'])
            ->middleware('role:super_admin,admin');
        Route::post('/referrals/{id}/notify-guardian', [WhatsAppApiController::class, 'notifyReferral']);

        // Laporan
        Route::get('/reports/summary', [DashboardApiController::class, 'reportSummary']);

        // Master Data
        Route::get('/master/classes', [MobileMasterDataController::class, 'classes']);
        Route::post('/master/classes', [MobileMasterDataController::class, 'storeClass'])
            ->middleware('role:super_admin,admin');
        Route::put('/master/classes/{class}', [MobileMasterDataController::class, 'updateClass'])
            ->middleware('role:super_admin,admin');
        Route::delete('/master/classes/{class}', [MobileMasterDataController::class, 'destroyClass'])
            ->middleware('role:super_admin,admin');

        Route::get('/master/majors', [MobileMasterDataController::class, 'majors']);
        Route::post('/master/majors', [MobileMasterDataController::class, 'storeMajor'])
            ->middleware('role:super_admin,admin');
        Route::put('/master/majors/{major}', [MobileMasterDataController::class, 'updateMajor'])
            ->middleware('role:super_admin,admin');
        Route::delete('/master/majors/{major}', [MobileMasterDataController::class, 'destroyMajor'])
            ->middleware('role:super_admin,admin');

        Route::get('/master/dormitories', [MobileMasterDataController::class, 'dormitories']);
        Route::post('/master/dormitories', [MobileMasterDataController::class, 'storeDormitory'])
            ->middleware('role:super_admin,admin');
        Route::put('/master/dormitories/{dormitory}', [MobileMasterDataController::class, 'updateDormitory'])
            ->middleware('role:super_admin,admin');
        Route::delete('/master/dormitories/{dormitory}', [MobileMasterDataController::class, 'destroyDormitory'])
            ->middleware('role:super_admin,admin');

        // Administrasi Super Admin
        Route::middleware('role:super_admin')->prefix('/admin')->group(function () {
            Route::get('/overview', [MobileAdminController::class, 'overview']);
            Route::get('/users', [MobileAdminController::class, 'users']);
            Route::post('/users/{user}/approve', [MobileAdminController::class, 'approve']);
            Route::post('/users/{user}/reject', [MobileAdminController::class, 'reject']);
            Route::post('/users/{user}/change-role', [MobileAdminController::class, 'changeRole']);
            Route::post('/users/{user}/quick-reset', [MobileAdminController::class, 'quickResetPassword']);
            Route::delete('/users/{user}', [MobileAdminController::class, 'destroy']);
        });
    });
});
