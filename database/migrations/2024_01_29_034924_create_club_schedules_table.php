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
        Schema::create('club_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('schedule_code')->unique();
            $table->string('club_code');
            $table->string('teacher_code');
            $table->string('schedule_name');
            $table->string('day_of_week');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('club_code')->references('club_code')->on('clubs');
            $table->foreign('teacher_code')->references('teacher_code')->on('teachers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_schedules');
    }
};
