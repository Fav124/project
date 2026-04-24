<?php

namespace Database\Seeders;

use App\Models\Dormitory;
use App\Models\Major;
use App\Models\Santri;
use App\Models\SchoolClass;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SantriSeeder extends Seeder
{
    public function run()
    {
        $classes = SchoolClass::all();
        $majors = Major::all();
        $dorms = Dormitory::all();

        $names = [
            'L' => ['Ahmad', 'Budi', 'Candra', 'Dedi', 'Eko', 'Fajar', 'Guntur', 'Hadi', 'Indra', 'Joko', 'Kevin', 'Lukman', 'Mulyono', 'Naufal', 'Oki', 'Prasetyo', 'Rizky', 'Sultan', 'Taufik', 'Umar', 'Vicky', 'Wahyu', 'Xavi', 'Yusuf', 'Zaki'],
            'P' => ['Aisyah', 'Bella', 'Citra', 'Dewi', 'Endah', 'Fitri', 'Gita', 'Hana', 'Indah', 'Julia', 'Kartika', 'Lestari', 'Maya', 'Nia', 'Olivia', 'Putri', 'Qonita', 'Rina', 'Sari', 'Tiara', 'Ulfa', 'Vina', 'Wati', 'Xena', 'Yanti', 'Zahra']
        ];

        for ($i = 0; $i < 60; $i++) {
            $gender = rand(0, 1) ? 'L' : 'P';
            $firstName = $names[$gender][array_rand($names[$gender])];
            $lastName = $names[$gender][array_rand($names[$gender])];
            $name = $firstName . ' ' . $lastName;
            
            $major = $majors->random();
            $class = $classes->filter(function($c) use ($major) {
                return strpos($c->name, $major->name) !== false;
            })->random();
            
            $dorm = $dorms->where('gender', $gender)->random();

            Santri::create([
                'name' => $name,
                'nis' => '2026' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'gender' => $gender,
                'class_id' => $class->id,
                'major_id' => $major->id,
                'dormitory_id' => $dorm->id,
                'dorm_room' => 'Kamar ' . rand(101, 110),
                'guardian_name' => 'Bpk/Ibu ' . $lastName,
                'guardian_phone' => '628' . rand(100000000, 999999999),
            ]);
        }
    }
}
