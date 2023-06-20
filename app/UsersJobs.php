<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;

class UsersJobs extends Model
{
    protected $guarded = [];

    protected $table = 'users_jobs';

    public static function getPriority($userId): string
    {
        $job = UsersJobs::firstOrNew(['user_id' => $userId]);

        if (isset($job)) {
            if ($job->count_jobs <= 10) {
                $priority = 'high';
            } elseif ($job->count_jobs <= 20) {
                $priority = 'medium';
            } else {
                $priority = 'normal';
            }
            $job->count_jobs++;
        } else {
            $priority = 'high';
            $job->count_jobs = 1;
        }

        $job->save();

        return $priority;
    }

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class,  'id', 'user_id');
    }
}
