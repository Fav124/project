<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use Illuminate\Http\Request;

class KamarController extends Controller
{
    public function index()
    {
        $kamars = Kamar::all();
        return view('master.kamar.index', compact('kamars'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama_kamar' => 'required|string']);
        Kamar::create($request->all());
        return back()->with('success', 'Kamar berhasil ditambahkan.');
    }

    public function destroy(Kamar $kamar)
    {
        $kamar->delete();
        return back()->with('success', 'Kamar berhasil dihapus.');
    }
}
