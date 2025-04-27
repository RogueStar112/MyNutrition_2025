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
        Schema::create('macronutrients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('food_id');
            $table->unsignedBigInteger('food_unit_id');
            $table->float('serving_size', 8, 1);
            $table->float('calories', 8, 1)->nullable();
            $table->float('fat', 8, 1)->nullable();
            $table->float('carbohydrates', 8, 1)->nullable();
            $table->float('protein', 8, 1)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('macronutrients');
    }
};
