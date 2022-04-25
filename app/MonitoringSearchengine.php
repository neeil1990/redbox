<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonitoringSearchengine extends Model
{
    protected $fillable = ['engine', 'lr', 'lang'];
}
