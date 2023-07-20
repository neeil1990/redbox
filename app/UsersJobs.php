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
        $job = UsersJobs::where(['user_id' => $userId])->first();

        if (isset($job)) {
            if ($job->count_jobs <= 10) {
                $priority = 'relevance_high';
            } elseif ($job->count_jobs <= 20) {
                $priority = 'relevance_medium';
            } else {
                $priority = 'relevance_normal';
            }
            $job->increment('count_jobs');
        } else {
            $priority = 'relevance_high';
            $job->count_jobs = 1;
            $job->save();
        }

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
