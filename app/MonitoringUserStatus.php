<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonitoringUserStatus extends Model
{
    protected $table = 'monitoring_user_status';
    protected $fillable = ['code', 'name'];

    public function getNameAttribute($val)
    {
        return __($val);
    }
}
