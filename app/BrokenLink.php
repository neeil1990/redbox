<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrokenLink extends Model
{
    protected $primaryKey = 'link_tracking_id';

    protected $table = 'broken_link';

    protected $guarded = [];

    /**
     * @return BelongsTo
     */
    public function linkTracking(): BelongsTo
    {
        return $this->belongsTo(LinkTracking::class);
    }
}
