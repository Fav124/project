<?php

namespace Database\Seeders;

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

        $complaints = [
            'Demam tinggi dan batuk berdahak sejak semalam.',
            'Sakit perut melilit dan diare (5x hari ini).',
            'Kepala pusing berputar dan mual.',
            'Luka lecet pada lutut karena jatuh saat olahraga.',
            'Gatal-gatal di seluruh tubuh setelah makan ikan.',
            'Nyeri pada bagian gigi belakang.',
            'Sesak nafas ringan karena asma kambuh.',
            'Mata merah dan terasa perih.',
            'Badan lemas dan tidak nafsu makan.',
            'Sakit tenggorokan dan sulit menelan.',
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
            'Fatigue Syndrome',
            'Faringitis Akut',
        ];

        $actions = [
            'Istirahat total, minum obat pereda nyeri.',
            'Pantang makan pedas dan asam, beri ORS.',
            'Berikan obat anti mual dan istirahat.',
            'Bersihkan luka dengan antiseptik, balut dengan kasa steril.',
            'Berikan obat anti alergi, hindari pemicu.',
            'Rujuk ke dokter gigi untuk pemeriksaan lebih lanjut.',
            'Berikan inhaler, monitoring pernapasan.',
            'Teteskan obat mata, kompres dengan air dingin.',
            'Cek tanda vital, berikan multivitamin.',
            'Berikan obat kumur antiseptik, hindari makanan panas.',
        ];

        // 1. Recovered Cases (History)
        foreach ($santris->slice(0, 20) as $index => $santri) {
            $complaintIndex = array_rand($complaints);
            $case = SicknessCase::create([
                'santri_id' => $santri->id,
                'visit_date' => now()->subDays(rand(10, 45)),
                'return_date' => now()->subDays(rand(5, 9)),
                'complaint' => $complaints[$complaintIndex],
                'diagnosis' => $diagnoses[$complaintIndex],
                'action_taken' => $actions[$complaintIndex],
                'status' => 'recovered',
                'handled_by' => 1,
                'notes' => 'Sudah diberikan perawatan dan sembuh total. Santri kembali ke asrama.',
                'picked_up_by' => 'Bpk/Ibu ' . explode(' ', $santri->name)[1],
                'picked_up_at' => now()->subDays(rand(5, 9))->format('H:i'),
            ]);

            // Add 1-3 random medicines with specific details
            $randomMeds = $medicines->random(rand(1, 3));
            foreach ($randomMeds as $med) {
                $case->medicines()->attach($med->id, [
                    'quantity' => rand(1, 5),
                    'status' => 'taken',
                    'notes' => 'Diberikan sesuai dosis yang dianjurkan.',
                ]);
            }
        }

        // 2. Active Cases (Current)
        foreach ($santris->slice(20, 10) as $index => $santri) {
            $complaintIndex = array_rand($complaints);
            $status = rand(0, 1) ? 'observed' : 'handled';
            
            $case = SicknessCase::create([
                'santri_id' => $santri->id,
                'visit_date' => now()->subDays(rand(0, 3)),
                'complaint' => $complaints[$complaintIndex],
                'diagnosis' => $diagnoses[$complaintIndex],
                'action_taken' => $actions[$complaintIndex],
                'status' => $status,
                'handled_by' => 1,
                'notes' => $status === 'observed' 
                    ? 'Masih dalam observasi, monitoring tanda vital.' 
                    : 'Sudah diberikan pengobatan awal, monitoring lanjutan.',
            ]);

            // Add medicines with mixed status
            $randomMeds = $medicines->random(rand(1, 3));
            foreach ($randomMeds as $med) {
                $case->medicines()->attach($med->id, [
                    'quantity' => rand(1, 3),
                    'status' => rand(0, 1) ? 'pending' : 'taken',
                    'notes' => rand(0, 1) ? 'Obat harian' : 'Obat saat perlu',
                ]);
            }
        }

        // 3. Referred Cases (Hospital Referrals)
        foreach ($santris->slice(30, 5) as $index => $santri) {
            $complaintIndex = array_rand($complaints);
            
            $case = SicknessCase::create([
                'santri_id' => $santri->id,
                'visit_date' => now()->subDays(rand(1, 7)),
                'complaint' => $complaints[$complaintIndex],
                'diagnosis' => $diagnoses[$complaintIndex] . ' (Suspect)',
                'action_taken' => 'Rujuk ke rumah sakit untuk pemeriksaan lebih lanjut.',
                'status' => 'referred',
                'handled_by' => 1,
                'hospital_name' => ['RSUD Syarifah Ambami', 'RS Siloam', 'Puskesmas Bangkalan'][$index % 3],
                'transport' => ['Ambulans Pondok', 'Mobil Pribadi', 'Ojek Online'][$index % 3],
                'companion_name' => 'Bpk/Ibu ' . explode(' ', $santri->name)[1],
                'notes' => 'Telah dirujuk dengan surat rujukan resmi. Wali santri mendampingi.',
            ]);

            // Add medicines for referral cases
            $randomMeds = $medicines->random(rand(1, 2));
            foreach ($randomMeds as $med) {
                $case->medicines()->attach($med->id, [
                    'quantity' => rand(1, 2),
                    'status' => 'pending',
                    'notes' => 'Obat diberikan sebelum rujukan.',
                ]);
            }
        }
    }
}
