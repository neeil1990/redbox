<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UniqueWordsController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:Unique words']);
    }

    /**
     * @return array|false|Application|Factory|View|mixed
     */
    public function index()
    {
        return view('unique-words.unique-words');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function countingUniqueWords(Request $request): JsonResponse
    {
        $listWords = self::stringToCollectionWords($request->phrases);
        $listWordsWithKeys = self::addKeysInCollect($listWords);
        return response()->json([
            'list' => $listWordsWithKeys[0],
            'length' => $listWordsWithKeys[1]
        ]);
    }

    /**
     * @param $listWords
     * @return array
     */
    public static function addKeysInCollect($listWords): array
    {
        $listWordsWithKeys = [];
        $i = 0;
        foreach ($listWords as $item) {
            $listWordsWithKeys[$i] = $item;
            $i++;
        }

        return [$listWordsWithKeys, $i];
    }

    /**
     * @param $string
     * @return Collection
     */
    public static function stringToCollectionWords($string): Collection
    {
        $string = mb_strtolower($string);
        $string = self::removeExtraSymbols($string);
        $phrases = self::getPhrases($string);
        $words = self::getWords($string);
        $t = self::searchWordsInPhrases($words, $phrases);
        $words = collect($t)->map(function ($arr) {
            return array_combine(['word', 'wordForms', 'numberOccurrences', 'keyPhrases'], $arr);
        });

        return $words->sortByDesc('numberOccurrences');
    }

    /**
     * @param $string
     * @return string
     */
    public static function removeExtraSymbols($string): string
    {
        $string = mb_eregi_replace('[^\w\s\n]', ' ', $string);
        $string = mb_eregi_replace('[ ]+', ' ', $string);
        return trim($string);
    }

    /**
     * @param $string
     * @return false|string[]
     */
    public static function getPhrases($string): array
    {
        $string = str_replace(["\r", "\n", "\r\n", "\n*"], PHP_EOL, $string);
        return explode(PHP_EOL, $string);
    }

    /**
     * @param $string
     * @return array
     */
    public static function getWords($string): array
    {
        $words = str_replace([" ", "\n", "\r\n", "\n*"], ' ', $string);
        $words = explode(' ', $words);
        return array_diff($words, array(""));
    }

    /**
     * @param $words
     * @param $phrases
     * @return array
     */
    public static function searchWordsInPhrases($words, $phrases): array
    {
        $countValues = array_count_values($words);
        $t = [];
        foreach ($words as $word) {
            foreach ($countValues as $key => $value) {
                if ($word == $key) {
                    $matches = self::searchMatches($phrases, $word);
                    array_push($t, [$word, $word, $value, $matches]);
                }
            }
        }

        return array_unique($t, SORT_REGULAR);
    }

    /**
     * @param $phrases
     * @param $word
     * @return array
     */
    public static function searchMatches($phrases, $word): array
    {
        $result = [];
        foreach ($phrases as $phrase) {
            if (Str::contains($phrase, $word)) {
                array_push($result, $phrase);
            }
        }
        return $result;
    }

    /**
     * @param $text
     * @return JsonResponse
     */
    public static function uploadFIle($text): JsonResponse
    {
        $fileName = md5(Carbon::now());
        Storage::put('files\\' . $fileName . '.csv', $text);
        return response()->json([
            'fileName' => $fileName
        ]);
    }

    /**
     * @param $words
     * @param $ids
     */
    public static function removeExtraItems($words, $ids)
    {
        $extraIds = explode(' ', $ids);
        foreach ($extraIds as $extraId) {
            $words->pull($extraId);
        }
    }

    /**
     * @param $request
     * @return string
     */
    public static function confirmSubject($request): string
    {
        $subject = '';
        if ($request->uniqueWord === 'on') {
            $subject .= __('Word') . ';';
        }
        if ($request->uniqueWordForms === 'on') {
            $subject .= __('Word forms') . ';';
        }
        if ($request->numberOccurrences === 'on') {
            $subject .= __('Number of occurrences') . ';';
        }
        if ($request->keyPhrases === 'on') {
            $subject .= __('Key phrases') . ';';
        }
        $subject .= "\n";

        return $subject;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createFile(Request $request): JsonResponse
    {
        if ($request->text) {
            return self::uploadFIle($request->text);
        }
        return self::uploadFIle($request->keyPhrases);
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function downloadFile(Request $request): BinaryFileResponse
    {
        return response()->download(storage_path('app/public/files/' . $request->fileName . '.csv'));
    }
}
