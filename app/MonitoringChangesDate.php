<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MonitoringChangesDate extends Model
{
    protected $guarded = [];

    protected $table = 'monitoring_changes_date';

    public function mainProject(): HasOne
    {
        return $this->hasOne(MonitoringProject::class, 'id', 'monitoring_project_id');
    }
}
