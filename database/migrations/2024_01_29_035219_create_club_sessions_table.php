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
        Schema::create('club_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_code')->unique();
            $table->string('schedule_code');
            $table->string('session_name');
            $table->date('date');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('schedule_code')->references('schedule_code')->on('club_schedules');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_sessions');
    }
};
