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
        $response['link'] = $request->link;
        $html = TextAnalyzer::curlInit($request->link);
        if ($html == false) {
            flash()->overlay('connection attempt failed', ' ')->error();
        } else {
            $html = mb_strtolower($html);
            $html = TextAnalyzer::removeHeaders($html);

            if (empty($request->noIndex)) {
                $html = TextAnalyzer::removeNoindexText($html);
            } else {
                $response['noIndex'] = true;
            }
            $linkText = TextAnalyzer::getLinkText($html);
            if (isset($request->hiddenText)) {
                $titleText = TextAnalyzer::getHiddenText($html, "<.*?title=\"(.*?)\".*?>");
                $altText = TextAnalyzer::getHiddenText($html, "<.*?alt=\"(.*?)\".*?>");
                $dataText = TextAnalyzer::getHiddenText($html, "<.*?data-text=\"(.*?)\".*?>");
                $response['hiddenText'] = true;
            }

            $text = TextAnalyzer::deleteEverythingExceptCharacters($html);
            if (empty($request->conjunctionsPrepositionsPronouns)) {
                $text = TextAnalyzer::removeConjunctionsPrepositionsPronouns($text);
                $titleText = TextAnalyzer::removeConjunctionsPrepositionsPronouns($titleText);
                $altText = TextAnalyzer::removeConjunctionsPrepositionsPronouns($altText);
                $dataText = TextAnalyzer::removeConjunctionsPrepositionsPronouns($dataText);
                $linkText = TextAnalyzer::removeConjunctionsPrepositionsPronouns($linkText);
            } else {
                $response['conjunctionsPrepositionsPronouns'] = true;
            }

            if (empty($request->listWords)) {
                $text = TextAnalyzer::removeWords($request->listWords, $text);
                $titleText = TextAnalyzer::removeWords($request->listWords, $titleText);
                $altText = TextAnalyzer::removeWords($request->listWords, $altText);
                $dataText = TextAnalyzer::removeWords($request->listWords, $dataText);
                $linkText = TextAnalyzer::removeWords($request->listWords, $linkText);
            } else {
                $response['listWords'] = $request->listWords;
            }

            $response['general'] = [
                'textLength' => Str::length($text),
                'countSpaces' => substr_count($text, ' '),
                'lengthWithOutSpaces' => Str::length($text) - substr_count($text, ' '),
                'countWords' => count(
                    str_word_count($text, 1, "аАбБвВгГдДеЕёЁжЖзЗиИйЙкКлЛмМнНоОпПрРсСтТуУфФхХцЦчЧшШщЩъыЫьэЭюЮяЯ")
                ),
            ];

            //clouds
            $response['linksText'] = TextAnalyzer::prepareCloud($linkText);
            $response['textWithoutLinks'] = TextAnalyzer::prepareCloud($text);
            $response['textWithLinks'] = TextAnalyzer::prepareCloud($text . $altText . $titleText . $dataText . $linkText);

            $response['totalWords'] = TextAnalyzer::AnalyzeWords($response['textWithoutLinks'], $response['linksText']);
            $response['phrases'] = TextAnalyzer::searchPhrases($text . $altText . $titleText . $dataText . $linkText);

            $graph = TextAnalyzer::prepareDataGraph($response['totalWords']);

            JavaScript::put([
                'textWithoutLinks' => $response['textWithoutLinks'],
                'linksText' => $response['linksText'],
                'textWithLinks' => $response['textWithLinks'],
                'graph' => $graph,

            ]);
            return view('text-analyzer.index', ['response' => $response]);
        }

        return Redirect::back();
    }

}
