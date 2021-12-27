<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MainProject extends Model
{
    protected $table = 'main_projects';

    protected $guarded = [];

    protected $casts = [
        'access' => 'array',
    ];

    public function getAccessAsStringAttribute()
    {
        return (is_array($this->access)) ? implode(', ', $this->access) : null;
    }
}
