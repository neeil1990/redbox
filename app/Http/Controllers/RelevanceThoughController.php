<?php

namespace App\Http\Controllers;

use App\ProjectRelevanceThough;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RelevanceThoughController extends Controller
{
    /**
     * @param ProjectRelevanceThough $though
     * @return View
     */
    public function show(ProjectRelevanceThough $though): View
    {
        $though->result = gzuncompress(base64_decode($though->result));
//        foreach (json_decode($though->result, true) as $item) {
//            var_dump($item);
//            echo '<br><br>';
//        }
//        dd(1);
        return view('relevance-analysis.though.show', [
            'though' => $though,
            'microtime' => microtime(true),
        ]);
    }
}
