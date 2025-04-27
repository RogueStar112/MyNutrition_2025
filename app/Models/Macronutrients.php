<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Macronutrients extends Model
{
    protected $table = 'macronutrients';

    protected $fillable = ['food_id', 'food_unit_id', 'serving_size', 'calories', 'fat', 'carbohydrates', 'protein'];

    public $timestamps = false;
}
