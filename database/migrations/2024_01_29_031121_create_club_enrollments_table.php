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
        Schema::create('club_enrollments', function (Blueprint $table) {
            $table->id();
            $table->string('student_code');
            $table->string('club_code');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('student_code')->references('student_code')->on('students');
            $table->foreign('club_code')->references('club_code')->on('clubs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_enrollments');
    }
};
