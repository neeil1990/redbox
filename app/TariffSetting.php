<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TariffSetting extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'message',
    ];

    public function fields()
    {
        return $this->hasMany(TariffSettingValue::class);
    }

    /**
     * @param User $user
     * @return bool
     */
    public static function checkDomainInformationLimits(User $user): bool
    {
        if (isset($request->domains)) {
            $countNewRecords = count(explode("\r\n", $request->domains));
        } else {
            $countNewRecords = 0;
        }

        if ($tariff = $user->tariff()) {

            $tariff = $tariff->getAsArray();
            $count = DomainInformation::where('user_id', '=', $user->id)->count();

            if (array_key_exists('DomainInformation', $tariff['settings'])) {

                if ($count + $countNewRecords >= $tariff['settings']['DomainInformation']['value']) {

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public static function checkDomainMonitoringLimits(): bool
    {
        $user = Auth::user();
        if ($tariff = $user->tariff()) {

            $tariff = $tariff->getAsArray();
            $count = DomainMonitoring::where('user_id', '=', Auth::id())->count();

            if (array_key_exists('domainMonitoringProject', $tariff['settings'])) {

                if ($count >= $tariff['settings']['domainMonitoringProject']['value']) {

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public static function checkTextAnalyserLimits(): bool
    {
        $now = Carbon::now();
        $user = Auth::user();
        if ($tariff = $user->tariff()) {

            $tariff = $tariff->getAsArray();
            $count = TextAnalyzer::where('user_id', '=', Auth::id())
                ->where('month', '=', $now->year . '-' . $now->month)
                ->sum('counter');

            if (array_key_exists('TextAnalyzer', $tariff['settings'])) {

                if ((int)$count >= $tariff['settings']['TextAnalyzer']['value']) {
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * @param int $newCount
     * @return bool
     */
    public static function checkSearchCompetitorsLimits(int $newCount): bool
    {
        $now = Carbon::now();

        $count = SearchCompetitors::where('user_id', '=', Auth::id())
            ->where('month', '=', $now->year . '-' . $now->month)
            ->sum('counter');

        /** @var User $user */
        $user = Auth::user();
        if ($tariff = $user->tariff()) {
            $tariff = $tariff->getAsArray();
        }

        if (isset($tariff['settings']['CompetitorAnalysisPhrases']) && $tariff['settings']['CompetitorAnalysisPhrases']['value'] > 0) {

            if ($newCount + $count > $tariff['settings']['CompetitorAnalysisPhrases']['value']) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return void
     */
    public static function saveStatistics(string $class, $userId, $count = 1)
    {
        if ($count > 0) {
            $now = Carbon::now();

            $record = $class::where('user_id', '=', $userId)
                ->where('month', '=', $now->year . '-' . $now->month)
                ->first();

            if (isset($record)) {
                $record->counter += $count;
            } else {
                $record = new $class();
                $record->month = $now->year . '-' . $now->month;
                $record->user_id = $userId;
                $record->counter = $count;
            }

            $record->save();
        }
    }

}
