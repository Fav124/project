<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use App\Services\LaporanService;
use Illuminate\Http\Request;

class ObatController extends Controller
{
    protected LaporanService $laporanService;

    public function __construct(LaporanService $laporanService)
    {
        $this->laporanService = $laporanService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Obat::query();

        if ($request->search) {
            $query->where('nama_obat', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_obat', 'like', '%' . $request->search . '%');
        }

        if ($request->filter) {
            switch ($request->filter) {
                case 'stok_menipis': $query->stokMenipis(); break;
                case 'kadaluarsa': $query->kadaluarsa(); break;
                case 'hampir_kadaluarsa': $query->hampirKadaluarsa(); break;
            }
        }

        $obats = $query->latest()->paginate(10);
        $stats = $this->laporanService->statusStokObat();

        return view('obat.index', compact('obats', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('obat.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_obat' => 'required|unique:obats,kode_obat',
            'nama_obat' => 'required|string|max:255',
            'kategori' => 'required|string',
            'golongan' => 'required|string',
            'bentuk_sediaan' => 'required|string',
            'satuan' => 'required|string',
            'stok' => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'tanggal_kadaluarsa' => 'required|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('obat', 'public');
        }

        $obat = Obat::create($data);

        return redirect()->route('obat.index')->with('success', 'Data obat berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Obat $obat)
    {
        $obat->load(['riwayatStok' => function($q) {
            $q->with('user')->latest()->limit(15);
        }]);
        return view('obat.show', compact('obat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Obat $obat)
    {
        return view('obat.edit', compact('obat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Obat $obat)
    {
        $request->validate([
            'kode_obat' => 'required|unique:obats,kode_obat,' . $obat->id,
            'nama_obat' => 'required|string|max:255',
            'kategori' => 'required|string',
            'golongan' => 'required|string',
            'bentuk_sediaan' => 'required|string',
            'satuan' => 'required|string',
            'stok_minimum' => 'required|integer|min:0',
            'tanggal_kadaluarsa' => 'required|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($obat->foto && \Storage::disk('public')->exists($obat->foto)) {
                \Storage::disk('public')->delete($obat->foto);
            }
            $data['foto'] = $request->file('foto')->store('obat', 'public');
        }

        $obat->update($data);

        return redirect()->route('obat.index')->with('success', 'Data obat berhasil diperbarui.');
    }

    /**
     * Update stock and log mutation.
     */
    public function updateStok(Request $request, Obat $obat)
    {
        $request->validate([
            'jenis_mutasi' => 'required|in:masuk,keluar,penyesuaian',
            'jumlah' => 'required|integer|min:1',
            'catatan' => 'nullable|string|max:255',
        ]);

        $stokSebelum = $obat->stok;
        $jumlah = $request->jumlah;

        if ($request->jenis_mutasi === 'masuk') {
            $stokSesudah = $stokSebelum + $jumlah;
        } elseif ($request->jenis_mutasi === 'keluar') {
            if ($stokSebelum < $jumlah) {
                return back()->with('error', 'Stok tidak mencukupi untuk mutasi keluar.');
            }
            $stokSesudah = $stokSebelum - $jumlah;
        } else {
            // Penyesuaian biasanya set stok langsung, tapi di sini kita anggap delta
            // Jika mau set stok langsung, logika perlu diubah. Kita pakai delta aja.
            $stokSesudah = $stokSebelum + $jumlah; // Default adjustment adds
        }

        $obat->update(['stok' => $stokSesudah]);

        $obat->riwayatStok()->create([
            'jenis_mutasi' => $request->jenis_mutasi,
            'jumlah' => $jumlah,
            'stok_sebelum' => $stokSebelum,
            'stok_sesudah' => $stokSesudah,
            'catatan' => $request->catatan,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Stok obat berhasil dimutasi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Obat $obat)
    {
        if ($obat->foto && \Storage::disk('public')->exists($obat->foto)) {
            \Storage::disk('public')->delete($obat->foto);
        }
        $obat->delete();
        return redirect()->route('obat.index')->with('success', 'Data obat berhasil dihapus.');
    }
}
