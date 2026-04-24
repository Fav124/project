<?php

namespace Database\Seeders;

use App\Models\Medicine;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    public function run()
    {
        $medicines = [
            ['name' => 'Paracetamol 500mg', 'unit' => 'Tablet', 'stock' => 100, 'minimum_stock' => 20, 'expiry_date' => '2027-12-31', 'description' => 'Pereda demam dan nyeri ringan.'],
            ['name' => 'Amoxicillin 500mg', 'unit' => 'Tablet', 'stock' => 50, 'minimum_stock' => 10, 'expiry_date' => '2026-06-30', 'description' => 'Antibiotik (perlu resep dokter).'],
            ['name' => 'Antasida Doen', 'unit' => 'Tablet Kunyah', 'stock' => 15, 'minimum_stock' => 20, 'expiry_date' => '2026-10-15', 'description' => 'Obat sakit maag dan lambung.'],
            ['name' => 'OBH Combi Plus', 'unit' => 'Botol', 'stock' => 12, 'minimum_stock' => 5, 'expiry_date' => '2025-05-20', 'description' => 'Sirup batuk dan flu.'],
            ['name' => 'Betadine 15ml', 'unit' => 'Botol', 'stock' => 8, 'minimum_stock' => 10, 'expiry_date' => '2028-01-01', 'description' => 'Antiseptik luka luar.'],
            ['name' => 'Kasa Steril', 'unit' => 'Box', 'stock' => 25, 'minimum_stock' => 5, 'expiry_date' => null, 'description' => 'Pembalut luka.'],
            ['name' => 'Minyak Kayu Putih 60ml', 'unit' => 'Botol', 'stock' => 20, 'minimum_stock' => 5, 'expiry_date' => '2029-12-31', 'description' => 'Penghangat tubuh.'],
            ['name' => 'Sangobion', 'unit' => 'Kapsul', 'stock' => 40, 'minimum_stock' => 10, 'expiry_date' => '2026-03-12', 'description' => 'Penambah darah / Anemia.'],
            ['name' => 'Promag', 'unit' => 'Tablet', 'stock' => 5, 'minimum_stock' => 15, 'expiry_date' => '2026-11-30', 'description' => 'Sakit maag.'],
            ['name' => 'Vitamin C 500mg', 'unit' => 'Tablet', 'stock' => 200, 'minimum_stock' => 50, 'expiry_date' => '2027-08-15', 'description' => 'Suplemen daya tahan tubuh.'],
            ['name' => 'CTM', 'unit' => 'Tablet', 'stock' => 60, 'minimum_stock' => 10, 'expiry_date' => '2024-12-31', 'description' => 'Anti alergi (Kadaluarsa untuk testing).'],
        ];

        foreach ($medicines as $medicine) {
            Medicine::create($medicine);
        }
    }
}
