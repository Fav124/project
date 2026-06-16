<?php

namespace App\Http\Controllers\Api;

use App\Models\HospitalReferral;
use App\Models\Major;
use App\Models\Medicine;
use App\Models\Santri;
use App\Models\SchoolClass;
use App\Models\SicknessCase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends BaseApiController
{
    public function index(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date', now()->subDays(14)->toDateString()));
        $endDate   = Carbon::parse($request->input('end_date', now()->toDateString()));

        $stats = [
            'santri_total'       => Santri::count(),
            'santri_l'           => Santri::where('gender', 'L')->count(),
            'santri_p'           => Santri::where('gender', 'P')->count(),
            'santri_sakit_aktif' => SicknessCase::whereIn('status', ['observed', 'handled', 'referred'])->count(),
            'obat_menipis'       => Medicine::whereColumn('stock', '<=', 'minimum_stock')->count(),
            'obat_kadaluarsa'    => Medicine::where('expiry_date', '<', now())->count(),
            'rujukan'            => HospitalReferral::whereBetween('referral_date', [$startDate, $endDate])->count(),
        ];

        $recentCases = SicknessCase::with(['santri:id,name,nis,gender', 'medicines:id,name'])
            ->latest('visit_date')
            ->take(5)
            ->get()
            ->map(fn($c) => $this->formatCase($c));

        $lowStockMedicines = Medicine::whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock')
            ->take(5)
            ->get()
            ->map(fn($m) => [
                'id'            => $m->id,
                'name'          => $m->name,
                'status'        => 'stok_kritis',
                'stock'         => $m->stock,
                'unit'          => $m->unit,
                'expiry_date'   => $m->expiry_date?->toDateString(),
            ]);

        $alertMedicines = Medicine::where(function ($q) {
                $q->whereColumn('stock', '<=', 'minimum_stock')
                  ->orWhere('expiry_date', '<', now())
                  ->orWhereBetween('expiry_date', [now(), now()->addMonths(3)]);
            })
            ->orderBy('stock')
            ->take(10)
            ->get()
            ->map(fn($m) => $this->formatAlertMedicine($m));

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

        $classDistribution = SicknessCase::with('santri.schoolClass')
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->get()
            ->groupBy(fn($c) => $c->santri?->schoolClass?->name ?? 'Tidak Diketahui')
            ->map(fn($group, $name) => ['class_name' => $name, 'count' => $group->count()])
            ->values();

        $majorDistribution = SicknessCase::with('santri.major')
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->get()
            ->groupBy(fn($c) => $c->santri?->major?->name ?? 'Tidak Diketahui')
            ->map(fn($group, $name) => ['major_name' => $name, 'count' => $group->count()])
            ->values();

        $frequentMedicines = DB::table('medicine_sickness_case')
            ->join('medicines', 'medicines.id', '=', 'medicine_sickness_case.medicine_id')
            ->join('sickness_cases', 'sickness_cases.id', '=', 'medicine_sickness_case.sickness_case_id')
            ->whereBetween('sickness_cases.visit_date', [$startDate, $endDate])
            ->select('medicines.name as medicine_name', DB::raw('COUNT(*) as count'))
            ->groupBy('medicines.id', 'medicines.name')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        return $this->success([
            'stats'              => $stats,
            'recent_cases'       => $recentCases,
            'low_stock_medicines'=> $lowStockMedicines,
            'alert_medicines'    => $alertMedicines,
            'sickness_trends'    => $sicknessTrends,
            'case_distribution'  => $caseDistribution,
            'class_distribution' => $classDistribution,
            'major_distribution' => $majorDistribution,
            'frequent_medicines' => $frequentMedicines,
            'filter' => [
                'start_date' => $startDate->toDateString(),
                'end_date'   => $endDate->toDateString(),
            ],
        ]);
    }

    // GET reports/daily-summary & reports/sickness
    public function reportSummary(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()->toDateString()));
        $endDate   = Carbon::parse($request->input('end_date', now()->endOfMonth()->toDateString()));

        $casesQuery = SicknessCase::whereBetween('visit_date', [$startDate, $endDate]);

        if ($request->filled('status')) {
            $casesQuery->where('status', $request->status);
        }

        $summary = [
            'total_santri'   => Santri::count(),
            'santri_sakit'   => SicknessCase::whereBetween('visit_date', [$startDate, $endDate])->count(),
            'rujukan_rs'     => HospitalReferral::whereBetween('referral_date', [$startDate, $endDate])->count(),
            'obat_menipis'   => Medicine::whereColumn('stock', '<=', 'minimum_stock')->count(),
            'sembuh'         => SicknessCase::whereBetween('visit_date', [$startDate, $endDate])
                ->where('status', 'recovered')->count(),
        ];

        $topDiagnoses = SicknessCase::selectRaw('diagnosis, count(*) as total')
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->whereNotNull('diagnosis')
            ->groupBy('diagnosis')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $perPage     = (int) $request->input('per_page', 100);
        $sicknessCases = SicknessCase::with(['santri:id,name,nis,gender', 'medicines:id,name'])
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->latest('visit_date')
            ->take($perPage)
            ->get()
            ->map(fn($c) => $this->formatCase($c));

        return $this->success([
            'summary'       => $summary,
            'top_diagnoses' => $topDiagnoses,
            'sickness_cases'=> $sicknessCases,
            'filter' => [
                'start_date' => $startDate->toDateString(),
                'end_date'   => $endDate->toDateString(),
            ],
        ]);
    }

    // GET reports/medicine
    public function medicineReport(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()->toDateString()));
        $endDate   = Carbon::parse($request->input('end_date', now()->endOfMonth()->toDateString()));

        $medicines = Medicine::withCount([
            'mutations as total_out' => fn($q) => $q->where('type', 'out')->whereBetween('date', [$startDate, $endDate]),
            'mutations as total_in'  => fn($q) => $q->where('type', 'in')->whereBetween('date', [$startDate, $endDate]),
        ])
        ->when($request->filled('status'), function ($q) use ($request) {
            $s = $request->status;
            if ($s === 'stok_kritis') $q->whereColumn('stock', '<=', 'minimum_stock');
            elseif ($s === 'kadaluarsa') $q->where('expiry_date', '<', now());
        })
        ->orderBy('name')
        ->get()
        ->map(fn($m) => [
            'id'            => $m->id,
            'name'          => $m->name,
            'kode_obat'     => $m->kode_obat ?? '',
            'kategori'      => $m->kategori ?? '',
            'stock'         => $m->stock,
            'minimum_stock' => $m->minimum_stock,
            'unit'          => $m->unit,
            'status'        => $this->medicineStatus($m),
            'expiry_date'   => $m->expiry_date?->toDateString(),
            'total_out'     => $m->total_out,
            'total_in'      => $m->total_in,
        ]);

        $summary = [
            'total_obat'      => Medicine::count(),
            'obat_menipis'    => Medicine::whereColumn('stock', '<=', 'minimum_stock')->count(),
            'obat_kadaluarsa' => Medicine::where('expiry_date', '<', now())->count(),
            'total_mutasi'    => \App\Models\MedicineMutation::whereBetween('date', [$startDate, $endDate])->count(),
        ];

        return $this->success([
            'medicines' => $medicines,
            'summary'   => $summary,
            'filter'    => [
                'start_date' => $startDate->toDateString(),
                'end_date'   => $endDate->toDateString(),
            ],
        ]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function formatCase($case): array
    {
        return [
            'id'           => $case->id,
            'santri'       => $case->santri ? [
                'id' => $case->santri->id, 'name' => $case->santri->name, 'nis' => $case->santri->nis,
            ] : null,
            'complaint'    => $case->complaint,
            'diagnosis'    => $case->diagnosis,
            'status'       => $case->status,
            'status_label' => $this->translateStatus($case->status),
            'visit_date'   => $case->visit_date?->toDateString(),
            'medicines'    => $case->medicines->map(fn($m) => ['id' => $m->id, 'name' => $m->name])->values(),
        ];
    }

    private function formatAlertMedicine($m): array
    {
        return [
            'id'          => $m->id,
            'name'        => $m->name,
            'status'      => $this->medicineStatus($m),
            'stock'       => $m->stock,
            'unit'        => $m->unit,
            'expiry_date' => $m->expiry_date?->toDateString(),
        ];
    }

    private function medicineStatus(Medicine $m): string
    {
        $now = now();
        if ($m->expiry_date && $m->expiry_date < $now)                          return 'kadaluarsa';
        if ($m->expiry_date && $m->expiry_date < $now->copy()->addMonths(3))    return 'segera_kadaluarsa';
        if ($m->stock <= $m->minimum_stock)                                      return 'stok_kritis';
        return 'aman';
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
