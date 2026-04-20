<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'santri_id',
        'recorded_by',
        'record_date',
        'complaint',
        'diagnosis',
        'treatment',
        'blood_pressure',
        'temperature',
        'pulse',
        'weight',
        'notes',
    ];

    protected $casts = [
        'record_date' => 'date',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
