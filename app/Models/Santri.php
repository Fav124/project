<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'name',
        'gender',
        'birth_place',
        'birth_date',
        'class_id',
        'major_id',
        'class_room',
        'dorm_room',
        'guardian_name',
        'guardian_phone',
        'guardian_relationship',
        'guardian_job',
        'guardian_address',
        'notes',
        'blood_type',
        'blood_pressure',
        'height',
        'weight',
        'allergies',
        'medical_history',
        'special_condition',
        'photo_path',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'height' => 'double',
        'weight' => 'double',
    ];

    public function guardians()
    {
        return $this->hasMany(Guardian::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function sicknessCases()
    {
        return $this->hasMany(SicknessCase::class);
    }

    public function hospitalReferrals()
    {
        return $this->hasMany(HospitalReferral::class);
    }
}
