<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AiGenerationStopWordCategory extends Model
{
    protected $fillable = ['user_id', 'name'];

    public function words()
    {
        return $this->hasMany(AiGenerationStopWords::class, 'category_id');
    }
}