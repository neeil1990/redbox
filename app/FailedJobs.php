<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FailedJobs extends Model
{
    protected $guarded = [];

    protected $table = 'failed_jobs';

    protected $casts = [
        'payload' => 'collection',
    ];
}
