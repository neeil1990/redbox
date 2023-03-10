<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ClusterLimit extends Model
{
    protected $table = 'clusters_limits';

    protected $guarded = [];

    public static function calculateCountRequests(array $request): int
    {
        $count = count(array_unique(array_diff(explode("\n", str_replace("\r", "", $request['phrases'])), [])));
        $multiplier = 1;

        if (filter_var($request['searchBase'], FILTER_VALIDATE_BOOLEAN)) {
            $multiplier += 1;
        }
        if (filter_var(filter_var($request['searchRelevance'], FILTER_VALIDATE_BOOLEAN))) {
            $multiplier += 1;
        }
        if (filter_var(filter_var($request['searchPhrases'], FILTER_VALIDATE_BOOLEAN))) {
            $multiplier += 1;
        }
        if (filter_var(filter_var($request['searchTarget'], FILTER_VALIDATE_BOOLEAN))) {
            $multiplier += 1;
        }

        $count *= $multiplier;

        return $count;
    }


    /**
     * @param int $count
     * @return bool
     */
    public static function checkClustersLimits(int $count): bool
    {
        $now = Carbon::now();
        $month = strlen($now->month) < 2 ? '0' . $now->month : $now->month;
        $user = Auth::user();

        if ($tariff = $user->tariff()) {
            $tariff = $tariff->getAsArray();
            if (array_key_exists('Clusters', $tariff['settings'])) {
                $limit = ClusterLimit::where('user_id', '=', Auth::id())
                    ->where('date', '=', "$now->year-$month")
                    ->first();

                $countRecordInThisMonth = 0;
                if (isset($limit)) {
                    $countRecordInThisMonth = $limit->count;
                }

                if ($countRecordInThisMonth + $count >= $tariff['settings']['Clusters']['value']) {
                    return true;
                } else {
                    if (isset($limit)) {
                        $limit->count += $count;
                    } else {
                        $limit = new ClusterLimit();
                        $limit->user_id = Auth::id();
                        $limit->date = "$now->year-$month";
                        $limit->count = $count;
                    }

                    $limit->save();
                }
            }
        }

        return false;
    }
}
