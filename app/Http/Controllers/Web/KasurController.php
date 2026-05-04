<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Kasur;
use Illuminate\Http\Request;

class KasurController extends Controller
{
    public function index()
    {
        $kasurs = Kasur::withCount('riwayats')->get();
        return view('master.kasur.index', compact('kasurs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_kasur' => 'required|string|unique:kasurs',
            'status' => 'required|in:tersedia,terisi,rusak',
        ]);
        Kasur::create($request->all());
        return back()->with('success', 'Data kasur berhasil ditambahkan.');
    }

    public function update(Request $request, Kasur $kasur)
    {
        $request->validate([
            'kode_kasur' => 'required|string|unique:kasurs,kode_kasur,' . $kasur->id,
            'status' => 'required|in:tersedia,terisi,rusak',
        ]);
        $kasur->update($request->all());
        return back()->with('success', 'Data kasur berhasil diperbarui.');
    }

    public function destroy(Kasur $kasur)
    {
        // Don't delete if it's currently occupied
        if ($kasur->status === 'terisi') {
            return back()->with('error', 'Tidak dapat menghapus kasur yang sedang terisi.');
        }
        $kasur->delete();
        return back()->with('success', 'Data kasur berhasil dihapus.');
    }
}
