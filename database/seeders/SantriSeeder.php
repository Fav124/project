<?php

namespace Database\Seeders;

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

        $names = [
            'L' => ['Ahmad', 'Budi', 'Candra', 'Dedi', 'Eko', 'Fajar', 'Guntur', 'Hadi', 'Indra', 'Joko', 'Kevin', 'Lukman', 'Mulyono', 'Naufal', 'Oki', 'Prasetyo', 'Rizky', 'Sultan', 'Taufik', 'Umar', 'Vicky', 'Wahyu', 'Xavi', 'Yusuf', 'Zaki'],
            'P' => ['Aisyah', 'Bella', 'Citra', 'Dewi', 'Endah', 'Fitri', 'Gita', 'Hana', 'Indah', 'Julia', 'Kartika', 'Lestari', 'Maya', 'Nia', 'Olivia', 'Putri', 'Qonita', 'Rina', 'Sari', 'Tiara', 'Ulfa', 'Vina', 'Wati', 'Xena', 'Yanti', 'Zahra']
        ];

        $birthPlaces = ['Jakarta', 'Bandung', 'Surabaya', 'Medan', 'Semarang', 'Yogyakarta', 'Makassar', 'Palembang', 'Denpasar', 'Manado'];
        $bloodTypes = ['A', 'B', 'AB', 'O'];
        $bloodPressures = ['120/80', '110/70', '130/90', '100/60', '140/90'];
        $heights = [150, 155, 160, 165, 170, 175, 180, 185];
        $weights = [45, 50, 55, 60, 65, 70, 75, 80, 85, 90];
        $allergiesList = ['Debu', 'Serbuk Sari', 'Makanan laut', 'Telur', 'Nuts', 'Tidak ada'];
        $medicalHistories = ['Flu', 'Demam Berdarah', 'Tifus', 'Tidak ada'];
        $specialConditions = ['Cacingan', 'Asma', 'Hipertensi', 'Diabetes', 'Tidak ada'];
        $notesList = [
            'Perlu pemeriksaan rutin',
            'Perlu perhatian khusus',
            'Sangat aktif dalam kegiatan ekstrakurikuler',
            'Perlu dukungan orang tua',
            'Sangat baik dalam akademik',
            'Perlu bimbingan konseling'
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
            
            $birthDate = now()->subYears(rand(16, 18))->subDays(rand(0, 364));
            
            Santri::create([
                'name' => $name,
                'nis' => '2026' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'gender' => $gender,
                'birth_place' => $birthPlaces[array_rand($birthPlaces)],
                'birth_date' => $birthDate,
                'class_id' => $class->id,
                'major_id' => $major->id,
                'class_room' => rand(1, 6) . rand(1, 99),
                'guardian_name' => 'Bpk/Ibu ' . $lastName,
                'guardian_phone' => '628' . rand(100000000, 999999999),
                'guardian_relationship' => ['Orang tua', 'Wali', 'Kakek', 'Nenek'][array_rand([0, 1, 2, 3])],
                'guardian_job' => ['Guru', 'Dokter', 'Teknik', 'Wiraswasta', 'Pensiunan'][array_rand([0, 1, 2, 3, 4])],
                'guardian_address' => rand(1, 5) . ' Jalan Mawar No. ' . rand(1, 100) . ', ' . ['Jakarta', 'Bandung', 'Surabaya'][array_rand([0, 1, 2])],
                'blood_type' => $bloodTypes[array_rand($bloodTypes)],
                'blood_pressure' => $bloodPressures[array_rand($bloodPressures)],
                'height' => $heights[array_rand($heights)],
                'weight' => $weights[array_rand($weights)],
                'allergies' => $allergiesList[array_rand($allergiesList)],
                'medical_history' => $medicalHistories[array_rand($medicalHistories)],
                'special_condition' => $specialConditions[array_rand($specialConditions)],
                'notes' => $notesList[array_rand($notesList)],
                'photo_path' => 'https://randomuser.me/api/portraits/' . ($gender === 'L' ? 'men' : 'women') . '/' . rand(1, 99) . '.jpg',
            ]);
        }
    }
}
