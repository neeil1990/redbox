<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonitoringOccurrence extends Model
{
    protected $fillable = ['monitoring_keyword_id', 'monitoring_searchengine_id', 'base', 'phrasal', 'exact'];
}
