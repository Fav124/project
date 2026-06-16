<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // keluhan <-> sickness_case pivot
        Schema::create('keluhan_sickness_case', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sickness_case_id');
            $table->unsignedBigInteger('keluhan_id');
            $table->timestamps();

            $table->foreign('sickness_case_id')->references('id')->on('sickness_cases')->onDelete('cascade');
            $table->foreign('keluhan_id')->references('id')->on('keluhans')->onDelete('cascade');
            $table->unique(['sickness_case_id', 'keluhan_id']);
        });

        // diagnosa <-> sickness_case pivot
        Schema::create('diagnosa_sickness_case', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sickness_case_id');
            $table->unsignedBigInteger('diagnosa_id');
            $table->timestamps();

            $table->foreign('sickness_case_id')->references('id')->on('sickness_cases')->onDelete('cascade');
            $table->foreign('diagnosa_id')->references('id')->on('diagnosas')->onDelete('cascade');
            $table->unique(['sickness_case_id', 'diagnosa_id']);
        });

        // tindakan <-> sickness_case pivot
        Schema::create('tindakan_sickness_case', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sickness_case_id');
            $table->unsignedBigInteger('tindakan_id');
            $table->timestamps();

            $table->foreign('sickness_case_id')->references('id')->on('sickness_cases')->onDelete('cascade');
            $table->foreign('tindakan_id')->references('id')->on('tindakans')->onDelete('cascade');
            $table->unique(['sickness_case_id', 'tindakan_id']);
        });

        // Add discharge_guardian_id to sickness_cases if not exists
        if (!Schema::hasColumn('sickness_cases', 'discharge_guardian_id')) {
            Schema::table('sickness_cases', function (Blueprint $table) {
                $table->unsignedBigInteger('discharge_guardian_id')->nullable()->after('discharge_notes');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('keluhan_sickness_case');
        Schema::dropIfExists('diagnosa_sickness_case');
        Schema::dropIfExists('tindakan_sickness_case');

        if (Schema::hasColumn('sickness_cases', 'discharge_guardian_id')) {
            Schema::table('sickness_cases', function (Blueprint $table) {
                $table->dropColumn('discharge_guardian_id');
            });
        }
    }
};
