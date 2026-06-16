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

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon(): bool
    {
        return $this->expiry_date &&
               $this->expiry_date->isFuture() &&
               $this->expiry_date->diffInMonths(now()) < 3;
    }
}
