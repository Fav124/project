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
        $kelas = Kelas::with(['santris.jurusan', 'santris.kamar'])->withCount('santris')->get();
        
        $kelas->each(function($k) {
            $majors = $k->santris->pluck('jurusan')->filter()->unique('id');
            $k->major_ids = $majors->pluck('id')->values()->toArray();
            $k->major_names = $majors->pluck('nama_jurusan')->values()->toArray();
            
            $k->santris_list = $k->santris->map(function($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->nama_lengkap,
                    'nis' => $s->nis,
                    'gender' => $s->jenis_kelamin,
                    'gender_label' => $s->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
                    'major' => $s->jurusan?->nama_jurusan,
                    'dormitory' => $s->kamar?->nama_kamar,
                ];
            })->values()->toArray();
            
            // Remove nested full relation to keep response size light
            unset($k->santris);
        });

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
