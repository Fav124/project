<?php

namespace Database\Seeders;

use App\Models\Major;
use App\Models\SchoolClass;
use Illuminate\Database\Seeder;

class SchoolClassSeeder extends Seeder
{
    public function run()
    {
        $classes = ['X', 'XI', 'XII'];
        $suffix = ['1', '2', '3'];
        $majors = Major::all();

        foreach ($classes as $c) {
            foreach ($suffix as $s) {
                $schoolClass = SchoolClass::create([
                    'name' => "$c $s",
                    'description' => "Kelas $c bagian $s",
                ]);

                // Sync random majors
                $schoolClass->majors()->sync($majors->random(rand(1, 2))->pluck('id'));
            }
        }
    }
}
