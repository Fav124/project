<?php

namespace App\Services;

use App\Models\RawatInap;
use Exception;
use Illuminate\Support\Facades\DB;

class RawatInapService
{
    /**
     * Daftarkan santri ke rawat inap
     */
    public function masukRawatInap(array $data): RawatInap
    {
        return DB::transaction(function () use ($data) {

            // Catat Rawat Inap
            $data['status_rawat'] = 'aktif';
            $rawatInap = RawatInap::create($data);

            return $rawatInap;
        });
    }

    /**
     * Selesaikan rawat inap (pulang/sembuh, pindah, dirujuk)
     */
    public function keluarRawatInap(int $rawatInapId, array $data): RawatInap
    {
        return DB::transaction(function () use ($rawatInapId, $data) {
            $rawatInap = RawatInap::lockForUpdate()->findOrFail($rawatInapId);

            if ($rawatInap->status_rawat !== 'aktif') {
                throw new Exception("Data rawat inap ini sudah selesai atau tidak aktif.");
            }

            // Update data rawat inap
            $rawatInap->update([
                'tanggal_keluar' => $data['tanggal_keluar'] ?? now(),
                'kondisi_keluar' => $data['kondisi_keluar'] ?? 'Tidak ada keterangan',
                'status_rawat' => $data['status_rawat'], // misal: selesai, dirujuk, pindah
            ]);



            return $rawatInap;
        });
    }
}
