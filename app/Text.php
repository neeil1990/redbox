<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Text extends Model
{
    /**
     * @param $str
     * @return int
     */
    public static function countingTextLength($str): int
    {
        return mb_strlen($str);
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
