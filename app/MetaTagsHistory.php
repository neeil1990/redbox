<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MetaTagsHistory extends Model
{
    protected $fillable = ['meta_tag_id', 'quantity', 'data'];

    protected $dates = ['created_at'];
}
