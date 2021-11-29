<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MetaTagsHistory extends Model
{
    protected $fillable = ['meta_tag_id', 'ideal', 'quantity', 'data'];

    protected $dates = ['created_at'];


    public function project()
    {
        return $this->belongsTo(MetaTag::class, 'meta_tag_id');
    }
}
