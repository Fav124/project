<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospitalReferral extends Model
{
    use HasFactory;

    protected $fillable = [
        'santri_id',
        'referred_by',
        'hospital_name',
        'referral_date',
        'reason',
        'diagnosis',
        'transport',
        'companion_name',
        'status',
        'notes',
    ];

    protected $casts = [
        'referral_date' => 'date',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }
}
