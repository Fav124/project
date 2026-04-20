<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Buat Super Admin pertama.
     * Jalankan: php artisan db:seed --class=SuperAdminSeeder
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'superadmin@deisa.com'],
            [
                'name'       => 'Super Admin',
                'email'      => 'superadmin@deisa.com',
                'password'   => Hash::make('SuperAdmin@123'),
                'role'       => 'super_admin',
                'status'     => 'approved',
                'approved_at'=> now(),
            ]
        );

        $this->command->info('✅ Super Admin berhasil dibuat!');
        $this->command->info('   Email   : superadmin@deisa.com');
        $this->command->info('   Password: SuperAdmin@123');
        $this->command->warn('   ⚠️  Segera ganti password setelah login pertama!');
    }
}
