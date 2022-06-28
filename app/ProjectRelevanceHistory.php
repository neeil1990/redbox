<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

class ProjectRelevanceHistory extends Model
{
    protected $table = 'project_relevance_history';

    protected $guarded = [];

    /**
     * @return HasMany
     */
    public function stories(): HasMany
    {
        return $this->hasMany(RelevanceHistory::class)->orderByDesc('last_check');
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'relevance_sharing', 'project_id');
    }

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return HasMany
     */
    public function sharing(): HasMany
    {
        return $this->hasMany(RelevanceSharing::class, 'project_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function relevanceTags(): BelongsToMany
    {
        return $this->belongsToMany(RelevanceTags::class, 'project_relevance_history_tags', 'relevance_history_id', 'tags_id');
    }

    /**
     * @param $stories
     * @return array
     */
    public static function calculateInfo($stories): array
    {
        $points = 0;
        $will = [];

        foreach ($stories as $story) {
            if ($story->calculate && !in_array($story->main_link, $will)) {
                $will[] = $story->main_link;
                $points += $story->points;
            }
        }

        if (count($will) == 0) {
            $count = 1;
        } else {
            $count = count($will);
        }
        return [
            'points' => $points / $count,
            'count' => $count
        ];
    }

    /**
     * @param $host
     * @param $time
     * @param $userId
     * @return ProjectRelevanceHistory
     */
    public static function createOrUpdate($host, $time, $userId): ProjectRelevanceHistory
    {
        $main = ProjectRelevanceHistory::firstOrNew([
            'name' => $host,
            'user_id' => $userId,
        ]);

        $main->last_check = $time;
        $main->save();

        return $main;
    }
}
