<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

#[Fillable([
    'nis', 'nisn', 'nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 
    'tanggal_lahir', 'alamat', 'no_hp', 'foto', 'kelas_id', 
    'jurusan_id', 'kamar_id', 'status_santri', 'catatan'
])]
class Santri extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class);
    }

    public function waliSantris(): HasMany
    {
        return $this->hasMany(WaliSantri::class);
    }

    public function kesehatan(): HasOne
    {
        return $this->hasOne(KesehatanSantri::class);
    }

    public function kunjungans(): HasMany
    {
        return $this->hasMany(Kunjungan::class);
    }

    public function rawatInaps(): HasMany
    {
        return $this->hasMany(RawatInap::class);
    }

    /**
     * Get active medical case
     */
    public function kasusAktif(): HasOne
    {
        return $this->hasOne(KasusSakit::class)->where('status_kasus', 'aktif');
    }

    /**
     * Check if student is currently ill/monitored (New system)
     */
    public function getIsSakitAttribute(): bool
    {
        return $this->kasusAktif()->exists();
    }
}
