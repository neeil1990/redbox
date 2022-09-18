<?php

namespace App\Http\Controllers;


use App\Classes\Tariffs\Facades\Tariffs;
use App\CompetitorConfig;
use App\CompetitorsProgressBar;
use App\SearchCompetitors;
use App\TariffSetting;
use App\TextAnalyzer;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Throwable;

class SearchCompetitorsController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:Competitor analysis']);
    }

    /**
     * @return array|false|Application|Factory|View|mixed
     */
    public function index()
    {
        $admin = User::isUserAdmin();
        $config = CompetitorConfig::first();

        return view('competitor-analysis.index', ['admin' => $admin, 'config' => $config]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function analyseSites(Request $request): JsonResponse
    {
        if (TariffSetting::checkSearchCompetitorsLimits($request->input('phrases'))) {
            return response()->json([
                'code' => 415,
                'message' => __('Your limits are exhausted this month')
            ]);
        }

        try {
            $analysis = new SearchCompetitors();
            $analysis->setPhrases($request->input('phrases'));
            $analysis->setRegion($request->input('region'));
            $analysis->setCount($request->input('count'));
            $analysis->setPageHash($request->input('pageHash'));
            $analysis->analyzeList();

            return response()->json([
                'result' => $analysis->getResult(),
                'code' => 200,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'code' => 200,
                'object' => CompetitorsProgressBar::where('page_hash', '=', $request->input('pageHash'))->delete(),
                'message' => 'Произошла непредвиденная ошибка, обратитесь к администратору'
            ]);
        }

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function startProgressBar(Request $request): JsonResponse
    {
        $progress = CompetitorsProgressBar::firstOrNew([
            'page_hash' => $request->input('pageHash')
        ]);

        $progress->save();

        return response()->json([
            'code' => 200,
            'object_id' => $progress->id
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getProgressBar(Request $request): JsonResponse
    {
        return response()->json([
            'percent' => CompetitorsProgressBar::where('page_hash', '=', $request->input('pageHash'))->first(['percent']),
            'code' => 200,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function removeProgressBar(Request $request): JsonResponse
    {
        return response()->json([
            'code' => 200,
            'object' => CompetitorsProgressBar::where('page_hash', '=', $request->input('pageHash'))->delete()
        ]);
    }

    /**
     * @return array|false|Application|Factory|View|mixed|void
     */
    public function config()
    {
        if (!User::isUserAdmin()) {
            return abort(403);
        }

        $now = Carbon::now();
        $counter = (int)SearchCompetitors::where('month', '=', $now->year . '-' . $now->month)
            ->sum('counter');
        $config = CompetitorConfig::first();

        return view('competitor-analysis.config', ['admin' => true, 'config' => $config, 'counter' => $counter]);

    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function editConfig(Request $request): RedirectResponse
    {
        $config = CompetitorConfig::first();
        $config->agrigators = trim($request->input('agrigators'));
        $config->urls_length = trim($request->input('urls_length'));
        $config->positions_length = trim($request->input('positions_length'));
        $config->save();

        return Redirect::back();
    }
}
