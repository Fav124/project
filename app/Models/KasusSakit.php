<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class KasusSakit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'santri_id',
        'kunjungan_id',
        'status_kasus',
        'diagnosa_terakhir',
        'tanggal_mulai',
        'tanggal_selesai'
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }

    public function kunjungan(): BelongsTo
    {
        return $this->belongsTo(Kunjungan::class);
    }

    public function riwayats(): HasMany
    {
        return $this->hasMany(RiwayatPerawatan::class, 'kasus_sakit_id');
    }

    public function riwayatAktif(): HasOne
    {
        return $this->hasOne(RiwayatPerawatan::class, 'kasus_sakit_id')->whereNull('tanggal_keluar');
    }
}
