<?php

namespace App;

class LinguaStem
{
    public $Stem_Caching = 0;
    public $Stem_Cache = [];
    public $perspectiveGround = '/((ив|ивши|ившись|ыв|ывши|ывшись)|((?<=[ая])(в|вши|вшись)))$/';
    public $reflexive = '/(с[яь])$/';
    public $adjective = '/(ее|ие|ые|ое|ими|ыми|ей|ий|ый|ой|ем|им|ым|ом|его|ого|еых|ую|юю|ая|яя|ою|ею|их)$/';
    public $participle = '/((ивш|ывш|ующ)|((?<=[ая])(ем|нн|вш|ющ|щ)))$/';
    public $verb = '/((ила|ыла|ена|ейте|уйте|ите|или|ыли|ей|уй|ил|ыл|им|ым|ены|ить|ыть|ишь|ую|ю)|((?<=[ая])(ла|на|ете|йте|ли|й|л|ем|н|ло|но|ет|ют|ны|ть|ешь|нно)))$/';
    public $noun = '/(а|ев|ов|ие|ье|е|иями|ями|ами|еи|ии|и|ией|ей|ой|ий|й|ы|ь|ию|ью|ю|ия|ья|я)$/';
    public $rare = '/^(.*?[аеиоуыэюя])(.*)$/';
    public $derivation = '/[^аеиоуыэюя][аеиоуыэюя]+[^аеиоуыэюя]+[аеиоуыэюя].*(?<=о)сть?$/';

    public function __construct()
    {
        mb_internal_encoding('UTF-8');
    }

    /**
     * @param $s
     * @param $re
     * @param $to
     * @return bool
     */
    public function search(&$s, $re, $to): bool
    {
        $orig = $s;
        $s = preg_replace($re, $to, $s);
        return $orig !== $s;
    }

    /**
     * @param $s
     * @param $re
     * @return false|int
     */
    public function though($re, $s)
    {
        return preg_match($re, $s);
    }

    /**
     * Получения корня слова
     * Не всегда корректно определяется корень слова
     *
     * @param $word
     * @return array|mixed|string|string[]
     */
    public function getRootWord($word)
    {
        $stem = $word;
        do {
            if (!preg_match($this->rare, $word, $p)) {
                break;
            }
            $start = $p[1];
            $RV = $p[2];

            if (!$RV || mb_strlen($RV) <= 3) {
                break;
            }

            if (!$this->search($RV, $this->perspectiveGround, '')) {
                $this->search($RV, $this->reflexive, '');

                if ($this->search($RV, $this->adjective, '')) {
                    $this->search($RV, $this->participle, '');
                } else {
                    if (!$this->search($RV, $this->verb, ''))
                        $this->search($RV, $this->noun, '');
                }
            }
            $this->search($RV, '/и$/', '');

            if ($this->though($this->derivation, $RV))
                $this->search($RV, '/ость?$/', '');

            if (!$this->search($RV, '/ь$/', '')) {
                $this->search($RV, '/ейше?/', '');
                $this->search($RV, '/нн$/', 'н');
            }

            $stem = $start . $RV;
        } while (false);

        return $stem;
    }
}
