<?php

namespace Database\Seeders;

use App\Models\Medicine;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    public function run()
    {
        $medicines = [
            ['name' => 'Paracetamol', 'unit' => 'Tablet', 'stock' => 100, 'minimum_stock' => 20, 'description' => 'Pereda demam dan nyeri.'],
            ['name' => 'Amoxicillin', 'unit' => 'Strip', 'stock' => 50, 'minimum_stock' => 10, 'description' => 'Antibiotik (perlu resep).'],
            ['name' => 'Antasida Doen', 'unit' => 'Tablet', 'stock' => 80, 'minimum_stock' => 15, 'description' => 'Obat sakit maag.'],
            ['name' => 'OBH Combi', 'unit' => 'Botol', 'stock' => 5, 'minimum_stock' => 10, 'description' => 'Obat batuk.'],
            ['name' => 'Betadine', 'unit' => 'Botol', 'stock' => 12, 'minimum_stock' => 5, 'description' => 'Obat luka luar.'],
        ];

        foreach ($medicines as $med) {
            Medicine::create($med);
        }
    }
}
