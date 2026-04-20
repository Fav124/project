<?php

namespace Database\Seeders;

use App\Models\HospitalReferral;
use App\Models\Santri;
use Illuminate\Database\Seeder;

class HospitalReferralSeeder extends Seeder
{
    public function run()
    {
        $santris = Santri::all();
        
        $hospitals = ['RSUD Dr. Soetomo', 'RS Islam Jakarta', 'Klinik Medika Pratama', 'RS Siloam'];

        foreach ($santris->take(3) as $index => $santri) {
            HospitalReferral::create([
                'santri_id' => $santri->id,
                'hospital_name' => $hospitals[$index % count($hospitals)],
                'referral_date' => now()->subDays(rand(1, 5)),
                'complaint' => 'Gejala tipus yang membutuhkan observasi lanjut.',
                'diagnosis' => 'Suspect Typhoid Fever',
                'notes' => 'Peralatan UKS tidak memadai untuk tes darah lengkap.',
                'transport' => 'Ambulans Sekolah',
                'companion_name' => 'Dr. Siti Petugas',
                'status' => $index == 0 ? 'treated' : 'referred',
            ]);
        }
    }
}
