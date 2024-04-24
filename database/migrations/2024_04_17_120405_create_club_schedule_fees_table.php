<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('club_schedule_fees', function (Blueprint $table) {
            $table->id();
            $table->string('schedule_code')->nullable();
            $table->unsignedInteger('club_teacher_fee');
            $table->unsignedInteger('class_teacher_fee');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('schedule_code')->references('schedule_code')->on('club_schedules')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_schedule_fees');
    }
};
