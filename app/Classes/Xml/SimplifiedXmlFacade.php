<?php

namespace App\Classes\Xml;

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
     * @param $lr
     */
    public function __construct($count, $lr)
    {
        $this->count = $count;
        $this->lr = $lr;

        return $this;
    }

    /**
     * @return array
     */
    public function getXMLResponse(): array
    {
        $response = $this->sendRequest();
        if (isset($response['response']['error'])) {
            $this->setPath('https://xmlproxy.ru/search/xml');
            $this->setUser('sv@prime-ltd.su');
            $this->setKey('2fdf7f2b218748ea34cf1afb8b6f8bbb');
            $response = $this->sendRequest();
        }

        return $response;
    }

    /**
     * @return mixed
     */
    protected function sendRequest()
    {
        $url = "$this->path/?user=$this->user&key=$this->key&groupby=attr%3Dd.mode%3Ddeep.groups-on-page%3D"
            . $this->count . ".docs-in-group%3D3";

        $response = Curl::to($url)
            ->withData($this->buildQuery())
            ->withResponseHeaders()
            ->returnResponseObject()
            ->get();

        $xml = $this->load($response->content);
        $json = json_encode($xml);

        return json_decode($json, TRUE);
    }

}
