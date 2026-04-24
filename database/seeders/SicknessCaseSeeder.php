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

        $complaints = [
            'Demam tinggi dan batuk berdahak sejak semalam.',
            'Sakit perut melilit dan diare (5x hari ini).',
            'Kepala pusing berputar dan mual.',
            'Luka lecet pada lutut karena jatuh saat olahraga.',
            'Gatal-gatal di seluruh tubuh setelah makan ikan.',
            'Nyeri pada bagian gigi belakang.',
            'Sesak nafas ringan karena asma kambuh.',
            'Mata merah dan terasa perih.',
        ];

        $diagnoses = [
            'Influenza / Common Cold',
            'Gastroenteritis Akut',
            'Vertigo Ringan',
            'Vulnus Excoriatio (Luka Lecet)',
            'Urtikaria (Alergi Makanan)',
            'Pulpitis (Sakit Gigi)',
            'Asma Bronkial (Eksaserbasi)',
            'Konjungtivitis',
        ];

        // 1. Recovered Cases (History)
        foreach ($santris->slice(0, 15) as $index => $santri) {
            $case = SicknessCase::create([
                'santri_id' => $santri->id,
                'visit_date' => now()->subDays(rand(10, 30)),
                'return_date' => now()->subDays(rand(5, 9)),
                'complaint' => $complaints[array_rand($complaints)],
                'diagnosis' => $diagnoses[array_rand($diagnoses)],
                'status' => 'recovered',
                'handled_by' => 1,
                'notes' => 'Sudah diberikan perawatan dan sembuh total.',
            ]);

            // Add 1-2 random medicines
            $randomMeds = $medicines->random(rand(1, 2));
            foreach ($randomMeds as $med) {
                $case->medicines()->attach($med->id, [
                    'quantity' => rand(1, 5),
                    'status' => 'taken'
                ]);
            }
        }

        // 2. Active Cases (Current)
        foreach ($santris->slice(15, 5) as $index => $santri) {
            $bed = $beds->where('status', 'available')->first();
            
            $case = SicknessCase::create([
                'santri_id' => $santri->id,
                'visit_date' => now()->subDays(rand(0, 2)),
                'complaint' => $complaints[array_rand($complaints)],
                'diagnosis' => $diagnoses[array_rand($diagnoses)],
                'status' => rand(0, 1) ? 'observed' : 'handled',
                'infirmary_bed_id' => $bed ? $bed->id : null,
                'handled_by' => 1,
            ]);

            if ($bed) {
                $bed->update([
                    'status' => 'occupied',
                    'occupant_name' => $santri->name
                ]);
            }

            // Add medicines
            $randomMeds = $medicines->random(rand(1, 3));
            foreach ($randomMeds as $med) {
                $case->medicines()->attach($med->id, [
                    'quantity' => rand(1, 3),
                    'status' => rand(0, 1) ? 'pending' : 'taken'
                ]);
            }
        }
    }
}
