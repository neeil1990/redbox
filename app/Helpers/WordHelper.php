<?php


namespace App\Helpers;


class WordHelper
{
    static public function strSplit(string $text) : array
    {
        return preg_split('/[^\p{L}]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
    }

    static public function getWordLowerArray(string $text) : array
    {
        return WordHelper::strSplit(mb_strtolower($text));
    }

    static public function getWordUpperArray(string $text) : array
    {
        return WordHelper::strSplit(mb_strtoupper($text));
    }

    static public function isFirstLetterUppercase(string $str) : bool
    {
        $firstLetter = mb_substr($str, 0, 1);

        return $firstLetter === mb_strtoupper($firstLetter);
    }

}
