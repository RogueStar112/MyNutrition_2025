<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Meal;
use App\Models\User;

class Food extends Model
{
    use HasFactory;

    protected $table = 'food';

    // protected $guarded = ['id']; 

    protected $fillable = ['name', 'user_id', 'source_id', 'created_at', 'updated_at', 'deleted_at', 'icon_code', 'description'];


    public $timestamps = true;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function meals(): hasMany

    {
        return $this->hasMany(Meal::class);
    }

    

    
}
