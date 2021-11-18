<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MetaTag extends Model
{
    protected $fillable = ['name', 'period', 'links', 'timeout'];
}
