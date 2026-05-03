<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Kunjungan;
use App\Models\Obat;
use App\Models\RawatInap;
use App\Models\Santri;
use App\Services\LaporanService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    protected LaporanService $laporanService;

    public function __construct(LaporanService $laporanService)
    {
        $this->laporanService = $laporanService;
    }

    public function index()
    {
        $today = Carbon::today();
        
        // 1. Ringkasan Utama (Counters)
        $stats = [
            'santri_sakit_hari_ini' => Kunjungan::whereDate('tanggal_kunjungan', $today)->distinct('santri_id')->count(),
            'kunjungan_hari_ini' => Kunjungan::whereDate('tanggal_kunjungan', $today)->count(),
            'rawat_inap_aktif' => RawatInap::where('status_rawat', 'aktif')->count(),
            'total_santri' => Santri::count(),
        ];

        // 2. Status Obat (Kritis & Kadaluarsa)
        $statusObat = $this->laporanService->statusStokObat();
        
        // Data khusus untuk Chart Kadaluarsa
        $statusKadaluarsa = [
            'kadaluarsa' => $statusObat['kadaluarsa'],
            'hampir_kadaluarsa' => $statusObat['hampir_kadaluarsa'],
            'aman' => Obat::whereDate('tanggal_kadaluarsa', '>', now()->addDays(90))->count(),
        ];
        
        // 3. Aktivitas Terbaru (Audit Trail)
        $recentActivities = AuditLog::with('user:id,name')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        // 4. Tren Grafik (7 Hari)
        $trenKunjungan = $this->laporanService->kunjunganPerPeriode(now()->subDays(6), now());
        
        // 5. Obat Terlaris (Bulan Ini)
        $obatPalingSering = $this->laporanService->obatPalingSeringDigunakan(now()->startOfMonth(), now()->endOfMonth(), 5);

        return view('dashboard.index', compact(
            'stats', 
            'statusObat', 
            'statusKadaluarsa',
            'recentActivities', 
            'trenKunjungan', 
            'obatPalingSering'
        ));
    }
}
