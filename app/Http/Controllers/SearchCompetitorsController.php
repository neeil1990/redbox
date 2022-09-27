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
                'message' => __('Exceeding the limit')
            ], 500);
        }
        $analysis = new SearchCompetitors();
        $analysis->setPhrases($request->input('phrases'));
        if (count($analysis->getPhrases()) > 40) {
            return response()->json([
                'message' => __('The maximum number of keywords is 40, and you have - ') . count($analysis->getPhrases())
            ], 500);
        }

        $analysis->setRegion($request->input('region'));
        $analysis->setCount($request->input('count'));
        $analysis->setPageHash($request->input('pageHash'));

        try {
            $analysis->analyzeList();

            return response()->json([
                'result' => $analysis->getResult(),
            ]);
        } catch (Throwable $e) {
            Log::debug('competitor error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'object' => CompetitorsProgressBar::where('page_hash', '=', $request->input('pageHash'))->delete(),
                'message' => __('An unexpected error has occurred, please contact the administrator')
            ], 500);
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
        $config->update($request->all());

        return Redirect::back();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getRecommendations(Request $request): JsonResponse
    {
        $phrases = json_decode($request->input('selectedPhrases'), true);
        $tags = json_decode($request->input('selectedTags'), true);

        if (count($phrases) < 0 || count($tags) < 0) {
            return response()->json([
                'message' => __('invalid data')
            ], 500);
        }

        $metaTags = json_decode($request->input('metaTags'), true);
        $countPhrases = count($phrases);

        return response()->json([
            'result' => SearchCompetitors::getRecommendations($phrases, $tags, $metaTags, $countPhrases, $request->input('count')),
            'tags' => $tags,
            'code' => 200
        ]);

    }
}
