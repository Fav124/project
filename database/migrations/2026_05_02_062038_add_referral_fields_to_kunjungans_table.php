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
        Schema::table('kunjungans', function (Blueprint $table) {
            $table->string('nama_rs')->nullable()->after('status_kunjungan');
            $table->string('transportasi')->nullable()->after('nama_rs');
            $table->string('nama_pendamping')->nullable()->after('transportasi');
            $table->dateTime('tanggal_rujukan')->nullable()->after('nama_pendamping');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kunjungans', function (Blueprint $table) {
            $table->dropColumn(['nama_rs', 'transportasi', 'nama_pendamping', 'tanggal_rujukan']);
        });
    }
};
