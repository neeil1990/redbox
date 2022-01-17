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
     */
    public function analyze(Request $request)
    {
        $searchCompetitors = new SearchCompetitors();
        $searchCompetitors->analyzeList($request->all());

        $sites = $searchCompetitors->scanSites();
        $pageNesting = $searchCompetitors->analysisPageNesting();
        $positions = $searchCompetitors->calculatePositions();
        $metaTags = $searchCompetitors->scanTags();

        $result = [
            'result' => $sites,
            'nested' => $pageNesting,
            'positions' => $positions,
            'meta' => $metaTags,
            'phrases' => $request->phrases,
            'region' => $request->region,
            'count' => $request->count,
        ];

        return view('search-competitors.index', $result);
    }
}
