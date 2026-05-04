<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index()
    {
        $jurusans = Jurusan::withCount('santris')->get();
        return view('master.jurusan.index', compact('jurusans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jurusan' => 'required|string|unique:jurusans',
            'warna' => 'nullable|string|max:7',
        ]);
        Jurusan::create($request->all());
        return back()->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function update(Request $request, Jurusan $jurusan)
    {
        $request->validate([
            'nama_jurusan' => 'required|string|unique:jurusans,nama_jurusan,' . $jurusan->id,
            'warna' => 'nullable|string|max:7',
        ]);
        $jurusan->update($request->all());
        return back()->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy(Jurusan $jurusan)
    {
        $jurusan->delete();
        return back()->with('success', 'Jurusan berhasil dihapus.');
    }
}
