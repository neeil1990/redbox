<?php


namespace App\Classes\Monitoring;

use App\MonitoringProject;

class ProjectDependencies
{
    const POSITION_PREFIX = 'engine';
    const POSITION_POSTFIX = 'latest';
    const POSITION_SEPARATOR = '_';

    protected $project;
    protected $queries;
    protected $engines;

    public function __construct(MonitoringProject $project)
    {
        $this->init($project);
    }

    private function init(MonitoringProject $project)
    {
        $this->project = $project->load(['searchengines']);

        $this->engines = $project['searchengines'];

        $this->queries = $project->keywords()
            ->addLastPositions(self::POSITION_SEPARATOR, self::POSITION_PREFIX, self::POSITION_POSTFIX, $this->engines->pluck('id'))
            ->get();
    }

    public function getQueries()
    {
        return $this->queries;
    }

    public function getEngines()
    {
        return $this->engines;
    }

    public function getLatestPositionCollect()
    {
        $latestPositions = collect([]);

        $engines = $this->getEngines();

        foreach ($engines as $engine)
        {
            $positions = $this->queries->map(function ($item) use ($engine) {
                return collect([
                    'query_id' => $item['id'],
                    'engine_id' => $engine['id'],
                    'position' => $item[$this->generateLatestKeyPosition($engine['id'])],
                ]);
            });

            $latestPositions = $latestPositions->merge($positions);
        }

        return $latestPositions;
    }

    private function generateLatestKeyPosition($engine_id)
    {
        return implode(self::POSITION_SEPARATOR, [self::POSITION_PREFIX, $engine_id, self::POSITION_POSTFIX]);
    }

}
