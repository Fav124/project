<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KesehatanSantriRequest;
use App\Models\KesehatanSantri;
use Illuminate\Http\JsonResponse;

class KesehatanSantriController extends Controller
{
    public function index(): JsonResponse
    {
        $kesehatan = KesehatanSantri::with('santri')->paginate(10);
        return response()->json($kesehatan);
    }

    public function store(KesehatanSantriRequest $request): JsonResponse
    {
        // One to one constraint: checking if santri already has a health record
        $exists = KesehatanSantri::where('santri_id', $request->santri_id)->exists();
        if ($exists) {
            return response()->json(['message' => 'Data kesehatan utama untuk santri ini sudah ada. Silakan gunakan metode update.'], 400);
        }

        $kesehatan = KesehatanSantri::create($request->validated());
        return response()->json([
            'message' => 'Data kesehatan santri berhasil dibuat.',
            'data' => $kesehatan
        ], 201);
    }

    public function show(KesehatanSantri $kesehatanSantri): JsonResponse
    {
        $kesehatanSantri->load('santri');
        return response()->json($kesehatanSantri);
    }

    public function update(KesehatanSantriRequest $request, KesehatanSantri $kesehatanSantri): JsonResponse
    {
        $kesehatanSantri->update($request->validated());
        return response()->json([
            'message' => 'Data kesehatan santri berhasil diupdate.',
            'data' => $kesehatanSantri
        ]);
    }

    public function destroy(KesehatanSantri $kesehatanSantri): JsonResponse
    {
        $kesehatanSantri->delete();
        return response()->json([
            'message' => 'Data kesehatan santri berhasil dihapus.'
        ]);
    }
}
