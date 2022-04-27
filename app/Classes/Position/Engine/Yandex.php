<?php


namespace App\Classes\Position\Engine;


use App\Classes\Position\Positions;

class Yandex extends Positions
{
    public function __construct($domain, $query, $lr)
    {
        $this->engine = 'https://xmlstock.com/yandex/xml/';

        $this->domain = $domain;
        $this->query = $query;
        $this->lr = $lr;

        parent::__construct();
    }
}
