<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

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
     * @param $html
     * @param $sites
     * @return int
     */
    public static function createOrUpdate($phrase, $link, $request, $site, $time, $mainHistory, $state, $historyId, $html = null, $sites = null): int
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
            'request' => json_encode($request),
            'last_check' => $time,
            'points' => $site['mainPoints'],
            'coverage' => $site['coverage'],
            'coverage_tf' => $site['coverageTf'],
            'width' => $site['width'],
            'density' => $site['density'],
            'position' => $site['position'],
            'project_relevance_history_id' => $mainHistory->id,
            'html_main_page' => $html,
            'sites' => $sites,
            'user_id' => $mainHistory->user_id
        ]);

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

    /**
     * @return hasOne
     */
    public function results(): hasOne
    {
        return $this->hasOne(RelevanceHistoryResult::class, 'project_id', 'id');
    }

    /**
     * @param int $count
     * @return bool
     */
    public static function checkRelevanceAnalysisLimits(int $count = 0): bool
    {
        $user = Auth::user();

        if ($tariff = $user->tariff()) {

            $tariff = $tariff->getAsArray();

            if (array_key_exists('RelevanceAnalysis', $tariff['settings'])) {
                $now = Carbon::now();
                $month = strlen($now->month) < 2 ? '0' . $now->month : $now->month;

                $countRecordInThisMonth = RelevanceAnalyseResults::where('user_id', '=', Auth::id())
                    ->where('last_check', 'like', '%' . $now->year . '-' . $month . '%')
                    ->count();

                if ($countRecordInThisMonth + $count >= $tariff['settings']['RelevanceAnalysis']['value']) {

                    return true;
                }
            }
        }

        return false;
    }
}
