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
            'santri_id' => 'required|exists:santris,id',
            'complaint' => 'required|string',
            'diagnosis' => 'nullable|string',
            'action_taken' => 'nullable|string',
            'status' => 'required|in:observed,handled,recovered,referred,rawat_inap,sembuh,dirujuk',
            'notes' => 'nullable|string',
            'medicines' => 'nullable|array',
            'medicines.*.id' => 'required|exists:obats,id',
            'medicines.*.quantity' => 'required|integer|min:1',
            'notify_guardian' => 'nullable|boolean',
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
                
                $kunjungan = Kunjungan::create([
                    'santri_id' => $request->santri_id,
                    'user_id' => auth()->id(),
                    'tanggal_kunjungan' => now(),
                    'keluhan_utama' => $request->complaint,
                    'diagnosa_sementara' => $request->diagnosis,
                    'tindakan' => $request->action_taken,
                    'status_kunjungan' => $statusKunjungan,
                    'catatan' => $request->notes,
                ]);

                // Handle Medicines
                if ($request->has('medicines')) {
                    foreach ($request->medicines as $med) {
                        $this->obatService->distribute($med['id'], [
                            'santri_id' => $request->santri_id,
                            'jumlah' => $med['quantity'],
                            'kunjungan_id' => $kunjungan->id,
                            'keterangan' => 'Pemberian dari kunjungan/pemeriksaan',
                        ]);
                    }
                }

                // Handle Monitoring (KasusSakit)
                if (in_array($statusKunjungan, ['rawat_inap', 'dirujuk', 'observasi'])) {
                    $this->medicalService->startCase([
                        'santri_id' => $request->santri_id,
                        'kunjungan_id' => $kunjungan->id,
                        'diagnosa' => $request->diagnosis,
                        'lokasi' => $statusKunjungan === 'dirujuk' ? 'rumah_sakit' : 'uks',
                        'kasur_id' => $request->infirmary_bed_id,
                        'kondisi' => 'Baru Datang',
                        'catatan' => $request->notes,
                    ]);
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

        return response()->json([
            'success' => true,
            'data' => [
                'santris' => $santris,
                'medicines' => $obats,
                'beds' => $beds,
            ]
        ]);
    }
}
