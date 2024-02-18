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
        Schema::create('students', function (Blueprint $table) {

            $table->uuid('id')->primary();
            $table->string('name');
            $table->uuid('user_id');
            $table->uuid('class_id');

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('class_id')->references('id')->on('classes');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
