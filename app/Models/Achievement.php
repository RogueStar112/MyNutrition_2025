<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Meal;
use App\Models\User;
use App\Models\Macronutrients;
use App\Models\Micronutrients;
use App\Models\FoodSource;

class Achievement extends Model
{
    use HasFactory;

    protected $table = 'achievements';
}
