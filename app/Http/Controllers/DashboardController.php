<?php

namespace App\Http\Controllers;

use App\Models\HealthRecord;
use App\Models\HospitalReferral;
use App\Models\InfirmaryBed;
use App\Models\Medicine;
use App\Models\Santri;
use App\Models\SicknessCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'santri' => Santri::count(),
            'rekam_kesehatan' => HealthRecord::count(),
            'santri_sakit_aktif' => SicknessCase::whereIn('status', ['observed', 'handled', 'referred'])->count(),
            'obat_menipis' => Medicine::whereColumn('stock', '<=', 'minimum_stock')->count(),
            'kasur_tersedia' => InfirmaryBed::where('status', 'available')->count(),
            'rujukan' => HospitalReferral::count(),
        ];

        // Recent Data
        $recentCases = SicknessCase::with('santri')
            ->latest('visit_date')
            ->take(5)
            ->get();

        $lowStockMedicines = Medicine::whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock')
            ->take(5)
            ->get();

        $recentReferrals = HospitalReferral::with('santri')
            ->latest('referral_date')
            ->take(5)
            ->get();

        // Chart Data: Sickness Trends (Last 7 Days)
        $sicknessTrends = SicknessCase::selectRaw('DATE(visit_date) as date, COUNT(*) as count')
            ->where('visit_date', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Chart Data: Case Status Distribution
        $caseDistribution = SicknessCase::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return view('dashboard', compact(
            'user', 
            'stats', 
            'recentCases', 
            'lowStockMedicines', 
            'recentReferrals',
            'sicknessTrends',
            'caseDistribution'
        ));
    }
}
