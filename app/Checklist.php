<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Checklist extends Model
{
    protected $guarded = [];

    protected $table = 'checklist_projects';

    public function tasks(): HasMany
    {
        return $this->hasMany(ChecklistTasks::class, 'project_id', 'id');
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(CheckListsLabels::class, 'checklist_project_checklist_label', 'checklist_project_id', 'checklist_label_id');
    }
}
