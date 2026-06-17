<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_id',
        'batch_number',
        'quantity',
        'expiry_date',
        'received_date',
        'notes',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'received_date' => 'date',
    ];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function isExpired(): bool
    {
        return $this->expiry_date->isPast();
    }

    public function isExpiringSoon(): bool
    {
        return $this->expiry_date->isFuture() &&
               $this->expiry_date->diffInMonths(now()) < 3;
    }

    public function getDaysUntilExpiryAttribute(): int
    {
        return now()->diffInDays($this->expiry_date, false);
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    public function scopeExpiringSoon($query)
    {
        return $query->where('expiry_date', '>', now())
                     ->where('expiry_date', '<=', now()->addMonths(3));
    }

    public function scopeNotExpired($query)
    {
        return $query->where('expiry_date', '>', now());
    }

    public function scopeAvailable($query)
    {
        return $query->where('quantity', '>', 0);
    }
}
