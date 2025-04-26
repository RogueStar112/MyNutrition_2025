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
        Schema::create('micronutrients', function (Blueprint $table) {
            $table->id();
            $table->integer('food_id');
            $table->float('sugars', 8, 1)->nullable();
            $table->float('saturates', 8, 1)->nullable();
            $table->float('fibre', 8, 1)->nullable();
            $table->float('salt', 8, 1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('micronutrients');
    }
};
