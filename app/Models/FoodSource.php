<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Food;

class FoodSource extends Model
{
    protected $table = 'food_source';

    protected $fillable = ['name', 'user_id'];

    public $timestamps = false;

    public function foods(): hasMany

    {
        return $this->hasMany(Food::class);
    }
}
