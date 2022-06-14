<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonitoringSearchengine extends Model
{
    protected $fillable = ['engine', 'lr'];

    public function location()
    {
        return $this->hasOne(Location::class, 'lr', 'lr');
    }
}
