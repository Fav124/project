<?php

namespace App\Services;

use App\Models\Obat;
use App\Models\RiwayatStokObat;
use Illuminate\Support\Facades\DB;
use Exception;

class ObatService
{
    /**
     * Lakukan mutasi stok obat dan catat riwayatnya.
     *
     * @param int $obatId
     * @param string $jenisMutasi (masuk, keluar, penyesuaian, rusak, kadaluarsa, retur)
     * @param int $jumlah (selalu positif)
     * @param string|null $catatan
     * @param int|null $userId
     * @return RiwayatStokObat
     * @throws Exception
     */
    public function mutasiStok(int $obatId, string $jenisMutasi, int $jumlah, ?string $catatan = null, ?int $userId = null): RiwayatStokObat
    {
        if ($jumlah <= 0) {
            throw new Exception("Jumlah mutasi harus lebih besar dari 0.");
        }

        return DB::transaction(function () use ($obatId, $jenisMutasi, $jumlah, $catatan, $userId) {
            // Lock row for update to prevent race conditions
            $obat = Obat::lockForUpdate()->findOrFail($obatId);
            
            $stokSebelum = $obat->stok;
            $stokSesudah = $stokSebelum;

            // Hitung stok sesudah berdasarkan jenis mutasi
            switch ($jenisMutasi) {
                case 'masuk':
                case 'retur':
                    $stokSesudah = $stokSebelum + $jumlah;
                    break;
                case 'keluar':
                case 'rusak':
                case 'kadaluarsa':
                    $stokSesudah = $stokSebelum - $jumlah;
                    break;
                case 'penyesuaian':
                    // Untuk penyesuaian, $jumlah adalah stok aktual saat opname
                    // Namun di konteks ini agar konsisten, kita asumsikan 
                    // jumlah adalah nilai selisih yang harus ditambahkan/dikurangi.
                    // Lebih aman memakai mutasi masuk/keluar untuk penambahan/pengurangan biasa.
                    // Jika memang ingin set nilai pasti, bisa diperbarui rulesnya. 
                    // Di sini kita anggap 'penyesuaian' menambah/mengurangi tergantung konteks,
                    // tapi default kita anggap sebagai 'tambah'. Untuk kemudahan, 
                    // kita asumsikan penyesuaian = selisih (bisa + atau - tapi di request 
                    // dipisah jadi masuk/keluar). Jika penyesuaian, jadikan stok_sesudah = $jumlah
                    $stokSesudah = $jumlah; 
                    // Kalkulasi ulang jumlah mutasi agar riwayatnya masuk akal
                    $jumlah = abs($stokSesudah - $stokSebelum);
                    break;
                default:
                    throw new Exception("Jenis mutasi tidak valid.");
            }

            if ($stokSesudah < 0) {
                throw new Exception("Stok tidak mencukupi untuk melakukan transaksi ini. Sisa stok: {$stokSebelum}");
            }

            // Update Stok di tabel Obat
            $obat->update(['stok' => $stokSesudah]);

            // Catat ke Riwayat Stok
            $riwayat = RiwayatStokObat::create([
                'obat_id' => $obat->id,
                'jenis_mutasi' => $jenisMutasi,
                'jumlah' => $jumlah,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'catatan' => $catatan,
                'user_id' => $userId,
            ]);

            return $riwayat;
        });
    }
}
