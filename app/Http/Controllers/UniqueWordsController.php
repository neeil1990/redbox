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

    public function index()
    {
        return view('unique-words.unique-words');
    }

    public function countingUniqueWords(Request $request): JsonResponse
    {
        $listWords = $this->stringToCollectionWords($request->phrases);
        $listWordsWithKeys = $this->addKeysInCollect($listWords);

        return response()->json([
            'list' => $listWordsWithKeys[0],
            'length' => $listWordsWithKeys[1]
        ]);
    }

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

    public function stringToCollectionWords($string): Collection
    {
        $string = mb_strtolower($string);
        $string = $this->removeExtraSymbols($string);
        $phrases = $this->getPhrases($string);
        $words = $this->getWords($string);
        $t = $this->searchWordsInPhrases($words, $phrases);
        $words = collect($t)->map(function ($arr) {
            return array_combine(['word', 'wordForms', 'numberOccurrences', 'keyPhrases'], $arr);
        });

        return $words->sortByDesc('numberOccurrences');
    }

    public static function removeExtraSymbols($string): string
    {
        $string = mb_eregi_replace('[^\w\s\n]', ' ', $string);
        $string = mb_eregi_replace('[ ]+', ' ', $string);
        $string = preg_replace('/[0-9]+/', '', $string);

        return trim($string);
    }

    public static function getPhrases($string): array
    {
        $string = str_replace(["\r", "\n", "\r\n", "\n*"], PHP_EOL, $string);

        return explode(PHP_EOL, $string);
    }

    public static function getWords($string): array
    {
        $words = str_replace([" ", "\n", "\r\n", "\n*", "\t"], ' ', $string);
        $words = explode(' ', $words);

        return array_diff($words, array(""));
    }

    public function searchWordsInPhrases($words, $phrases): array
    {
        $countValues = array_count_values($words);
        $t = [];
        foreach ($words as $word) {
            Log::debug($word, [substr_count($word, '	')]);
            foreach ($countValues as $key => $value) {
                if ($word == $key) {
                    $matches = $this->searchMatches($phrases, $word);
                    $t[] = [$word, $word, $value, $matches];
                }
            }
        }

        return array_unique($t, SORT_REGULAR);
    }

    public static function searchMatches($phrases, $word): array
    {
        $result = [];
        foreach ($phrases as $phrase) {
            if (Str::contains($phrase, $word)) {
                $result[] = $phrase;
            }
        }

        return $result;
    }

    public static function uploadFIle($text): JsonResponse
    {
        $fileName = md5(Carbon::now());
        Storage::put('files\\' . $fileName . '.csv', $text);

        return response()->json([
            'fileName' => $fileName
        ]);
    }

    public function createFile(Request $request): JsonResponse
    {
        if ($request->text) {
            return $this->uploadFIle($request->text);
        }

        return $this->uploadFIle($request->keyPhrases);
    }

    public function downloadFile(Request $request): BinaryFileResponse
    {
        return response()->download(storage_path('app/public/files/' . $request->fileName . '.csv'));
    }
}
