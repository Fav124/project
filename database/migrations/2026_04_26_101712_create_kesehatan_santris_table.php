<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kesehatan_santris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->cascadeOnDelete();
            $table->string('golongan_darah')->nullable();
            $table->text('alergi')->nullable();
            $table->text('riwayat_penyakit')->nullable();
            $table->text('kondisi_khusus')->nullable();
            $table->decimal('tinggi_badan', 5, 2)->nullable()->comment('dalam cm');
            $table->decimal('berat_badan', 5, 2)->nullable()->comment('dalam kg');
            $table->string('tekanan_darah')->nullable();
            $table->text('catatan_kesehatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kesehatan_santris');
    }
};
