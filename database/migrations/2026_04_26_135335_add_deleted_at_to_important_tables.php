<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('santris', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('obats', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('kunjungans', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('pemberian_obats', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('rawat_inaps', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('wali_santris', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('kesehatan_santris', function (Blueprint $table) { $table->softDeletes(); });
    }

    public function down(): void
    {
        Schema::table('santris', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('obats', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('kunjungans', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('pemberian_obats', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('rawat_inaps', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('wali_santris', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('kesehatan_santris', function (Blueprint $table) { $table->dropSoftDeletes(); });
    }
};
