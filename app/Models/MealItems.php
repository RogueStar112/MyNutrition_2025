<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Meal;
use App\Models\Macronutrients;

class MealItems extends Model
{
    use HasFactory;

    protected $table = 'meal_items';

    protected $fillable = ['name', 'meal_id', 'food_id', 'food_unit_id', 'serving_size', 'quantity', 'created_at', 'updated_at'];


    public $timestamps = true;


    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }

    public function macronutrient()
    {
        return $this->belongsTo(Macronutrient::class);
    }
}
