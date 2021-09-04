<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int project_id
 * @property text description
 */
class ProjectDescription extends Model
{
    public $table = 'project_description';

    public $guarded = [];
}
