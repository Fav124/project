<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleUserSeeder::class,
        ]);

        // Seed basic reference data
        \App\Models\Kelas::firstOrCreate(['nama_kelas' => 'Kelas 1A', 'deskripsi' => 'Kelas Reguler']);
        \App\Models\Jurusan::firstOrCreate(['nama_jurusan' => 'IPA', 'deskripsi' => 'Ilmu Pengetahuan Alam']);
        \App\Models\Kamar::firstOrCreate([
            'nama_kamar' => 'Kamar Asrama Putra 01', 
            'kapasitas_kasur' => 4,
            'kasur_terisi' => 0
        ]);
    }
}
