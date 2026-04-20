<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfirmaryBed extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'room_name',
        'status',
        'occupant_name',
        'notes',
    ];

    public function sicknessCases()
    {
        return $this->hasMany(SicknessCase::class);
    }
}
