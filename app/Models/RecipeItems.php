<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Macronutrients;


class RecipeItems extends Model
{

    protected $fillable = ['recipe_id', 'food_id', 'food_unit_id', 'serving_size', 'quantity', 'created_at', 'updated_at', 'description'];


    public function macronutrient()
    {
        return $this->belongsTo(Macronutrient::class);
    }
}
