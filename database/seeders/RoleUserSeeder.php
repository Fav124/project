<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        User::firstOrCreate(
            ['email' => 'superadmin@deihealth.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'role' => Role::SUPER_ADMIN,
                'is_approved' => true,
            ]
        );

        // Create Admin
        User::firstOrCreate(
            ['email' => 'admin@deihealth.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
                'role' => Role::ADMIN,
                'is_approved' => true,
            ]
        );

        // Create Petugas Kesehatan
        User::firstOrCreate(
            ['email' => 'petugas@deihealth.com'],
            [
                'name' => 'Petugas Kesehatan',
                'password' => Hash::make('password123'),
                'role' => Role::PETUGAS_KESEHATAN,
                'is_approved' => true,
            ]
        );
    }
}
