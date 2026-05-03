<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keluhan_masters', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->string('kategori')->nullable()->comment('Kepala, Perut, Dada, Umum, dll');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('keluhan_kunjungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kunjungan_id')->constrained('kunjungans')->cascadeOnDelete();
            $table->foreignId('keluhan_master_id')->constrained('keluhan_masters')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keluhan_kunjungan');
        Schema::dropIfExists('keluhan_masters');
    }
};
