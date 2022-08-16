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

        if (isset($job)) {
            if ($job->count_jobs <= 10) {
                $priority = 'high';
            } elseif ($job->count_jobs <= 20) {
                $priority = 'medium';
            } else {
                $priority = 'low';
            }
        } else {
            $priority = 'high';
        }

        $job->count_jobs++;
        $job->save();

        return $priority;
    }
}
