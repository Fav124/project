<?php

namespace Database\Seeders;

use App\Models\InfirmaryBed;
use App\Models\Medicine;
use App\Models\Santri;
use App\Models\SicknessCase;
use Illuminate\Database\Seeder;

class SicknessCaseSeeder extends Seeder
{
    public function run()
    {
        $santris = Santri::all();
        $medicines = Medicine::all();
        $beds = InfirmaryBed::all();

        foreach ($santris->take(8) as $index => $santri) {
            SicknessCase::create([
                'santri_id' => $santri->id,
                'visit_date' => now()->subDays(rand(0, 7)),
                'complaint' => 'Pusing dan demam ringan.',
                'diagnosis' => 'Kecapean / Influenza',
                'action_taken' => 'Istirahat dan pemberian paracetamol',
                'medicine_id' => $medicines->random()->id,
                'infirmary_bed_id' => $index < 2 ? $beds[$index]->id : null,
                'status' => $index < 2 ? 'observed' : 'recovered',
            ]);
            
            if ($index < 2) {
                $beds[$index]->update(['status' => 'occupied']);
            }
        }
    }
}
