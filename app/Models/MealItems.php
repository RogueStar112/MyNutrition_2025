<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealItems extends Model
{
    use HasFactory;

    protected $table = 'meal_items';

    public $timestamps = true;

}
