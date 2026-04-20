<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function classes()
    {
        return $this->belongsToMany(SchoolClass::class);
    }

    public function santris()
    {
        return $this->hasMany(Santri::class);
    }
}
