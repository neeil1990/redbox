<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AiGenerationStopWords extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(AiGenerationStopWordCategory::class, 'category_id');
    }
}
