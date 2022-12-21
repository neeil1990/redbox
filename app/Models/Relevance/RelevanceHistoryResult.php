<?php

namespace App\Models\Relevance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RelevanceHistoryResult extends Model
{
    protected $guarded = [];

    protected $table = 'relevance_history_result';

    /**
     * @return HasOne
     */
    public function mainHistory(): HasOne
    {
        return $this->hasOne(RelevanceHistory::class, 'id', 'project_id');
    }
}
