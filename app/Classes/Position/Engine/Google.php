<?php


namespace App\Classes\Position\Engine;


use App\Classes\Position\Positions;

class Google extends Positions
{
    public function __construct($domain, $query, $lr, $save = true)
    {
        $this->engine = 'https://xmlstock.com/google/xml/';

        $this->domain = $domain;
        $this->query = $query;
        $this->lr = $lr;
        $this->save = $save;

        parent::__construct();
    }
}
