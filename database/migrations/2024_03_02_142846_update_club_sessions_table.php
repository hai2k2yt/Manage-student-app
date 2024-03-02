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
        Schema::table('club_sessions', function (Blueprint $table) {
            // Xóa cột club_id
            $table->dropForeign(['club_id']);
            $table->dropColumn('club_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('club_sessions', function (Blueprint $table) {
            // Tạo lại cột club_id
            $table->uuid('club_id');
            $table->foreign('club_id')->references('id')->on('clubs');
        });
    }
};
