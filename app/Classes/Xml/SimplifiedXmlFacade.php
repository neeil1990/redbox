<?php

namespace App\Classes\Xml;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Ixudra\Curl\Facades\Curl;

class SimplifiedXmlFacade extends XmlFacade
{
    /**
     * Количество сайтов на странице
     *
     * @var
     */
    protected $count;

    /**
     * @param $count
     * @param $lr -- region
     */
    public function __construct($count, $lr)
    {
        $this->count = $count;
        $this->lr = $lr;

        return $this;
    }

    /**
     * @param bool $boolean
     * @return array
     */
    public function getXMLResponse(bool $boolean = false): array
    {
        $response = $this->sendRequest($boolean);
        if (isset($response['response']['error'])) {
            $this->setPath('https://xmlproxy.ru/search/xml');
            $this->setUser('sv@prime-ltd.su');
            $this->setKey('2fdf7f2b218748ea34cf1afb8b6f8bbb');
            $response = $this->sendRequest($boolean);
        }

        return $response;
    }

    /**
     * @param bool $bool
     * @return array
     */
    protected function sendRequest(bool $bool = false): array
    {
        if ($bool) {
            $result = $this->sendRequestV1();
        } else {
            $result = $this->sendRequestV2();
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function sendRequestV1(): array
    {
        $query = str_replace(' ', '%20', $this->query);
        $url = "$this->path?user=$this->user&key=$this->key&query=$query&groupby=attr%3Dd.mode%3Ddeep.groups-on-page%3D"
            . $this->count . ".docs-in-group%3D3&lr=$this->lr&sortby=$this->sortby&page=>$this->page";

        $response = Curl::to($url)
            ->withData($this->buildQuery())
            ->withResponseHeaders()
            ->returnResponseObject()
            ->get();

        $xml = $this->load($response->content);

        $json = json_encode($xml);

        return json_decode($json, TRUE);
    }

    /**
     * @return array
     */
    protected function sendRequestV2(): array
    {
        $query = str_replace(' ', '%20', $this->query);
        $url = "$this->path?user=$this->user&key=$this->key&query=$query&groupby=attr%3Dd.mode%3Ddeep.groups-on-page%3D"
            . $this->count . ".docs-in-group%3D3&lr=$this->lr&sortby=$this->sortby&page=>$this->page";

        $arrContextOptions = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ];
        $response = file_get_contents($url, false, stream_context_create($arrContextOptions));
        $xml = $this->load($response);

        $json = json_encode($xml);
        $responseArray = json_decode($json, TRUE);

        $sites = [];
        foreach ($responseArray['response']['results']['grouping']['group'] as $item) {
            if (array_key_exists(0, $item['doc'])) {
                $sites[] = Str::lower($item['doc'][0]['url']);
            } else {
                $sites[] = Str::lower($item['doc']['url']);
            }
        }

        return $sites;
    }

}
