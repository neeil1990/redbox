<?php

namespace App\Http\Controllers;

use App\Classes\Xml\SimplifiedXmlFacade;
use App\Relevance;
use App\RelevanceAnalyseResults;
use App\RelevanceAnalysisConfig;
use App\TestRelevance;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RelevanceController extends Controller
{
    /**
     * @return array|false|Application|Factory|View|mixed
     */
    public function index()
    {
        $admin = false;
        foreach (Auth::user()->role as $role) {
            if ($role == '1' || $role == '3') {
                $admin = true;
                break;
            }
        }
        $config = RelevanceAnalysisConfig::first();

        return view('relevance-analysis.index', ['admin' => $admin, 'config' => $config]);
    }

    /**
     * @return array|false|Application|Factory|View|mixed
     */
    public function testView()
    {
        $admin = false;
        foreach (Auth::user()->role as $role) {
            if ($role == '1' || $role == '3') {
                $admin = true;
                break;
            }
        }
        $config = RelevanceAnalysisConfig::first();

        return view('relevance-analysis.test', ['admin' => $admin, 'config' => $config]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function analysis(Request $request): JsonResponse
    {
        $relevance = new Relevance($request->input('link'), $request->input('separator'));

        $messages = [
            'link.required' => __('A link to the landing page is required.'),
            'phrase.required' => __('The keyword is required to fill in.'),
            'siteList.required' => __('The list of sites is required to fill in.'),
        ];

        if ($request->input('type') === 'phrase') {
            $request->validate([
                'link' => 'required|website',
                'phrase' => 'required',
            ], $messages);
        } else {
            $request->validate([
                'link' => 'required|website',
                'siteList' => 'required',
            ], $messages);

            $sitesList = str_replace("\r\n", "\n", $request->input('siteList'));
            $sitesList = explode("\n", $sitesList);

            if (count($sitesList) <= 7) {
                return response()->json([
                    'countError' => 'Список сайов должен содержать минимум 7 сайтов'
                ], 500);
            }

            foreach ($sitesList as $item) {
                $relevance->domains[] = [
                    'item' => str_replace('www.', "", mb_strtolower(trim($item))),
                    'ignored' => false,
                ];
            }
        }

        $relevance->getMainPageHtml();

        if ($request->input('type') === 'phrase') {
            $xml = new SimplifiedXmlFacade(50, $request->input('region'));
            $xml->setQuery($request->input('phrase'));
            $xmlResponse = $xml->getXMLResponse();

            $relevance->removeIgnoredDomains(
                $request->input('count'),
                $request->input('ignoredDomains'),
                $xmlResponse
            );

        }
        $relevance->parseSites();
        $relevance->analysis($request);

        return RelevanceController::successResponse($relevance);
    }

    /**
     * Повторный анализ конкурентов с использованием html посадочной страницы, которая была получена во время прошлого запроса
     * @param Request $request
     * @return JsonResponse
     */
    public function repeatRelevanceAnalysis(Request $request): JsonResponse
    {
        try {
            $params = RelevanceAnalyseResults::where('user_id', '=', Auth::id())->first();
            $relevance = new Relevance($params->main_page_link, $request->input('separator'));
            $relevance->setMainPage($params->html_main_page);
            $relevance->setDomains($params->sites);
            $relevance->parseSites();
            $relevance->analysis($request);
            return RelevanceController::successResponse($relevance);
        } catch (Exception $e) {
            return RelevanceController::errorResponse($request, $e);
        }
    }

    /**
     * Парсинг посадочной страницы и забираем данные конкурентов полученые во время прошлого сканирования
     * @param Request $request
     * @return JsonResponse
     */
    public function repeatMainPageAnalysis(Request $request): JsonResponse
    {
        $messages = [
            'link.required' => __('A link to the landing page is required.'),
        ];

        $request->validate([
            'link' => 'required|website',
        ], $messages);

        try {
            $relevance = new Relevance($request->input('link'), $request->input('separator'));
            $params = RelevanceAnalyseResults::where('user_id', '=', Auth::id())->first();
            $relevance->getMainPageHtml();
            $relevance->setSites($params->sites);
            $relevance->analysis($request);
            return RelevanceController::successResponse($relevance);
        } catch (Exception $e) {
            return RelevanceController::errorResponse($request, $e);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function testAnalyse(Request $request): JsonResponse
    {
        Log::debug('c', [$request->count]);
        Log::debug('reh', [$request->region]);
        die();
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

        $relevance = new TestRelevance($request->input('link'), $request->input('separator'));
        $relevance->getMainPageHtml();

        if ($request->input('type') === 'list') {
            $sitesList = str_replace("\r\n", "\n", $request->input('siteList'));
            $sitesList = explode("\n", $sitesList);

            if (count($sitesList) <= 7) {
                return response()->json([
                    'countError' => 'Список сайов должен содержать минимум 7 сайтов'
                ], 500);
            }

            foreach ($sitesList as $item) {
                $relevance->domains[] = [
                    'item' => str_replace('www.', "", mb_strtolower(trim($item))),
                    'ignored' => false,
                ];
            }
        } else {
            $xml = new SimplifiedXmlFacade(50, $request->input('region'));
            $xml->setQuery($request->input('phrase'));
            $xmlResponse = $xml->getXMLResponse();
            $relevance->removeIgnoredDomains(
                $request->input('count'),
                $request->input('ignoredDomains'),
                $xmlResponse
            );
        }
        $relevance->parseSites();

        $relevance->analysis($request);

        return RelevanceController::successResponse($relevance);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function configureChildrenRows(Request $request): JsonResponse
    {
        $filename = md5(microtime(true));
        $filePath = public_path('children/' . $filename . '.json');
        $wordForms = json_decode($request->sessionStorage, true);

        $result = [];
        foreach ($wordForms as $wordForm) {
            foreach ($wordForm as $keyword => $word) {
                if ($keyword != 'total') {
                    $result[$keyword] = $word;
                }
            }
        }
        if (File::put($filePath, json_encode($result, JSON_UNESCAPED_UNICODE))) {
            return response()->json([
                'filename' => $filename
            ], 201);
        } else {
            return response()->json([
                'message' => 'Файл не создан'
            ], 500);
        }
    }

    /**
     * @param $fileName
     * @return array|false|Application|Factory|View|mixed
     */
    public function showChildrenRows($fileName)
    {
        $filePath = public_path('children/' . $fileName . '.json');

        $file = File::get($filePath);
        $array = json_decode($file, true);

        return view('relevance-analysis.children', ['array' => $array]);
    }

    /**
     * @param $relevance
     * @return JsonResponse
     */
    public function successResponse($relevance): JsonResponse
    {
        $config = RelevanceAnalysisConfig::first();

        $count = count($relevance->sites);
        $text = Relevance::concatenation([$relevance->competitorsText, $relevance->competitorsLinks]);
        $avgCountWords = TextLengthController::countingWord($text) / $count;
        $mainPageText = Relevance::concatenation([
            $relevance->mainPage['html'],
            $relevance->mainPage['linkText'],
            $relevance->mainPage['hiddenText']
        ]);

        return response()->json([
            'clouds' => [
                'competitors' => [
                    'totalTf' => $relevance->competitorsCloud['totalTf'],
                    'textTf' => $relevance->competitorsCloud['textTf'],
                    'linkTf' => $relevance->competitorsCloud['linkTf'],

                    'textAndLinks' => $relevance->competitorsTextAndLinksCloud,
                    'links' => $relevance->competitorsLinksCloud,
                    'text' => $relevance->competitorsTextCloud,
                ],
                'mainPage' => [
                    'totalTf' => $relevance->mainPage['totalTf'],
                    'textTf' => $relevance->mainPage['textTf'],
                    'linkTf' => $relevance->mainPage['linkTf'],
                    'textWithLinks' => $relevance->mainPage['textWithLinks'],
                    'links' => $relevance->mainPage['links'],
                    'text' => $relevance->mainPage['text'],
                ]
            ],
            'avg' => [
                'countWords' => $avgCountWords,
                'countSymbols' => Str::length($text) / $count,
            ],
            'mainPage' => [
                'countWords' => TextLengthController::countingWord($mainPageText),
                'countSymbols' => Str::length($mainPageText),
            ],
            'unigramTable' => $relevance->wordForms,
            'sites' => $relevance->sites,
            'tfCompClouds' => $relevance->tfCompClouds,
            'phrases' => $relevance->phrases,
            //new functions
            'avgCoveragePercent' => $relevance->avgCoveragePercent ?? null,
            'recommendations' => $relevance->recommendations ?? null,
            'ltp_count' => $config->ltp_count,
            'ltps_count' => $config->ltps_count,
            'recommendations_count' => $config->recommendations_count,
            'scanned_sites_count' => $config->scanned_sites_count,
            'hide_ignored_domains' => $config->hide_ignored_domains,
            'boostPercent' => $config->boostPercent,
        ]);
    }

    /**
     * @param $request
     * @param $e
     * @return JsonResponse
     */
    public static function errorResponse($request, $e): JsonResponse
    {
        Log::debug('relevance scan error', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'request' => $request->all()
        ]);

        return response()->json()->setStatusCode(500);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function changeConfig(Request $request): RedirectResponse
    {
        $config = RelevanceAnalysisConfig::first();
        if (!$config) {
            $config = new RelevanceAnalysisConfig();
        }

        $config->count_sites = $request->count;
        $config->region = $request->region;
        $config->ignored_domains = $request->ignored_domains;
        $config->separator = $request->separator;

        $config->noindex = $request->noindex;
        $config->meta_tags = $request->meta_tags;
        $config->parts_of_speech = $request->parts_of_speech;
        $config->remove_my_list_words = $request->remove_my_list_words;
        $config->my_list_words = $request->my_list_words;
        $config->hide_ignored_domains = $request->hide_ignored_domains;

        $config->ltp_count = $request->ltp_count;
        $config->ltps_count = $request->ltps_count;
        $config->scanned_sites_count = $request->scanned_sites_count;
        $config->recommendations_count = $request->recommendations_count;

        $config->boostPercent = $request->boostPercent;

        $config->save();

        return Redirect::back();
    }
}
