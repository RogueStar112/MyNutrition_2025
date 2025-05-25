<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tag';

    protected $fillable = ['name', 'color_text', 'color_bg', 'created_at', 'updated_at', 'user_id'];
}
