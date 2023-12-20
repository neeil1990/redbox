<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


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

    /**
     * @param $request
     * @return int
     */
    public static function createNewProject($request): int
    {
        $project = new Project();
        $project->project_name = $request->project_name;
        if (empty($request->short_description)) {

            $project->short_description = Str::limit(strip_tags($request->description), 70);
        } else {
            $project->short_description = $request->short_description;
        }
        $project->short_description = str_replace('&nbsp;', ' ', htmlentities($project->short_description));
        $project->short_description = html_entity_decode($project->short_description);

        $project->user_id = Auth::id();
        $project->save();

        return $project->id;
    }
}
