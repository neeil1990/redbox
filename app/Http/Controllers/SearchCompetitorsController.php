<?php

namespace App\Http\Controllers;


use App\Classes\Tariffs\Facades\Tariffs;
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

        $xmlResult = SearchCompetitors::analyzeList($request->all());
        $sites = SearchCompetitors::scanSites($xmlResult);

        return response()->json([
            'sites' => $sites['sites'],
            'metaTags' => $sites['metaTags'],
            'scanResult' => $xmlResult,
            'code' => 200,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function analyseNesting(Request $request): JsonResponse
    {
        $pageNesting = SearchCompetitors::analysisPageNesting($request->scanResult);

        return response()->json([
            'sites' => $request->sites,
            'scanResult' => $request->scanResult,
            'nesting' => $pageNesting,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function analysePositions(Request $request): JsonResponse
    {
        $positions = SearchCompetitors::calculatePositions($request);

        return response()->json([
            'sites' => $request->sites,
            'scanResult' => $request->scanResult,
            'positions' => $positions
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function analyseTags(Request $request): JsonResponse
    {
        $tags = SearchCompetitors::scanTags($request->metaTags);

        return response()->json([
            'metaTags' => $tags,
        ]);
    }

}
