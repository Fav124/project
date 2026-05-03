<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnosas', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->nullable()->comment('Kode ICD-10 jika tersedia');
            $table->string('nama')->unique();
            $table->string('kategori')->nullable()->comment('Contoh: Penyakit Infeksi, Gangguan Pencernaan, dll');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pivot table for Kunjungan <-> Diagnosa (many-to-many)
        Schema::create('diagnosa_kunjungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kunjungan_id')->constrained('kunjungans')->cascadeOnDelete();
            $table->foreignId('diagnosa_id')->constrained('diagnosas')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnosa_kunjungan');
        Schema::dropIfExists('diagnosas');
    }
};
