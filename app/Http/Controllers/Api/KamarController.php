<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KamarController extends Controller
{
    public function index(): JsonResponse
    {
        $kamars = Kamar::with(['santris.kelas'])
            ->withCount('santris')
            ->get()
            ->map(function ($k) {
                $mappedSantris = $k->santris->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'name' => $s->nama_lengkap,
                        'nis' => $s->nis,
                        'gender' => $s->jenis_kelamin,
                        'class' => $s->kelas ? $s->kelas->nama_kelas : null,
                    ];
                });

                return [
                    'id' => $k->id,
                    'nama_kamar' => $k->nama_kamar,
                    'catatan' => $k->catatan,
                    'building' => 'Gedung Utama',
                    'gender' => str_contains(strtolower($k->nama_kamar), 'putri') ? 'P' : 'L',
                    'supervisor_name' => 'Ustadz / Ustadzah',
                    'santri_count' => $k->santris_count,
                    'santris' => $mappedSantris,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $kamars
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(['nama_kamar' => 'required|string']);
        $k = Kamar::create($request->all());
        $k->loadCount('santris');

        $dorm = [
            'id' => $k->id,
            'nama_kamar' => $k->nama_kamar,
            'catatan' => $k->catatan,
            'building' => 'Gedung Utama',
            'gender' => str_contains(strtolower($k->nama_kamar), 'putri') ? 'P' : 'L',
            'supervisor_name' => 'Ustadz / Ustadzah',
            'santri_count' => $k->santris_count ?? 0,
        ];

        return response()->json([
            'message' => 'Kamar berhasil ditambahkan.',
            'data' => $dorm
        ], 201);
    }

    public function show(Kamar $kamar): JsonResponse
    {
        $kamar->loadCount('santris');
        $kamar->load(['santris.kelas']);

        $mappedSantris = $kamar->santris->map(function ($s) {
            return [
                'id' => $s->id,
                'name' => $s->nama_lengkap,
                'nis' => $s->nis,
                'gender' => $s->jenis_kelamin,
                'class' => $s->kelas ? $s->kelas->nama_kelas : null,
            ];
        });

        $dorm = [
            'id' => $kamar->id,
            'nama_kamar' => $kamar->nama_kamar,
            'catatan' => $kamar->catatan,
            'building' => 'Gedung Utama',
            'gender' => str_contains(strtolower($kamar->nama_kamar), 'putri') ? 'P' : 'L',
            'supervisor_name' => 'Ustadz / Ustadzah',
            'santri_count' => $kamar->santris_count ?? 0,
            'santris' => $mappedSantris,
        ];
        return response()->json($dorm);
    }

    public function update(Request $request, Kamar $kamar): JsonResponse
    {
        $request->validate(['nama_kamar' => 'required|string']);
        $kamar->update($request->all());
        $kamar->loadCount('santris');

        $dorm = [
            'id' => $kamar->id,
            'nama_kamar' => $kamar->nama_kamar,
            'catatan' => $kamar->catatan,
            'building' => 'Gedung Utama',
            'gender' => str_contains(strtolower($kamar->nama_kamar), 'putri') ? 'P' : 'L',
            'supervisor_name' => 'Ustadz / Ustadzah',
            'santri_count' => $kamar->santris_count ?? 0,
        ];

        return response()->json([
            'message' => 'Kamar berhasil diperbarui.',
            'data' => $dorm
        ]);
    }

    public function destroy(Kamar $kamar): JsonResponse
    {
        $kamar->delete();
        return response()->json(['message' => 'Kamar berhasil dihapus.']);
    }
}
