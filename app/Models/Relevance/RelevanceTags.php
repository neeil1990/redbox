<?php

namespace App\Models\Relevance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RelevanceTags extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected $table = 'relevance_tags';

    /**
     * @return BelongsToMany
     */
    public function relevanceHistory(): BelongsToMany
    {
        return $this->belongsToMany(ProjectRelevanceHistory::class);
    }
}
