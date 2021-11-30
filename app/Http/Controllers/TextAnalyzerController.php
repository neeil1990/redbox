<?php

namespace App\Http\Controllers;

use App\TextAnalyzer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;
use JavaScript;
use Symfony\Component\VarDumper\VarDumper;


class TextAnalyzerController extends Controller
{

    public function index()
    {
        return view('text-analyzer.index');
    }

    /**
     * @param Request $request
     * @return array|Application|Factory|RedirectResponse|View|mixed
     */
    public function analyze(Request $request)
    {
        $response = [];
        $titleText = '';
        $altText = '';
        $dataText = '';
        if (isset($request->link)) {
            $html = TextAnalyzer::curlInit($request->link);
            if ($html == false) {
                flash()->overlay('connection attempt failed', ' ')->error();
            } else {
                $html = mb_strtolower($html);
                $html = TextAnalyzer::removeHeaders($html);
                if (isset($request->altTitle)) {
//                    $titleText = TextAnalyzer::getTittleText($html);
//                    $altText = TextAnalyzer::getAltText($html);
//                    $dataText = TextAnalyzer::getDataText($html);
                    $titleText = TextAnalyzer::getHiddenText($html, "<.*?title=\"(.*?)\".*?>");
                    $altText = TextAnalyzer::getHiddenText($html, "<.*?alt=\"(.*?)\".*?>");
                    $dataText = TextAnalyzer::getHiddenText($html, "<.*?data-text=\"(.*?)\".*?>");
//                    VarDumper::dump($titleText);
//                    VarDumper::dump($altText);
//                    VarDumper::dump($dataText);
//                    dd();
                }
                if (isset($request->noIndex)) {
                    $html = TextAnalyzer::removeNoindexText($html);
                }
                $text = TextAnalyzer::deleteEverythingExceptCharacters($html);
                if (isset($request->listWords)) {
                    $text = TextAnalyzer::removeWords($request->listWords, $text);
                    $titleText = TextAnalyzer::removeWords($request->listWords, $titleText);
                    $altText = TextAnalyzer::removeWords($request->listWords, $altText);
                    $dataText = TextAnalyzer::removeWords($request->listWords, $dataText);
                }
                if (isset($request->conjunctionsPrepositionsPronouns)) {
                    $text = TextAnalyzer::removeConjunctionsPrepositionsPronouns($text);
                    $titleText = TextAnalyzer::removeConjunctionsPrepositionsPronouns($titleText);
                    $altText = TextAnalyzer::removeConjunctionsPrepositionsPronouns($altText);
                    $dataText = TextAnalyzer::removeConjunctionsPrepositionsPronouns($dataText);
                }
                //clouds
                $response['linksText'] = TextAnalyzer::prepareCloud($titleText);
                $response['textWithLinks'] = TextAnalyzer::prepareCloud($text . $titleText . $dataText);
                $response['textWithoutLinks'] = TextAnalyzer::prepareCloud($text);

                $response['phrases'] = TextAnalyzer::searchPhrases($text);

                $response['totalWords'] = TextAnalyzer::AnalyzeWords($response['textWithoutLinks'], $response['textWithLinks']);
                $response['general'] = [
                    'textLength' => Str::length($text),
                    'countSpaces' => substr_count($text, ' '),
                    'lengthWithOutSpaces' => Str::length($text) - substr_count($text, ' '),
                    'countWords' => count(
                        str_word_count($text, 1, "аАбБвВгГдДеЕёЁжЖзЗиИйЙкКлЛмМнНоОпПрРсСтТуУфФхХцЦчЧшШщЩъыЫьэЭюЮяЯ")
                    ),
                ];

                JavaScript::put([
                    'textWithoutLinks' => $response['textWithoutLinks'],
                    'linksText' => $response['linksText'],
                    'textWithLinks' => $response['textWithLinks'],
                ]);
                return view('text-analyzer.index', ['response' => $response]);
            }
        }

        return Redirect::back();
    }

}
