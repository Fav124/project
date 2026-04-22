<?php

namespace App\Http\Controllers;

use App\Models\Dormitory;
use App\Models\HealthRecord;
use App\Models\HospitalReferral;
use App\Models\InfirmaryBed;
use App\Models\Major;
use App\Models\Medicine;
use App\Models\Santri;
use App\Models\SchoolClass;
use App\Models\SicknessCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'santri_total' => Santri::count(),
            'santri_l' => Santri::where('gender', 'L')->count(),
            'santri_p' => Santri::where('gender', 'P')->count(),
            'kelas' => SchoolClass::count(),
            'jurusan' => Major::count(),
            'asrama' => Dormitory::count(),
            'rekam_kesehatan' => HealthRecord::count(),
            'santri_sakit_aktif' => SicknessCase::whereIn('status', ['observed', 'handled', 'referred'])->count(),
            'obat_menipis' => Medicine::whereColumn('stock', '<=', 'minimum_stock')->count(),
            'obat_kadaluarsa' => Medicine::where('expiry_date', '<', now())->count(),
            'kasur_tersedia' => InfirmaryBed::where('status', 'available')->count(),
            'rujukan' => HospitalReferral::count(),
        ];

        // Recent Data
        $recentCases = SicknessCase::with(['santri', 'medicines'])
            ->latest('visit_date')
            ->take(5)
            ->get();

        $lowStockMedicines = Medicine::whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock')
            ->take(5)
            ->get();

        // Chart Data: Sickness Trends (Last 14 Days)
        $sicknessTrends = SicknessCase::select(DB::raw('DATE(visit_date) as date'), DB::raw('COUNT(*) as count'))
            ->where('visit_date', '>=', now()->subDays(14))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Chart Data: Case Status Distribution
        $caseDistribution = SicknessCase::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Chart Data: Santri by Major
        $santriByMajor = Major::withCount('santris')->get();

        // Chart Data: Santri by Class
        $santriByClass = SchoolClass::withCount('santris')->get();

        // Chart Data: Medicine by Expiry Status
        $medicineExpiry = [
            'expired' => Medicine::where('expiry_date', '<', now())->count(),
            'expiring_soon' => Medicine::whereBetween('expiry_date', [now(), now()->addMonths(3)])->count(),
            'safe' => Medicine::where('expiry_date', '>', now()->addMonths(3))->count(),
        ];

        return view('dashboard', compact(
            'user', 
            'stats', 
            'recentCases', 
            'lowStockMedicines', 
            'sicknessTrends',
            'caseDistribution',
            'santriByMajor',
            'santriByClass',
            'medicineExpiry'
        ));
    }
}
