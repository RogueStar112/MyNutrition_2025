<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodSource extends Model
{
    protected $table = 'food_source';

    protected $fillable = ['name', 'user_id'];

    public $timestamps = false;
}
