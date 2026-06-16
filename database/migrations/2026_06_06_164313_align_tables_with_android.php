<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AlignTablesWithAndroid extends Migration
{
    public function up()
    {
        // 1. Update sickness_cases status enum to varchar to allow rawat_inap, and add new fields
        DB::statement("ALTER TABLE sickness_cases MODIFY COLUMN status VARCHAR(50) DEFAULT 'observed'");

        Schema::table('sickness_cases', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('notes');
            $table->string('hospital_name')->nullable()->after('photo_path');
            $table->string('transport')->nullable()->after('hospital_name');
            $table->string('companion_name')->nullable()->after('transport');
            $table->string('picked_up_by')->nullable()->after('companion_name');
            $table->timestamp('picked_up_at')->nullable()->after('picked_up_by');
            $table->text('discharge_notes')->nullable()->after('picked_up_at');
        });

        // 2. Update hospital_referrals status enum to varchar
        DB::statement("ALTER TABLE hospital_referrals MODIFY COLUMN status VARCHAR(50) DEFAULT 'referred'");

        // 3. Add medical and detail fields to santris
        Schema::table('santris', function (Blueprint $table) {
            $table->string('guardian_relationship')->nullable()->after('guardian_phone');
            $table->string('guardian_job')->nullable()->after('guardian_relationship');
            $table->text('guardian_address')->nullable()->after('guardian_job');
            $table->string('blood_type', 10)->nullable()->after('notes');
            $table->string('blood_pressure', 20)->nullable()->after('blood_type');
            $table->double('height')->nullable()->after('blood_pressure');
            $table->double('weight')->nullable()->after('height');
            $table->text('allergies')->nullable()->after('weight');
            $table->text('medical_history')->nullable()->after('allergies');
            $table->text('special_condition')->nullable()->after('medical_history');
            $table->string('photo_path')->nullable()->after('special_condition');
        });

        // 4. Add columns to medicines
        Schema::table('medicines', function (Blueprint $table) {
            $table->string('code')->nullable()->after('id');
            $table->string('category')->nullable()->after('name');
            $table->string('formulation')->nullable()->after('category');
            $table->string('location')->nullable()->after('formulation');
        });

        // 5. Create guardians table
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('relationship');
            $table->string('phone');
            $table->text('address')->nullable();
            $table->string('job')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 6. Create settings table
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // 7. Create medicine_mutations table
        Schema::create('medicine_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // 'in' or 'out'
            $table->integer('amount');
            $table->integer('before_stock');
            $table->integer('after_stock');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 8. Create lookups tables (keluhans, diagnosas, tindakans)
        Schema::create('keluhans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('diagnosas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('tindakans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tindakans');
        Schema::dropIfExists('diagnosas');
        Schema::dropIfExists('keluhans');
        Schema::dropIfExists('medicine_mutations');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('guardians');

        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn(['code', 'category', 'formulation', 'location']);
        });

        Schema::table('santris', function (Blueprint $table) {
            $table->dropColumn([
                'guardian_relationship', 'guardian_job', 'guardian_address',
                'blood_type', 'blood_pressure', 'height', 'weight',
                'allergies', 'medical_history', 'special_condition', 'photo_path'
            ]);
        });

        DB::statement("ALTER TABLE hospital_referrals MODIFY COLUMN status ENUM('pending', 'ongoing', 'completed') DEFAULT 'pending'");

        Schema::table('sickness_cases', function (Blueprint $table) {
            $table->dropColumn([
                'photo_path', 'hospital_name', 'transport', 'companion_name',
                'picked_up_by', 'picked_up_at', 'discharge_notes'
            ]);
        });
        
        DB::statement("ALTER TABLE sickness_cases MODIFY COLUMN status ENUM('observed', 'handled', 'recovered', 'referred') DEFAULT 'observed'");
    }
}
