<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kasur extends Model
{
    use HasFactory;

    protected $fillable = ['kode_kasur', 'status'];

    public function riwayats()
    {
        return $this->hasMany(RiwayatPerawatan::class);
    }
}
