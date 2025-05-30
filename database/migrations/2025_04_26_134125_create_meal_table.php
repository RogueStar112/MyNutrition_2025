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
        Schema::create('meal', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);
            $table->unsignedBigInteger('user_id');
            $table->dateTime('time_planned');
            $table->timestamps();
            $table->integer('is_eaten');
            $table->integer('is_notified')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal');
    }
};
