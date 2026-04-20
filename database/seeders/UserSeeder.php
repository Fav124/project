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
            'name' => 'Favian Super Admin',
            'email' => 'superadmin@deisa.id',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'status' => 'approved',
        ]);

        // Admin
        User::create([
            'name' => 'Ahmad Admin',
            'email' => 'admin@deisa.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'approved',
        ]);

        // Petugas Kesehatan
        User::create([
            'name' => 'Dr. Siti Petugas',
            'email' => 'petugas@deisa.id',
            'password' => Hash::make('password'),
            'role' => 'petugas_kesehatan',
            'status' => 'approved',
        ]);

        // Pending User
        User::create([
            'name' => 'Calon Petugas',
            'email' => 'pending@deisa.id',
            'password' => Hash::make('password'),
            'role' => 'petugas_kesehatan',
            'status' => 'pending',
        ]);
    }
}
