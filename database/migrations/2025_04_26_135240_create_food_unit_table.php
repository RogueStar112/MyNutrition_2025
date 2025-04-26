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
        Schema::create('food_unit', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->float('base_value');
            $table->unsignedBigInteger('unit_type_id');
        });

        DB::table('food_unit')->insert([
            ['name' => 'gram', 'short_name' => 'g', 'base_value' => 1, 'unit_type_id' => 1],
            ['name' => 'pound', 'short_name' => 'lb', 'base_value' => 454, 'unit_type_id' => 1],
            ['name' => 'kilogram', 'short_name' => 'kg', 'base_value' => 1000, 'unit_type_id' => 1],
            ['name' => 'miligram', 'short_name' => 'mg', 'base_value' => 0.001, 'unit_type_id' => 1],
            ['name' => 'piece', 'short_name' => 'pc', 'base_value' => 1, 'unit_type_id' => 2],
            ['name' => 'portion', 'short_name' => 'portion', 'base_value' => 1, 'unit_type_id' => 2],
            ['name' => 'slice', 'short_name' => 'slice', 'base_value' => 1, 'unit_type_id' => 2],
            ['name' => 'tablespoon', 'short_name' => 'tbsp', 'base_value' => 15, 'unit_type_id' => 3],
            ['name' => 'teaspoon', 'short_name' => 'tsp', 'base_value' => 5, 'unit_type_id' => 3],
            ['name' => 'mililitre', 'short_name' => 'ml', 'base_value' => 1, 'unit_type_id' => 4],
            ['name' => 'centilitre', 'short_name' => 'cl', 'base_value' => 10, 'unit_type_id' => 4],
            ['name' => 'litre', 'short_name' => 'l', 'base_value' => 1000, 'unit_type_id' => 4]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_unit');
    }
};
