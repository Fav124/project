<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\Obat;
use App\Models\Santri;
use App\Models\User;
use App\Models\WaliSantri;

class GlobalSearchService
{
    /**
     * Run a global search across all important entities.
     * Returns a structured, paginated result.
     */
    public function search(string $query, int $limit = 5): array
    {
        $q = trim($query);

        $santris = Santri::where('nama_lengkap', 'like', "%{$q}%")
            ->orWhere('nis', 'like', "%{$q}%")
            ->orWhere('nisn', 'like', "%{$q}%")
            ->limit($limit)
            ->get(['id', 'nis', 'nama_lengkap']);

        $obats = Obat::where('nama_obat', 'like', "%{$q}%")
            ->orWhere('kode_obat', 'like', "%{$q}%")
            ->orWhere('nomor_batch', 'like', "%{$q}%")
            ->limit($limit)
            ->get(['id', 'kode_obat', 'nama_obat', 'stok']);

        $walis = WaliSantri::where('nama_wali', 'like', "%{$q}%")
            ->limit($limit)
            ->get(['id', 'santri_id', 'nama_wali', 'hubungan_wali']);

        $kelas = Kelas::where('nama_kelas', 'like', "%{$q}%")
            ->limit($limit)
            ->get(['id', 'nama_kelas']);

        $jurusans = Jurusan::where('nama_jurusan', 'like', "%{$q}%")
            ->limit($limit)
            ->get(['id', 'nama_jurusan']);

        $petugas = User::where('name', 'like', "%{$q}%")
            ->whereIn('role', ['admin', 'super_admin', 'petugas_kesehatan'])
            ->limit($limit)
            ->get(['id', 'name', 'email', 'role']);

        return [
            'query'    => $q,
            'results'  => [
                'santri'  => $santris,
                'obat'    => $obats,
                'wali'    => $walis,
                'kelas'   => $kelas,
                'jurusan' => $jurusans,
                'petugas' => $petugas,
            ],
            'totals' => [
                'santri'  => $santris->count(),
                'obat'    => $obats->count(),
                'wali'    => $walis->count(),
                'kelas'   => $kelas->count(),
                'jurusan' => $jurusans->count(),
                'petugas' => $petugas->count(),
            ],
        ];
    }
}
