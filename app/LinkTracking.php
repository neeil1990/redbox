<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinkTracking extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'link_tracking';

    protected $guarded = [];

    /**
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectTracking::class, 'project_tracking_id','id');
    }
}
