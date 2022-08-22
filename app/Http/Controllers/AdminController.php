<?php

namespace App\Http\Controllers;

use App\Jobs;
use App\ProjectRelevanceHistory;
use App\RelevanceAllUniqueDomains;
use App\RelevanceAllUniquePages;
use App\RelevanceAnalysisConfig;
use App\RelevanceHistory;
use App\RelevanceStatistics;
use App\RelevanceUniqueDomains;
use App\RelevanceUniquePages;
use App\UsersJobs;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:Super Admin|admin']);
    }

    /**
     * @return View|void
     */
    public function relevanceHistoryProjects(): View
    {
        $firstDay = new Carbon('first day of this month');

        return view('relevance-analysis.all', [
            'admin' => true,
            'projects' => ProjectRelevanceHistory::with('though')->get(),
            'config' => RelevanceAnalysisConfig::first(),
            'usersJobs' => UsersJobs::where('count_jobs', '>', 0)->with('user')->get(),
            'statistics' => [
                'toDay' => RelevanceStatistics::where('date', '=', Carbon::now()->toDateString())->first(),
                'month' => RelevanceStatistics::where('created_at', '>=', $firstDay->toDateString())->sum('count_checks'),
                'countProjects' => ProjectRelevanceHistory::count(),
                'countSavedResults' => RelevanceHistory::count(),
                'pages' => RelevanceUniquePages::count(),
                'domains' => RelevanceUniqueDomains::count(),
                'allDomains' => RelevanceAllUniqueDomains::count(),
                'allPages' => RelevanceAllUniquePages::count(),
                'countJobs' => UsersJobs::where('count_jobs', '>', 0)->sum('count_jobs'),
            ]
        ]);
    }

    /**
     * @return View
     */
    public function showConfig(): View
    {
        $config = RelevanceAnalysisConfig::first();
        $host = env('DB_HOST', '127.0.0.1');
        $db_name = env('DB_DATABASE', 'lk_redbox_su_db');
        $user = env('DB_USERNAME', 'lk_redbox_su_usr');
        $password = env('DB_PASSWORD', '0066FJVQ16Muz63j');
        $connection = mysqli_connect($host, $user, $password, $db_name);

        $query = 'SELECT table_name AS `Table`,
                        round(((data_length + index_length) / 1024 / 1024), 2)
                    FROM information_schema.TABLES
                    WHERE table_name = "relevance_history_result";';
        $result = mysqli_query($connection, $query);
        $result = $result->fetch_assoc();

        return view('relevance-analysis.relevance-config', [
            'admin' => true,
            'config' => $config,
            'size' => $result['round(((data_length + index_length) / 1024 / 1024), 2)']
        ]);
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function changeConfig(Request $request): RedirectResponse
    {
        $config = RelevanceAnalysisConfig::first();
        if (!$config) {
            $config = new RelevanceAnalysisConfig();
        }

        $config->count_sites = $request->count;
        $config->region = $request->region;
        $config->ignored_domains = $request->ignored_domains;
        $config->separator = $request->separator;

        $config->noindex = $request->noindex;
        $config->meta_tags = $request->meta_tags;
        $config->parts_of_speech = $request->parts_of_speech;
        $config->remove_my_list_words = $request->remove_my_list_words;
        $config->my_list_words = $request->my_list_words;
        $config->hide_ignored_domains = $request->hide_ignored_domains;

        $config->ltp_count = $request->ltp_count;
        $config->ltps_count = $request->ltps_count;
        $config->scanned_sites_count = $request->scanned_sites_count;
        $config->recommendations_count = $request->recommendations_count;

        $config->boostPercent = $request->boostPercent;

        $config->save();

        return Redirect::back();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function changeCleaningInterval(Request $request): JsonResponse
    {
        $config = RelevanceAnalysisConfig::first();
        if (!$config) {
            $config = new RelevanceAnalysisConfig();
        }

        $config->cleaning_interval = $request->newInterval;
        $config->save();

        return response()->json([
            'success' => true,
            'message' => __('Cleaning parameters have been successfully changed'),
            'code' => 200
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getCountQueue(): JsonResponse
    {
        return response()->json([
            'count' => UsersJobs::where('count_jobs', '>', 0)->sum('count_jobs')
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getUserJobs(): JsonResponse
    {
        return response()->json([
            'jobs' => UsersJobs::where('count_jobs', '>', 0)->with('user')->get()
        ]);
    }
}
