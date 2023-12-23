<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewsNotification extends Model
{
    protected $table = 'news_notification';

    protected $guarded = [];

    public $timestamps = false;
}
