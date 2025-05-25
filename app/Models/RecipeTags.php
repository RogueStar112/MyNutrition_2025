<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Tags;

class RecipeTags extends Model
{
    protected $table = 'recipe_tags';

    protected $fillable = ['recipe_id', 'tag_id', 'created_at', 'updated_at'];

    public $timestamps = true;

    public function tags(): hasMany {
        return $this->hasMany(Tags::class);
    }

}
