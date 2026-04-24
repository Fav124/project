<?php

namespace App\Http\Controllers\Api;

use App\Models\HospitalReferral;
use App\Models\InfirmaryBed;
use App\Models\Medicine;
use App\Models\Santri;
use App\Models\SicknessCase;

class MobileDashboardController extends BaseApiController
{
    public function index()
    {
        $stats = [
            'total_santri' => Santri::count(),
            'kasus_aktif' => SicknessCase::whereIn('status', ['observed', 'handled', 'referred'])->count(),
            'rujukan_aktif' => HospitalReferral::whereIn('status', ['pending', 'ongoing'])->count(),
            'obat_menipis' => Medicine::whereColumn('stock', '<=', 'minimum_stock')->count(),
            'kasur_tersedia' => InfirmaryBed::where('status', 'available')->count(),
        ];

        $recentCases = SicknessCase::with(['santri', 'bed'])
            ->latest('visit_date')
            ->take(5)
            ->get()
            ->map(fn ($case) => [
                'id' => $case->id,
                'santri_name' => $case->santri?->name,
                'complaint' => $case->complaint,
                'status' => $case->status,
                'visit_date' => optional($case->visit_date)->toDateString(),
                'bed_code' => $case->bed?->code,
            ]);

        $recentReferrals = HospitalReferral::with('santri')
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

        return $this->success([
            'stats' => $stats,
            'recent_cases' => $recentCases,
            'recent_referrals' => $recentReferrals,
        ]);
    }
}
