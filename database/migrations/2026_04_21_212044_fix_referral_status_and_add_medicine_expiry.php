<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixReferralStatusAndAddMedicineExpiry extends Migration
{
    public function up()
    {
        // 1. Add expiry_date to medicines if not exists
        if (!Schema::hasColumn('medicines', 'expiry_date')) {
            Schema::table('medicines', function (Blueprint $table) {
                $table->date('expiry_date')->nullable()->after('minimum_stock');
            });
        }

        // 2. Safely fix hospital_referrals status enum
        DB::statement("ALTER TABLE hospital_referrals MODIFY COLUMN status VARCHAR(50)");
        DB::table('hospital_referrals')->where('status', 'referred')->update(['status' => 'pending']);
        DB::table('hospital_referrals')->where('status', 'treated')->update(['status' => 'ongoing']);
        DB::table('hospital_referrals')->where('status', 'returned')->update(['status' => 'completed']);
        DB::table('hospital_referrals')->whereNotIn('status', ['pending', 'ongoing', 'completed'])->update(['status' => 'pending']);
        DB::statement("ALTER TABLE hospital_referrals MODIFY COLUMN status ENUM('pending', 'ongoing', 'completed') DEFAULT 'pending'");
        
        // 3. Rename complaint to reason using raw SQL to avoid Doctrine dependency
        if (Schema::hasColumn('hospital_referrals', 'complaint')) {
            DB::statement("ALTER TABLE hospital_referrals CHANGE complaint reason TEXT");
        }
    }

    public function down()
    {
        if (Schema::hasColumn('medicines', 'expiry_date')) {
            Schema::table('medicines', function (Blueprint $table) {
                $table->dropColumn('expiry_date');
            });
        }

        DB::statement("ALTER TABLE hospital_referrals MODIFY COLUMN status VARCHAR(50)");
        DB::table('hospital_referrals')->where('status', 'pending')->update(['status' => 'referred']);
        DB::table('hospital_referrals')->where('status', 'ongoing')->update(['status' => 'treated']);
        DB::table('hospital_referrals')->where('status', 'completed')->update(['status' => 'returned']);
        DB::statement("ALTER TABLE hospital_referrals MODIFY COLUMN status ENUM('referred', 'treated', 'returned') DEFAULT 'referred'");

        if (Schema::hasColumn('hospital_referrals', 'reason')) {
            DB::statement("ALTER TABLE hospital_referrals CHANGE reason complaint TEXT");
        }
    }
}
