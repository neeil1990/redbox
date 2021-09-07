<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BehaviorsPhrase extends Model
{
    protected $fillable = ['code', 'phrase'];

    public function behavior()
    {
        return $this->belongsTo(Behavior::class);
    }
}
