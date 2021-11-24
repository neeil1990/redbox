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
        'length_title_min',
        'length_title_max',
        'length_description_min',
        'length_description_max',
        'length_keywords_min',
        'length_keywords_max',
    ];
}
