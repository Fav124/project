<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::withCount('santris')->get();
        return view('master.kelas.index', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama_kelas' => 'required|string|unique:kelas']);
        Kelas::create($request->all());
        return back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(Kelas $kela)
    {
        $kela->load(['santris.jurusan']);
        
        // Grouping santri by jurusan for display
        $groupedSantri = $kela->santris->groupBy(function($s) {
            return $s->jurusan->nama_jurusan ?? 'Tanpa Jurusan';
        });

        return view('master.kelas.show', compact('kela', 'groupedSantri'));
    }

    public function destroy(Kelas $kela)
    {
        $kela->delete();
        return back()->with('success', 'Kelas berhasil dihapus.');
    }
}
