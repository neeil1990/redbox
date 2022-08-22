<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MonitoringStat extends Model
{
    protected $fillable = ['queue', 'queue_id', 'model_class', 'model_id', 'errors'];

    public function scopeWithoutErrors($query)
    {
        return $query->where('errors', false);
    }

    public function scopeWithErrors($query)
    {
        return $query->where('errors', true);
    }

    public function scopeCurrentDay($query)
    {
        return $query->where('created_at', '>', Carbon::now()->startOfDay());
    }

    public function scopeCurrentMonth($query)
    {
        return $query->where('created_at', '>', Carbon::now()->startOfMonth());
    }
}
