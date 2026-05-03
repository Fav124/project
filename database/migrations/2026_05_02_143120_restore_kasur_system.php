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
        Schema::create('kasurs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kasur')->unique();
            $table->enum('status', ['tersedia', 'terisi', 'rusak'])->default('tersedia');
            $table->timestamps();
        });

        Schema::table('riwayat_perawatans', function (Blueprint $table) {
            $table->foreignId('kasur_id')->after('lokasi_perawatan')->nullable()->constrained('kasurs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('riwayat_perawatans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kasur_id');
        });
        Schema::dropIfExists('kasurs');
    }
};
