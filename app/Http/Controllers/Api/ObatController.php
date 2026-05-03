<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MutasiStokRequest;
use App\Http\Requests\ObatRequest;
use App\Models\Obat;
use App\Services\ObatService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ObatController extends Controller
{
    protected ObatService $obatService;

    public function __construct(ObatService $obatService)
    {
        $this->obatService = $obatService;
    }

    public function index(Request $request): JsonResponse
    {
        $query = Obat::query();

        if ($request->search) {
            $query->where('nama_obat', 'like', "%{$request->search}%")
                  ->orWhere('kode_obat', 'like', "%{$request->search}%");
        }

        if ($request->low_stock) $query->stokMenipis();
        if ($request->expired) $query->kadaluarsa();
        if ($request->expiring_soon) $query->hampirKadaluarsa();

        $paginator = $query->paginate($request->input('per_page', 15));
        
        $mappedData = collect($paginator->items())->map(function ($obat) {
            $obat->setAppends(['status_obat']);
            return [
                'id' => $obat->id,
                'kode_obat' => $obat->kode_obat,
                'name' => $obat->nama_obat,
                'kategori' => $obat->kategori,
                'bentuk_sediaan' => $obat->bentuk_sediaan,
                'unit' => $obat->satuan,
                'stock' => $obat->stok,
                'minimum_stock' => $obat->stok_minimum,
                'expiry_date' => $obat->tanggal_kadaluarsa ? $obat->tanggal_kadaluarsa->toDateString() : null,
                'lokasi_penyimpanan' => $obat->lokasi_penyimpanan,
                'description' => $obat->deskripsi,
                'status' => $obat->status_obat,
            ];
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

    public function store(ObatRequest $request): JsonResponse
    {
        $obat = Obat::create($request->validated());

        if ($obat->stok > 0) {
            try {
                $obat->riwayatStok()->create([
                    'jenis_mutasi' => 'masuk',
                    'jumlah' => $obat->stok,
                    'stok_sebelum' => 0,
                    'stok_sesudah' => $obat->stok,
                    'catatan' => 'Stok awal saat pendaftaran obat',
                    'user_id' => auth()->id(),
                ]);
            } catch (Exception $e) {}
        }

        return response()->json([
            'success' => true,
            'message' => 'Obat berhasil didaftarkan.',
            'data' => $obat
        ], 201);
    }

    public function show(Obat $obat): JsonResponse
    {
        $obat->load(['riwayatStok' => function ($query) {
            $query->latest()->limit(10);
        }]);
        $obat->setAppends(['status_obat']);

        $data = [
            'id' => $obat->id,
            'kode_obat' => $obat->kode_obat,
            'name' => $obat->nama_obat,
            'kategori' => $obat->kategori,
            'bentuk_sediaan' => $obat->bentuk_sediaan,
            'unit' => $obat->satuan,
            'stock' => $obat->stok,
            'minimum_stock' => $obat->stok_minimum,
            'expiry_date' => $obat->tanggal_kadaluarsa ? $obat->tanggal_kadaluarsa->toDateString() : null,
            'lokasi_penyimpanan' => $obat->lokasi_penyimpanan,
            'description' => $obat->deskripsi,
            'status' => $obat->status_obat,
            'riwayat_stok' => $obat->riwayatStok->map(function($r) {
                return [
                    'id' => $r->id,
                    'type' => $r->jenis_mutasi,
                    'amount' => $r->jumlah,
                    'date' => $r->created_at->format('Y-m-d H:i'),
                    'notes' => $r->catatan,
                ];
            })
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function update(ObatRequest $request, Obat $obat): JsonResponse
    {
        $obat->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data obat berhasil diperbarui.',
            'data' => $obat
        ]);
    }

    public function destroy(Obat $obat): JsonResponse
    {
        $obat->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data obat berhasil dihapus.'
        ]);
    }

    public function mutasi(MutasiStokRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $riwayat = $this->obatService->mutasiStok(
                $validated['obat_id'],
                $validated['jenis_mutasi'],
                $validated['jumlah'],
                $validated['catatan'] ?? null,
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Mutasi stok berhasil dicatat.',
                'data' => $riwayat
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan mutasi stok.',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
