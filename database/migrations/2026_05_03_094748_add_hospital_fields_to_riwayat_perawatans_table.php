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
            $table->string('nama_rs')->nullable()->after('lokasi_perawatan');
            $table->text('info_rs')->nullable()->after('nama_rs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_perawatans', function (Blueprint $table) {
            $table->dropColumn(['nama_rs', 'info_rs']);
        });
    }
};
