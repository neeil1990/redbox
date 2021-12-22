<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewsLikes extends Model
{
    /**
     * @var string
     */
    protected $table = 'news_likes';

    /**
     * @var array
     */
    protected $guarded = [];
}
