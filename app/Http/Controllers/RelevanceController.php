<?php

namespace App\Http\Controllers;

use App\Classes\Xml\SimplifiedXmlFacade;
use App\Relevance;
use App\RelevanceAnalyseResults;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RelevanceController extends Controller
{
    public function index()
    {
        return view('relevance-analyzer.index');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function analyse(Request $request): JsonResponse
    {
        $start = microtime(true);
        $xml = new SimplifiedXmlFacade(20, $request->region);
        $xml->setQuery($request->phrase);
        $xmlResponse = $xml->getXMLResponse();

        $relevance = new Relevance($request);
        $relevance->getMainPageHtml($request->link);
        $relevance->removeIgnoredDomains($request->count, $request->ignoredDomains, $xmlResponse['response']['results']['grouping']['group']);
        $relevance->parseXmlResponse();
        $relevance->analyse($request);
        $relevance->params->save();

        $finish = microtime(true);
        $delta = $finish - $start;
        Log::debug('analyse', [$delta]);

        return RelevanceController::prepareResponse($relevance, $request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function repeatMainPageAnalyse(Request $request): JsonResponse
    {
        $relevance = new Relevance($request);
        $params = RelevanceAnalyseResults::where('user_id', '=', Auth::id())->first();
        $sites = explode($relevance->separator, $params->sites);
        unset($sites[count($sites) - 1]);
        $relevance->getMainPageHtml($request->link);
        $relevance->setSites($sites, $params->sites);
        $relevance->setPages($sites, $params->html_relevance);

        $relevance->analyse($request);
        $relevance->params->save();

        return RelevanceController::prepareResponse($relevance, $request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function repeatRelevanceAnalyse(Request $request): JsonResponse
    {
        $params = RelevanceAnalyseResults::where('user_id', '=', Auth::id())->first();
        $xml = new SimplifiedXmlFacade(20, $request->region);
        $xml->setQuery($request->phrase);
        $xmlResponse = $xml->getXMLResponse();

        $relevance = new Relevance($request);
        $relevance->setMainPage($params->html_main_page);
        $relevance->removeIgnoredDomains($request->count, $request->ignoredDomains, $xmlResponse['response']['results']['grouping']['group']);
        $relevance->parseXmlResponse();
        $relevance->analyse($request);
        $relevance->params->save();

        return RelevanceController::prepareResponse($relevance, $request);
    }

    /**
     * @param $relevance
     * @param $request
     * @return JsonResponse
     */
    public function prepareResponse($relevance, $request): JsonResponse
    {
        $text = $relevance->competitorsText . ' ' . $relevance->competitorsLinks;
        $avgLength = Str::length($text) / $request->count;
        $avgSpaces = TextLengthController::countingSpaces($text) / $request->count - 1;
        $avgCountWords = TextLengthController::countingWord($text) / $request->count;

        $mainPageText = $relevance->mainPage['html'] . ' ' . $relevance->mainPage['linkText'];
        $length = Str::length($mainPageText);
        $countSpaces = TextLengthController::countingSpaces($mainPageText) - 1;
        $countWords = TextLengthController::countingWord($mainPageText);

        return response()->json([
            'clouds' => [
                'competitorsTextAndLinksCloud' => $relevance->competitorsTextAndLinksCloud,
                'competitorsLinksCloud' => $relevance->competitorsLinksCloud,
                'competitorsTextCloud' => $relevance->competitorsTextCloud,
                'mainPageTextWithLinksCloud' => $relevance->mainPage['textWithLinksCloud'],
                'mainPageLinksCloud' => $relevance->mainPage['linksCloud'],
                'mainPageTextCloud' => $relevance->mainPage['textCloud'],
            ],
            'unigramTable' => $relevance->wordForms,
            'sites' => $relevance->sites,
            'avg' => [
                'countWords' => $avgCountWords,
                'countSpaces' => $avgSpaces,
                'countSymbols' => $avgLength,
                'countSymbolsWithoutSpaces' => $avgLength - $avgSpaces
            ],
            'mainPage' => [
                'countWords' => $countWords,
                'countSpaces' => $countSpaces,
                'countSymbols' => $length,
                'countSymbolsWithoutSpaces' => $length - $countSpaces
            ]
        ]);
    }

}
