<?php


namespace App\Classes\Services;

use App\Exceptions\XmlRiverException;
use Ixudra\Curl\Facades\Curl;

class XmlRiver
{
    private $url;
    private $user;
    private $key;

    private $query;
    private $regions;

    public function __construct($query, $regions)
    {
        $this->url = config('xmlriver.url');
        $this->user = config('xmlriver.user');
        $this->key = config('xmlriver.key');

        $this->query = $query;
        $this->regions = $regions;
    }

    public function get()
    {
        $data = [
            'user' => $this->user,
            'key' => $this->key,
            'regions' => $this->regions,
            'query' => $this->query,
        ];

        $params = http_build_query($data);

        $response = Curl::to($this->url . '?' . $params)
            ->asJson()
            ->get();

        if(isset($response->content)) {
            $info = collect($response->content->includingPhrases->info)->last();

            return filter_var($info, FILTER_SANITIZE_NUMBER_INT);
        }else{
            if(isset($response->error))
                throw new XmlRiverException($response->error);
            else
                throw new XmlRiverException("Something went wrong!");
        }
    }
}
