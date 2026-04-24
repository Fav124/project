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
        'dormitory_id',
        'class_room',
        'dorm_room',
        'guardian_name',
        'guardian_phone',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function dormitory()
    {
        return $this->belongsTo(Dormitory::class);
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
