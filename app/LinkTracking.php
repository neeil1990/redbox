<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LinkTracking extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'link_tracking';

    protected $guarded = [];

    /**
     * @return HasOne
     */
    public function brokenLink(): HasOne
    {
        return $this->hasOne(BrokenLink::class);
    }
}
