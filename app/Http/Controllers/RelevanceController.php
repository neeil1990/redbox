<?php

namespace App\Http\Controllers;

use App\Classes\Xml\SimplifiedXmlFacade;
use App\Relevance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RelevanceController extends Controller
{
    public function index()
    {
        return view('relevance-analyzer.index');
    }

    public function analyse(Request $request)
    {
        try {
            $xml = new SimplifiedXmlFacade($request->count, $request->lr);
            $xml->setQuery($request->phrase);
            $xmlResponse = $xml->getXMLResponse();

            $relevance = new Relevance();
            $relevance->getMainPageHtml($request->link);
            $relevance->removeIgnoredDomains($request->count, $request->ignoredDomains, $xmlResponse['response']['results']['grouping']['group']);
            $relevance->parseXmlResponse();
            $relevance->analyse($request);

            return response()->json([
                'clouds' => [
                    'competitorsLinksCloud' => $relevance->competitorsLinksCloud,
                    'mainPageLinksCloud' => $relevance->mainPage['linksCloud'],
                    'competitorsTextAndLinksCloud' => $relevance->competitorsTextAndLinksCloud,
                    'competitorsTextCloud' => $relevance->competitorsTextCloud,
                    'mainPageTextWithLinksCloud' => $relevance->mainPage['textWithLinksCloud'],
                    'mainPageTextCloud' => $relevance->mainPage['textCloud'],
                ],
                'unigramTable' => $relevance->wordForms
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $xmlResponse,
                'error' => true
            ])->setStatusCode(500);
        }
    }
}
