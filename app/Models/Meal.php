<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Food;
use App\Models\User;
use App\Models\MealItems;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meal extends Model
{
    use HasFactory;

    protected $table = 'meal';

    protected $guarded = ['id']; 

    public $timestamps = true;
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function foods(): HasMany
    {
        return $this->hasMany(Food::class);
    }

    public function mealItems(): hasMany 
    {
        return $this->hasMany(MealItems::class);
    }
}
