<?php

namespace Database\Seeders;

use App\Models\HospitalReferral;
use App\Models\Santri;
use App\Models\SicknessCase;
use Illuminate\Database\Seeder;

class HospitalReferralSeeder extends Seeder
{
    public function run()
    {
        $santris = Santri::all();
        
        $hospitals = [
            'RSUD Syarifah Ambami',
            'RS Siloam',
            'Puskesmas Bangkalan',
            'RS Anna Medika',
        ];

        $reasons = [
            'Perlu rontgen paru-paru.',
            'Demam tinggi > 39C tidak turun dalam 24 jam.',
            'Luka sobek cukup dalam, perlu penjahitan.',
            'Curiga usus buntu (Apendisitis).',
        ];

        foreach ($santris->slice(30, 4)->values() as $index => $santri) {
            // Create a sickness case for this referral
            SicknessCase::create([
                'santri_id' => $santri->id,
                'visit_date' => now()->subDays($index + 2),
                'complaint' => $reasons[$index],
                'status' => 'referred',
                'handled_by' => 1,
            ]);

            HospitalReferral::create([
                'santri_id' => $santri->id,
                'referred_by' => 1,
                'hospital_name' => $hospitals[$index],
                'referral_date' => now()->subDays($index + 2),
                'diagnosis' => 'Suspect ' . $reasons[$index],
                'reason' => $reasons[$index],
                'status' => rand(0, 1) ? 'ongoing' : 'completed',
                'notes' => 'Telah didampingi oleh wali santri.',
            ]);
        }
    }
}
