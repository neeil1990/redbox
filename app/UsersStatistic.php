<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersStatistic extends Model
{
    protected $fillable = ['monitoring_project'];

    protected $casts = [
        'monitoring_project' => 'collection',
    ];
}
