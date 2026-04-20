<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHealthManagementTables extends Migration
{
    public function up()
    {
        Schema::create('santris', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->nullable()->unique();
            $table->string('name');
            $table->enum('gender', ['L', 'P']);
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('class_room')->nullable();
            $table->string('dorm_room')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit')->default('strip');
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('minimum_stock')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('infirmary_beds', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('room_name')->default('UKS');
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            $table->string('occupant_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('record_date');
            $table->text('complaint');
            $table->string('diagnosis')->nullable();
            $table->text('treatment')->nullable();
            $table->string('blood_pressure')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->unsignedInteger('pulse')->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('sickness_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained()->cascadeOnDelete();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('medicine_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('infirmary_bed_id')->nullable()->constrained()->nullOnDelete();
            $table->date('visit_date');
            $table->text('complaint');
            $table->string('diagnosis')->nullable();
            $table->text('action_taken')->nullable();
            $table->text('medicine_notes')->nullable();
            $table->enum('status', ['observed', 'handled', 'recovered', 'referred'])->default('observed');
            $table->date('return_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('hospital_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained()->cascadeOnDelete();
            $table->foreignId('referred_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('hospital_name');
            $table->date('referral_date');
            $table->text('complaint');
            $table->string('diagnosis')->nullable();
            $table->string('transport')->nullable();
            $table->string('companion_name')->nullable();
            $table->enum('status', ['referred', 'treated', 'returned'])->default('referred');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hospital_referrals');
        Schema::dropIfExists('sickness_cases');
        Schema::dropIfExists('health_records');
        Schema::dropIfExists('infirmary_beds');
        Schema::dropIfExists('medicines');
        Schema::dropIfExists('santris');
    }
}
