<?php

namespace App\Http\Controllers\Api;

use App\Models\HospitalReferral;
use App\Models\InfirmaryBed;
use App\Models\Medicine;
use App\Models\Santri;
use App\Models\SicknessCase;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MobileReportController extends BaseApiController
{
    public function summary(Request $request)
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

        $topComplaints = SicknessCase::selectRaw('COALESCE(diagnosis, complaint) as label, count(*) as total')
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->groupBy('label')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->map(fn ($item) => [
                'label' => $item->label,
                'total' => $item->total,
            ]);

        $recentReferrals = HospitalReferral::with('santri')
            ->whereBetween('referral_date', [$startDate, $endDate])
            ->latest('referral_date')
            ->take(5)
            ->get()
            ->map(fn ($referral) => [
                'id' => $referral->id,
                'santri_name' => $referral->santri?->name,
                'hospital_name' => $referral->hospital_name,
                'status' => $referral->status,
                'referral_date' => optional($referral->referral_date)->toDateString(),
            ]);

        $lowStockMedicines = Medicine::whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock')
            ->take(5)
            ->get()
            ->map(fn ($medicine) => [
                'id' => $medicine->id,
                'name' => $medicine->name,
                'stock' => $medicine->stock,
                'minimum_stock' => $medicine->minimum_stock,
                'unit' => $medicine->unit,
            ]);

        return $this->success([
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'summary' => $summary,
            'top_complaints' => $topComplaints,
            'recent_referrals' => $recentReferrals,
            'low_stock_medicines' => $lowStockMedicines,
        ]);
    }
}
