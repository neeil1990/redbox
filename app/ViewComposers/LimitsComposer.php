<?php

namespace App\ViewComposers;

use App\Classes\Monitoring\PositionLimit;
use App\ClusterLimit;
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
        if (Auth::check()) {
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
    }

    /**
     * @param string $code
     * @param $user
     * @return array
     */
    public static function getUsedLimit(string $code, $user): array
    {
        switch ($code) {
            case 'RelevanceAnalysis':
                $now = Carbon::now();
                $month = strlen($now->month) < 2 ? '0' . $now->month : $now->month;
                return [
                    'count' => (int)RelevanceHistory::where('user_id', '=', $user->id)
                        ->where('last_check', 'like', '%' . $now->year . '-' . $month . '%')
                        ->count(),
                    'position' => 1
                ];

            case 'TextAnalyzer':
                $now = Carbon::now();
                return [
                    'count' => (int)TextAnalyzer::where('user_id', '=', $user->id)
                        ->where('month', '=', $now->year . '-' . $now->month)
                        ->sum('counter'),
                    'position' => 2
                ];

            case 'CompetitorAnalysisPhrases':
                $now = Carbon::now();
                return [
                    'count' => (int)SearchCompetitors::where('user_id', '=', $user->id)
                        ->where('month', '=', $now->year . '-' . $now->month)
                        ->sum('counter'),
                    'position' => 3
                ];

            case 'Clusters':
                $now = Carbon::now();
                $month = strlen($now->month) < 2 ? '0' . $now->month : $now->month;

                return [
                    'count' => ClusterLimit::where('user_id', '=', Auth::id())
                            ->where('date', '=', "$now->year-$month")
                            ->first('count')->count ?? 0,
                    'position' => 4
                ];

            case 'domainMonitoringProject':
                return [
                    'count' => (int)DomainMonitoring::where('user_id', '=', $user->id)->count(),
                    'position' => 5
                ];

            case 'DomainInformation':
                return [
                    'count' => (int)DomainInformation::where('user_id', '=', $user->id)->count(),
                    'position' => 6
                ];

            case 'MetaTagsProject':
                return [
                    'count' => MetaTag::where('user_id', '=', Auth::id())->count(),
                    'position' => 7,
                ];

            case 'MetaTagsPages':
                $metaTagsProjects = MetaTag::where('user_id', '=', Auth::id())->get();

                $metaTagsHistoriesCount = 0;
                foreach ($metaTagsProjects as $metaTagsProject) {
                    $metaTagsHistoriesCount += $metaTagsProject->histories()->where('id', '>', 0)->count();
                }
                return [
                    'count' => $metaTagsHistoriesCount,
                    'position' => 8
                ];

            case 'monitoring':
                return [
                    'count' => (new PositionLimit(Auth::id()))->getCounter(),
                    'position' => 9
                ];

            case 'PasswordGenerator':
                return [
                    'count' => __('Restrictions are not tracked'),
                    'position' => 10
                ];

            case 'TextLength':
                return [
                    'count' => __('Restrictions are not tracked'),
                    'position' => 11
                ];

            case 'ListComparison':
                return [
                    'count' => __('Restrictions are not tracked'),
                    'position' => 12
                ];

            case 'UniqueWords':
                return [
                    'count' => __('Restrictions are not tracked'),
                    'position' => 13
                ];

            case 'HtmlEditor':
                return [
                    'count' => __('Restrictions are not tracked'),
                    'position' => 14
                ];

            case 'RemoveDublicate':
                return [
                    'count' => __('Restrictions are not tracked'),
                    'position' => 15
                ];

            case 'UTM':
                return [
                    'count' => __('Restrictions are not tracked'),
                    'position' => 16
                ];

            case 'ROI':
                return [
                    'count' => __('Restrictions are not tracked'),
                    'position' => 17
                ];

            case 'BacklinkProject':
                return [
                    'count' => ProjectTracking::where('user_id', '=', $user->id)->count(),
                    'position' => 18
                ];

            case 'BacklinkLinks':
                $projectTracking = ProjectTracking::where('user_id', '=', $user->id)->with('link')->get();

                $projectTrackingLinks = 0;
                foreach ($projectTracking as $item) {
                    $projectTrackingLinks += count($item->link);
                }

                return [
                    'count' => $projectTrackingLinks,
                    'position' => 19
                ];

            case 'behavior':
                return [
                    'count' => $user->behaviors()->count(),
                    'position' => 20
                ];

            case 'HttpHeaders':
                return [
                    'count' => __('Restrictions are not tracked'),
                    'position' => 21
                ];

            case 'GeneratorWords':
                return [
                    'count' => __('Restrictions are not tracked'),
                    'position' => 22
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

            case 'Clusters':
                return 4;

            case 'domainMonitoringProject':
                return 5;

            case 'DomainInformation':
                return 6;

            case 'MetaTagsProject':
                return 7;

            case 'MetaTagsPages':
                return 8;

            case 'behavior':
                return 9;

            case 'BacklinkProject':
                return 11;

            case 'BacklinkLinks':
                return 12;

            case 'monitoring':
                return 13;

            case 'ListComparison':
                return 14;

            case 'HttpHeaders':
                return 15;

            case 'TextLength':
                return 16;

            case 'RemoveDublicate':
                return 17;

            case 'UTM':
                return 18;

            case 'PasswordGenerator':
                return 19;

            case 'HtmlEditor':
                return 20;

            case 'ROI':
                return 21;

            case 'GeneratorWords':
                return 22;

            case 'UniqueWords':
                return 23;

            default:
                return 100;
        }
    }

}
