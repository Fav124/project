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
        if (Schema::hasColumn('riwayat_perawatans', 'kasur_id')) {
            Schema::table('riwayat_perawatans', function (Blueprint $table) {
                $table->dropForeign(['kasur_id']);
                $table->dropColumn('kasur_id');
            });
        }

        if (Schema::hasColumn('rawat_inaps', 'kasur_id')) {
            Schema::table('rawat_inaps', function (Blueprint $table) {
                $table->dropForeign(['kasur_id']);
                $table->dropColumn('kasur_id');
            });
        }

        Schema::dropIfExists('kasurs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not needed for dropping
    }
};
