<?php

namespace App\Http\Controllers;

use App\Models\HospitalReferral;
use App\Models\InfirmaryBed;
use App\Models\Medicine;
use App\Models\Santri;
use App\Models\SicknessCase;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()->toDateString()));
        $endDate = Carbon::parse($request->input('end_date', now()->endOfMonth()->toDateString()));

        $summary = [
            'total_santri' => Santri::count(),
            'santri_sakit' => SicknessCase::whereBetween('visit_date', [$startDate, $endDate])->count(),
            'rujukan_rs' => HospitalReferral::whereBetween('referral_date', [$startDate, $endDate])->count(),
            'obat_menipis' => Medicine::whereColumn('stock', '<=', 'minimum_stock')->count(),
            'kasur_tersedia' => InfirmaryBed::where('status', 'available')->count(),
        ];

        $topComplaints = SicknessCase::selectRaw('diagnosis, count(*) as total')
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->whereNotNull('diagnosis')
            ->groupBy('diagnosis')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $recentReferrals = HospitalReferral::with('santri')
            ->whereBetween('referral_date', [$startDate, $endDate])
            ->latest('referral_date')
            ->take(5)
            ->get();

        $lowStockMedicines = Medicine::whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock')
            ->take(5)
            ->get();

        return view('health.reports.index', compact(
            'summary',
            'topComplaints',
            'recentReferrals',
            'lowStockMedicines',
            'startDate',
            'endDate'
        ));
    }
    public function print(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()->toDateString()));
        $endDate = Carbon::parse($request->input('end_date', now()->endOfMonth()->toDateString()));

        $summary = [
            'total_santri' => Santri::count(),
            'santri_sakit' => SicknessCase::whereBetween('visit_date', [$startDate, $endDate])->count(),
            'rujukan_rs' => HospitalReferral::whereBetween('referral_date', [$startDate, $endDate])->count(),
            'obat_menipis' => Medicine::whereColumn('stock', '<=', 'minimum_stock')->count(),
            'kasur_tersedia' => InfirmaryBed::where('status', 'available')->count(),
        ];

        $allCases = SicknessCase::with('santri')
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->orderBy('visit_date')
            ->get();

        $referrals = HospitalReferral::with('santri')
            ->whereBetween('referral_date', [$startDate, $endDate])
            ->get();

        return view('health.reports.print', compact('summary', 'allCases', 'referrals', 'startDate', 'endDate'));
    }
}
