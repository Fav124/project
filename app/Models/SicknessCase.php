<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SicknessCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'santri_id',
        'handled_by',
        'medicine_id',
        'infirmary_bed_id',
        'visit_date',
        'complaint',
        'diagnosis',
        'action_taken',
        'medicine_notes',
        'status',
        'return_date',
        'notes',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'return_date' => 'date',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function bed()
    {
        return $this->belongsTo(InfirmaryBed::class, 'infirmary_bed_id');
    }
}
