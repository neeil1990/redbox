<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
     * @param string $phrases
     * @return bool
     */
    public static function checkSearchCompetitorsLimits(string $phrases): bool
    {
        $newRequest = count(explode("\n", $phrases));
        $now = Carbon::now();

        $count = SearchCompetitors::where('user_id', '=', Auth::id())
            ->where('month', '=', $now->year . '-' . $now->month)
            ->sum('counter');

        /** @var User $user */
        $user = Auth::user();
        if ($tariff = $user->tariff()) {
            $tariff = $tariff->getAsArray();
        }

        if (isset($tariff['settings']['CompetitorAnalysisPhrases']) && $tariff['settings']['CompetitorAnalysisPhrases'] > 0) {

            if ($newRequest + $count > $tariff['settings']['CompetitorAnalysisPhrases']) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return void
     */
    public static function saveStatistics(string $class)
    {
        $now = Carbon::now();

        /**
         * унаследовано от Model, но не является экземпляром Model @var $class Model
         */
        $record = $class::firstOrNew(
            ['month' => $now->year . '-' . $now->month],
            ['user_id' => Auth::id()]
        );

        $record->counter++;

        $record->save();
    }

}
