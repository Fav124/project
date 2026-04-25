<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('email');
            $table->string('job_title')->nullable()->after('phone');
            $table->string('profile_photo_path')->nullable()->after('job_title');
            $table->text('address')->nullable()->after('profile_photo_path');
            $table->text('bio')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'job_title',
                'profile_photo_path',
                'address',
                'bio',
            ]);
        });
    }
};
