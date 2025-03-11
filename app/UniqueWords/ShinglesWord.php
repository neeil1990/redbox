<?php


namespace App\UniqueWords;


use App\Helpers\WordHelper;
use cijic\phpMorphy\Morphy;

class ShinglesWord
{
    protected $text = "";
    protected $morphy;

    public function __construct()
    {
        $this->morphy = new Morphy("ru");
    }

    public function getShinglesAroundWord(array $words) : array
    {
        $shingles = [];

        $text = WordHelper::strSplit($this->getText());

        foreach ($text as $index => $txt) {
            $word = mb_strtoupper($txt);

            if (in_array($word, $words)) {
                $phrase = "";

                $before = $this->_getWordByIndex($text, $index - 1);
                $after = $this->_getWordByIndex($text, $index + 1);

                $mb = $this->_getPartOfSpeech($before);

                if (in_array($mb, ['С', 'П']) && !WordHelper::isFirstLetterUppercase($txt)) {
                    $phrase .= $before . ' ';
                } else if (in_array($mb, ['ПРЕДЛ', 'СОЮЗ'])) {
                    $phrase .= $before . ' ';
                    $phrase .= $this->_getWordByIndex($text, $index - 2);
                }

                $phrase .= $txt . ' ';

                $ma = $this->_getPartOfSpeech($after);

                if (in_array($ma, ['С'])) {
                    $phrase .= $after . ' ';
                } else if (in_array($ma, ['ПРЕДЛ', 'КР_ПРИЛ', 'П'])) {
                    $phrase .= $after . ' ';
                    $phrase .= $this->_getWordByIndex($text, $index + 2);
                }

                $shingles[] = $phrase;
            }
        }

        return $shingles;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    private function _getWordByIndex(array $words, int $index) : string
    {
        $word = "";

        if ($index >= 0 && count($words) > $index) {
            $word = $words[$index];
        }

        return $word;
    }

    private function _getPartOfSpeech(string $str)
    {
        $part = $this->morphy->getPartOfSpeech(mb_strtoupper($str));

        return $part[0] ?? "";
    }
}
