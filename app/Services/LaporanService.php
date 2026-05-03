<?php

namespace App\Services;

use App\Models\Kunjungan;
use App\Models\Obat;
use App\Models\PemberianObat;
use App\Models\RawatInap;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanService
{
    /**
     * Rekap kunjungan per periode harian/bulanan
     */
    public function kunjunganPerPeriode(Carbon $start, Carbon $end, string $groupBy = 'date')
    {
        $format = $groupBy === 'month' ? '%Y-%m' : '%Y-%m-%d';

        return Kunjungan::whereBetween('tanggal_kunjungan', [$start->startOfDay(), $end->endOfDay()])
            ->select(DB::raw("DATE_FORMAT(tanggal_kunjungan, '{$format}') as periode"), DB::raw('count(*) as total'))
            ->groupBy('periode')
            ->orderBy('periode')
            ->get();
    }

    /**
     * Rekap jumlah santri unik yang sakit per periode
     */
    public function santriSakitPerPeriode(Carbon $start, Carbon $end)
    {
        return Kunjungan::whereBetween('tanggal_kunjungan', [$start->startOfDay(), $end->endOfDay()])
            ->distinct('santri_id')
            ->count('santri_id');
    }

    /**
     * Rekap obat paling sering digunakan
     */
    public function obatPalingSeringDigunakan(Carbon $start, Carbon $end, int $limit = 10)
    {
        return PemberianObat::with('obat:id,kode_obat,nama_obat,satuan')
            ->whereHas('kunjungan', function ($q) use ($start, $end) {
                $q->whereBetween('tanggal_kunjungan', [$start->startOfDay(), $end->endOfDay()]);
            })
            ->select('obat_id', DB::raw('SUM(jumlah) as total_keluar'), DB::raw('COUNT(*) as frekuensi_diberikan'))
            ->groupBy('obat_id')
            ->orderByDesc('total_keluar')
            ->limit($limit)
            ->get();
    }

    /**
     * Rekap status stok obat (memanfaatkan scope di model Obat)
     */
    public function statusStokObat()
    {
        return [
            'kadaluarsa' => Obat::kadaluarsa()->count(),
            'hampir_kadaluarsa' => Obat::hampirKadaluarsa()->count(),
            'stok_menipis' => Obat::stokMenipis()->count(),
            'stok_habis' => Obat::stokHabis()->count(),
            'aktif' => Obat::aktif()->count(),
        ];
    }

    /**
     * Detail obat bermasalah (kadaluarsa, stok habis, dll) untuk laporan lengkap
     */
    public function detailObatBermasalah(string $jenis)
    {
        $query = Obat::query();

        switch ($jenis) {
            case 'kadaluarsa': $query->kadaluarsa(); break;
            case 'hampir_kadaluarsa': $query->hampirKadaluarsa(); break;
            case 'stok_menipis': $query->stokMenipis(); break;
            case 'stok_habis': $query->stokHabis(); break;
            default: return collect();
        }

        return $query->select('id', 'kode_obat', 'nama_obat', 'stok', 'stok_minimum', 'tanggal_kadaluarsa', 'lokasi_penyimpanan')->get();
    }

    /**
     * Rekap rawat inap per periode
     */
    public function rawatInapPerPeriode(Carbon $start, Carbon $end)
    {
        return RawatInap::whereBetween('tanggal_masuk', [$start->startOfDay(), $end->endOfDay()])
            ->select('status_rawat', DB::raw('count(*) as total'))
            ->groupBy('status_rawat')
            ->get();
    }

    /**
     * Rekap kunjungan berdasarkan petugas kesehatan
     */
    public function kunjunganBerdasarkanPetugas(Carbon $start, Carbon $end)
    {
        return Kunjungan::with('petugas:id,name')
            ->whereBetween('tanggal_kunjungan', [$start->startOfDay(), $end->endOfDay()])
            ->select('user_id', DB::raw('count(*) as total_menangani'))
            ->groupBy('user_id')
            ->orderByDesc('total_menangani')
            ->get();
    }

    /**
     * Rekap kunjungan berdasarkan kelas
     */
    public function kunjunganBerdasarkanKelas(Carbon $start, Carbon $end)
    {
        return Kunjungan::whereBetween('tanggal_kunjungan', [$start->startOfDay(), $end->endOfDay()])
            ->join('santris', 'kunjungans.santri_id', '=', 'santris.id')
            ->join('kelas', 'santris.kelas_id', '=', 'kelas.id')
            ->select('kelas.id', 'kelas.nama_kelas', DB::raw('count(kunjungans.id) as total_kunjungan'))
            ->groupBy('kelas.id', 'kelas.nama_kelas')
            ->orderByDesc('total_kunjungan')
            ->get();
    }

    /**
     * Rekap kunjungan berdasarkan jurusan
     */
    public function kunjunganBerdasarkanJurusan(Carbon $start, Carbon $end)
    {
        return Kunjungan::whereBetween('tanggal_kunjungan', [$start->startOfDay(), $end->endOfDay()])
            ->join('santris', 'kunjungans.santri_id', '=', 'santris.id')
            ->join('jurusans', 'santris.jurusan_id', '=', 'jurusans.id')
            ->select('jurusans.id', 'jurusans.nama_jurusan', DB::raw('count(kunjungans.id) as total_kunjungan'))
            ->groupBy('jurusans.id', 'jurusans.nama_jurusan')
            ->orderByDesc('total_kunjungan')
            ->get();
    }

    /**
     * Detail pasien yang sakit dalam periode tertentu
     */
    public function detailPasienSakit(Carbon $start, Carbon $end)
    {
        return Kunjungan::with(['santri:id,nama_lengkap,nis,kelas_id', 'santri.kelas:id,nama_kelas', 'petugas:id,name', 'pemberianObats.obat'])
            ->whereBetween('tanggal_kunjungan', [$start->startOfDay(), $end->endOfDay()])
            ->orderBy('tanggal_kunjungan', 'desc')
            ->get();
    }
}
