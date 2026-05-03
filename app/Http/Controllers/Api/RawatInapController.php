<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RawatInapRequest;
use App\Models\RawatInap;
use App\Services\RawatInapService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RawatInapController extends Controller
{
    protected RawatInapService $rawatInapService;

    public function __construct(RawatInapService $rawatInapService)
    {
        $this->rawatInapService = $rawatInapService;
    }

    public function index(Request $request): JsonResponse
    {
        $query = RawatInap::with(['santri', 'kunjungan'])->latest('tanggal_masuk');
        
        if ($request->has('status_rawat')) {
            $query->where('status_rawat', $request->status_rawat);
        }

        return response()->json($query->paginate(15));
    }

    public function store(RawatInapRequest $request): JsonResponse
    {
        try {
            $rawatInap = $this->rawatInapService->masukRawatInap($request->validated());

            return response()->json([
                'message' => 'Santri berhasil didaftarkan ke rawat inap.',
                'data' => $rawatInap
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal mendaftarkan rawat inap.',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function show(RawatInap $rawatInap): JsonResponse
    {
        $rawatInap->load(['santri', 'kunjungan']);
        return response()->json($rawatInap);
    }

    /**
     * Endpoint untuk memulangkan / menyelesaikan rawat inap
     */
    public function keluar(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'tanggal_keluar' => 'required|date',
            'kondisi_keluar' => 'required|string',
            'status_rawat' => 'required|in:selesai,pindah,dirujuk',
        ]);

        try {
            $rawatInap = $this->rawatInapService->keluarRawatInap($id, $request->all());

            return response()->json([
                'message' => 'Rawat inap diselesaikan.',
                'data' => $rawatInap
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal menyelesaikan rawat inap.',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
