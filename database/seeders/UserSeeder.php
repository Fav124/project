<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Super Admin
        User::create([
            'name' => 'Ustadz Fulan',
            'email' => 'superadmin@deihealth.id',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'status' => 'approved',
        ]);

        // Admin
        User::create([
            'name' => 'Admin Fauzi',
            'email' => 'admin@deihealth.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'approved',
        ]);

        // Petugas Kesehatan
        User::create([
            'name' => 'Dr. Budi',
            'email' => 'petugas@deisa.id',
            'password' => Hash::make('password'),
            'role' => 'petugas_kesehatan',
            'status' => 'approved',
        ]);

        // Pending User
        User::create([
            'name' => 'Calon Petugas',
            'email' => 'pending@deihelath.id',
            'password' => Hash::make('password'),
            'role' => 'petugas_kesehatan',
            'status' => 'pending',
        ]);
    }
}
