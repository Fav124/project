<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Diagnosa extends Model
{
    protected $fillable = ['kode', 'nama', 'kategori', 'deskripsi', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function kunjungans(): BelongsToMany
    {
        return $this->belongsToMany(Kunjungan::class, 'diagnosa_kunjungan');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
