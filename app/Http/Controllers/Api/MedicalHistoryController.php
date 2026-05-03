<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use Illuminate\Http\JsonResponse;

class MedicalHistoryController extends Controller
{
    /**
     * Mengambil riwayat kesehatan komprehensif seorang santri
     */
    public function show(int $santriId): JsonResponse
    {
        // Pastikan santri ada
        $santri = Santri::findOrFail($santriId);

        // Ambil semua kunjungan santri ini beserta relasi detail medis
        $riwayatKunjungan = $santri->kunjungans()
            ->with(['petugas:id,name', 'pemberianObat.obat', 'rawatInap'])
            ->orderByDesc('tanggal_kunjungan')
            ->get();

        // Di dunia nyata, ini juga bisa digabung dengan data dari KesehatanSantri (alergi, dll)
        $dataKesehatanUtama = $santri->kesehatanSantri;

        return response()->json([
            'santri' => [
                'id' => $santri->id,
                'nama' => $santri->nama_lengkap,
                'nis' => $santri->nis,
            ],
            'data_kesehatan_utama' => $dataKesehatanUtama,
            'riwayat_kunjungan' => $riwayatKunjungan
        ]);
    }
}
