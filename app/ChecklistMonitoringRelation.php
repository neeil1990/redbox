<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChecklistMonitoringRelation extends Model
{
    protected $table = 'checklist_relation_with_monitoring';

    protected $guarded = [];

    public $timestamps = false;

    public function checklist(): HasOne
    {
        return $this->hasOne(Checklist::class, 'id', 'checklist_id');
    }
}
