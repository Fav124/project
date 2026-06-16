<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineMutation extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_id',
        'type',
        'amount',
        'before_stock',
        'after_stock',
        'notes',
    ];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
