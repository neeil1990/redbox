<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RelevanceSharing extends Model
{
    protected $table = 'relevance_sharing';

    protected $guarded = [];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function project(): HasMany
    {
        return $this->hasMany(ProjectRelevanceHistory::class, 'id', 'project_id');
    }

    /**
     * @return HasOne
     */
    public function item(): HasOne
    {
        return $this->hasOne(ProjectRelevanceHistory::class, 'id', 'project_id');
    }

    /**
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }
}
