<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Diagnosa;
use App\Models\KeluhanMaster;
use App\Models\TindakanMaster;
use Illuminate\Http\Request;

class MedicalTagController extends Controller
{
    // ── Diagnosa ────────────────────────────────────────────
    public function searchDiagnosa(Request $request)
    {
        $q = $request->get('q', '');
        $data = Diagnosa::active()
            ->when($q, fn($query) => $query->where('nama', 'like', "%{$q}%")
                ->orWhere('kode', 'like', "%{$q}%")
                ->orWhere('kategori', 'like', "%{$q}%"))
            ->orderBy('kategori')->orderBy('nama')
            ->limit(20)->get();
        return response()->json($data);
    }

    public function storeDiagnosa(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:255']);
        $diagnosa = Diagnosa::firstOrCreate(
            ['nama' => trim($request->nama)],
            ['kategori' => 'Lainnya', 'is_active' => true]
        );
        return response()->json($diagnosa, 201);
    }

    public function allDiagnosa()
    {
        $data = Diagnosa::active()->orderBy('kategori')->orderBy('nama')->get()
            ->groupBy('kategori');
        return response()->json($data);
    }

    // ── Keluhan ────────────────────────────────────────────
    public function searchKeluhan(Request $request)
    {
        $q = $request->get('q', '');
        $data = KeluhanMaster::active()
            ->when($q, fn($query) => $query->where('nama', 'like', "%{$q}%")
                ->orWhere('kategori', 'like', "%{$q}%"))
            ->orderBy('kategori')->orderBy('nama')
            ->limit(20)->get();
        return response()->json($data);
    }

    public function storeKeluhan(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:255']);
        $keluhan = KeluhanMaster::firstOrCreate(
            ['nama' => trim($request->nama)],
            ['kategori' => 'Lainnya', 'is_active' => true]
        );
        return response()->json($keluhan, 201);
    }

    public function allKeluhan()
    {
        $data = KeluhanMaster::active()->orderBy('kategori')->orderBy('nama')->get()
            ->groupBy('kategori');
        return response()->json($data);
    }

    // ── Tindakan ────────────────────────────────────────────
    public function searchTindakan(Request $request)
    {
        $q = $request->get('q', '');
        $data = TindakanMaster::active()
            ->when($q, fn($query) => $query->where('nama', 'like', "%{$q}%")
                ->orWhere('kategori', 'like', "%{$q}%"))
            ->orderBy('kategori')->orderBy('nama')
            ->limit(20)->get();
        return response()->json($data);
    }

    public function storeTindakan(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:255']);
        $tindakan = TindakanMaster::firstOrCreate(
            ['nama' => trim($request->nama)],
            ['kategori' => 'Lainnya', 'is_active' => true]
        );
        return response()->json($tindakan, 201);
    }

    public function allTindakan()
    {
        $data = TindakanMaster::active()->orderBy('kategori')->orderBy('nama')->get()
            ->groupBy('kategori');
        return response()->json($data);
    }
}
