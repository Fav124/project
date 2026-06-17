<?php

namespace Database\Seeders;

use App\Models\Guardian;
use App\Models\Santri;
use Illuminate\Database\Seeder;

class GuardianSeeder extends Seeder
{
    public function run()
    {
        $santris = Santri::all();

        $relationships = [
            'Bapak Kandung',
            'Ibu Kandung',
            'Kakak Laki-laki',
            'Kakak Perempuan',
            'Nenek',
            'Kakek',
            'Paman',
            'Bibi',
            'Saudara Laki-laki',
            'Saudara Perempuan',
        ];

        $jobs = [
            'Petani',
            'Pedagang',
            'PNS',
            'Wiraswasta',
            'Buruh',
            'Guru',
            'Nelayan',
            'Driver',
            'Karyawan Swasta',
            'Ibu Rumah Tangga',
        ];

        $addresses = [
            'Jl. Raya Bangkalan No. 123',
            'Jl. Merdeka No. 45',
            'Dusun Krajan RT 01 RW 02',
            'Desa Tanjung Bumi',
            'Jl. Ahmad Yani No. 78',
            'Dusun Sumber Anyar',
            'Jl. Sudirman No. 56',
            'Desa Arosbaya',
            'Jl. Panglima Sudirman No. 90',
            'Dusun Kamal',
        ];

        foreach ($santris as $santri) {
            // Each santri gets 1-3 guardians
            $numGuardians = rand(1, 3);
            
            for ($i = 0; $i < $numGuardians; $i++) {
                $lastName = explode(' ', $santri->name)[1] ?? 'Santri';
                
                Guardian::create([
                    'santri_id' => $santri->id,
                    'name' => $i === 0 
                        ? 'Bpk/Ibu ' . $lastName 
                        : $relationships[array_rand($relationships)] . ' ' . $lastName,
                    'relationship' => $relationships[array_rand($relationships)],
                    'phone' => '628' . rand(100000000, 999999999),
                    'address' => $addresses[array_rand($addresses)],
                    'job' => $jobs[array_rand($jobs)],
                    'is_primary' => $i === 0, // First guardian is primary
                    'notes' => $i === 0 
                        ? 'Wali utama santri, dapat dihubungi kapan saja.' 
                        : 'Wali cadangan untuk keadaan darurat.',
                ]);
            }
        }
    }
}
