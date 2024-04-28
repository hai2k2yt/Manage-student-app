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
        Schema::create('club_enrollment_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_enrollment_id')->nullable();
            $table->date('from');
            $table->date('to')->nullable();
            $table->integer('status');
            $table->timestamps();

            $table->foreign('club_enrollment_id')->references('id')->on('club_enrollments')->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_enrollment_histories');
    }
};
