<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            MajorSeeder::class,
            SchoolClassSeeder::class,
            SantriSeeder::class,
            MedicineSeeder::class,
            InfirmaryBedSeeder::class,
            SicknessCaseSeeder::class,
            HospitalReferralSeeder::class,
        ]);
    }
}
