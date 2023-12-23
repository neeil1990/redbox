<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int project_id
 * @property string description
 */
class ProjectDescription extends Model
{
    public $table = 'project_description';

    public $guarded = [];

    public static function storeDescriptionProject($description, $projectId)
    {
        $projectDescription = new ProjectDescription();
        $projectDescription->description = $description;
        $projectDescription->project_id = $projectId;
        $projectDescription->save();
    }
}
