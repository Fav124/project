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
        Schema::table('rawat_inaps', function (Blueprint $table) {
            $table->enum('tipe_rawat', ['uks', 'rs', 'pulang'])->default('uks')->after('kasur_id');
            $table->datetime('estimasi_kembali')->nullable()->after('tanggal_masuk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rawat_inaps', function (Blueprint $table) {
            $table->dropColumn(['tipe_rawat', 'estimasi_kembali']);
        });
    }
};
