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
        Schema::table('kelas', function (Blueprint $table) {
            $table->string('warna')->default('#6c757d')->after('nama_kelas');
        });

        Schema::table('jurusans', function (Blueprint $table) {
            $table->string('warna')->default('#6c757d')->after('nama_jurusan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropColumn('warna');
        });

        Schema::table('jurusans', function (Blueprint $table) {
            $table->dropColumn('warna');
        });
    }
};
