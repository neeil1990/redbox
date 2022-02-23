<?php

namespace App\Http\Controllers;

use App\Classes\Xml\SimplifiedXmlFacade;
use App\Relevance;
use App\RelevanceAnalyseResults;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RelevanceController extends Controller
{
    public function index()
    {
        return view('relevance-analyzer.index');
    }

    /**
     * @param Request $request
     * @return JsonResponse|void
     */
    public function analyse(Request $request)
    {
        $params = RelevanceAnalyseResults::where('user_id', '=', Auth::id())->first();
        if (isset($params)) {
            $configHash = RelevanceAnalyseResults::calculateHash([
                $request->noIndex,
                $request->hiddenText,
                $request->conjunctionsPrepositionsPronouns,
                $request->switchMyListWords,
                $request->listWords
            ]);
            $xmlHash = RelevanceAnalyseResults::calculateHash([
                $request->phrase,
                $request->link,
                $request->region,
                $request->count
            ]);

            if ($params->xml_hash == $xmlHash && $params->config_hash == $configHash) {
                return response()->json([
                    'repeat' => true,
                    'message' => 'Ваш прошлый анализ был выполнен с такими же параметрами, вы уверены что хотите запустить анализ снова?'
                ])->setStatusCode(500);
            }

            if ($params->xml_hash == $xmlHash && $params->config_hash != $configHash) {
                //не парсить страницы, а обрабатывать начальную версию кода применяя к ней выбранные параметры
                return RelevanceController::analyseWithoutXmlRequest($request, $params);
            }

            if (
                $params->xml_hash != $xmlHash && $params->config_hash != $configHash ||
                $params->xml_hash != $xmlHash && $params->config_hash == $configHash
            ) {
                //Ключевые данные изменены, прогоняем анализ с 0
                return RelevanceController::analyseWithXmlRequest($request);
            }
        } else {
            //Первый запрос юзера
            return RelevanceController::analyseWithXmlRequest($request);
        }
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function analyseWithXmlRequest($request): JsonResponse
    {
        $xml = new SimplifiedXmlFacade(20, $request->lr);
        $xml->setQuery($request->phrase);
        $xmlResponse = $xml->getXMLResponse();

        $relevance = new Relevance($request);
        $relevance->getMainPageHtml($request->link);
        $relevance->removeIgnoredDomains($request->count, $request->ignoredDomains, $xmlResponse['response']['results']['grouping']['group']);
        $relevance->parseXmlResponse();
        $relevance->analyse($request);
        $relevance->params->save();

        return RelevanceController::prepareResponse($relevance);
    }

    /**
     * @param $request
     * @param $params
     * @return JsonResponse
     */
    public function analyseWithoutXmlRequest($request, $params): JsonResponse
    {
        $relevance = new Relevance($request);
        $sites = explode($relevance->separator, $params->sites);
        unset($sites[count($sites) - 1]);

        $relevance->setMainPage($params->html_main_page);
        $relevance->setSites($sites);
        $relevance->setPages($sites, $params->html_relevance);

        $relevance->analyse($request);
        $relevance->params->save();

        return RelevanceController::prepareResponse($relevance);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function repeatAnalyse(Request $request): JsonResponse
    {
        return RelevanceController::analyseWithXmlRequest($request);
    }

    /**
     * @param $relevance
     * @return JsonResponse
     */
    public function prepareResponse($relevance): JsonResponse
    {
        return response()->json([
            'clouds' => [
                'competitorsLinksCloud' => $relevance->competitorsLinksCloud,
                'mainPageLinksCloud' => $relevance->mainPage['linksCloud'],
                'competitorsTextAndLinksCloud' => $relevance->competitorsTextAndLinksCloud,
                'competitorsTextCloud' => $relevance->competitorsTextCloud,
                'mainPageTextWithLinksCloud' => $relevance->mainPage['textWithLinksCloud'],
                'mainPageTextCloud' => $relevance->mainPage['textCloud'],
            ],
            'unigramTable' => $relevance->wordForms,
            'sites' => $relevance->sites,
        ]);
    }

}
