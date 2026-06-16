<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Models\Keluhan;
use App\Models\Diagnosa;
use App\Models\Tindakan;

class AlignSeeder extends Seeder
{
    public function run()
    {
        // 1. Seed Settings
        $settings = [
            'app_name' => 'DEI Health',
            'institution_name' => 'Pondok Pesantren DEI',
            'default_min_stock' => '5',
            'batas_hampir_kadaluarsa_hari' => '90',
        ];

        foreach ($settings as $key => $val) {
            Setting::updateOrCreate(['key' => $key], ['value' => $val]);
        }

        // 2. Seed Keluhans
        $keluhans = [
            'Demam',
            'Sakit Kepala',
            'Batuk',
            'Pilek',
            'Sakit Perut',
            'Gatal-gatal',
            'Diare',
            'Mual / Muntah',
            'Luka Ringan',
            'Sesak Napas',
            'Lemas / Capek',
        ];

        foreach ($keluhans as $name) {
            Keluhan::firstOrCreate(['name' => $name]);
        }

        // 3. Seed Diagnosas
        $diagnosas = [
            'Influenza',
            'Dyspepsia (Maag)',
            'Dermatitis',
            'Gastroenteritis (Diare)',
            'Cephalgia',
            'Febris (Demam)',
            'Pharyngitis',
            'Asma Bronkiale',
            'Vulnus Excoriatum (Luka Lecet)',
        ];

        foreach ($diagnosas as $name) {
            Diagnosa::firstOrCreate(['name' => $name]);
        }

        // 4. Seed Tindakans
        $tindakans = [
            'Istirahat di UKS',
            'Pemberian Obat',
            'Kompres Hangat',
            'Pembersihan & Perawatan Luka',
            'Observasi Tanda Vital',
            'Rujuk ke Puskesmas / RS',
        ];

        foreach ($tindakans as $name) {
            Tindakan::firstOrCreate(['name' => $name]);
        }
    }
}
