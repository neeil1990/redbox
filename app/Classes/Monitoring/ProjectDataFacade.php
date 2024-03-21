<?php


namespace App\Classes\Monitoring;

use Illuminate\Database\Eloquent\Collection;

class ProjectDataFacade
{
    static public function projectsExtension(Collection $projects): void
    {
        $projects->transform(function($item){
            (new ProjectData($item))->extension();
            return $item;
        });
    }
}
