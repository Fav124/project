<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use App\Models\Obat;
use App\Models\Santri;
use Illuminate\Http\Request;

class KunjunganController extends Controller
{
    protected \App\Services\ObatService $obatService;
    protected \App\Services\MedicalCaseService $medicalService;

    public function __construct(\App\Services\ObatService $obatService, \App\Services\MedicalCaseService $medicalService)
    {
        $this->obatService = $obatService;
        $this->medicalService = $medicalService;
    }

    public function index(Request $request)
    {
        $kunjungans = Kunjungan::with(['santri.kelas', 'petugas', 'rawatInap', 'kasusSakit.riwayatAktif'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('santri', function ($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('tanggal_kunjungan')
            ->paginate(15);

        return view('kunjungan.index', compact('kunjungans'));
    }

    public function create()
    {
        $santris = Santri::where('status_santri', 'aktif')->get();
        // Show all medicines that are not expired and have stock > 0
        $obats = Obat::whereDate('tanggal_kadaluarsa', '>=', now())
            ->where('stok', '>', 0)
            ->get();
        $kasurs = \App\Models\Kasur::where('status', 'tersedia')->get();
        return view('kunjungan.create', compact('santris', 'obats', 'kasurs'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Dasar
        $request->validate([
            'santri_id' => 'required|exists:santris,id',
            'keluhan' => 'required|string',
            'tindak_lanjut' => 'required|in:kembali_kamar,rawat_inap,rujuk_rs,pulang',
        ]);

        // 2. Cek Double Entry (Cek jika santri sedang rawat inap aktif)
        $activeInpatient = \App\Models\RawatInap::where('santri_id', $request->santri_id)
            ->where('status_rawat', 'aktif')
            ->exists();

        if ($activeInpatient) {
            return back()->with('error', 'Santri tersebut masih dalam status Rawat Inap aktif. Selesaikan rawat inap sebelumnya terlebih dahulu.')->withInput();
        }

        try {
            \DB::transaction(function() use ($request) {
                // 3. Simpan Kunjungan
                $statusKunjungan = 'sembuh'; // Default: Selesai & Kembali ke kamar
                if ($request->tindak_lanjut === 'rawat_inap') $statusKunjungan = 'rawat_inap';
                if ($request->tindak_lanjut === 'rujuk_rs') $statusKunjungan = 'dirujuk';
                if ($request->tindak_lanjut === 'pulang') $statusKunjungan = 'sembuh';

                $kunjungan = Kunjungan::create([
                    'santri_id' => $request->santri_id,
                    'user_id' => auth()->id(),
                    'tanggal_kunjungan' => now(),
                    'keluhan_utama' => $request->keluhan,
                    'riwayat_keluhan' => $request->anamnesis,
                    'diagnosa_sementara' => $request->diagnosa_sementara,
                    'tindakan' => $request->tindakan,
                    'status_kunjungan' => $statusKunjungan,
                    'catatan' => $request->catatan,
                ]);

                // 4. Simpan Resep & Kurangi Stok (Jika ada)
                if ($request->obats) {
                    foreach ($request->obats as $obatData) {
                        if (!empty($obatData['id']) && !empty($obatData['jumlah'])) {
                            // Cek stok tersedia sebelum mutasi
                            $obat = Obat::findOrFail($obatData['id']);
                            if ($obat->stok < $obatData['jumlah']) {
                                throw new \Exception("Stok obat '{$obat->nama_obat}' tidak mencukupi (Tersedia: {$obat->stok}).");
                            }
                            
                            // Gunakan service mutasi stok
                            $this->obatService->mutasiStok(
                                $obatData['id'], 
                                'keluar', 
                                $obatData['jumlah'], 
                                "Pemberian obat via Kunjungan #" . $kunjungan->id,
                                auth()->id()
                            );
                            
                            \App\Models\PemberianObat::create([
                                'kunjungan_id' => $kunjungan->id,
                                'santri_id' => $request->santri_id,
                                'obat_id' => $obat->id,
                                'jumlah' => $obatData['jumlah'],
                                'aturan_pakai' => $obatData['aturan'],
                                'diberikan_oleh' => auth()->id(),
                            ]);
                        }
                    }
                }

                // 5. Handle Rawat Inap / Izin Luar (Workflow Kasus Sakit)
                if (in_array($request->tindak_lanjut, ['rawat_inap', 'rujuk_rs', 'pulang'])) {
                    $lokasiMap = [
                        'rawat_inap' => 'uks',
                        'rujuk_rs' => 'rumah_sakit',
                        'pulang' => 'rumah',
                    ];

                    $this->medicalService->startCase([
                        'santri_id' => $request->santri_id,
                        'kunjungan_id' => $kunjungan->id,
                        'lokasi' => $lokasiMap[$request->tindak_lanjut],
                        'nama_rs' => $request->nama_rs,
                        'info_rs' => $request->info_rs,
                        'penjemput' => $request->penjemput,
                        'hubungan_penjemput' => $request->hubungan_penjemput,
                        'kontak_penjemput' => $request->kontak_penjemput,
                        'kasur_id' => $request->kasur_id,
                        'diagnosa' => $kunjungan->diagnosa_sementara,
                        'kondisi' => $request->kondisi_masuk ?: $request->keluhan,
                        'catatan' => $request->alasan_rawat ?: ($request->alasan_luar ?: $request->catatan),
                    ]);
                }
            });

            return redirect()->route('kunjungan.index')->with('success', 'Data pemeriksaan berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Kunjungan $kunjungan)
    {
        $kunjungan->load(['santri.kesehatan', 'petugas', 'pemberianObats.obat', 'rawatInap']);
        return view('kunjungan.show', compact('kunjungan'));
    }
}
