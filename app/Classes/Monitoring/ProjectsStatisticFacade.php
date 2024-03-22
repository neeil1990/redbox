<?php


namespace App\Classes\Monitoring;


use Illuminate\Support\Facades\Auth;

class ProjectsStatisticFacade
{
    static public function getTodayProjects()
    {
        $user = Auth::user();

        $statistics = $user->statistics()->selectMonitoringProjectsToday()->first();

        if(!$statistics)
            return null;

        $projects = $statistics['monitoring_project'];

        return $projects;
    }

    static public function getMidTopPct(string $filedPct): float
    {
        $projects = self::getTodayProjects();

        if(!$projects)
            return 0;

        return round($projects->pluck($filedPct)->sum() / $projects->count(), 2);
    }

    static public function getMidMasteredBudgetPct(): float
    {
        $projects = self::getTodayProjects();

        if(!$projects)
            return 0;

        $budget = $projects->pluck('budget')->sum();
        $mastered = $projects->pluck('mastered')->sum();

        return round($mastered / ($budget / 100), 2);
    }
}
