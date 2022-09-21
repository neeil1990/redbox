<?php

namespace App\Classes\Xml;

use App\TelegramBot;
use Exception;
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
     * @param $lr -- region
     */
    public function __construct($lr, $count = 100)
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
        return $this->sendRequest();
    }

    /**
     * @param bool $lastTry
     * @return array|Exception
     */
    protected function sendRequest(bool $lastTry = false)
    {
        $query = str_replace(' ', '%20', $this->query);
        $url = "$this->path?user=$this->user&key=$this->key&query=$query&groupby=attr%3Dd.mode%3Ddeep.groups-on-page%3D"
            . $this->count . ".docs-in-group%3D3&lr=$this->lr&sortby=$this->sortby&page=>$this->page";

        $response = file_get_contents($url, false, stream_context_create([
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ]));
        $xml = $this->load($response);

        $json = json_encode($xml);
        $result = json_decode($json, true);

        if (isset($result['response']['error'])) {
            if ($lastTry) {
                TelegramBot::sendMessage("XML error: " . $result['response']['error'], 938341087);
                TelegramBot::sendMessage("XML error: " . $result['response']['error'], 169011279);

                return new Exception($result['response']['error']);
            }

            $this->setPath('https://xmlproxy.ru/search/xml');
            $this->setUser('sv@prime-ltd.su');
            $this->setKey('2fdf7f2b218748ea34cf1afb8b6f8bbb');
            return $this->sendRequest(true);

        } else {
            $sites = [];
            foreach ($result['response']['results']['grouping']['group'] as $item) {
                if (array_key_exists(0, $item['doc'])) {
                    $sites[] = Str::lower($item['doc'][0]['url']);
                } else {
                    $sites[] = Str::lower($item['doc']['url']);
                }
            }

            return $sites;
        }
    }

    /**
     * @param $request
     * @return bool|int
     */
    public static function getPosition($request)
    {
        $xml = new SimplifiedXmlFacade($request['region']);
        $xml->setQuery($request['phrase']);
        $xmlResponse = $xml->getXMLResponse();

        return array_search(Str::lower($request['link']), $xmlResponse);
    }

}
