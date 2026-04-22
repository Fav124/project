<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDormitoriesTable extends Migration
{
    public function up()
    {
        Schema::create('dormitories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('building')->nullable();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->string('supervisor_name')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('santris', function (Blueprint $table) {
            $table->foreignId('dormitory_id')->nullable()->after('major_id')->constrained()->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('santris', function (Blueprint $table) {
            $table->dropForeign(['dormitory_id']);
            $table->dropColumn('dormitory_id');
        });
        Schema::dropIfExists('dormitories');
    }
}
