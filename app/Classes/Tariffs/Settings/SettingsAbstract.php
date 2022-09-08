<?php


namespace App\Classes\Tariffs\Settings;


use App\DomainInformation;
use App\DomainMonitoring;
use App\ProjectTracking;
use App\RelevanceHistory;
use App\SearchCompetitors;
use App\TariffSettingValue;
use App\TextAnalyzer;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

abstract class SettingsAbstract
{
    protected $tariff;
    protected $settings;

    public function get(): array
    {
        $this->settings = [];

        $settings = TariffSettingValue::where('tariff', $this->tariff)->get();
        foreach ($settings as $setting) {
            $used = $this->getUsedLimit($setting->property->code);
            $percent = gettype($used) === 'integer' ? ceil($used / ($setting->value / 100)) : 100;

            $this->settings[$setting->property->code] = [
                'name' => $setting->property->name,
                'message' => $this->replaceMsg($setting->property->message, $setting->value),
                'value' => $setting->value,
                'used' => $used,
                'percent' => $percent
            ];
        }

        return $this->settings;
    }

    protected function replaceMsg(?string $str, $val)
    {
        if (!$str)
            return null;

        $str = __($str);

        $str = str_replace('{TARIFF}', $this->tariff, $str);
        $str = str_replace('{VALUE}', $val, $str);

        return $str;
    }

    /**
     * @param string $code
     * @return int|string
     */
    protected function getUsedLimit(string $code)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user == null) {
            return 'Нет данных';
        }

        $now = Carbon::now();
        $month = strlen($now->month) < 2 ? '0' . $now->month : $now->month;

        switch ($code) {
            case 'CompetitorAnalysisPhrases':
                return (int)SearchCompetitors::where('user_id', '=', $user->id)
                    ->where('month', '=', $now->year . '-' . $now->month)
                    ->sum('counter');

            case 'TextAnalyzer':
                return (int)TextAnalyzer::where('user_id', '=', $user->id)
                    ->where('month', '=', $now->year . '-' . $now->month)
                    ->sum('counter');

            case 'RelevanceAnalysis':
                return (int)RelevanceHistory::where('user_id', '=', $user->id)
                    ->where('last_check', 'like', '%' . $now->year . '-' . $month . '%')
                    ->count();

            case 'domainMonitoringProject':
                return (int)DomainMonitoring::where('user_id', '=', $user->id)->count();

            case 'BacklinkProject':
                return (int)ProjectTracking::where('user_id', '=', $user->id)->count();

            case 'DomainInformation':
                return (int)DomainInformation::where('user_id', '=', $user->id)->count();

            case 'behavior':
                return $user->behaviors()->count();

            case 'MetaTagsProject':
            case 'MetaTagsPages':
            case 'price':
            case 'UniqueWords':
            case 'HtmlEditor':
            case 'CompetitorAnalysis':

            default:
                return 'Нет данных';
        }
    }
}
