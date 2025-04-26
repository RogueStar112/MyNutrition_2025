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
        //  Schema::table('meal_notifications', function($table) {
        //     $table->dropColumn('type');
        // });

        Schema::table('meal_notifications', function (Blueprint $table) {
            $table->integer('type')->default(1);
        });

        Schema::table('exercise', function (Blueprint $table) {
            $table->integer('calories_active')->nullable();
            $table->integer('calories_total')->nullable();
            $table->integer('average_bpm')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
