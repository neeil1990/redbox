<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jobs extends Model
{
    protected $guarded = [];

    protected $table = 'jobs';

    protected $casts = [
        'payload' => 'collection',
    ];

    public function scopePositionsQueue($query)
    {
        return $query->whereIn('queue', ['position_low', 'position_high']);
    }

    public function scopePositionsLowQueue($query)
    {
        return $query->where('queue', 'position_low');
    }

    public function scopePositionHighQueue($query)
    {
        return $query->where('queue', 'position_high');
    }
}
