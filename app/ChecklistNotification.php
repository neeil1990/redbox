<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChecklistNotification extends Model
{
    protected $table = 'checklist_notification';

    protected $guarded = [];

    public function task(): HasOne
    {
        return $this->hasOne(ChecklistTasks::class, 'id', 'checklist_task_id');
    }
}
