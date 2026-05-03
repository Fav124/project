<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

#[Fillable([
    'santri_id', 'nama_wali', 'hubungan_wali', 'pekerjaan', 
    'no_hp', 'alamat', 'catatan'
])]
class WaliSantri extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }
}
