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
        $title = '';
        $alt = '';
        $data = '';
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
            $link = TextAnalyzer::getLinkText($html);
            if (isset($request->hiddenText)) {
                $title = TextAnalyzer::getHiddenText($html, "<.*?title=\"(.*?)\".*>");
                $alt = TextAnalyzer::getHiddenText($html, "<.*?alt=\"(.*?)\".*>");
                $data = TextAnalyzer::getHiddenText($html, "<.*?data-text=\"(.*?)\".*>");
                $response['hiddenText'] = true;
            }

            $html = TextAnalyzer::clearHTMLFromLinks($html);
            $text = TextAnalyzer::deleteEverythingExceptCharacters($html);
            if (empty($request->conjunctionsPrepositionsPronouns)) {
                $text = TextAnalyzer::removeConjunctionsPrepositionsPronouns($text);
                $title = TextAnalyzer::removeConjunctionsPrepositionsPronouns($title);
                $alt = TextAnalyzer::removeConjunctionsPrepositionsPronouns($alt);
                $data = TextAnalyzer::removeConjunctionsPrepositionsPronouns($data);
                $link = TextAnalyzer::removeConjunctionsPrepositionsPronouns($link);
            } else {
                $response['conjunctionsPrepositionsPronouns'] = true;
            }

            if (isset($request->switchMyListWords)) {
                $text = TextAnalyzer::removeWords($request->listWords, $text);
                $title = TextAnalyzer::removeWords($request->listWords, $title);
                $alt = TextAnalyzer::removeWords($request->listWords, $alt);
                $data = TextAnalyzer::removeWords($request->listWords, $data);
                $link = TextAnalyzer::removeWords($request->listWords, $link);
                $response['listWords'] = $request->listWords;
            }

            $total = $text . ' ' . $alt . ' ' . $title . ' ' . $data;
            $length = Str::length($total . ' ' . $link);
            $countSpaces = substr_count($total . ' ' . $link, ' ');
            $response['general'] = [
                'textLength' => $length,
                'countSpaces' => $countSpaces,
                'lengthWithOutSpaces' => $length - $countSpaces,
                'countWords' => count(
                    str_word_count($text, 1, "аАбБвВгГдДеЕёЁжЖзЗиИйЙкКлЛмМнНоОпПрРсСтТуУфФхХцЦчЧшШщЩъыЫьэЭюЮяЯ")
                ),
            ];

            //clouds
            $textWithoutLinks = TextAnalyzer::prepareCloud($total);
            $linksText = TextAnalyzer::prepareCloud($link);
            $textWithLinks = TextAnalyzer::prepareCloud($total . $link);

            $response['totalWords'] = TextAnalyzer::AnalyzeWords($total, $link);
            $response['phrases'] = TextAnalyzer::searchPhrases($total . $link);

            JavaScript::put([
                'textWithoutLinks' => $textWithoutLinks,
                'textWithLinks' => $textWithLinks,
                'linksText' => $linksText,
                'graph' => TextAnalyzer::prepareDataGraph($response['totalWords']),

            ]);
            return view('text-analyzer.index', ['response' => $response]);
        }

        return Redirect::back();

    }

}
