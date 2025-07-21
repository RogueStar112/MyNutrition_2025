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
        Schema::create('user_types', function (Blueprint $table) {

            $table->id();
            $table->string('name');

        });

        DB::table('user_types')->insert([

            /* 
               user type id and their respective ids:

               1 - admin
               2 - user
               3 - premium_user
               4 - trial_premium_user
               5 - tester
               6 - trusted

            */

               
            ['name' => 'admin'],
            ['name' => 'user'],
            ['name' => 'premium_user'],
            ['name' => 'trial_premium_user'],
            ['name' => 'tester'],
            ['name' => 'trusted_user']

        ]);

        Schema::table('users', function (Blueprint $table) {

            
            $table->unsignedBigInteger('user_type_id')->references('id')->on('user_types')->default(2);



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
