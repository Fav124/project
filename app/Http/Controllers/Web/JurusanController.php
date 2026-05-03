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
        $request->validate(['nama_jurusan' => 'required|string|unique:jurusans']);
        Jurusan::create($request->all());
        return back()->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function destroy(Jurusan $jurusan)
    {
        $jurusan->delete();
        return back()->with('success', 'Jurusan berhasil dihapus.');
    }
}
