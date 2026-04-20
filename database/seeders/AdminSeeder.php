<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@deisa.com'],
            [
                'name' => 'Admin UKS',
                'email' => 'admin@deisa.com',
                'password' => Hash::make('AdminUks@123'),
                'role' => 'admin',
                'status' => 'approved',
                'approved_at' => now(),
            ]
        );

        $this->command->info('Admin UKS berhasil dibuat.');
        $this->command->info('Email   : admin@deisa.com');
        $this->command->info('Password: AdminUks@123');
    }
}
