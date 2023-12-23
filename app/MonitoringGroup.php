<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonitoringGroup extends Model
{
    protected $fillable = ['monitoring_project_id', 'type', 'name'];
    protected $with = ['users'];

    public function keywords()
    {
        return $this->hasMany(MonitoringKeyword::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
