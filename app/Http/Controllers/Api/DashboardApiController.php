<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dormitory;
use App\Models\HospitalReferral;
use App\Models\InfirmaryBed;
use App\Models\Major;
use App\Models\Medicine;
use App\Models\Santri;
use App\Models\SchoolClass;
use App\Models\SicknessCase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends Controller
{
    public function index(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date', now()->subDays(14)->toDateString()));
        $endDate   = Carbon::parse($request->input('end_date', now()->toDateString()));

        $stats = [
            'santri_total'      => Santri::count(),
            'santri_l'          => Santri::where('gender', 'L')->count(),
            'santri_p'          => Santri::where('gender', 'P')->count(),
            'santri_sakit_aktif' => SicknessCase::whereIn('status', ['observed', 'handled', 'referred'])->count(),
            'obat_menipis'      => Medicine::whereColumn('stock', '<=', 'minimum_stock')->count(),
            'obat_kadaluarsa'   => Medicine::where('expiry_date', '<', now())->count(),
            'kasur_tersedia'    => InfirmaryBed::where('status', 'available')->count(),
            'kasur_total'       => InfirmaryBed::count(),
            'rujukan'           => HospitalReferral::whereBetween('referral_date', [$startDate, $endDate])->count(),
        ];

        $recentCases = SicknessCase::with(['santri:id,name,nis,gender', 'medicines:id,name'])
            ->latest('visit_date')
            ->take(5)
            ->get()
            ->map(fn($c) => $this->formatCase($c));

        $lowStockMedicines = Medicine::whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock')
            ->take(5)
            ->get(['id', 'name', 'stock', 'unit', 'minimum_stock', 'expiry_date']);

        $sicknessTrends = SicknessCase::select(DB::raw('DATE(visit_date) as date'), DB::raw('COUNT(*) as count'))
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $caseDistribution = SicknessCase::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(fn($item) => [
                'status'       => $item->status,
                'status_label' => $this->translateStatus($item->status),
                'count'        => $item->count,
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'stats'             => $stats,
                'recent_cases'      => $recentCases,
                'low_stock_medicines' => $lowStockMedicines,
                'sickness_trends'   => $sicknessTrends,
                'case_distribution' => $caseDistribution,
                'filter' => [
                    'start_date' => $startDate->toDateString(),
                    'end_date'   => $endDate->toDateString(),
                ],
            ],
        ]);
    }

    public function reportSummary(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()->toDateString()));
        $endDate   = Carbon::parse($request->input('end_date', now()->endOfMonth()->toDateString()));

        $summary = [
            'total_santri'   => Santri::count(),
            'santri_sakit'   => SicknessCase::whereBetween('visit_date', [$startDate, $endDate])->count(),
            'rujukan_rs'     => HospitalReferral::whereBetween('referral_date', [$startDate, $endDate])->count(),
            'obat_menipis'   => Medicine::whereColumn('stock', '<=', 'minimum_stock')->count(),
            'kasur_tersedia' => InfirmaryBed::where('status', 'available')->count(),
        ];

        $topDiagnoses = SicknessCase::selectRaw('diagnosis, count(*) as total')
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->whereNotNull('diagnosis')
            ->groupBy('diagnosis')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'summary'      => $summary,
                'top_diagnoses' => $topDiagnoses,
                'filter' => [
                    'start_date' => $startDate->toDateString(),
                    'end_date'   => $endDate->toDateString(),
                ],
            ],
        ]);
    }

    private function formatCase($case): array
    {
        return [
            'id'          => $case->id,
            'santri'      => $case->santri ? ['id' => $case->santri->id, 'name' => $case->santri->name, 'nis' => $case->santri->nis] : null,
            'complaint'   => $case->complaint,
            'diagnosis'   => $case->diagnosis,
            'status'      => $case->status,
            'status_label' => $this->translateStatus($case->status),
            'visit_date'  => $case->visit_date?->toDateString(),
            'medicines'   => $case->medicines->map(fn($m) => ['id' => $m->id, 'name' => $m->name])->values(),
        ];
    }

    private function translateStatus(string $status): string
    {
        return match ($status) {
            'observed'  => 'Observasi',
            'handled'   => 'Ditangani',
            'recovered' => 'Sembuh',
            'referred'  => 'Dirujuk',
            default     => ucfirst($status),
        };
    }
}
