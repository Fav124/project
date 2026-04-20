<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClassesAndMajorsToSantris extends Migration
{
    public function up()
    {
        Schema::create('majors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('major_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('santris', function (Blueprint $table) {
            $table->foreignId('class_id')->nullable()->after('birth_date')->constrained('classes')->nullOnDelete();
            $table->foreignId('major_id')->nullable()->after('class_id')->constrained('majors')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('santris', function (Blueprint $table) {
            $table->dropConstrainedForeignId('major_id');
            $table->dropConstrainedForeignId('class_id');
        });

        Schema::dropIfExists('classes');
        Schema::dropIfExists('majors');
    }
}
