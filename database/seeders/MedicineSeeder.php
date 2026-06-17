<?php

namespace Database\Seeders;

use App\Models\Medicine;
use App\Models\MedicineBatch;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    public function run()
    {
        $medicines = [
            [
                'code' => 'OBT001',
                'name' => 'Paracetamol 500mg',
                'category' => 'Pereda Nyeri',
                'formulation' => 'Tablet',
                'unit' => 'Tablet',
                'stock' => 100,
                'minimum_stock' => 20,
                'expiry_date' => '2027-12-31',
                'location' => 'Lemari Obat A',
                'description' => 'Pereda demam dan nyeri ringan.',
                'batches' => [
                    ['batch_number' => 'BTH-0001-001', 'quantity' => 50, 'expiry_date' => '2027-12-31', 'received_date' => '2024-01-15'],
                    ['batch_number' => 'BTH-0001-002', 'quantity' => 30, 'expiry_date' => '2028-06-30', 'received_date' => '2024-06-20'],
                    ['batch_number' => 'BTH-0001-003', 'quantity' => 20, 'expiry_date' => '2027-09-15', 'received_date' => '2024-09-10'],
                ]
            ],
            [
                'code' => 'OBT002',
                'name' => 'Amoxicillin 500mg',
                'category' => 'Antibiotik',
                'formulation' => 'Tablet',
                'unit' => 'Tablet',
                'stock' => 50,
                'minimum_stock' => 10,
                'expiry_date' => '2026-06-30',
                'location' => 'Lemari Obat B',
                'description' => 'Antibiotik (perlu resep dokter).',
                'batches' => [
                    ['batch_number' => 'BTH-0002-001', 'quantity' => 30, 'expiry_date' => '2026-06-30', 'received_date' => '2024-02-01'],
                    ['batch_number' => 'BTH-0002-002', 'quantity' => 20, 'expiry_date' => '2026-12-15', 'received_date' => '2024-07-15'],
                ]
            ],
            [
                'code' => 'OBT003',
                'name' => 'Antasida Doen',
                'category' => 'Antasida',
                'formulation' => 'Tablet Kunyah',
                'unit' => 'Tablet',
                'stock' => 15,
                'minimum_stock' => 20,
                'expiry_date' => '2026-10-15',
                'location' => 'Lemari Obat A',
                'description' => 'Obat sakit maag dan lambung.',
                'batches' => [
                    ['batch_number' => 'BTH-0003-001', 'quantity' => 15, 'expiry_date' => '2026-10-15', 'received_date' => '2024-03-20'],
                ]
            ],
            [
                'code' => 'OBT004',
                'name' => 'OBH Combi Plus',
                'category' => 'Batuk & Flu',
                'formulation' => 'Sirup',
                'unit' => 'Botol',
                'stock' => 12,
                'minimum_stock' => 5,
                'expiry_date' => '2025-05-20',
                'location' => 'Lemari Obat C',
                'description' => 'Sirup batuk dan flu.',
                'batches' => [
                    ['batch_number' => 'BTH-0004-001', 'quantity' => 8, 'expiry_date' => '2025-05-20', 'received_date' => '2024-04-10'],
                    ['batch_number' => 'BTH-0004-002', 'quantity' => 4, 'expiry_date' => '2025-08-30', 'received_date' => '2024-08-15'],
                ]
            ],
            [
                'code' => 'OBT005',
                'name' => 'Betadine 15ml',
                'category' => 'Antiseptik',
                'formulation' => 'Cair',
                'unit' => 'Botol',
                'stock' => 8,
                'minimum_stock' => 10,
                'expiry_date' => '2028-01-01',
                'location' => 'Lemari Obat D',
                'description' => 'Antiseptik luka luar.',
                'batches' => [
                    ['batch_number' => 'BTH-0005-001', 'quantity' => 5, 'expiry_date' => '2028-01-01', 'received_date' => '2024-05-01'],
                    ['batch_number' => 'BTH-0005-002', 'quantity' => 3, 'expiry_date' => '2028-06-15', 'received_date' => '2024-09-20'],
                ]
            ],
            [
                'code' => 'OBT006',
                'name' => 'Kasa Steril',
                'category' => 'Perban',
                'formulation' => 'Kasa',
                'unit' => 'Box',
                'stock' => 25,
                'minimum_stock' => 5,
                'expiry_date' => null,
                'location' => 'Lemari Obat E',
                'description' => 'Pembalut luka.',
                'batches' => [
                    ['batch_number' => 'BTH-0006-001', 'quantity' => 15, 'expiry_date' => null, 'received_date' => '2024-01-10'],
                    ['batch_number' => 'BTH-0006-002', 'quantity' => 10, 'expiry_date' => null, 'received_date' => '2024-07-25'],
                ]
            ],
            [
                'code' => 'OBT007',
                'name' => 'Minyak Kayu Putih 60ml',
                'category' => 'Penghangat',
                'formulation' => 'Cair',
                'unit' => 'Botol',
                'stock' => 20,
                'minimum_stock' => 5,
                'expiry_date' => '2029-12-31',
                'location' => 'Lemari Obat A',
                'description' => 'Penghangat tubuh.',
                'batches' => [
                    ['batch_number' => 'BTH-0007-001', 'quantity' => 12, 'expiry_date' => '2029-12-31', 'received_date' => '2024-02-15'],
                    ['batch_number' => 'BTH-0007-002', 'quantity' => 8, 'expiry_date' => '2030-06-30', 'received_date' => '2024-08-30'],
                ]
            ],
            [
                'code' => 'OBT008',
                'name' => 'Sangobion',
                'category' => 'Suplemen Darah',
                'formulation' => 'Kapsul',
                'unit' => 'Kapsul',
                'stock' => 40,
                'minimum_stock' => 10,
                'expiry_date' => '2026-03-12',
                'location' => 'Lemari Obat B',
                'description' => 'Penambah darah / Anemia.',
                'batches' => [
                    ['batch_number' => 'BTH-0008-001', 'quantity' => 25, 'expiry_date' => '2026-03-12', 'received_date' => '2024-03-12'],
                    ['batch_number' => 'BTH-0008-002', 'quantity' => 15, 'expiry_date' => '2026-09-30', 'received_date' => '2024-09-15'],
                ]
            ],
            [
                'code' => 'OBT009',
                'name' => 'Promag',
                'category' => 'Antasida',
                'formulation' => 'Tablet',
                'unit' => 'Tablet',
                'stock' => 5,
                'minimum_stock' => 15,
                'expiry_date' => '2026-11-30',
                'location' => 'Lemari Obat A',
                'description' => 'Sakit maag.',
                'batches' => [
                    ['batch_number' => 'BTH-0009-001', 'quantity' => 5, 'expiry_date' => '2026-11-30', 'received_date' => '2024-04-20'],
                ]
            ],
            [
                'code' => 'OBT010',
                'name' => 'Vitamin C 500mg',
                'category' => 'Suplemen',
                'formulation' => 'Tablet',
                'unit' => 'Tablet',
                'stock' => 200,
                'minimum_stock' => 50,
                'expiry_date' => '2027-08-15',
                'location' => 'Lemari Obat F',
                'description' => 'Suplemen daya tahan tubuh.',
                'batches' => [
                    ['batch_number' => 'BTH-0010-001', 'quantity' => 100, 'expiry_date' => '2027-08-15', 'received_date' => '2024-05-10'],
                    ['batch_number' => 'BTH-0010-002', 'quantity' => 60, 'expiry_date' => '2028-02-28', 'received_date' => '2024-10-15'],
                    ['batch_number' => 'BTH-0010-003', 'quantity' => 40, 'expiry_date' => '2027-12-31', 'received_date' => '2024-12-01'],
                ]
            ],
            [
                'code' => 'OBT011',
                'name' => 'CTM',
                'category' => 'Anti Alergi',
                'formulation' => 'Tablet',
                'unit' => 'Tablet',
                'stock' => 60,
                'minimum_stock' => 10,
                'expiry_date' => '2024-12-31',
                'location' => 'Lemari Obat G',
                'description' => 'Anti alergi (Kadaluarsa untuk testing).',
                'batches' => [
                    ['batch_number' => 'BTH-0011-001', 'quantity' => 60, 'expiry_date' => '2024-12-31', 'received_date' => '2023-12-31'],
                ]
            ],
        ];

        foreach ($medicines as $medicineData) {
            $batches = $medicineData['batches'];
            unset($medicineData['batches']);
            
            $medicine = Medicine::create($medicineData);
            
            foreach ($batches as $batchData) {
                MedicineBatch::create([
                    'medicine_id' => $medicine->id,
                    'batch_number' => $batchData['batch_number'],
                    'quantity' => $batchData['quantity'],
                    'expiry_date' => $batchData['expiry_date'],
                    'received_date' => $batchData['received_date'],
                ]);
            }
        }
    }
}
