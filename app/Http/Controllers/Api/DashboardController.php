<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use App\Models\Obat;
use App\Models\RawatInap;
use App\Models\Santri;
use App\Models\KasusSakit;
use App\Services\LaporanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    protected LaporanService $laporanService;

    public function __construct(LaporanService $laporanService)
    {
        $this->laporanService = $laporanService;
    }

    public function index(): JsonResponse
    {
        $startDate = Carbon::now()->subDays(14);
        $endDate = Carbon::now();

        // 1. Stats
        $santriTotal = Santri::count();
        $santriL = Santri::where('jenis_kelamin', 'L')->count();
        $santriP = Santri::where('jenis_kelamin', 'P')->count();
        $santriSakitAktif = KasusSakit::where('status_kasus', 'aktif')->count();
        $statusObat = $this->laporanService->statusStokObat();
        
        $stats = [
            'santri_total' => $santriTotal,
            'santri_l' => $santriL,
            'santri_p' => $santriP,
            'santri_sakit_aktif' => $santriSakitAktif,
            'obat_menipis' => $statusObat['stok_menipis'],
            'obat_kadaluarsa' => $statusObat['kadaluarsa'],
            'kasur_tersedia' => 0, // Mock for now since beds removed
            'kasur_total' => 0,
            'rujukan' => Kunjungan::where('status_kunjungan', 'dirujuk')->count(),
        ];

        // 2. Recent Cases
        $recentCases = Kunjungan::with(['santri.kelas'])
            ->latest('tanggal_kunjungan')
            ->limit(5)
            ->get()
            ->map(function ($k) {
                return [
                    'id' => $k->id,
                    'santri' => [
                        'id' => $k->santri->id,
                        'name' => $k->santri->nama_lengkap,
                        'nis' => $k->santri->nis,
                    ],
                    'complaint' => $k->keluhan_utama,
                    'status' => $k->status_kunjungan,
                    'status_label' => $this->getStatusLabel($k->status_kunjungan),
                    'visit_date' => $k->tanggal_kunjungan->format('Y-m-d H:i'),
                ];
            });

        // 3. Low Stock Medicines
        $lowStockMedicines = Obat::where('stok', '<=', \DB::raw('stok_minimum'))
            ->limit(5)
            ->get()
            ->map(function ($o) {
                return [
                    'id' => $o->id,
                    'name' => $o->nama_obat,
                    'unit' => $o->satuan,
                    'stock' => $o->stok,
                    'status' => 'menipis',
                ];
            });

        // 4. Sickness Trends (14 days)
        $trends = $this->laporanService->kunjunganPerPeriode($startDate, $endDate);
        $sicknessTrends = collect($trends)->map(function ($t) {
            return [
                'date' => $t['periode'],
                'count' => $t['total'],
            ];
        });

        // 5. Case Distribution
        $distribution = Kunjungan::select('status_kunjungan', \DB::raw('count(*) as total'))
            ->groupBy('status_kunjungan')
            ->get()
            ->map(function ($d) {
                return [
                    'status' => $d->status_kunjungan,
                    'status_label' => $this->getStatusLabel($d->status_kunjungan),
                    'count' => $d->total,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Dashboard data retrieved.',
            'data' => [
                'stats' => $stats,
                'recent_cases' => $recentCases,
                'low_stock_medicines' => $lowStockMedicines,
                'sickness_trends' => $sicknessTrends,
                'case_distribution' => $distribution,
                'filter' => [
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                ]
            ]
        ]);
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'sembuh' => 'Sembuh',
            'rawat_inap' => 'Rawat Inap',
            'dirujuk' => 'Dirujuk',
            'observasi' => 'Observasi',
        ];
        return $labels[$status] ?? ucfirst($status);
    }
}
