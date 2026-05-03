<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Kamar;
use App\Models\Kelas;
use App\Models\Santri;
use Illuminate\Http\Request;

class SantriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Santri::with(['kelas', 'jurusan']);

        if ($request->search) {
            $query->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%');
        }

        $santris = $query->latest()->paginate(10);

        return view('santri.index', compact('santris'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kelas = Kelas::all();
        $jurusans = Jurusan::all();
        $kamars = Kamar::all();
        
        return view('santri.create', compact('kelas', 'jurusans', 'kamars'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:santris,nis',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas_id' => 'required|exists:kelas,id',
            'status_santri' => 'required|in:aktif,cuti,lulus,pindah',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('santri', 'public');
        }

        $santri = Santri::create($data);
        
        // Save Guardian Data
        if ($request->nama_wali) {
            \App\Models\WaliSantri::create([
                'santri_id' => $santri->id,
                'nama_wali' => $request->nama_wali,
                'hubungan_wali' => $request->hubungan_wali,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat_wali,
            ]);
        }

        // Save Health Info
        $santri->kesehatan()->create([
            'golongan_darah' => $request->golongan_darah,
            'alergi' => $request->alergi,
            'riwayat_penyakit' => $request->riwayat_penyakit,
            'kondisi_khusus' => $request->kondisi_khusus,
            'tinggi_badan' => $request->tinggi_badan,
            'berat_badan' => $request->berat_badan,
        ]);

        return redirect()->route('santri.index')->with('success', 'Data santri berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Santri $santri)
    {
        $santri->load(['kelas', 'jurusan', 'kamar', 'waliSantris', 'kesehatan']);
        return view('santri.show', compact('santri'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Santri $santri)
    {
        $santri->load('kesehatan');
        $kelas = Kelas::all();
        $jurusans = Jurusan::all();
        $kamars = Kamar::all();
        
        return view('santri.edit', compact('santri', 'kelas', 'jurusans', 'kamars'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Santri $santri)
    {
        $request->validate([
            'nis' => 'required|unique:santris,nis,' . $santri->id,
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas_id' => 'required|exists:kelas,id',
            'status_santri' => 'required|in:aktif,cuti,lulus,pindah',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('santri', 'public');
        }

        $santri->update($data);

        // Update Guardian Data
        if ($request->nama_wali) {
            \App\Models\WaliSantri::updateOrCreate(
                ['santri_id' => $santri->id],
                [
                    'nama_wali' => $request->nama_wali,
                    'hubungan_wali' => $request->hubungan_wali,
                    'no_hp' => $request->no_hp,
                    'alamat' => $request->alamat_wali,
                ]
            );
        }

        // Update Health Info
        $santri->kesehatan()->updateOrCreate(
            ['santri_id' => $santri->id],
            [
                'golongan_darah' => $request->golongan_darah,
                'alergi' => $request->alergi,
                'riwayat_penyakit' => $request->riwayat_penyakit,
                'kondisi_khusus' => $request->kondisi_khusus,
                'tinggi_badan' => $request->tinggi_badan,
                'berat_badan' => $request->berat_badan,
            ]
        );

        return redirect()->route('santri.index')->with('success', 'Data santri berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Santri $santri)
    {
        $santri->delete();
        return redirect()->route('santri.index')->with('success', 'Data santri berhasil dihapus.');
    }

    /**
     * Show form for editing health info only.
     */
    public function editHealth(Santri $santri)
    {
        $santri->load('kesehatan');
        return view('santri.edit-health', compact('santri'));
    }

    /**
     * Update health info only.
     */
    public function updateHealth(Request $request, Santri $santri)
    {
        $santri->kesehatan()->updateOrCreate(
            ['santri_id' => $santri->id],
            [
                'golongan_darah' => $request->golongan_darah,
                'alergi' => $request->alergi,
                'riwayat_penyakit' => $request->riwayat_penyakit,
                'kondisi_khusus' => $request->kondisi_khusus,
                'tinggi_badan' => $request->tinggi_badan,
                'berat_badan' => $request->berat_badan,
                'tekanan_darah' => $request->tekanan_darah,
                'catatan_kesehatan' => $request->catatan_kesehatan,
            ]
        );

        return redirect()->route('santri.show', $santri->id)->with('success', 'Data kesehatan berhasil diperbarui.');
    }
}
