<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\VarDumper\VarDumper;

class UniqueWordsController extends Controller
{
    /**
     * @return array|false|Application|Factory|View|mixed
     */
    public function index()
    {
        return view('pages.unique-words');
    }

    /**
     * @param Request $request
     * @return array|false|Application|Factory|View|mixed
     */
    public function countingUniqueWords(Request $request)
    {
        $listWords = self::stringToCollectionWords($request->phrases);
        $oldPhrases = $request->phrases;

        return view('pages.unique-words', compact('listWords', 'oldPhrases'));
    }

    /**
     * @param $string
     * @return \Illuminate\Support\Collection
     */
    public static function stringToCollectionWords($string)
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
        return explode("\r\n", $string);
    }

    /**
     * @param $string
     * @return array
     */
    public static function getWords($string): array
    {
        $string = explode("\r\n", $string);
        $string = implode(" ", $string);
        return explode(" ", $string);
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
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function downloadUniqueWords(Request $request): BinaryFileResponse
    {
        $words = self::stringToCollectionWords($request->phrases);
        self::removeExtraItems($words, $request->extraId);
        $text = self::confirmText($words, $request);
        $subject = self::confirmSubject($request);

        return self::uploadFIle($subject . $text);
    }

    /**
     * @param $text
     * @return BinaryFileResponse
     */
    public static function uploadFIle($text): BinaryFileResponse
    {
        $fileName = md5(Carbon::now());
        Storage::put('files\\' . $fileName . '.csv', $text);
        return response()->download(storage_path('app/public/files/' . $fileName . '.csv'));
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
     * @param $words
     * @param $request
     * @return string
     */
    public static function confirmText($words, $request): string
    {
        $result = '';
        $separator = self::confirmSeparator($request);
        foreach ($words as $word) {
            if ($request->uniqueWord === 'on') {
                $result .= $word['word'] . ';';
            }
            if ($request->uniqueWordForms === 'on') {
                $result .= $word['wordForms'] . ';';
            }
            if ($request->numberOccurrences === 'on') {
                $result .= $word['numberOccurrences'] . ';';
            }
            if ($request->keyPhrases === 'on') {
                foreach ($word['keyPhrases'] as $item) {
                    $result .= $item . "\n" . $separator;
                }
            }
            $result .= "\n";
        }
        return $result;
    }

    /**
     * @param $request
     * @return string
     */
    public static function confirmSeparator($request): string
    {
        $countOptions = 0;
        if ($request->uniqueWord === 'on') {
            $countOptions++;
        }
        if ($request->uniqueWordForms === 'on') {
            $countOptions++;
        }
        if ($request->numberOccurrences === 'on') {
            $countOptions++;
        }
        if ($request->keyPhrases === 'on') {
            $countOptions++;
        }

        switch ($countOptions) {
            case 2:
                $separator = ';';
                break;
            case 3:
                $separator = ';;';
                break;
            case 4:
                $separator = ';;;';
                break;
            default:
                $separator = '';
        }

        return $separator;
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
     * @return BinaryFileResponse
     */
    public function downloadUniquePhrases(Request $request): BinaryFileResponse
    {
        return self::uploadFIle(trim($request->keyPhrases));
    }
}
