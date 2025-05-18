<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Micronutrients extends Model
{
    //

    protected $fillable = ['food_id', 'sugars', 'saturates', 'fibre', 'salt'];

    public $timestamps = false;

}
