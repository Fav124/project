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
        // 1. Tabel Utama Kasus Sakit
        Schema::create('kasus_sakits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained()->onDelete('cascade');
            $table->foreignId('kunjungan_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status_kasus', ['aktif', 'sembuh', 'selesai'])->default('aktif');
            $table->string('diagnosa_terakhir')->nullable();
            $table->datetime('tanggal_mulai');
            $table->datetime('tanggal_selesai')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Tabel Riwayat Perawatan / Perpindahan Lokasi
        Schema::create('riwayat_perawatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kasus_sakit_id')->constrained('kasus_sakits')->onDelete('cascade');
            $table->enum('lokasi_perawatan', ['uks', 'rumah_sakit', 'rumah', 'pondok']);
            $table->datetime('tanggal_masuk');
            $table->datetime('tanggal_keluar')->nullable();
            $table->string('alasan_pindah')->nullable();
            $table->text('kondisi_masuk')->nullable();
            $table->text('kondisi_keluar')->nullable();
            $table->foreignId('kasur_id')->nullable()->constrained('kasurs')->onDelete('set null');
            $table->foreignId('petugas_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_perawatans');
        Schema::dropIfExists('kasus_sakits');
    }
};
