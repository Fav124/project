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
        'visit_date',
        'complaint',
        'diagnosis',
        'action_taken',
        'medicine_notes',
        'status',
        'return_date',
        'notes',
        'photo_path',
        // Referral fields
        'hospital_name',
        'transport',
        'companion_name',
        // Discharge fields
        'picked_up_by',
        'picked_up_at',
        'discharge_notes',
        'discharge_guardian_id',
    ];

    protected $casts = [
        'visit_date'  => 'date',
        'return_date' => 'date',
        'picked_up_at'=> 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /** @deprecated use handledBy() */
    public function handler()
    {
        return $this->handledBy();
    }

    public function medicines()
    {
        return $this->belongsToMany(Medicine::class, 'medicine_sickness_case')
                    ->withPivot(['id', 'quantity', 'status', 'notes'])
                    ->withTimestamps();
    }

    public function keluhans()
    {
        return $this->belongsToMany(Keluhan::class, 'keluhan_sickness_case', 'sickness_case_id', 'keluhan_id');
    }

    public function diagnosas()
    {
        return $this->belongsToMany(Diagnosa::class, 'diagnosa_sickness_case', 'sickness_case_id', 'diagnosa_id');
    }

    public function tindakans()
    {
        return $this->belongsToMany(Tindakan::class, 'tindakan_sickness_case', 'sickness_case_id', 'tindakan_id');
    }

    public function dischargeGuardian()
    {
        return $this->belongsTo(Guardian::class, 'discharge_guardian_id');
    }
}
