<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'obat_id', 'jenis_mutasi', 'jumlah', 'stok_sebelum', 
    'stok_sesudah', 'catatan', 'user_id'
])]
class RiwayatStokObat extends Model
{
    use HasFactory;

    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
