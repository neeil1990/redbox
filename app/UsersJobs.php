<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class UsersJobs extends Model
{
    protected $guarded = [];

    protected $table = 'users_jobs';

    /**
     * @param $userId
     * @return string
     */
    public static function getPriority($userId): string
    {
        $job = UsersJobs::firstOrNew(['user_id' => $userId]);

        Log::debug('job', [$job]);

        if (!isset($job) || $job->count_jobs == 0) {
            $priority = 'high';
        } else {
            $priority = 'default';
        }

        $job->count_jobs++;
        $job->save();

        return $priority;
    }
}
