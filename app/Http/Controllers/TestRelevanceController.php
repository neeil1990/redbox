<?php

namespace App\Http\Controllers;

use App\ProjectRelevanceHistory;
use App\Queue;
use App\RelevanceAnalysisConfig;
use App\TestRelevance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class TestRelevanceController extends Controller
{
    /**
     * @return View
     */
    public function testView(): View
    {
        $config = RelevanceAnalysisConfig::first();
        $admin = false;
        foreach (Auth::user()->role as $role) {
            if ($role == '1' || $role == '3') {
                $admin = true;
                break;
            }
        }

        return view('relevance-analysis.test.index', [
            'admin' => $admin,
            'config' => $config,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function testAnalyse(Request $request): JsonResponse
    {
        $messages = [
            'link.required' => __('A link to the landing page is required.'),
            'phrase.required_without' => __('The keyword is required to fill in.'),
            'siteList.required_without' => __('The list of sites is required to fill in.'),
        ];

        $request->validate([
            'link' => 'required|website',
            'phrase' => 'required_without:siteList|not_website',
            'siteList' => 'required_without:link',
        ], $messages);

        $relevance = new TestRelevance($request->input('link'), $request->input('phrase'), $request->input('separator'));
        $relevance->getMainPageHtml();

        if ($request['type'] == 'phrase') {
            $relevance->analysisByPhrase($request->all());
        } elseif ($request['type'] == 'list') {
            $relevance->analysisByList($request['siteList']);
        }

        $relevance->analysis($request->all(), Auth::id());

        return RelevanceController::successResponse($relevance);
    }

    /**
     * @return View
     */
    public function createQueue(): View
    {
        $config = RelevanceAnalysisConfig::first();
        $admin = false;
        foreach (Auth::user()->role as $role) {
            if ($role == '1' || $role == '3') {
                $admin = true;
                break;
            }
        }

        return view('relevance-analysis.test.queue', [
            'config' => $config,
            'admin' => $admin,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createTaskQueue(Request $request): JsonResponse
    {
        $rows = explode("\n", $request->params);
        foreach ($rows as $row) {
            Queue::addInQueue($row, $request);
        }

        return response()->json([]);
    }

    /**
     * @return View
     */
    public function history(): View
    {
        $history = [];
        $main = ProjectRelevanceHistory::where('user_id', '=', Auth::id())->get();
        foreach ($main as $item) {
            foreach ($item->stories as $story) {
                $history[] = $story;
            }
        }

        $admin = false;
        foreach (Auth::user()->role as $role) {
            if ($role == '1' || $role == '3') {
                $admin = true;
                break;
            }
        }

        $config = RelevanceAnalysisConfig::first();

        return view('relevance-analysis.test.history', [
            'main' => $main,
            'history' => $history,
            'admin' => $admin,
            'config' => $config
        ]);
    }
}
