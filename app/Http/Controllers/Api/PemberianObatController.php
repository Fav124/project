<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PemberianObatRequest;
use App\Models\PemberianObat;
use App\Services\PemberianObatService;
use Exception;
use Illuminate\Http\JsonResponse;

class PemberianObatController extends Controller
{
    protected PemberianObatService $pemberianObatService;

    public function __construct(PemberianObatService $pemberianObatService)
    {
        $this->pemberianObatService = $pemberianObatService;
    }

    public function index(): JsonResponse
    {
        $data = PemberianObat::with(['kunjungan', 'santri', 'obat', 'pemberi'])->latest()->paginate(15);
        return response()->json($data);
    }

    public function store(PemberianObatRequest $request): JsonResponse
    {
        try {
            $result = $this->pemberianObatService->berikanObat(
                $request->validated(),
                $request->user()?->id
            );

            return response()->json([
                'message' => 'Obat berhasil diberikan.',
                'warning' => $result['warning'],
                'data' => $result['pemberian']
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal memberikan obat.',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function show(PemberianObat $pemberianObat): JsonResponse
    {
        $pemberianObat->load(['kunjungan', 'santri', 'obat', 'pemberi']);
        return response()->json($pemberianObat);
    }

    // Update dan Destroy umumnya dinonaktifkan atau sangat dibatasi untuk 
    // PemberianObat karena terhubung langsung ke mutasi stok.
    // Jika ada revisi, idealnya dilakukan Retur obat.
    // Di MVP ini kita tidak mengekspos update/destroy.
}
