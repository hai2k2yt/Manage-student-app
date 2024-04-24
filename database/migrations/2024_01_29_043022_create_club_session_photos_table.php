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
        Schema::create('club_session_photos', function (Blueprint $table) {
            $table->id();
            $table->string('session_code');
            $table->string('photo_url');
            $table->timestamps();

            $table->foreign('session_code')->references('session_code')->on('club_sessions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_session_photos');
    }
};
