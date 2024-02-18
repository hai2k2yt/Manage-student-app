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
        Schema::create('absence_report', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('club_session_id');
            $table->uuid('student_id');
            $table->string('reason');
            $table->enum('status', ['pending', 'approved', 'rejected']);

            $table->foreign('club_session_id')->references('id')->on('club_sessions');
            $table->foreign('student_id')->references('id')->on('students');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absence_report');
    }
};
