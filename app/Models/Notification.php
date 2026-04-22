<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'type',
        'user_id',
        'is_read',
        'action_url',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, $userId = null)
    {
        $userId = $userId ?: auth()->id();
        return $query->where(function($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhereNull('user_id');
        });
    }

    public function getIconAttribute()
    {
        return match($this->type) {
            'success' => 'mdi-check-circle',
            'warning' => 'mdi-alert',
            'danger' => 'mdi-alert-octagon',
            default => 'mdi-information',
        };
    }

    public function getColorClassAttribute()
    {
        return match($this->type) {
            'success' => 'text-success',
            'warning' => 'text-warning',
            'danger' => 'text-danger',
            default => 'text-info',
        };
    }
}
