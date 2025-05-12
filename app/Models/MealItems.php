<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealItems extends Model
{
    use HasFactory;

    protected $table = 'meal_items';

    protected $fillable = ['name', 'meal_id', 'food_id', 'food_unit_id', 'serving_size', 'quantity', 'created_at', 'updated_at'];


    public $timestamps = true;

}
