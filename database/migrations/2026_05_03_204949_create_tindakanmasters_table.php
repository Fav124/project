<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tindakan_masters', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->string('kategori')->nullable()->comment('Fisik, Farmakologi, Observasi, Rujukan, dll');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('tindakan_kunjungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kunjungan_id')->constrained('kunjungans')->cascadeOnDelete();
            $table->foreignId('tindakan_master_id')->constrained('tindakan_masters')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tindakan_kunjungan');
        Schema::dropIfExists('tindakan_masters');
    }
};
