<?php


namespace App\Classes\Locations\Searches;


use App\Classes\Locations\Region;

class Google extends Region
{
    public function __construct()
    {
        $this->source = 'google';

        parent::__construct();
    }

    public function get(string $name)
    {
        $location = $this->location->where('name', 'like', $name.'%')->where('source', $this->source);
        if($location->count())
            return $location->get();
        else
            return false;
    }
}
