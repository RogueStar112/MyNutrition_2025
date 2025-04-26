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
        Schema::create('user_health_logs', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->float('weight', 5, 2)->nullable();
            $table->float('height', 5, 2)->nullable();
            $table->float('bmi', 3, 1)->nullable();
            $table->float('bodyfat', 3, 1)->nullable();
            $table->timestamp('time_updated');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_health_logs');
    }
};
