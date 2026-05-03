<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPerawatan extends Model
{
    protected $fillable = [
        'kasus_sakit_id',
        'lokasi_perawatan',
        'kasur_id',
        'tanggal_masuk',
        'tanggal_keluar',
        'alasan_pindah',
        'kondisi_masuk',
        'kondisi_keluar',
        'petugas_id',
        'nama_rs',
        'info_rs',
        'penjemput',
        'kontak_penjemput',
        'hubungan_penjemput',
        'catatan'
    ];

    protected $casts = [
        'tanggal_masuk' => 'datetime',
        'tanggal_keluar' => 'datetime',
    ];

    public function kasusSakit(): BelongsTo
    {
        return $this->belongsTo(KasusSakit::class);
    }

    public function kasur(): BelongsTo
    {
        return $this->belongsTo(Kasur::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
