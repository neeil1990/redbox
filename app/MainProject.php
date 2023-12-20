<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MainProject extends Model
{
    protected $table = 'main_projects';

    protected $guarded = [];

    protected $casts = [
        'access' => 'array',
    ];

    public function getAccessAsStringAttribute(): ?string
    {
        return (is_array($this->access)) ? implode(', ', $this->access) : null;
    }

    public function statistics(): HasMany
    {
        return $this->hasMany(VisitStatistic::class, 'project_id', 'id');
    }
}
