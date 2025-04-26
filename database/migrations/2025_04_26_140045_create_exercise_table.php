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
        Schema::create('exercise', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('exercise_type_id');
            $table->float('distance');
            $table->float('duration');
            $table->timestamp('exercise_start');

        });

        Schema::create('exercise_type', function (Blueprint $table) {

            $table->id();
            $table->string('name', 16);

        });

        DB::table('exercise_type')->insert([

            ['name' => 'walk'],
            ['name' => 'run'],
            ['name' => 'cycle']

        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise');
    }
};
