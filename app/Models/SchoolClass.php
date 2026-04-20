<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'description',
    ];

    public function majors()
    {
        return $this->belongsToMany(Major::class);
    }

    public function santris()
    {
        return $this->hasMany(Santri::class, 'class_id');
    }
}
