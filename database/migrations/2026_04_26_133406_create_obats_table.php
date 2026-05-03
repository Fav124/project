<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obats', function (Blueprint $table) {
            $table->id();
            $table->string('kode_obat')->unique();
            $table->string('nama_obat');
            $table->string('kategori')->comment('Analgesik, Antibiotik, dll. Menggunakan string untuk fleksibilitas di MVP');
            $table->string('bentuk_sediaan')->comment('Tablet, Sirup, Salep, dll');
            $table->string('satuan')->comment('Strip, Botol, Tube, Box');
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(10);
            $table->date('tanggal_kadaluarsa');
            $table->string('nomor_batch')->nullable();
            $table->string('lokasi_penyimpanan')->nullable();
            $table->text('deskripsi')->nullable();
            // Catatan: status_obat tidak disimpan di database agar tidak ada anomali. 
            // Dihitung melalui model (Computed Attribute).
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obats');
    }
};
