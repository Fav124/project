<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

#[Fillable([
    'santri_id', 'kunjungan_id', 'tipe_rawat', 'tanggal_masuk', 
    'estimasi_kembali', 'tanggal_keluar', 'alasan_rawat', 'kondisi_masuk', 
    'kondisi_keluar', 'status_rawat', 'catatan'
])]
class RawatInap extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $casts = [
        'tanggal_masuk' => 'datetime',
        'estimasi_kembali' => 'datetime',
        'tanggal_keluar' => 'datetime',
    ];

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }

    public function kunjungan(): BelongsTo
    {
        return $this->belongsTo(Kunjungan::class);
    }
}
