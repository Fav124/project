<?php

namespace Database\Seeders;

use App\Models\Dormitory;
use Illuminate\Database\Seeder;

class DormitorySeeder extends Seeder
{
    public function run()
    {
        $dorms = [
            ['name' => 'Abu Bakar Ash-Shiddiq', 'building' => 'Gedung A', 'gender' => 'L', 'supervisor_name' => 'Ust. Ahmad Fauzi'],
            ['name' => 'Umar bin Khattab', 'building' => 'Gedung A', 'gender' => 'L', 'supervisor_name' => 'Ust. Ridwan Hakim'],
            ['name' => 'Utsman bin Affan', 'building' => 'Gedung B', 'gender' => 'L', 'supervisor_name' => 'Ust. Zulkifli'],
            ['name' => 'Ali bin Abi Thalib', 'building' => 'Gedung B', 'gender' => 'L', 'supervisor_name' => 'Ust. Hasan Basri'],
            ['name' => 'Khadijah Al-Kubra', 'building' => 'Gedung C', 'gender' => 'P', 'supervisor_name' => 'Usth. Siti Aminah'],
            ['name' => 'Aisyah bint Abu Bakar', 'building' => 'Gedung C', 'gender' => 'P', 'supervisor_name' => 'Usth. Fatimah'],
            ['name' => 'Fathimah Az-Zahra', 'building' => 'Gedung D', 'gender' => 'P', 'supervisor_name' => 'Usth. Zainab'],
        ];

        foreach ($dorms as $dorm) {
            Dormitory::create($dorm);
        }
    }
}
