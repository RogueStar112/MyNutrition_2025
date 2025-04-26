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
        Schema::create('exercise_unit', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->float('base_value');
            $table->unsignedBigInteger('unit_type_id');
        });

        DB::table('exercise_unit')->insert([
            ['name' => 'kilometre', 'short_name' => 'km', 'base_value' => 1000, 'unit_type_id' => 1],
            ['name' => 'metre', 'short_name' => 'm', 'base_value' => 1, 'unit_type_id' => 1],
            ['name' => 'mile', 'short_name' => 'mi', 'base_value' => 1609, 'unit_type_id' => 1],

        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise_unit');
    }
};
