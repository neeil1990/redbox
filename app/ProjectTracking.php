<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectTracking extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'project_tracking';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'monitoring_project_id', 'project_name', 'total_link',
    ];

    protected $guarded = [];

    /**
     * @return HasMany
     */
    public function link(): HasMany
    {
        return $this->hasMany(LinkTracking::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id','id');
    }
}
