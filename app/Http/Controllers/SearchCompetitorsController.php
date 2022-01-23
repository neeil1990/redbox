<?php

namespace App\Http\Controllers;

use App\SearchCompetitors;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
    public function analyzeSites(Request $request): JsonResponse
    {
        $searchCompetitors = new SearchCompetitors();
        $scanResult = $searchCompetitors->analyzeList($request->all());
        $sites = $searchCompetitors->scanSites($scanResult);

        return response()->json([
            'sites' => $sites['sites'],
            'metaTags' => $sites['metaTags'],
            'scanResult' => $scanResult
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
