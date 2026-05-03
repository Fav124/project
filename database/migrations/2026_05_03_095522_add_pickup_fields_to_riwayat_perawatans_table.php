<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('riwayat_perawatans', function (Blueprint $table) {
            $table->string('penjemput')->nullable()->after('info_rs');
            $table->string('kontak_penjemput')->nullable()->after('penjemput');
            $table->string('hubungan_penjemput')->nullable()->after('kontak_penjemput');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_perawatans', function (Blueprint $table) {
            $table->dropColumn(['penjemput', 'kontak_penjemput', 'hubungan_penjemput']);
        });
    }
};
