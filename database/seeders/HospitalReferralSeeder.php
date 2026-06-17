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
        
        $hospitals = [
            'RSUD Syarifah Ambami',
            'RS Siloam Bangkalan',
            'Puskesmas Bangkalan',
            'RS Anna Medika',
            'RSUD Dr. Soetomo',
        ];

        $reasons = [
            'Perlu rontgen paru-paru untuk diagnosis pneumonia.',
            'Demam tinggi > 39C tidak turun dalam 24 jam, curiga demam berdarah.',
            'Luka sobek cukup dalam pada lengan kanan, perlu penjahitan.',
            'Curiga usus buntu (Apendisitis), nyeri perut kanan bawah.',
            'Trauma kepala akibat jatuh, perlu CT scan.',
            'Sesak nafas berat, curiga infeksi paru-paru.',
            'Nyeri dada kiri, perlu EKG untuk evaluasi jantung.',
            'Fraktur tulang kaki, perlu rontgen dan penanganan ortopedi.',
        ];

        $diagnoses = [
            'Pneumonia (Suspect)',
            'Demam Berdarah Dengue (Suspect)',
            'Luka Tusuk Memerlukan Jahitan',
            'Apendisitis Akut (Suspect)',
            'Trauma Capitis (Suspect)',
            'Infeksi Saluran Pernapasan Berat',
            'Nyeri Dada Non-Kardial (Suspect)',
            'Fraktur Tibia (Suspect)',
        ];

        $transports = [
            'Ambulans Pondok',
            'Mobil Pribadi Keluarga',
            'Ojek Online',
            'Ambulans Puskesmas',
            'Mobil Teman Santri',
        ];

        $companions = [
            'Bapak Kandung',
            'Ibu Kandung',
            'Kakak Laki-laki',
            'Nenek',
            'Paman',
        ];

        foreach ($santris->slice(35, 8)->values() as $index => $santri) {
            $reasonIndex = $index % count($reasons);
            $status = rand(0, 1) ? 'ongoing' : 'completed';
            
            HospitalReferral::create([
                'santri_id' => $santri->id,
                'referred_by' => 1,
                'hospital_name' => $hospitals[$index % count($hospitals)],
                'referral_date' => now()->subDays(rand(1, 14)),
                'diagnosis' => $diagnoses[$reasonIndex],
                'reason' => $reasons[$reasonIndex],
                'transport' => $transports[$index % count($transports)],
                'companion_name' => $companions[$index % count($companions)],
                'status' => $status,
                'notes' => $status === 'ongoing' 
                    ? 'Santri masih dalam perawatan di rumah sakit. Wali menunggu di ruang tunggu.'
                    : 'Santri sudah kembali dari rumah sakit dengan resep obat dan instruksi lanjutan.',
            ]);
        }
    }
}
