<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kunjungans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Petugas Kesehatan');
            $table->datetime('tanggal_kunjungan');
            $table->string('keluhan_utama');
            $table->text('riwayat_keluhan')->nullable();
            $table->decimal('suhu', 4, 1)->nullable()->comment('Celcius');
            $table->string('tekanan_darah')->nullable()->comment('Contoh: 120/80');
            $table->integer('denyut_nadi')->nullable()->comment('bpm');
            $table->integer('pernapasan')->nullable()->comment('x/menit');
            $table->string('diagnosa_sementara')->nullable();
            $table->text('tindakan')->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status_kunjungan', ['baru', 'dipantau', 'sembuh', 'dirujuk', 'rawat_inap'])->default('baru');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungans');
    }
};
