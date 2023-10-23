<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonitoringDataTableColumnsProject extends Model
{
    protected $fillable = [
        'monitoring_project_id',
        'words',
        'middle',
        'mastered',
        'mastered_percent',
        'mastered_info',
        'top3',
        'diff_top3',
        'top5',
        'diff_top5',
        'top10',
        'diff_top10',
        'top30',
        'diff_top30',
        'top100',
        'diff_top100'
    ];

    protected $casts = [
        'mastered_info' => 'collection',
    ];
}
