<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CheckListsLabels extends Model
{
    protected $table = 'check_lists_labels';

    protected $guarded = [];

    public function checklists(): BelongsToMany
    {
        return $this->belongsToMany(CheckLists::class);
    }
}
