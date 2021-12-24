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

    public function s(&$s, $re, $to)
    {
        $orig = $s;
        $s = preg_replace($re, $to, $s);
        return $orig !== $s;
    }

    public function m($s, $re)
    {
        return preg_match($re, $s);
    }

    public function stem_word($word)
    {
        $word = mb_strtolower($word);
        $word = str_replace('ё', 'е', $word);
        if ($this->Stem_Caching && isset($this->Stem_Cache[$word])) {
            return $this->Stem_Cache[$word];
        }
        $stem = $word;
        do {
            if (!preg_match($this->rare, $word, $p)) break;
            $start = $p[1];
            $RV = $p[2];
            if (!$RV) break;

            if (!$this->s($RV, $this->perspectiveGround, '')) {
                $this->s($RV, $this->reflexive, '');

                if ($this->s($RV, $this->adjective, '')) {
                    $this->s($RV, $this->participle, '');
                } else {
                    if (!$this->s($RV, $this->verb, ''))
                        $this->s($RV, $this->noun, '');
                }
            }
            $this->s($RV, '/и$/', '');

            if ($this->m($RV, $this->derivation))
                $this->s($RV, '/ость?$/', '');

            if (!$this->s($RV, '/ь$/', '')) {
                $this->s($RV, '/ейше?/', '');
                $this->s($RV, '/нн$/', 'н');
            }

            $stem = $start . $RV;
        } while (false);
        if ($this->Stem_Caching) $this->Stem_Cache[$word] = $stem;
        return $stem;
    }
}
