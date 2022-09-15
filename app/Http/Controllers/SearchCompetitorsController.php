<?php

namespace App\Http\Controllers;


use App\Classes\Tariffs\Facades\Tariffs;
use App\CompetitorsProgressBar;
use App\SearchCompetitors;
use App\TariffSetting;
use App\TextAnalyzer;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

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
        return view('competitor-analysis.index');
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
}
