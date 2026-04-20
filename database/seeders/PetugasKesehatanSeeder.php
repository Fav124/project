<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PetugasKesehatanSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'petugas@deisa.com'],
            [
                'name' => 'Petugas Kesehatan',
                'email' => 'petugas@deisa.com',
                'password' => Hash::make('PetugasUks@123'),
                'role' => 'petugas_kesehatan',
                'status' => 'approved',
                'approved_at' => now(),
            ]
        );

        $this->command->info('Petugas Kesehatan berhasil dibuat.');
        $this->command->info('Email   : petugas@deisa.com');
        $this->command->info('Password: PetugasUks@123');
    }
}
