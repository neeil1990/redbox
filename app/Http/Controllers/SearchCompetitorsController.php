<?php

namespace App\Http\Controllers;

use App\SearchCompetitors;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SearchCompetitorsController extends Controller
{
    /**
     * @return array|false|Application|Factory|View|mixed
     */
    public function index()
    {
        return view('search-competitors.index');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function analyzeSites(Request $request)
    {
        Log::debug('debug', $request->all());
        $searchCompetitors = new SearchCompetitors();
        $scanResult = $searchCompetitors->analyzeList($request->all());
        $sites = $searchCompetitors->scanSites($scanResult);
//        $positions = $searchCompetitors->calculatePositions();

        return response()->json([
            'sites' => $sites,
            'scanResult' => $scanResult
        ]);

//        $metaTags = $searchCompetitors->scanTags();
//
//        $result = [
//            'result' => $sites,
//            'nested' => $pageNesting,
//            'positions' => $positions,
//            'meta' => $metaTags,
//            'phrases' => $request->phrases,
//            'region' => $request->region,
//            'count' => $request->count,
//        ];
//
//        return view('search-competitors.index', $result);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function analyseNesting(Request $request)
    {
        $pageNesting = SearchCompetitors::analysisPageNesting($request->scanResult);

        return response()->json([
            'nesting' => $pageNesting,
            'scanResult' => $request->scanResult
        ]);
    }

    public function analysePositions(Request $request)
    {
        $positions = SearchCompetitors::calculatePositions($request);
    }
}
