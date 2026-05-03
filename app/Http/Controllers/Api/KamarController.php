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
        $kamars = Kamar::all();
        return response()->json($kamars);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(['nama_kamar' => 'required|string']);
        $kamar = Kamar::create($request->all());

        return response()->json([
            'message' => 'Kamar berhasil ditambahkan.',
            'data' => $kamar
        ], 201);
    }

    public function show(Kamar $kamar): JsonResponse
    {
        return response()->json($kamar);
    }

    public function update(Request $request, Kamar $kamar): JsonResponse
    {
        $request->validate(['nama_kamar' => 'required|string']);
        $kamar->update($request->all());

        return response()->json([
            'message' => 'Kamar berhasil diperbarui.',
            'data' => $kamar
        ]);
    }

    public function destroy(Kamar $kamar): JsonResponse
    {
        $kamar->delete();
        return response()->json(['message' => 'Kamar berhasil dihapus.']);
    }
}
