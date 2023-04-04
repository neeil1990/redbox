<?php

namespace App\Classes\Locations\Searches;

use App\Classes\Locations\Region;
use Ixudra\Curl\Facades\Curl;

class Yandex extends Region
{
    private $url;
    private $token;
    private $client_id;

    public function __construct()
    {
        $this->source = 'yandex';
        $config = config('location.yandex');

        $this->url = $config['url'];
        $this->token = $config['token'];
        $this->client_id = $config['client_id'];

        parent::__construct();
    }

    public function get(string $name)
    {
        $location = $this->location->where('name', 'like', $name.'%')->where('source', $this->source);
        if($location->count())
            return $location->get();

        $response = $this->requestYandex($name);

        if(!$response->regions)
            return false;

        $regions = collect();
        foreach ($response->regions as $region){

            $lr = $region->id;
            $name = $region->name . ', ' . $region->parent->name;

            $location = $this->store($lr, $name);
            $regions->push($location);
        }

        return $regions;
    }

    public function requestYandex(string $name)
    {
        $response = Curl::to($this->url)
            ->withHeader('Authorization: OAuth oauth_token="'. $this->token .'", oauth_client_id="'. $this->client_id .'"')
            ->withData(['name' => $name])
            ->asJson()
            ->get();

        return $response;
    }


}
