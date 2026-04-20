<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyClassesMajorRelationship extends Migration
{
    public function up()
    {
        // Remove old column major_id
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['major_id']);
            $table->dropColumn('major_id');
        });

        // Create pivot table major_school_class
        Schema::create('major_school_class', function (Blueprint $table) {
            $table->id();
            $table->foreignId('major_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained('classes')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('major_school_class');

        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('major_id')->nullable()->constrained()->nullOnDelete();
        });
    }
}
