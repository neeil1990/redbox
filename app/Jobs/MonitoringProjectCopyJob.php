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

        $this->sendProcess("Группы скопированы");

        $keywordIds = [];
        foreach ($original->keywords as $keyword) {
            $newKeyword = $keyword->replicate();
            $newKeyword->monitoring_project_id = $newProject->id;
            $newKeyword->monitoring_group_id = $groupIds[$keyword->monitoring_group_id];
            $newKeyword->save();
            $keywordIds[$keyword->id] = $newKeyword->id;
        }

        $this->sendProcess("Ключевые запросы скопированы");

        $searchengineIds = [];
        foreach ($original->searchengines as $engine) {
            $newEngine = $engine->replicate();
            $newEngine->monitoring_project_id = $newProject->id;
            $newEngine->save();
            $searchengineIds[$engine->id] = $newEngine->id;
        }

        $this->sendProcess("Поисковые системы скопированы");

        $keywordsCount = $original->keywords()->count();

        $this->sendProcess("Копирование цен и позиций ключевых запросов");
        $this->sendProcess("Ключевых запросов осталось: " . $keywordsCount);

        foreach ($original->keywords as $keyword) {
            foreach ($keyword->positions as $position) {
                $newPosition = $position->replicate();
                $newPosition->monitoring_keyword_id = $keywordIds[$position->monitoring_keyword_id];
                $newPosition->monitoring_searchengine_id = $searchengineIds[$position->monitoring_searchengine_id];
                $newPosition->created_at = $position->created_at;
                $newPosition->updated_at = $position->updated_at;
                $newPosition->save();
            }

            foreach ($keyword->prices as $price) {
                $newPrice = $price->replicate();
                $newPrice->monitoring_keyword_id = $keywordIds[$price->monitoring_keyword_id];
                $newPrice->monitoring_searchengine_id = $searchengineIds[$price->monitoring_searchengine_id];
                $newPrice->save();
            }

            $keywordsCount -= 1;

            $this->sendProcess("Ключевых запросов осталось: " . $keywordsCount);
        }

        $this->sendProcess("Позиции и цены скопированы");

        foreach ($original->competitors as $competitor) {
            $newCompetitor = $competitor->replicate();
            $newCompetitor->monitoring_project_id = $newProject->id;
            $newCompetitor->save();
        }

        $this->sendProcess("Конкуренты скопированы");

        $this->sendProcess("Копирование завершено (<a href='/monitoring/$newProject->id' target='_blank'>Перейти</a>)");
    }

    protected function sendProcess(string $message)
    {
        event(new MonitoringProjectCopyProgress($this->userId, $message));
    }
}
