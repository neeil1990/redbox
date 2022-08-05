<?php


namespace App\Classes\Monitoring;


use App\MonitoringProject;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class CacheOfUserForPosition
{
    protected $userId;

    protected $project;

    private $cacheKeyForPosition;

    public function __construct(MonitoringProject $project)
    {
        $this->userId = auth()->id();

        $this->project = $project;

        $this->generateCacheKey();
    }

    public function getCacheKey()
    {
        return $this->cacheKeyForPosition;
    }

    public function getPath()
    {
        $parts = array_slice(str_split($hash = sha1($this->getCacheKey()), 2), 0, 2);

        return storage_path('framework/cache/data').'/'.implode('/', $parts).'/'.$hash;
    }

    public function getLastModified()
    {
        if(File::exists($this->getPath()))
            return Carbon::parse(File::lastModified($this->getPath()))->timezone('Europe/Moscow')->format('d.m.Y H:i');

        return null;
    }

    public function deleteCache()
    {
        Cache::forget($this->getCacheKey());
    }

    private function generateCacheKey()
    {
        $this->cacheKeyForPosition = "topPositionsCacheForUser" . $this->userId . "Project" . $this->project->id;
    }
}
