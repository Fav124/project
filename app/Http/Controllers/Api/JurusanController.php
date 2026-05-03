<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index(): JsonResponse
    {
        $jurusans = Jurusan::withCount('santris')->get();
        return response()->json($jurusans);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(['nama_jurusan' => 'required|string|unique:jurusans']);
        $jurusan = Jurusan::create($request->all());

        return response()->json([
            'message' => 'Jurusan berhasil ditambahkan.',
            'data' => $jurusan
        ], 201);
    }

    public function show(Jurusan $jurusan): JsonResponse
    {
        $jurusan->loadCount('santris');
        return response()->json($jurusan);
    }

    public function update(Request $request, Jurusan $jurusan): JsonResponse
    {
        $request->validate(['nama_jurusan' => 'required|string|unique:jurusans,nama_jurusan,' . $jurusan->id]);
        $jurusan->update($request->all());

        return response()->json([
            'message' => 'Jurusan berhasil diperbarui.',
            'data' => $jurusan
        ]);
    }

    public function destroy(Jurusan $jurusan): JsonResponse
    {
        $jurusan->delete();
        return response()->json(['message' => 'Jurusan berhasil dihapus.']);
    }
}
