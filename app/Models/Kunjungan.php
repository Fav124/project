<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

#[Fillable([
    'santri_id', 'user_id', 'tanggal_kunjungan', 'keluhan_utama', 
    'riwayat_keluhan', 'suhu', 'tekanan_darah', 'denyut_nadi', 
    'pernapasan', 'diagnosa_sementara', 'tindakan', 'catatan', 
    'status_kunjungan'
])]
class Kunjungan extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $casts = [
        'tanggal_kunjungan' => 'datetime',
        'suhu' => 'decimal:1',
        'denyut_nadi' => 'integer',
        'pernapasan' => 'integer',
    ];

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pemberianObats(): HasMany
    {
        return $this->hasMany(PemberianObat::class);
    }
    
    public function rawatInap(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(RawatInap::class);
    }

    public function kasusSakit(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(KasusSakit::class, 'kunjungan_id');
    }
}
