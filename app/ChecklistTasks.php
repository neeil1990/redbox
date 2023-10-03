<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChecklistTasks extends Model
{
    protected $guarded = [];

    protected $table = 'checklist_tasks';

    public function project(): HasOne
    {
        return $this->hasOne(CheckLists::class, 'id', 'project_id');
    }
}
