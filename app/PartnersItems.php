<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

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

    public function generateShortLink(): string
    {
        $link = Str::random();

        if (empty($this->where('short_link', '=', $link)->first())) {
            return $link;
        }

        return $this->generateShortLink();
    }
}
