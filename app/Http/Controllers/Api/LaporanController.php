<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LaporanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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
}
