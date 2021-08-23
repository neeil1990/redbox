<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Behavior extends Model
{
    public $incrementing = false;
    protected $fillable = ['id', 'code', 'domain', 'minutes'];

}
