<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\DashboardApiController;
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
    });
});
