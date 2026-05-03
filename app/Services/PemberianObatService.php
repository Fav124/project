<?php

namespace App\Services;

use App\Models\Kunjungan;
use App\Models\Obat;
use App\Models\PemberianObat;
use Exception;
use Illuminate\Support\Facades\DB;

class PemberianObatService
{
    protected ObatService $obatService;

    public function __construct(ObatService $obatService)
    {
        $this->obatService = $obatService;
    }

    /**
     * @param array $data
     * @param int|null $userId
     * @return array
     * @throws Exception
     */
    public function berikanObat(array $data, ?int $userId): array
    {
        return DB::transaction(function () use ($data, $userId) {
            $kunjungan = Kunjungan::findOrFail($data['kunjungan_id']);
            $obat = Obat::lockForUpdate()->findOrFail($data['obat_id']);

            // 1. Validasi Kadaluarsa
            if ($obat->status_obat === 'kadaluarsa') {
                throw new Exception("Obat sudah kadaluarsa dan tidak boleh diberikan.");
            }

            // 2. Validasi Stok
            if ($obat->stok < $data['jumlah']) {
                throw new Exception("Stok obat tidak mencukupi. Sisa stok: {$obat->stok}");
            }

            // 3. Mutasi Stok via ObatService
            $catatanMutasi = "Diberikan kepada santri ID {$kunjungan->santri_id} pada kunjungan ID {$kunjungan->id}. " . ($data['catatan'] ?? '');
            
            $this->obatService->mutasiStok(
                $obat->id,
                'keluar',
                $data['jumlah'],
                $catatanMutasi,
                $userId
            );

            // 4. Catat Pemberian Obat
            $pemberian = PemberianObat::create([
                'kunjungan_id' => $kunjungan->id,
                'santri_id' => $kunjungan->santri_id,
                'obat_id' => $obat->id,
                'jumlah' => $data['jumlah'],
                'dosis' => $data['dosis'],
                'aturan_pakai' => $data['aturan_pakai'],
                'catatan' => $data['catatan'] ?? null,
                'diberikan_oleh' => $userId,
            ]);

            // 5. Cek Warning Hampir Kadaluarsa
            $warning = null;
            if ($obat->status_obat === 'hampir_kadaluarsa') {
                $warning = "Peringatan: Obat yang diberikan mendekati tanggal kadaluarsa ({$obat->tanggal_kadaluarsa->format('Y-m-d')}).";
            }

            return [
                'pemberian' => $pemberian,
                'warning' => $warning
            ];
        });
    }
}
