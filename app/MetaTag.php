<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function histories(): HasMany
    {
        return $this->hasMany(MetaTagsHistory::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
