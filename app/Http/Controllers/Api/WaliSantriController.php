<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WaliSantriRequest;
use App\Models\WaliSantri;
use Illuminate\Http\JsonResponse;

class WaliSantriController extends Controller
{
    public function index(): JsonResponse
    {
        $walis = WaliSantri::with('santri')->paginate(10);
        return response()->json($walis);
    }

    public function store(WaliSantriRequest $request): JsonResponse
    {
        $wali = WaliSantri::create($request->validated());
        return response()->json([
            'message' => 'Data wali santri berhasil ditambahkan.',
            'data' => $wali
        ], 201);
    }

    public function show(WaliSantri $waliSantri): JsonResponse
    {
        $waliSantri->load('santri');
        return response()->json($waliSantri);
    }

    public function update(WaliSantriRequest $request, WaliSantri $waliSantri): JsonResponse
    {
        $waliSantri->update($request->validated());
        return response()->json([
            'message' => 'Data wali santri berhasil diupdate.',
            'data' => $waliSantri
        ]);
    }

    public function destroy(WaliSantri $waliSantri): JsonResponse
    {
        $waliSantri->delete();
        return response()->json([
            'message' => 'Data wali santri berhasil dihapus.'
        ]);
    }
}
