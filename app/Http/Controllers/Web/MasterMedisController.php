<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Diagnosa;
use App\Models\KeluhanMaster;
use App\Models\TindakanMaster;
use Illuminate\Http\Request;

class MasterMedisController extends Controller
{
    // ── INDEX ────────────────────────────────────────────────
    public function index()
    {
        $diagnosas  = Diagnosa::orderBy('kategori')->orderBy('nama')->paginate(20, ['*'], 'diagnosa_page');
        $keluhanList= KeluhanMaster::orderBy('kategori')->orderBy('nama')->paginate(20, ['*'], 'keluhan_page');
        $tindakans  = TindakanMaster::orderBy('kategori')->orderBy('nama')->paginate(20, ['*'], 'tindakan_page');

        $diagnosaKategori = Diagnosa::select('kategori')->distinct()->pluck('kategori');
        $keluhanKategori  = KeluhanMaster::select('kategori')->distinct()->pluck('kategori');
        $tindakanKategori = TindakanMaster::select('kategori')->distinct()->pluck('kategori');

        return view('master-medis.index', compact(
            'diagnosas', 'keluhanList', 'tindakans',
            'diagnosaKategori', 'keluhanKategori', 'tindakanKategori'
        ));
    }

    // ── STORE ────────────────────────────────────────────────
    public function storeDiagnosa(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255|unique:diagnosas,nama',
            'kode'     => 'nullable|string|max:20',
            'kategori' => 'nullable|string|max:100',
            'deskripsi'=> 'nullable|string',
        ]);
        Diagnosa::create($request->only('nama','kode','kategori','deskripsi') + ['is_active' => true]);
        return back()->with('success', 'Diagnosa berhasil ditambahkan.')->withFragment('tab-diagnosa');
    }

    public function storeKeluhan(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255|unique:keluhan_masters,nama',
            'kategori' => 'nullable|string|max:100',
        ]);
        KeluhanMaster::create($request->only('nama','kategori') + ['is_active' => true]);
        return back()->with('success', 'Keluhan berhasil ditambahkan.')->withFragment('tab-keluhan');
    }

    public function storeTindakan(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255|unique:tindakan_masters,nama',
            'kategori' => 'nullable|string|max:100',
        ]);
        TindakanMaster::create($request->only('nama','kategori') + ['is_active' => true]);
        return back()->with('success', 'Tindakan berhasil ditambahkan.')->withFragment('tab-tindakan');
    }

    // ── UPDATE ───────────────────────────────────────────────
    public function updateDiagnosa(Request $request, Diagnosa $diagnosa)
    {
        $request->validate([
            'nama'     => 'required|string|max:255|unique:diagnosas,nama,'.$diagnosa->id,
            'kode'     => 'nullable|string|max:20',
            'kategori' => 'nullable|string|max:100',
            'deskripsi'=> 'nullable|string',
            'is_active'=> 'boolean',
        ]);
        $diagnosa->update($request->only('nama','kode','kategori','deskripsi','is_active'));
        return back()->with('success', 'Diagnosa berhasil diperbarui.');
    }

    public function updateKeluhan(Request $request, KeluhanMaster $keluhan)
    {
        $request->validate([
            'nama'     => 'required|string|max:255|unique:keluhan_masters,nama,'.$keluhan->id,
            'kategori' => 'nullable|string|max:100',
            'is_active'=> 'boolean',
        ]);
        $keluhan->update($request->only('nama','kategori','is_active'));
        return back()->with('success', 'Keluhan berhasil diperbarui.');
    }

    public function updateTindakan(Request $request, TindakanMaster $tindakan)
    {
        $request->validate([
            'nama'     => 'required|string|max:255|unique:tindakan_masters,nama,'.$tindakan->id,
            'kategori' => 'nullable|string|max:100',
            'is_active'=> 'boolean',
        ]);
        $tindakan->update($request->only('nama','kategori','is_active'));
        return back()->with('success', 'Tindakan berhasil diperbarui.');
    }

    // ── TOGGLE ACTIVE ────────────────────────────────────────
    public function toggleDiagnosa(Diagnosa $diagnosa)
    {
        $diagnosa->update(['is_active' => !$diagnosa->is_active]);
        return back()->with('success', 'Status diagnosa diperbarui.');
    }

    public function toggleKeluhan(KeluhanMaster $keluhan)
    {
        $keluhan->update(['is_active' => !$keluhan->is_active]);
        return back()->with('success', 'Status keluhan diperbarui.');
    }

    public function toggleTindakan(TindakanMaster $tindakan)
    {
        $tindakan->update(['is_active' => !$tindakan->is_active]);
        return back()->with('success', 'Status tindakan diperbarui.');
    }

    // ── DESTROY ──────────────────────────────────────────────
    public function destroyDiagnosa(Diagnosa $diagnosa)
    {
        $diagnosa->delete();
        return back()->with('success', 'Diagnosa berhasil dihapus.');
    }

    public function destroyKeluhan(KeluhanMaster $keluhan)
    {
        $keluhan->delete();
        return back()->with('success', 'Keluhan berhasil dihapus.');
    }

    public function destroyTindakan(TindakanMaster $tindakan)
    {
        $tindakan->delete();
        return back()->with('success', 'Tindakan berhasil dihapus.');
    }
}
