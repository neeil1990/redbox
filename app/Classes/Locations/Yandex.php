<?php


namespace App\Classes\Locations;

use Ixudra\Curl\Facades\Curl;
use App\Location;

class Yandex extends Region
{
    private $url;
    private $token;
    private $client_id;


    public function __construct()
    {
        $config = config('location.yandex');

        $this->url = $config['url'];
        $this->token = $config['token'];
        $this->client_id = $config['client_id'];

        $this->location = new Location();
    }

    public function get(string $name)
    {
        $location = $this->location->yandex()->where('name', 'like', $name.'%');
        if($location->count())
            return $location->get();

        $response = Curl::to($this->url)
            ->withHeader('Authorization: OAuth oauth_token="'. $this->token .'", oauth_client_id="'. $this->client_id .'"')
            ->withData(['name' => $name])
            ->asJson()
            ->get();

        if(!$response->regions)
            return false;

        $regions = collect();
        foreach ($response->regions as $region){

            $new = $this->location->create([
                'lr' => $region->id,
                'name' => $region->name . ', ' . $region->parent->name,
                'source' => 'yandex'
            ]);

            $regions->push($new);
        }

        return $regions;
    }

}
