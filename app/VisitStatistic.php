<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VisitStatistic extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function project(): HasOne
    {
        return $this->hasOne(MainProject::class, 'id', 'project_id');
    }
}