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
        Schema::table('students', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->change();

            $table->uuid('class_id')->nullable()->change();

//            // Add foreign key constraints with set null on delete
//            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
//            $table->foreign('class_id')->references('id')->on('classes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {


            $table->uuid('user_id')->nullable(false)->change();

            $table->uuid('class_id')->nullable(false)->change();
        });
    }
};
