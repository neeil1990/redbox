<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonitoringKeywordPrice extends Model
{
    protected $fillable = [
        'monitoring_keyword_id',
        'monitoring_searchengine_id',
        'top1',
        'top3',
        'top5',
        'top10',
        'top20',
        'top50',
        'top100',
    ];
}
