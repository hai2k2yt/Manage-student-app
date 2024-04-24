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
        Schema::create('clubs', function (Blueprint $table) {

            $table->id();
            $table->string('club_code')->unique();
            $table->string('name');
            $table->string('teacher_code')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('teacher_code')->references('teacher_code')->on('teachers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};
