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
                'message' => __('Your limits are exhausted this month')
            ], 500);
        }

        try {
            $analysis = new SearchCompetitors();
            $analysis->setPhrases($request->input('phrases'));
            $analysis->setRegion($request->input('region'));
            $analysis->setCount($request->input('count'));
            $analysis->setPageHash($request->input('pageHash'));
            $analysis->analyzeList();

            return response()->json([
                'result' => $analysis->getResult(),
            ]);
        } catch (Throwable $e) {
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
        if (count($request->input('selectedPhrases')) < 0 || count($request->input('selectedTags')) < 0) {
            return response()->json([
                'message' => __('invalid data')
            ], 500);
        }

        $config = CompetitorConfig::first();

        if ($request->input('count') === 10) {
            $delimiter = $config->count_repeat_top_10;
        } else {
            $delimiter = $config->count_repeat_top_20;
        }

        $phrases = $request->input('selectedPhrases');
        $tags = $request->input('selectedTags');
        $metaTags = $request->input('metaTags');
        $countPhrases = count($phrases);

        $information = [];
        foreach ($phrases as $phrase) {
            foreach ($metaTags[$phrase] as $tag => $values) {
                if (in_array($tag, $tags)) {
                    $information[$tag][] = $values;
                }
            }
        }

        $result = [];
        foreach ($information as $tag => $values) {
            foreach ($values as $value) {
                foreach ($value as $word => $count) {
                    if (isset($result[$tag][$word])) {
                        $result[$tag][$word] += $count;
                    } else {
                        $result[$tag][$word] = $count;
                    }
                }
            }
        }

        foreach ($result as $tag => $values) {
            foreach ($values as $word => $count) {
                if (($count / $countPhrases) < $delimiter || $word === 0) {
                    unset($result[$tag][$word]);
                }
            }
        }

        return response()->json([
            'result' => $result,
            'tags' => $tags,
            'code' => 200
        ]);

    }
}
