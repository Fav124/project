<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    public function run()
    {
        $majors = [
            ['name' => 'IPA', 'description' => 'Ilmu Pengetahuan Alam'],
            ['name' => 'IPS', 'description' => 'Ilmu Pengetahuan Sosial'],
            ['name' => 'Tahfidz', 'description' => 'Program Khusus Penghafal Al-Qur\'an'],
            ['name' => 'Bahasa', 'description' => 'Program Studi Bahasa & Sastra'],
            ['name' => 'Teknik', 'description' => 'Keahlian Teknik Komputer & Jaringan'],
        ];

        foreach ($majors as $major) {
            Major::create($major);
        }
    }
}
