<?php


namespace App\Classes\Position\Engine;


use App\Classes\Position\Positions;

class Yandex extends Positions
{
    public function __construct($domain, $query, $lr, $save = true)
    {
        $this->engine = 'https://xmlstock.com/yandex/xml/';

        $this->domain = $domain;
        $this->query = $query;
        $this->lr = $lr;
        $this->save = $save;

        parent::__construct();
    }
}
