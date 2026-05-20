<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KunjunganRequest;
use App\Models\Kasur;
use App\Models\Kunjungan;
use App\Models\Obat;
use App\Models\RawatInap;
use App\Models\Santri;
use App\Services\MedicalCaseService;
use App\Services\ObatService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KunjunganController extends Controller
{
    protected ObatService $obatService;
    protected MedicalCaseService $medicalService;

    public function __construct(ObatService $obatService, MedicalCaseService $medicalService)
    {
        $this->obatService = $obatService;
        $this->medicalService = $medicalService;
    }

    public function index(Request $request): JsonResponse
    {
        $query = Kunjungan::with(['santri.kelas', 'petugas'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('santri', function ($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status_kunjungan', $status);
            })
            ->when($request->date, function ($query, $date) {
                $query->whereDate('tanggal_kunjungan', $date);
            })
            ->orderByDesc('tanggal_kunjungan');

        $paginator = $query->paginate($request->input('per_page', 15));

        $mappedData = collect($paginator->items())->map(function ($k) {
            return $this->mapKunjunganForMobile($k);
        });

        return response()->json([
            'success' => true,
            'data' => $mappedData,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ]
        ]);
    }

    private function mapKunjunganForMobile($k)
    {
        return [
            'id' => $k->id,
            'santri' => [
                'id' => $k->santri->id,
                'name' => $k->santri->nama_lengkap,
                'nis' => $k->santri->nis,
                'class' => $k->santri->kelas?->nama_kelas,
            ],
            'complaint' => $k->keluhan_utama,
            'diagnosis' => $k->diagnosa_sementara,
            'action_taken' => $k->tindakan,
            'notes' => $k->catatan,
            'status' => $k->status_kunjungan,
            'status_label' => $this->getStatusLabel($k->status_kunjungan),
            'visit_date' => $k->tanggal_kunjungan->format('Y-m-d H:i'),
            'handled_by' => $k->petugas?->name,
            'photo_url' => $k->foto ? url($k->foto) : null,
            'medicines' => $k->pemberianObats->map(function($p) {
                return [
                    'id' => $p->obat_id,
                    'name' => $p->obat->nama_obat,
                    'quantity' => $p->jumlah,
                    'unit' => $p->obat->satuan,
                ];
            })
        ];
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'sembuh' => 'Sembuh',
            'rawat_inap' => 'Rawat Inap',
            'dirujuk' => 'Dirujuk',
            'observasi' => 'Observasi',
        ];
        return $labels[$status] ?? ucfirst($status);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'santri_ids' => 'required|array|min:1',
            'santri_ids.*' => 'exists:santris,id',
            'complaint' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'action_taken' => 'nullable|string',
            'diagnosa_ids' => 'nullable|array',
            'keluhan_ids' => 'nullable|array',
            'tindakan_ids' => 'nullable|array',
            'status' => 'required|in:observed,handled,recovered,referred,rawat_inap,sembuh,dirujuk',
            'notes' => 'nullable|string',
            'medicines' => 'nullable|array',
            'medicines.*.id' => 'required|exists:obats,id',
            'medicines.*.quantity' => 'required|integer|min:1',
            'notify_guardian' => 'nullable|boolean',
            'photo_base64' => 'nullable|string',
        ]);

        try {
            $result = DB::transaction(function () use ($request) {
                // Map status from mobile to DB status
                $statusMap = [
                    'observed' => 'observasi',
                    'handled' => 'sembuh',
                    'recovered' => 'sembuh',
                    'referred' => 'dirujuk',
                    'rawat_inap' => 'rawat_inap',
                    'sembuh' => 'sembuh',
                    'dirujuk' => 'dirujuk',
                ];
                $statusKunjungan = $statusMap[$request->status] ?? 'sembuh';
                
                $diagnosaIds  = array_filter((array) $request->input('diagnosa_ids', []));
                $keluhanIds   = array_filter((array) $request->input('keluhan_ids', []));
                $tindakanIds  = array_filter((array) $request->input('tindakan_ids', []));

                $diagnosaNames = \App\Models\Diagnosa::whereIn('id', $diagnosaIds)->pluck('nama')->join(', ');
                $keluhanNames  = \App\Models\KeluhanMaster::whereIn('id', $keluhanIds)->pluck('nama')->join(', ');
                $tindakanNames = \App\Models\TindakanMaster::whereIn('id', $tindakanIds)->pluck('nama')->join(', ');

                $keluhanUtama = $keluhanNames;
                if ($request->complaint) {
                    $keluhanUtama = $keluhanUtama ? $keluhanUtama . ' - ' . $request->complaint : $request->complaint;
                }

                $tindakanSummary = $tindakanNames;
                if ($request->action_taken) {
                    $tindakanSummary = $tindakanSummary ? $tindakanSummary . ' - ' . $request->action_taken : $request->action_taken;
                }

                $fotoPath = null;
                if ($request->photo_base64) {
                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->photo_base64));
                    $imageName = 'kunjungan_' . time() . '_' . uniqid() . '.jpg';
                    \Illuminate\Support\Facades\Storage::disk('public')->put('kunjungan_photos/' . $imageName, $imageData);
                    $fotoPath = 'storage/kunjungan_photos/' . $imageName;
                }

                foreach ($request->santri_ids as $santri_id) {
                    $kunjungan = Kunjungan::create([
                        'santri_id' => $santri_id,
                        'user_id' => auth()->id(),
                        'tanggal_kunjungan' => now(),
                        'keluhan_utama' => $keluhanUtama ?: 'Tidak ada keluhan spesifik',
                        'diagnosa_sementara' => $diagnosaNames ?: $request->diagnosis,
                        'tindakan' => $tindakanSummary,
                        'status_kunjungan' => $statusKunjungan,
                        'catatan' => $request->notes,
                        'foto' => $fotoPath,
                    ]);

                    // Sync tags
                    if (!empty($diagnosaIds)) $kunjungan->diagnosas()->sync($diagnosaIds);
                    if (!empty($keluhanIds)) $kunjungan->keluhanMasters()->sync($keluhanIds);
                    if (!empty($tindakanIds)) $kunjungan->tindakanMasters()->sync($tindakanIds);

                    // Add medicines
                    if ($request->medicines) {
                        foreach ($request->medicines as $med) {
                            \App\Models\PemberianObat::create([
                                'kunjungan_id' => $kunjungan->id,
                                'obat_id' => $med['id'],
                                'jumlah' => $med['quantity'],
                                'aturan_pakai' => 'Sesuai anjuran dokter',
                                'diberikan_oleh' => auth()->id(),
                                'waktu_pemberian' => now(),
                            ]);
                            
                            $obat = \App\Models\Obat::find($med['id']);
                            if ($obat) {
                                $obat->decrement('stok', $med['quantity']);
                            }
                        }
                    }

                    // Create KasusSakit if needed
                    if (in_array($statusKunjungan, ['observasi', 'rawat_inap', 'dirujuk'])) {
                        \App\Models\KasusSakit::create([
                            'santri_id' => $santri_id,
                            'kunjungan_id' => $kunjungan->id,
                            'status' => $statusKunjungan,
                        ]);
                    }

                    if ($request->notify_guardian) {
                        // In a real app, this would queue a WhatsApp notification job
                    }
                }

                return $kunjungan;
            });

            return response()->json([
                'success' => true,
                'message' => 'Data pemeriksaan berhasil disimpan.',
                'data' => $this->mapKunjunganForMobile($result->load(['santri', 'petugas', 'pemberianObats.obat']))
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data pemeriksaan.',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function show(Kunjungan $kunjungan): JsonResponse
    {
        $kunjungan->load(['santri.kelas', 'petugas', 'pemberianObats.obat']);
        return response()->json([
            'success' => true,
            'data' => $this->mapKunjunganForMobile($kunjungan)
        ]);
    }

    public function update(Request $request, Kunjungan $kunjungan): JsonResponse
    {
        $kunjungan->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Data kunjungan berhasil diperbarui.',
            'data' => $this->mapKunjunganForMobile($kunjungan->load(['santri', 'petugas', 'pemberianObats.obat']))
        ]);
    }

    public function destroy(Kunjungan $kunjungan): JsonResponse
    {
        $kunjungan->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data kunjungan berhasil dihapus.'
        ]);
    }

    public function formData(): JsonResponse
    {
        $santris = Santri::where('status_santri', 'aktif')->select('id', 'nis', 'nama_lengkap')->get()->map(function($s) {
            return ['id' => $s->id, 'name' => $s->nama_lengkap, 'nis' => $s->nis];
        });
        
        // Show all medicines that are not expired and have stock > 0
        $obats = Obat::whereDate('tanggal_kadaluarsa', '>=', now())
            ->where('stok', '>', 0)
            ->get()
            ->map(function($o) {
                return ['id' => $o->id, 'name' => $o->nama_obat, 'unit' => $o->satuan, 'stock' => $o->stok];
            });

        $beds = Kasur::where('status', 'tersedia')->get()->map(function($b) {
            return ['id' => $b->id, 'code' => $b->kode_kasur, 'status' => $b->status];
        });

        $diagnosas = \App\Models\Diagnosa::where('status', 'aktif')->orderBy('nama')->get()->map(function($d) {
            return ['id' => $d->id, 'name' => $d->nama];
        });
        $keluhans = \App\Models\KeluhanMaster::where('status', 'aktif')->orderBy('nama')->get()->map(function($k) {
            return ['id' => $k->id, 'name' => $k->nama];
        });
        $tindakans = \App\Models\TindakanMaster::where('status', 'aktif')->orderBy('nama')->get()->map(function($t) {
            return ['id' => $t->id, 'name' => $t->nama];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'santris' => $santris,
                'medicines' => $obats,
                'beds' => $beds,
                'diagnoses' => $diagnosas,
                'keluhans' => $keluhans,
                'tindakans' => $tindakans,
            ]
        ]);
    }

    public function notifyGuardian($id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Notifikasi perkembangan kesehatan telah dikirim ke Wali Santri.'
        ]);
    }
}
