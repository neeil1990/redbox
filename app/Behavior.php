<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Behavior extends Model
{
    public $incrementing = false;
    protected $fillable = ['id', 'code', 'domain', 'minutes', 'clicks', 'pages'];

    public function phrases()
    {
        return $this->hasMany(BehaviorsPhrase::class);
    }
}
