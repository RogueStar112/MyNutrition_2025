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
        Schema::create('recipe', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);
            $table->unsignedBigInteger('user_id');
            $table->string('subheading', 128);
            $table->timestamps();
            $table->string('description', 9999)->nullable();
        });

        Schema::table('recipe', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            // $table->foreign('source_id')->references('id')->on('food_source')->cascadeOnDelete();

        
        });
        
        Schema::create('recipe_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recipe_id');
            $table->unsignedBigInteger('food_id');
            $table->unsignedBigInteger('food_unit_id');
            $table->float('serving_size', 8, 1);
            $table->float('quantity', 8, 1);
            $table->timestamps();
            $table->string('description', 100)->nullable();
        });

        Schema::table('recipe_items', function(Blueprint $table) {
            $table->foreign('recipe_id')->references('id')->on('recipe')->cascadeOnDelete();
            $table->foreign('food_id')->references('id')->on('food')->cascadeOnDelete();
            $table->foreign('food_unit_id')->references('id')->on('food_unit')->cascadeOnDelete();

        
        });

        Schema::create('tag', function(Blueprint $table) {
            
            $table->id();
            $table->string('name', 64);
            $table->string('color_text', 7);
            $table->string('color_bg', 7);
            $table->timestamps();

        });



        Schema::create('recipe_tags', function(Blueprint $table) {

            // colour values are #FFFFFF for instance.
            $table->id();
            $table->unsignedBigInteger('recipe_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();

        });

        Schema::table('recipe_tags', function(Blueprint $table) {
            
            $table->foreign('tag_id')->references('id')->on('tag')->cascadeOnDelete();
            $table->foreign('recipe_id')->references('id')->on('recipe')->cascadeOnDelete();
            
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe');
        Schema::dropIfExists('recipe_items');
        Schema::dropIfExists('tag');
        Schema::dropIfExists('recipe_tags');
        
    }
};
