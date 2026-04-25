<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountSettingController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\HospitalReferralController;
use App\Http\Controllers\InfirmaryBedController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SicknessCaseController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\DormitoryController;
use Illuminate\Support\Facades\Route;

// ─── Guest Routes ─────────────────────────────────────────────────────────────

Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register'])->name('register.submit');
});

Route::middleware('auth')->group(function () {
    Route::get('/pengaturan-akun/foto/{user}', [AccountSettingController::class, 'photo'])->name('account.profile-photo');
    Route::get('/pengaturan-akun', [AccountSettingController::class, 'edit'])->name('account.settings.edit');
    Route::put('/pengaturan-akun', [AccountSettingController::class, 'update'])->name('account.settings.update');
});

// ─── Authenticated Routes (approved users) ────────────────────────────────────

Route::middleware(['auth', 'approved'])->group(function () {
    Route::get('/',          [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout',   [AuthController::class, 'logout'])->name('logout');

    Route::get('/santri', [SantriController::class, 'index'])->name('santri.index');
    Route::post('/santri', [SantriController::class, 'store'])->name('santri.store');
    Route::get('/santri/{santri}', [SantriController::class, 'show'])->name('santri.show');
    Route::put('/santri/{santri}', [SantriController::class, 'update'])->name('santri.update');
    Route::delete('/santri/{santri}', [SantriController::class, 'destroy'])
        ->middleware('role:super_admin,admin')
        ->name('santri.destroy');

    Route::get('/kelas', [SchoolClassController::class, 'index'])->name('classes.index');
    Route::post('/kelas', [SchoolClassController::class, 'store'])->name('classes.store');
    Route::get('/kelas/{class}', [SchoolClassController::class, 'show'])->name('classes.show');
    Route::put('/kelas/{class}', [SchoolClassController::class, 'update'])->name('classes.update');
    Route::delete('/kelas/{class}', [SchoolClassController::class, 'destroy'])
        ->middleware('role:super_admin,admin')
        ->name('classes.destroy');

    Route::get('/jurusan', [MajorController::class, 'index'])->name('majors.index');
    Route::post('/jurusan', [MajorController::class, 'store'])->name('majors.store');
    Route::get('/jurusan/{major}', [MajorController::class, 'show'])->name('majors.show');
    Route::put('/jurusan/{major}', [MajorController::class, 'update'])->name('majors.update');
    Route::delete('/jurusan/{major}', [MajorController::class, 'destroy'])
        ->middleware('role:super_admin,admin')
        ->name('majors.destroy');

    Route::get('/asrama', [DormitoryController::class, 'index'])->name('dormitories.index');
    Route::post('/asrama', [DormitoryController::class, 'store'])->name('dormitories.store');
    Route::put('/asrama/{dormitory}', [DormitoryController::class, 'update'])->name('dormitories.update');
    Route::delete('/asrama/{dormitory}', [DormitoryController::class, 'destroy'])
        ->middleware('role:super_admin,admin')
        ->name('dormitories.destroy');

    Route::get('/obat', [MedicineController::class, 'index'])->name('medicines.index');
    Route::post('/obat', [MedicineController::class, 'store'])->name('medicines.store');
    Route::get('/obat/{medicine}', [MedicineController::class, 'show'])->name('medicines.show');
    Route::put('/obat/{medicine}', [MedicineController::class, 'update'])->name('medicines.update');
    Route::delete('/obat/{medicine}', [MedicineController::class, 'destroy'])
        ->middleware('role:super_admin,admin')
        ->name('medicines.destroy');

    Route::get('/kasur-uks', [InfirmaryBedController::class, 'index'])->name('beds.index');
    Route::post('/kasur-uks', [InfirmaryBedController::class, 'store'])->name('beds.store');
    Route::get('/kasur-uks/{bed}', [InfirmaryBedController::class, 'show'])->name('beds.show');
    Route::put('/kasur-uks/{bed}', [InfirmaryBedController::class, 'update'])->name('beds.update');
    Route::delete('/kasur-uks/{bed}', [InfirmaryBedController::class, 'destroy'])
        ->middleware('role:super_admin,admin')
        ->name('beds.destroy');


    Route::get('/santri-sakit', [SicknessCaseController::class, 'index'])->name('sickness-cases.index');
    Route::post('/santri-sakit', [SicknessCaseController::class, 'store'])->name('sickness-cases.store');
    Route::get('/santri-sakit/{sicknessCase}', [SicknessCaseController::class, 'show'])->name('sickness-cases.show');
    Route::put('/santri-sakit/{sicknessCase}', [SicknessCaseController::class, 'update'])->name('sickness-cases.update');
    Route::post('/santri-sakit/{sicknessCase}/notify-guardian', [SicknessCaseController::class, 'notifyGuardian'])->name('sickness-cases.notify');
    Route::post('/santri-sakit/{sicknessCase}/mark-recovered', [SicknessCaseController::class, 'markRecovered'])->name('sickness-cases.recovered');
    Route::put('/santri-sakit/medicine/{pivotId}/update-status', [SicknessCaseController::class, 'updateMedicineStatus'])->name('sickness-cases.medicine-status');
    Route::delete('/santri-sakit/{sicknessCase}', [SicknessCaseController::class, 'destroy'])->name('sickness-cases.destroy');

    Route::get('/rujukan-rs', [HospitalReferralController::class, 'index'])->name('referrals.index');
    Route::post('/rujukan-rs', [HospitalReferralController::class, 'store'])->name('referrals.store');
    Route::get('/rujukan-rs/{referral}', [HospitalReferralController::class, 'show'])->name('referrals.show');
    Route::put('/rujukan-rs/{referral}', [HospitalReferralController::class, 'update'])->name('referrals.update');
    Route::post('/rujukan-rs/{referral}/notify-guardian', [HospitalReferralController::class, 'notifyGuardian'])->name('referrals.notify');
    Route::delete('/rujukan-rs/{referral}', [HospitalReferralController::class, 'destroy'])->name('referrals.destroy');

    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/cetak', [ReportController::class, 'print'])->name('reports.print');
});

// ─── Super Admin Routes ───────────────────────────────────────────────────────

Route::middleware(['auth', 'role:super_admin'])
    ->prefix('super-admin')
    ->name('super-admin.')
    ->group(function () {

        // Logout (super admin bypasses 'approved' middleware)
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // Dashboard
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');

        // User management
        Route::get('/users',             [SuperAdminController::class, 'users'])->name('users.index');
        Route::get('/approvals',         [SuperAdminController::class, 'users'])->defaults('status', 'pending')->name('approvals.index');
        Route::get('/users/{user}',      [SuperAdminController::class, 'showUser'])->name('users.show');
        Route::post('/users/{user}/approve',       [SuperAdminController::class, 'approve'])->name('users.approve');
        Route::post('/users/{user}/reject',        [SuperAdminController::class, 'reject'])->name('users.reject');
        Route::post('/users/{user}/change-role',   [SuperAdminController::class, 'changeRole'])->name('users.change-role');
        Route::post('/users/{user}/reset-password',[SuperAdminController::class, 'resetPassword'])->name('users.reset-password');
        Route::post('/users/{user}/quick-reset',   [SuperAdminController::class, 'quickResetPassword'])->name('users.quick-reset');
        Route::delete('/users/{user}',             [SuperAdminController::class, 'destroy'])->name('users.destroy');
    });
