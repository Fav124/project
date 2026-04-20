<?php

namespace Database\Seeders;

use App\Models\Major;
use App\Models\Santri;
use App\Models\SchoolClass;
use Illuminate\Database\Seeder;

class SantriSeeder extends Seeder
{
    public function run()
    {
        $classes = SchoolClass::all();
        $majors = Major::all();

        $names = [
            'Budi Santoso', 'Siti Aminah', 'Ahmad Hidayat', 'Dewi Lestari', 
            'Rizky Pratama', 'Putri Utami', 'Fajar Ramadhan', 'Lutfi Hakim',
            'Anisa Rahma', 'Zaki Mubarak', 'Hafizah', 'Irfan Maulana'
        ];

        foreach ($names as $index => $name) {
            $class = $classes->random();
            Santri::create([
                'nis' => '1200' . (10 + $index),
                'name' => $name,
                'gender' => $index % 2 == 0 ? 'L' : 'P',
                'birth_place' => 'Jakarta',
                'birth_date' => '2008-05-' . (10 + $index),
                'class_id' => $class->id,
                'major_id' => $class->majors->first()->id ?? $majors->random()->id,
                'dorm_room' => 'Gedung ' . chr(65 + rand(0, 3)) . ' Kamar 0' . rand(1, 9),
                'guardian_name' => 'Wali dari ' . $name,
                'guardian_phone' => '62812345678' . $index,
            ]);
        }
    }
}
