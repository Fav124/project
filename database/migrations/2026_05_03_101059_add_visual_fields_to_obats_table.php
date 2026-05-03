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
        Schema::table('obats', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('nama_obat');
            $table->string('golongan')->default('bebas')->after('kategori'); // bebas, bebas_terbatas, keras, narkotika, psikotropika
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obats', function (Blueprint $table) {
            $table->dropColumn(['foto', 'golongan']);
        });
    }
};
