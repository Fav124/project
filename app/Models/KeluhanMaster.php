<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class KeluhanMaster extends Model
{
    protected $fillable = ['nama', 'kategori', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function kunjungans(): BelongsToMany
    {
        return $this->belongsToMany(Kunjungan::class, 'keluhan_kunjungan');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
