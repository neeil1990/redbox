<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RelevanceHistory extends Model
{
    protected $guarded = [];

    protected $table = 'relevance_history';

    /**
     * @param $phrase
     * @param $link
     * @param $request
     * @param $site
     * @param $time
     * @param $mainHistory
     * @param $state
     * @param $historyId
     * @return int
     */
    public static function createOrUpdate($phrase, $link, $request, $site, $time, $mainHistory, $state, $historyId): int
    {
        if ($historyId > 0) {
            $history = RelevanceHistory::where('id', '=', $historyId)->first();
            if ($history->state == -1) {
                $history->delete();
            } else {
                $history->state = $state;
                $history->save();
            }
        }

        $history = new RelevanceHistory([
            'phrase' => $phrase,
            'main_link' => $link,
            'region' => $request['region'],
            'state' => $state,
            'request' => json_encode($request)
        ]);

        $history->last_check = $time;
        $history->points = $site['mainPoints'];
        $history->coverage = $site['coverage'];
        $history->coverage_tf = $site['coverageTf'];
        $history->width = $site['width'];
        $history->density = $site['density'];
        $history->position = $site['position'];

        $history->project_relevance_history_id = $mainHistory->id;
        $history->save();

        return $history->id;
    }

    /**
     * @return BelongsTo
     */
    public function projectRelevanceHistory(): BelongsTo
    {
        return $this->belongsTo(ProjectRelevanceHistory::class);
    }

    /**
     * @return HasOne
     */
    public function mainHistory(): HasOne
    {
        return $this->hasOne(ProjectRelevanceHistory::class, 'id', 'project_relevance_history_id');
    }
}
