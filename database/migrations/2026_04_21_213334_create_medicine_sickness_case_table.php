<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicineSicknessCaseTable extends Migration
{
    public function up()
    {
        Schema::create('medicine_sickness_case', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sickness_case_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->enum('status', ['pending', 'taken'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medicine_sickness_case');
    }
}
