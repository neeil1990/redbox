<?php

namespace App\Http\Controllers;


use App\Classes\Tariffs\Facades\Tariffs;
use App\SearchCompetitors;
use App\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
    public function analyzeSites(Request $request): JsonResponse
    {
        $this->checkTariff($request);

        $xmlResult = SearchCompetitors::analyzeList($request->all());
        $sites = SearchCompetitors::scanSites($xmlResult);

        return response()->json([
            'sites' => $sites['sites'],
            'metaTags' => $sites['metaTags'],
            'scanResult' => $xmlResult
        ]);
    }

    protected function checkTariff(Request $request)
    {
        $phrases = explode("\n", $request->input('phrases'));
        $count = count($phrases);

        /** @var User $user */
        $user = Auth::user();
        // Проверка тарифа
        if($tariff = $user->tariff())
            $tariff = $tariff->getAsArray();

        if(isset($tariff['settings']['CompetitorAnalysisPhrases']) && $tariff['settings']['CompetitorAnalysisPhrases'] > 0){

            if($count > $tariff['settings']['CompetitorAnalysisPhrases']){
                abort(403, 'Для тарифа: ' . $tariff['name'] . ' лимит ' . $tariff['settings']['CompetitorAnalysisPhrases'] . ' фраз`ы.');
            }
        }
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
