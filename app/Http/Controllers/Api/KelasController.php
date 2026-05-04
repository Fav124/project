<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index(): JsonResponse
    {
        $kelas = Kelas::withCount('santris')->get();
        return response()->json([
            'success' => true,
            'data' => [
                'items' => $kelas
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(['nama_kelas' => 'required|string|unique:kelas']);
        $kelas = Kelas::create($request->all());

        return response()->json([
            'message' => 'Kelas berhasil ditambahkan.',
            'data' => $kelas
        ], 201);
    }

    public function show(Kelas $kela): JsonResponse
    {
        $kela->load(['santris.jurusan']);

        $groupedSantri = $kela->santris->groupBy(function ($s) {
            return $s->jurusan->nama_jurusan ?? 'Tanpa Jurusan';
        });

        return response()->json([
            'kelas' => $kela,
            'santri_per_jurusan' => $groupedSantri,
        ]);
    }

    public function update(Request $request, Kelas $kela): JsonResponse
    {
        $request->validate(['nama_kelas' => 'required|string|unique:kelas,nama_kelas,' . $kela->id]);
        $kela->update($request->all());

        return response()->json([
            'message' => 'Kelas berhasil diperbarui.',
            'data' => $kela
        ]);
    }

    public function destroy(Kelas $kela): JsonResponse
    {
        $kela->delete();
        return response()->json(['message' => 'Kelas berhasil dihapus.']);
    }
}
