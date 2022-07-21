<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


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
     * @param $main
     * @return array
     */
    public static function calculateInfo($main): array
    {
        $points = 0;
        $position = 0;

        $items = RelevanceHistory::where('project_relevance_history_id', '=', $main->id)
            ->distinct(['main_link', 'phrase', 'region'])
            ->get(['main_link', 'phrase', 'region']);

        foreach ($items as $item) {
            $record = RelevanceHistory::where('main_link', '=', $item->main_link)
                ->where('project_relevance_history_id', '=', $main->id)
                ->where('phrase', '=', $item->phrase)
                ->where('region', '=', $item->region)
                ->where('calculate', '=', 1)
                ->latest('last_check')
                ->first();

            if (isset($record)) {
                $points += $record->points;
                $position += $record->position == 0 ? 100 : $record->position;
            }
        }

        $count = count($items);
        if ($count != 0) {
            $points = $points / $count;
            $position = $position / $count;
        }
        $countChecks = RelevanceHistory::where('project_relevance_history_id', '=', $main->id)->count();

        $main->count_sites = $count;
        $main->total_points = $points;
        $main->count_checks = $countChecks;
        $main->avg_position = $position;
        $main->save();

        return [
            'points' => $points,
            'count' => $count,
            'countChecks' => $countChecks,
            'avgPosition' => $position
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

        if (isset($main->id)) {
            $main->count_checks += 1;
        } else {
            $main->count_checks = 1;
        }

        $main->last_check = $time;
        $main->save();

        return $main;
    }
}
