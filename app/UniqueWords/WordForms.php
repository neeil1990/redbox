<?php


namespace App\UniqueWords;

use cijic\phpMorphy\Morphy;
use App\Helpers\WordHelper;

class WordForms
{
    protected $morphy;
    protected $words = [];
    protected $count = 0;

    public function __construct(string $text)
    {
        $this->morphy = new Morphy("ru");

        $this->words = array_count_values(WordHelper::getWordUpperArray($text));
    }

    public function getWordFormsInText(string $str) {
        $count = 0;
        $formsInText = [];

        $forms = $this->getAllWordForms($str);

        if ($forms) {
            foreach ($forms as $form) {
                if (isset($this->words[$form]) && !in_array($form, $formsInText)) {
                    $formsInText[] = $form;

                    $count += $this->words[$form];

                    unset($this->words[$form]);
                }
            }
        }

        $this->count = $count;

        return $formsInText;
    }

    public function getAllWordForms(string $str)
    {
        return $this->morphy->getAllForms($this->_toUpper($str));
    }

    public function getCount(): int
    {
        return $this->count;
    }

    private function _toUpper($word)
    {
        return mb_strtoupper($word);
    }
}
