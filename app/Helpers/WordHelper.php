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

}
