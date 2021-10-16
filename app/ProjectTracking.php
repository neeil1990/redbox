<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectTracking extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'project_tracking';

    protected $guarded = [];

    /**
     * @return HasMany
     */
    public function link(): HasMany
    {
        return $this->hasMany(LinkTracking::class);
    }
}
