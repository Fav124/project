<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_obat',
        'name',
        'kategori',
        'bentuk_sediaan',
        'unit',
        'stock',
        'minimum_stock',
        'expiry_date',
        'lokasi_penyimpanan',
        'description',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    protected $appends = ['status', 'riwayat_stok'];

    public function mutations()
    {
        return $this->hasMany(MedicineMutation::class);
    }

    public function sicknessCases()
    {
        return $this->belongsToMany(SicknessCase::class, 'medicine_sickness_case')
                    ->withPivot(['id', 'quantity', 'status', 'notes'])
                    ->withTimestamps();
    }

    public function batches()
    {
        return $this->hasMany(MedicineBatch::class)->orderBy('expiry_date');
    }

    public function availableBatches()
    {
        return $this->hasMany(MedicineBatch::class)
                    ->where('quantity', '>', 0)
                    ->orderBy('expiry_date');
    }

    public function getAvailableStockAttribute(): int
    {
        return $this->availableBatches()->sum('quantity');
    }

    public function getExpiredStockAttribute(): int
    {
        return $this->batches()->expired()->sum('quantity');
    }

    public function getExpiringSoonStockAttribute(): int
    {
        return $this->batches()->expiringSoon()->sum('quantity');
    }

    public function isExpired(): bool
    {
        return $this->availableBatches()->expired()->exists();
    }

    public function isExpiringSoon(): bool
    {
        return $this->availableBatches()->expiringSoon()->exists();
    }

    public function getNearestExpiryDateAttribute()
    {
        return $this->availableBatches()->notExpired()->first()?->expiry_date;
    }

    public function getStatusAttribute(): string
    {
        if ($this->isExpired()) {
            return 'kadaluarsa';
        } elseif ($this->isExpiringSoon()) {
            return 'segera_expired';
        } elseif ($this->stock <= $this->minimum_stock) {
            return 'stok_kritis';
        } else {
            return 'aman';
        }
    }

    public function getRiwayatStokAttribute(): array
    {
        return $this->mutations()->latest()->get()->map(function ($mutation) {
            return [
                'id' => $mutation->id,
                'type' => $mutation->type,
                'amount' => $mutation->amount,
                'stok_sebelum' => $mutation->before_stock,
                'stok_sesudah' => $mutation->after_stock,
                'date' => $mutation->created_at->format('d M Y H:i'),
                'notes' => $mutation->notes,
            ];
        })->toArray();
    }
}
