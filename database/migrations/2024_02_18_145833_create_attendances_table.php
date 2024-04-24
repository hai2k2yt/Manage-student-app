<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('session_code');
            $table->string('student_code');
            $table->unsignedInteger('present');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('session_code')->references('session_code')->on('club_sessions');
            $table->foreign('student_code')->references('student_code')->on('students');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
