<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CountingTextLengthController extends Controller
{

    /**
     * @return Factory|View
     */
    public function index(): View
    {
        return view('pages.length');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function countingTextLength(Request $request): RedirectResponse
    {
        $length = Str::length($request->text);
        $countSpaces = self::countingSpaces($request->text);
        $lengthWithOutSpaces = $length - $countSpaces;
        Session::flash('text', $request->text);
        Session::flash('length', $length);
        Session::flash('countSpaces', $countSpaces);
        Session::flash('lengthWithOutSpaces', $lengthWithOutSpaces);
        Session::flash('countWord', self::countingWord($request->text));

        return Redirect::back();
    }

    /**
     * @param $str
     * @return int
     */
    public static function countingSpaces($str): int
    {
        return substr_count($str, ' ');
    }

    /**
     * @param $str
     * @return int
     */
    public static function countingWord($str): int
    {
        return
            count(
                str_word_count(
                    $str,
                    1,
                    "аАбБвВгГдДеЕёЁжЖзЗиИйЙкКлЛмМнНоОпПрРсСтТуУфФхХцЦчЧшШщЩъыЫьэЭюЮяЯ"

                )
            );
    }

}
