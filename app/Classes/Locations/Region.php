<?php


namespace App\Classes\Locations;

use Illuminate\Support\Collection;
use Ixudra\Curl\Facades\Curl;
use App\Location;

abstract class Region
{
    protected $location;
    protected $source;

    public function __construct()
    {
        $this->location = new Location();
    }

    abstract public function get(string $name);

    protected function store($lr, $name)
    {
        return $this->location->create([
            'source' => $this->source,
            'lr' => $lr,
            'name' => $name
        ]);
    }

}
