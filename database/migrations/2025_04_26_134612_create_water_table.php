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
        Schema::create('fluid_type', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('water', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('fluid_id');
            $table->float('amount', 8, 1);
            $table->timestamp('time_taken');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('fluid_id')->references('id')->on('fluid_type');

        });

        DB::table('fluid_type')->insert([
            ['name' => 'water'],
            ['name' => 'coke'],
            ['name' => 'milk'],
            ['name' => 'fruit']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water');
    }
};
