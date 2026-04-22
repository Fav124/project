<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dormitory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'building',
        'gender',
        'supervisor_name',
        'description',
    ];

    public function santris()
    {
        return $this->hasMany(Santri::class);
    }
}
