<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rawat_inaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->cascadeOnDelete();
            $table->foreignId('kunjungan_id')->nullable()->constrained('kunjungans')->nullOnDelete();
            $table->foreignId('kasur_id')->nullable()->constrained('kasurs')->nullOnDelete();
            $table->datetime('tanggal_masuk');
            $table->datetime('tanggal_keluar')->nullable();
            $table->text('alasan_rawat');
            $table->text('kondisi_masuk');
            $table->text('kondisi_keluar')->nullable();
            $table->enum('status_rawat', ['aktif', 'selesai', 'pindah', 'dirujuk'])->default('aktif');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rawat_inaps');
    }
};
