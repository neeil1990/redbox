<?php

namespace App\Http\Controllers;

use App\Common;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;


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
        $html = Common::curlInit($request->link);
        if ($html == false) {
            flash()->overlay('connection attempt failed', ' ')->error();
        } else {
            $text = Common::deleteEverythingExceptCharacters($html);
            $response = [];
            $response['textLength'] = Str::length($text);
            $response['countSpaces'] = substr_count($text, ' ');
            $response['lengthWithOutSpaces'] = $response['textLength'] - $response['countSpaces'];
            $response['countWords'] = count(
                str_word_count(
                    $text,
                    1,
                    "аАбБвВгГдДеЕёЁжЖзЗиИйЙкКлЛмМнНоОпПрРсСтТуУфФхХцЦчЧшШщЩъыЫьэЭюЮяЯ"
                )
            );

            return view('text-analyzer.index', ['response' => $response]);
        }

        return Redirect::back();
    }

}
