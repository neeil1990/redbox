<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TextLengthController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:Counting text length']);
    }

    /**
     * @return Factory|View
     */
    public function index(): View
    {
        return view('pages.length');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function countingTextLength(Request $request): JsonResponse
    {
        $length = Str::length($request->text);
        $countSpaces = self::countingSpaces($request->text);
        $lengthWithOutSpaces = $length - $countSpaces;
        $data = [
            'success' => true,
            'text' => $request->text,
            'length' => $length,
            'countSpaces' => $countSpaces,
            'lengthWithOutSpaces' => $lengthWithOutSpaces,
            'countWords' => self::countingWord($request->text)
        ];

        return response()->json(['data' => $data]);
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
