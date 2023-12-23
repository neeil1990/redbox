<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartnersGroups extends Model
{
    protected $table = 'partners_groups';

    protected $guarded = [];

    public function items(): HasMany
    {
        return $this->hasMany(PartnersItems::class);
    }
}
