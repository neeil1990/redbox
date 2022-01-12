<?php

namespace App\Http\Controllers;

use App\SearchCompetitors;
use Illuminate\Http\Request;

class SearchCompetitorsController extends Controller
{
    public function index()
    {
        return view('search-competitors.index');
    }

    public function analyze(Request $request)
    {
        $searchCompetitors = new SearchCompetitors();
        $searchCompetitors->analyzeList($request->all());

        $result = [
            'result' => $searchCompetitors->scanSites(),
            'positions' => $searchCompetitors->calculatePositions(),
            'meta' => $searchCompetitors->scanTags(),
            'phrases' => $request->phrases,
            'region' => $request->region,
            'count' => $request->count,
        ];

        return view('search-competitors.index', $result);
    }
}
