<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * @property int user_id
 * @property string short_description
 * @property string description
 * @property string project_name
 */
class Project extends Model
{
    public $guarded = [];

    /**
     * @return HasMany
     */
    public function descriptions()
    {
        return $this->hasMany('App\ProjectDescription')
            ->orderBy('id', 'desc')
            ->latest('created_at')
            ->limit(20);
    }
}
