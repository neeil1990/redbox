<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetaTagsHistory extends Model
{
    protected $fillable = ['meta_tag_id', 'ideal', 'quantity', 'data'];

    protected $dates = ['created_at'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(MetaTag::class, 'meta_tag_id');
    }
}
