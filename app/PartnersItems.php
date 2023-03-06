<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnersItems extends Model
{
    protected $table = 'partners_items';

    protected $guarded = [];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(PartnersGroups::class, 'partners_groups_id', 'id');
    }

    public function delete()
    {
        if (file_exists(public_path('storage\\' . $this->image))) {
            unlink(public_path('storage\\' . $this->image));
        }

        parent::delete();
    }
}
