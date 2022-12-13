<?php

namespace App\ViewComposers;

use App\DomainInformation;
use App\DomainMonitoring;
use App\MetaTag;
use App\ProjectTracking;
use App\RelevanceHistory;
use App\SearchCompetitors;
use App\TextAnalyzer;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LimitsComposer
{

    public function compose(View $view)
    {
        /** @var User $user */
        $user = Auth::user();

        $tariff = $user->tariff();

        $tariffLimits = [];
        if (isset($tariff)) {
            $tariffLimits = $tariff->getAsArray()['settings'];
        }

        $limitsStatistics = [];
        foreach ($tariffLimits as $tariffKey => $tariffValue) {
            $info = LimitsComposer::getUsedLimit($tariffKey, $user);
            $limitsStatistics[$tariffKey]['used'] = $info['count'];
            $limitsStatistics[$tariffKey]['position'] = $info['position'];
            $limitsStatistics[$tariffKey]['name'] = $tariffValue['name'];
            $limitsStatistics[$tariffKey]['value'] = $tariffValue['value'];
        }

        $limitsStatistics = collect($limitsStatistics)->sortBy('position')->toArray();

        $view->with(compact('limitsStatistics'));
    }

    /**
     * @param string $code
     * @param $user
     * @return array
     */
    public static function getUsedLimit(string $code, $user): array
    {
        $now = Carbon::now();
        $month = strlen($now->month) < 2 ? '0' . $now->month : $now->month;

        $metaTagsProjects = MetaTag::where('user_id', '=', Auth::id())->get();

        $metaTagsHistoriesCount = 0;
        foreach ($metaTagsProjects as $metaTagsProject) {
            $metaTagsHistoriesCount += $metaTagsProject->histories()->where('id', '>', 0)->count();
        }

        $projectTracking = ProjectTracking::where('user_id', '=', $user->id)->with('link')->get();

        $projectTrackingLinks = 0;
        foreach ($projectTracking as $item) {
            $projectTrackingLinks += count($item->link);
        }

        switch ($code) {

            case 'RelevanceAnalysis':
                return [
                    'count' => (int)RelevanceHistory::where('user_id', '=', $user->id)
                        ->where('last_check', 'like', '%' . $now->year . '-' . $month . '%')
                        ->count(),
                    'position' => 1
                ];

            case 'TextAnalyzer':
                return [
                    'count' => (int)TextAnalyzer::where('user_id', '=', $user->id)
                        ->where('month', '=', $now->year . '-' . $now->month)
                        ->sum('counter'),
                    'position' => 2
                ];

            case 'CompetitorAnalysisPhrases':
                return [
                    'count' => (int)SearchCompetitors::where('user_id', '=', $user->id)
                        ->where('month', '=', $now->year . '-' . $now->month)
                        ->sum('counter'),
                    'position' => 3
                ];

            case 'domainMonitoringProject':
                return [
                    'count' => (int)DomainMonitoring::where('user_id', '=', $user->id)->count(),
                    'position' => 4
                ];

            case 'DomainInformation':
                return [
                    'count' => (int)DomainInformation::where('user_id', '=', $user->id)->count(),
                    'position' => 5
                ];

            case 'MetaTagsProject':
                return [
                    'count' => count($metaTagsProjects->toArray()),
                    'position' => 6,
                ];

            case 'MetaTagsPages':
                return [
                    'count' => $metaTagsHistoriesCount,
                    'position' => 7
                ];

            case 'GeneratorWords':
                return [
                    'count' => __('Restrictions are not tracked'),
                    'position' => 8
                ];

            case 'PasswordGenerator':
                return [
                    'count' => __('Restrictions are not tracked'),
                    'position' => 9
                ];

            case 'TextLength':
                return [
                    'count' => __('Restrictions are not tracked'),
                    'position' => 10
                ];

            case 'ListComparison':
                return [
                    'count' => __('Restrictions are not tracked'),
                    'position' => 11
                ];

            case 'UniqueWords':
                return [
                    'count' => __('Restrictions are not tracked'),
                    'position' => 12
                ];

            case 'HtmlEditor':
                return [
                    'count' => __('Restrictions are not tracked'),
                    'position' => 13
                ];

            case 'RemoveDublicate':
                return [
                    'count' => __('Restrictions are not tracked'),
                    'position' => 14
                ];

            case 'UTM':
                return [
                    'count' => __('Restrictions are not tracked'),
                    'position' => 15
                ];

            case 'ROI':
                return [
                    'count' => __('Restrictions are not tracked'),
                    'position' => 16
                ];

            case 'BacklinkProject':
                return [
                    'count' => count($projectTracking),
                    'position' => 17
                ];

            case 'BacklinkLinks':
                return [
                    'count' => $projectTrackingLinks,
                    'position' => 18
                ];

            case 'behavior':
                return [
                    'count' => $user->behaviors()->count(),
                    'position' => 19
                ];
            case 'HttpHeaders':
                return [
                    'count' => __('Restrictions are not tracked'),
                    'position' => 20
                ];

            default:
                return [
                    'count' => 100000,
                    'position' => 100
                ];
        }
    }

    /**
     * @param $code
     * @return int
     */
    public static function getPosition($code): int
    {
        switch ($code) {

            case 'RelevanceAnalysis':
                return 1;

            case 'TextAnalyzer':
                return 2;

            case 'CompetitorAnalysisPhrases':
                return 3;

            case 'domainMonitoringProject':
                return 4;

            case 'DomainInformation':
                return 5;

            case 'MetaTagsProject':
                return 6;

            case 'MetaTagsPages':
                return 7;

            case 'GeneratorWords':
                return 8;

            case 'PasswordGenerator':
                return 9;

            case 'TextLength':
                return 10;

            case 'ListComparison':
                return 11;

            case 'UniqueWords':
                return 12;

            case 'HtmlEditor':
                return 13;

            case 'RemoveDublicate':
                return 14;

            case 'behavior':
                return 15;

            case 'HttpHeaders':
                return 16;

            case 'UTM':
                return 17;

            case 'ROI':
                return 18;

            case 'BacklinkProject':
                return 19;

            case 'BacklinkLinks':
                return 20;

            default:
                return 100;
        }
    }

}
