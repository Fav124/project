<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Appended;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

#[Fillable([
    'kode_obat', 'nama_obat', 'foto', 'kategori', 'golongan', 'bentuk_sediaan', 'satuan', 
    'stok', 'stok_minimum', 'tanggal_kadaluarsa', 
    'lokasi_penyimpanan', 'deskripsi'
])]
#[Appended(['status_obat'])]
class Obat extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $casts = [
        'tanggal_kadaluarsa' => 'date',
        'stok' => 'integer',
        'stok_minimum' => 'integer',
    ];

    public function riwayatStok(): HasMany
    {
        return $this->hasMany(RiwayatStokObat::class);
    }

    /**
     * Computed Status Obat
     */
    public function getStatusObatAttribute(): string
    {
        $now = now();
        $batasPeringatan = Setting::get('batas_hampir_kadaluarsa_hari', 90);

        if ($this->tanggal_kadaluarsa->isPast()) {
            return 'kadaluarsa';
        }

        if ($this->tanggal_kadaluarsa->diffInDays($now) <= $batasPeringatan) {
            return 'hampir_kadaluarsa';
        }

        if ($this->stok <= 0) {
            return 'habis';
        }

        if ($this->stok <= $this->stok_minimum) {
            return 'stok_menipis';
        }

        return 'aktif';
    }

    // --- SCOPES ---

    public function scopeKadaluarsa(Builder $query): void
    {
        $query->whereDate('tanggal_kadaluarsa', '<', now());
    }

    public function scopeHampirKadaluarsa(Builder $query, int $hari = 90): void
    {
        $query->whereDate('tanggal_kadaluarsa', '>=', now())
              ->whereDate('tanggal_kadaluarsa', '<=', now()->addDays($hari));
    }

    public function scopeStokMenipis(Builder $query): void
    {
        $query->whereColumn('stok', '<=', 'stok_minimum')
              ->where('stok', '>', 0);
    }

    public function scopeStokHabis(Builder $query): void
    {
        $query->where('stok', '<=', 0);
    }

    public function scopeAktif(Builder $query): void
    {
        $query->whereDate('tanggal_kadaluarsa', '>=', now())
              ->whereColumn('stok', '>', 'stok_minimum');
    }
}
