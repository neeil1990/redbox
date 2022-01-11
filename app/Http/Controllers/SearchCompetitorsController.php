<?php

namespace App\Http\Controllers;

use App\SearchCompetitors;
use App\TextAnalyzer;
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

        return view('search-competitors.index', [
            'result' => $searchCompetitors->scanSites(),
            'meta' => $searchCompetitors->scanTags(),
            'positions' => $searchCompetitors->calculatePositions(),
            'phrases' => $request->phrases,
            'region' => $request->region,
        ]);
    }
}
