<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For kunjungans table
        DB::statement("ALTER TABLE kunjungans MODIFY COLUMN status_kunjungan ENUM('baru', 'dipantau', 'sembuh', 'dirujuk', 'rawat_inap', 'pulang') DEFAULT 'baru'");

        // For kasus_sakits table
        DB::statement("ALTER TABLE kasus_sakits MODIFY COLUMN status_kasus ENUM('aktif', 'sembuh', 'selesai', 'pulang') DEFAULT 'aktif'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE kunjungans MODIFY COLUMN status_kunjungan ENUM('baru', 'dipantau', 'sembuh', 'dirujuk', 'rawat_inap') DEFAULT 'baru'");
        DB::statement("ALTER TABLE kasus_sakits MODIFY COLUMN status_kasus ENUM('aktif', 'sembuh', 'selesai') DEFAULT 'aktif'");
    }
};
