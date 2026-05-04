<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\KasusSakit;
use App\Services\MedicalCaseService;
use Illuminate\Http\Request;

class RawatInapController extends Controller
{
    protected $medicalService;

    public function __construct(MedicalCaseService $medicalService)
    {
        $this->medicalService = $medicalService;
    }

    public function index()
    {
        $activeCases = KasusSakit::with(['santri.kelas', 'santri.waliSantris', 'riwayatAktif.petugas'])
            ->whereIn('status_kasus', ['aktif', 'pulang'])
            ->orderByDesc('tanggal_mulai')
            ->get();

        $availableBeds = \App\Models\Kasur::where('status', 'tersedia')->get();

        return view('rawat-inap.index', compact('activeCases', 'availableBeds'));
    }

    /**
     * Transition student to a new location.
     */
    public function pindah(Request $request, $id)
    {
        $request->validate([
            'lokasi' => 'required|in:uks,rumah_sakit,rumah,pondok',
        ]);

        try {
            $this->medicalService->transition($id, [
                'lokasi' => $request->lokasi,
                'nama_rs' => $request->nama_rs,
                'info_rs' => $request->info_rs,
                'penjemput' => $request->penjemput,
                'hubungan_penjemput' => $request->hubungan_penjemput,
                'kontak_penjemput' => $request->kontak_penjemput,
                'kondisi_keluar' => $request->kondisi_terakhir,
                'kasur_id' => $request->kasur_id,
                'alasan_pindah' => $request->alasan_pindah,
                'catatan' => $request->catatan,
            ]);

            return redirect()->route('rawat-inap.index')->with('success', 'Lokasi perawatan santri berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memindahkan santri: ' . $e->getMessage());
        }
    }

    /**
     * Finish medical case (Healthy).
     */
    public function selesai(Request $request, $id)
    {
        try {
            $this->medicalService->transition($id, [
                'status_kasus' => 'sembuh',
                'kondisi_keluar' => $request->kondisi_keluar ?: 'Sudah Sehat',
                'catatan' => $request->catatan_keluar,
            ]);

            return redirect()->route('rawat-inap.index')->with('success', 'Santri dinyatakan sehat dan kasus ditutup.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyelesaikan perawatan: ' . $e->getMessage());
        }
    }
    /**
     * Update active treatment history.
     */
    public function updateRiwayat(Request $request, $id)
    {
        $case = KasusSakit::findOrFail($id);
        $riwayat = $case->riwayatAktif;

        try {
            $this->medicalService->updateHistory($riwayat->id, [
                'kondisi_masuk' => $request->kondisi_terakhir,
                'catatan' => $request->catatan,
            ]);

            return redirect()->route('rawat-inap.index')->with('success', 'Data perawatan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }
}
