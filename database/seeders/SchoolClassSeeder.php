<?php

namespace Database\Seeders;

use App\Models\Major;
use App\Models\SchoolClass;
use Illuminate\Database\Seeder;

class SchoolClassSeeder extends Seeder
{
    public function run()
    {
        $majors = Major::all();
        
        $levels = ['X', 'XI', 'XII'];
        
        foreach ($levels as $level) {
            foreach ($majors as $major) {
                $class = SchoolClass::create([
                    'name' => "$level " . $major->name,
                    'description' => "Kelas $level untuk peminatan " . $major->description,
                ]);
                
                $class->majors()->attach($major->id);
            }
        }
    }
}
