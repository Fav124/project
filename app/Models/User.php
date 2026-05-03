<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as AuthenticAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role', 'is_approved', 'status', 'no_hp'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $appends = ['role_label', 'status_label'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => \App\Enums\Role::class,
            'is_approved' => 'boolean',
        ];
    }

    public function getRoleLabelAttribute()
    {
        if (!$this->role) return 'Unknown';
        return match ($this->role->value ?? $this->role) {
            \App\Enums\Role::SUPER_ADMIN->value => 'Super Admin',
            \App\Enums\Role::ADMIN->value => 'Admin',
            \App\Enums\Role::PETUGAS_KESEHATAN->value => 'Petugas Kesehatan',
            default => 'Unknown',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'active' => 'Aktif',
            'pending' => 'Pending',
            'frozen' => 'Dibekukan',
            'blocked' => 'Diblokir',
            default => 'Unknown',
        };
    }
}
