<?php

namespace Database\Seeders;

use App\Models\InfirmaryBed;
use Illuminate\Database\Seeder;

class InfirmaryBedSeeder extends Seeder
{
    public function run()
    {
        for ($i = 1; $i <= 6; $i++) {
            InfirmaryBed::create([
                'code' => 'BED-0' . $i,
                'room_name' => $i <= 3 ? 'Ruang Putra' : 'Ruang Putri',
                'status' => 'available',
                'notes' => 'Kasur standar UKS',
            ]);
        }
    }
}
