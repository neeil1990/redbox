<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CacheDataBase extends Model
{
    protected $table = "cache";

    protected $primaryKey = "key";

    protected $keyType = 'string';

    public $incrementing = false;
}
