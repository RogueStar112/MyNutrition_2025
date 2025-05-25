<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Food;
use App\Models\User;
use App\Models\MealItems;
use App\Models\Macronutrients;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Recipe extends Model
{
    protected $table = 'recipe';

    protected $fillable = ['name', 'user_id', 'subheading', 'created_at', 'updated_at', 'description', 'is_public'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
