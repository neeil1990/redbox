<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MetaTag extends Model
{
    protected $fillable = [
        'status',
        'name',
        'period',
        'links',
        'timeout',
        'title_min',
        'title_max',
        'description_min',
        'description_max',
        'keywords_min',
        'keywords_max',
    ];

    public function histories()
    {
        return $this->hasMany(MetaTagsHistory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
