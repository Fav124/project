<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'stock',
        'minimum_stock',
        'expiry_date',
        'description',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon()
    {
        return $this->expiry_date && 
               $this->expiry_date->isFuture() && 
               $this->expiry_date->diffInMonths(now()) < 3;
    }

    public function sicknessCases()
    {
        return $this->belongsToMany(SicknessCase::class, 'medicine_sickness_case')
                    ->withPivot(['id', 'quantity', 'status', 'notes'])
                    ->withTimestamps();
    }
}
