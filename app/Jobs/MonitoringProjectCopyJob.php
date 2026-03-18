<?php

namespace App\Jobs;

use App\Events\MonitoringProjectCopyProgress;
use App\Events\MonitoringProjectCreated;
use App\MonitoringProject;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MonitoringProjectCopyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $newProject;
    public $originalProject;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $newProject, $originalProject)
    {
        $this->userId = $userId;
        $this->newProject = $newProject;
        $this->originalProject = $originalProject;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $newProject = $this->newProject;
        $original = $this->originalProject;

        $groupIds = [];
        foreach ($original->groups as $groups) {
            $newGroup = $groups->replicate();
            $newGroup->monitoring_project_id = $newProject->id;
            $newGroup->save();
            $groupIds[$groups->id] = $newGroup->id;
        }

        $keywordIds = [];
        foreach ($original->keywords as $keyword) {
            $newKeyword = $keyword->replicate();
            $newKeyword->monitoring_project_id = $newProject->id;
            $newKeyword->monitoring_group_id = $groupIds[$keyword->monitoring_group_id];
            $newKeyword->save();
            $keywordIds[$keyword->id] = $newKeyword->id;
        }

        $searchengineIds = [];
        foreach ($original->searchengines as $engine) {
            $newEngine = $engine->replicate();
            $newEngine->monitoring_project_id = $newProject->id;
            $newEngine->save();
            $searchengineIds[$engine->id] = $newEngine->id;
        }

        $keywordsCount = $original->keywords()->count();

        foreach ($original->keywords as $keyword) {
            dispatch(new CopyKeywordsMonitoringProjectJob($keyword, $keywordIds, $searchengineIds));
            $this->sendProcess("Ключевых запросов осталось: " . $keywordsCount);

            $keywordsCount -= 1;
        }

        foreach ($original->competitors as $competitor) {
            $newCompetitor = $competitor->replicate();
            $newCompetitor->monitoring_project_id = $newProject->id;
            $newCompetitor->save();
        }

        $this->sendProcess("Проект скопирован. <a href='/monitoring/$newProject->id' target='_blank'>Перейти</a>");
    }

    protected function sendProcess(string $message)
    {
        event(new MonitoringProjectCopyProgress($this->userId, $message));
    }
}
