<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LaporanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    protected LaporanService $laporanService;

    public function __construct(LaporanService $laporanService)
    {
        $this->laporanService = $laporanService;
    }

    /**
     * Utility untuk parsing date dari request
     */
    private function getDates(Request $request): array
    {
        $start = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->startOfMonth();
        $end = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : now()->endOfMonth();
        return [$start, $end];
    }

    public function kunjungan(Request $request): JsonResponse
    {
        [$start, $end] = $this->getDates($request);
        $groupBy = $request->input('group_by', 'date');

        $data = $this->laporanService->kunjunganPerPeriode($start, $end, $groupBy);
        $totalSantriSakit = $this->laporanService->santriSakitPerPeriode($start, $end);

        return response()->json([
            'periode' => ['start' => $start->toDateString(), 'end' => $end->toDateString()],
            'total_santri_unik_sakit' => $totalSantriSakit,
            'grafik_kunjungan' => $data,
        ]);
    }

    public function penggunaanObat(Request $request): JsonResponse
    {
        [$start, $end] = $this->getDates($request);
        $limit = (int) $request->input('limit', 10);

        $data = $this->laporanService->obatPalingSeringDigunakan($start, $end, $limit);

        return response()->json([
            'periode' => ['start' => $start->toDateString(), 'end' => $end->toDateString()],
            'data' => $data,
        ]);
    }

    public function statusObat(Request $request): JsonResponse
    {
        $ringkasan = $this->laporanService->statusStokObat();
        
        $response = ['ringkasan' => $ringkasan];

        // Jika request meminta detail spesifik, misal ?detail=kadaluarsa
        if ($request->has('detail')) {
            $response['detail'] = $this->laporanService->detailObatBermasalah($request->input('detail'));
        }

        return response()->json($response);
    }

    public function rawatInap(Request $request): JsonResponse
    {
        [$start, $end] = $this->getDates($request);

        $data = $this->laporanService->rawatInapPerPeriode($start, $end);

        return response()->json([
            'periode' => ['start' => $start->toDateString(), 'end' => $end->toDateString()],
            'data' => $data,
        ]);
    }

    public function rekapDemografi(Request $request): JsonResponse
    {
        [$start, $end] = $this->getDates($request);

        return response()->json([
            'periode' => ['start' => $start->toDateString(), 'end' => $end->toDateString()],
            'berdasarkan_petugas' => $this->laporanService->kunjunganBerdasarkanPetugas($start, $end),
            'berdasarkan_kelas' => $this->laporanService->kunjunganBerdasarkanKelas($start, $end),
            'berdasarkan_jurusan' => $this->laporanService->kunjunganBerdasarkanJurusan($start, $end),
        ]);
    }

    public function sicknessReport(Request $request): JsonResponse
    {
        [$start, $end] = $this->getDates($request);

        // 1. Get statistics for the summary
        $totalSantri = \App\Models\Santri::where('status_santri', 'aktif')->count();
        $santriSakit = \App\Models\Kunjungan::whereBetween('tanggal_kunjungan', [$start->startOfDay(), $end->endOfDay()])
            ->distinct('santri_id')
            ->count('santri_id');
        $rujukanRs = \App\Models\Kunjungan::whereBetween('tanggal_kunjungan', [$start->startOfDay(), $end->endOfDay()])
            ->where('status_kunjungan', 'dirujuk')
            ->count();
        $obatMenipis = \App\Models\Obat::stokMenipis()->count();
        $kasurTersedia = \App\Models\Kasur::where('status', 'tersedia')->count();
        $rawatInap = \App\Models\Kunjungan::whereBetween('tanggal_kunjungan', [$start->startOfDay(), $end->endOfDay()])
            ->where('status_kunjungan', 'rawat_inap')
            ->count();
        $sembuh = \App\Models\Kunjungan::whereBetween('tanggal_kunjungan', [$start->startOfDay(), $end->endOfDay()])
            ->where('status_kunjungan', 'sembuh')
            ->count();

        // 2. Get top diagnoses
        $topDiagnoses = \App\Models\Kunjungan::whereBetween('tanggal_kunjungan', [$start->startOfDay(), $end->endOfDay()])
            ->whereNotNull('diagnosa_sementara')
            ->where('diagnosa_sementara', '!=', '')
            ->select('diagnosa_sementara as diagnosis', DB::raw('count(*) as total'))
            ->groupBy('diagnosa_sementara')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // 3. Get sickness cases mapped
        $kunjungans = \App\Models\Kunjungan::with(['santri.kelas', 'petugas', 'pemberianObats.obat'])
            ->whereBetween('tanggal_kunjungan', [$start->startOfDay(), $end->endOfDay()])
            ->orderByDesc('tanggal_kunjungan')
            ->get();

        $mappedCases = $kunjungans->map(function($k) {
            return [
                'id' => $k->id,
                'santri' => [
                    'id' => $k->santri->id ?? 0,
                    'name' => $k->santri->nama_lengkap ?? '-',
                    'nis' => $k->santri->nis ?? null,
                    'class' => $k->santri->kelas?->nama_kelas ?? null,
                ],
                'complaint' => $k->keluhan_utama,
                'diagnosis' => $k->diagnosa_sementara,
                'action_taken' => $k->tindakan,
                'notes' => $k->catatan,
                'status' => $k->status_kunjungan,
                'status_label' => $this->getStatusLabel($k->status_kunjungan),
                'visit_date' => $k->tanggal_kunjungan ? $k->tanggal_kunjungan->format('Y-m-d H:i') : null,
                'handled_by' => $k->petugas?->name,
                'photo_url' => $k->foto ? url($k->foto) : null,
                'medicines' => $k->pemberianObats ? $k->pemberianObats->map(function($p) {
                    return [
                        'id' => $p->obat_id,
                        'name' => $p->obat->nama_obat ?? '-',
                        'quantity' => $p->jumlah,
                        'unit' => $p->obat->satuan ?? '',
                    ];
                }) : [],
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Laporan santri sakit berhasil dimuat.',
            'data' => [
                'summary' => [
                    'total_santri' => $totalSantri,
                    'santri_sakit' => $santriSakit,
                    'rujukan_rs' => $rujukanRs,
                    'obat_menipis' => $obatMenipis,
                    'kasur_tersedia' => $kasurTersedia,
                    'rawat_inap' => $rawatInap,
                    'sembuh' => $sembuh
                ],
                'top_diagnoses' => $topDiagnoses,
                'sickness_cases' => $mappedCases,
                'filter' => [
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString()
                ]
            ]
        ]);
    }

    public function medicineReport(Request $request): JsonResponse
    {
        [$start, $end] = $this->getDates($request);

        $obats = \App\Models\Obat::all();
        $totalObat = $obats->count();
        $obatMenipis = \App\Models\Obat::stokMenipis()->count();
        $obatKadaluarsa = \App\Models\Obat::kadaluarsa()->count();

        $riwayatCount = \App\Models\RiwayatStokObat::whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])->count();
        $pemberianCount = \App\Models\PemberianObat::whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])->count();
        $totalMutasi = $riwayatCount + $pemberianCount;

        $mappedObats = $obats->map(function($o) use ($start, $end) {
            $pemberianOut = \App\Models\PemberianObat::where('obat_id', $o->id)
                ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
                ->sum('jumlah');

            $riwayatOut = \App\Models\RiwayatStokObat::where('obat_id', $o->id)
                ->whereIn('jenis_mutasi', ['keluar', 'rusak', 'kadaluarsa'])
                ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
                ->sum('jumlah');

            $totalOut = $pemberianOut + $riwayatOut;

            $totalIn = \App\Models\RiwayatStokObat::where('obat_id', $o->id)
                ->whereIn('jenis_mutasi', ['masuk', 'retur'])
                ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
                ->sum('jumlah');

            $status = 'aman';
            if ($o->stok <= 0) {
                $status = 'habis';
            } elseif ($o->stok <= $o->stok_minimum) {
                $status = 'menipis';
            }

            return [
                'id' => $o->id,
                'name' => $o->nama_obat,
                'kode_obat' => $o->kode_obat,
                'kategori' => $o->kategori ?? '-',
                'stock' => (int) $o->stok,
                'minimum_stock' => (int) $o->stok_minimum,
                'unit' => $o->satuan,
                'status' => $status,
                'expiry_date' => $o->tanggal_kadaluarsa ? Carbon::parse($o->tanggal_kadaluarsa)->toDateString() : null,
                'total_out' => (int) $totalOut,
                'total_in' => (int) $totalIn,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Laporan mutasi obat berhasil dimuat.',
            'data' => [
                'medicines' => $mappedObats,
                'summary' => [
                    'total_obat' => $totalObat,
                    'obat_menipis' => $obatMenipis,
                    'obat_kadaluarsa' => $obatKadaluarsa,
                    'total_mutasi' => $totalMutasi,
                ],
                'filter' => [
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString()
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
