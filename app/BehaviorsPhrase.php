<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BehaviorsPhrase extends Model
{
    protected $fillable = ['code', 'phrase', 'sort'];

    public function behavior()
    {
        return $this->belongsTo(Behavior::class);
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', true);
    }

    public function scopeFail($query)
    {
        return $query->where('status', false);
    }

    public function scopeUnique($query)
    {
        return $query->groupBy('phrase');
    }

    public function scopeSortOrder($query)
    {
        return $query->orderBy('sort', 'asc');
    }
}
