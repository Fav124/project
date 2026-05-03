<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Kunjungan::with(['santri.kelas'])
            ->where('status_kunjungan', 'dirujuk')
            ->when($request->search, function ($query, $search) {
                $query->whereHas('santri', function ($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%");
                })->orWhere('nama_rs', 'like', "%{$search}%");
            })
            ->latest('tanggal_rujukan');

        $paginator = $query->paginate($request->input('per_page', 15));

        $mappedData = collect($paginator->items())->map(function ($referral) {
            return $this->mapReferralForMobile($referral);
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

    private function mapReferralForMobile($referral)
    {
        return [
            'id' => $referral->id,
            'santri' => [
                'id' => $referral->santri->id,
                'name' => $referral->santri->nama_lengkap,
                'nis' => $referral->santri->nis,
                'class' => $referral->santri->kelas?->nama_kelas,
            ],
            'hospital_name' => $referral->nama_rs,
            'referral_date' => $referral->tanggal_rujukan ? $referral->tanggal_rujukan->format('Y-m-d H:i') : null,
            'complaint' => $referral->keluhan_utama,
            'diagnosis' => $referral->diagnosa_sementara,
            'transport' => $referral->transportasi,
            'companion_name' => $referral->nama_pendamping,
            'status' => 'referred',
            'status_label' => 'Dirujuk',
            'notes' => $referral->catatan,
            'referred_by' => $referral->petugas?->name,
        ];
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'santri_id' => 'required|exists:santris,id',
            'hospital_name' => 'required|string|max:255',
            'referral_date' => 'required|date',
            'complaint' => 'required|string',
            'diagnosis' => 'nullable|string',
            'transport' => 'nullable|string',
            'companion_name' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $referral = Kunjungan::create([
            'santri_id' => $request->santri_id,
            'user_id' => auth()->id(),
            'tanggal_kunjungan' => now(),
            'keluhan_utama' => $request->complaint,
            'diagnosa_sementara' => $request->diagnosis,
            'status_kunjungan' => 'dirujuk',
            'nama_rs' => $request->hospital_name,
            'transportasi' => $request->transport,
            'nama_pendamping' => $request->companion_name,
            'tanggal_rujukan' => $request->referral_date,
            'catatan' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rujukan berhasil dibuat.',
            'data' => $this->mapReferralForMobile($referral)
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $referral = Kunjungan::with(['santri.kelas', 'petugas'])
            ->where('id', $id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $this->mapReferralForMobile($referral)
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $referral = Kunjungan::findOrFail($id);
        
        $referral->update([
            'nama_rs' => $request->hospital_name ?? $referral->nama_rs,
            'transportasi' => $request->transport ?? $referral->transportasi,
            'nama_pendamping' => $request->companion_name ?? $referral->nama_pendamping,
            'tanggal_rujukan' => $request->referral_date ?? $referral->tanggal_rujukan,
            'catatan' => $request->notes ?? $referral->catatan,
            'keluhan_utama' => $request->complaint ?? $referral->keluhan_utama,
            'diagnosa_sementara' => $request->diagnosis ?? $referral->diagnosa_sementara,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data rujukan berhasil diperbarui.',
            'data' => $this->mapReferralForMobile($referral)
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $referral = Kunjungan::findOrFail($id);
        $referral->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data rujukan berhasil dihapus.'
        ]);
    }

    public function notifyGuardian($id): JsonResponse
    {
        // Placeholder for notification logic
        return response()->json([
            'success' => true,
            'message' => 'Notifikasi rujukan telah dikirim ke Wali Santri.'
        ]);
    }
}
