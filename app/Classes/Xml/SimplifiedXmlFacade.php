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
            $response = $this->sendRequest($boolean, true);
        }

        return $response;
    }

    /**
     * @param bool $prepareResponse
     * @param bool $lastTry
     * @return array|Exception
     */
    protected function sendRequest(bool $prepareResponse, bool $lastTry = false)
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

        if (isset($result['response']['error']) && $lastTry) {
            TelegramBot::sendMessage("XML error: " . $result['response']['error'], 938341087);
            TelegramBot::sendMessage("XML error: " . $result['response']['error'], 169011279);

            return new Exception($result['response']['error']);
        }

        if ($prepareResponse) {
            $sites = [];
            foreach ($result['response']['results']['grouping']['group'] as $item) {
                if (array_key_exists(0, $item['doc'])) {
                    $sites[] = Str::lower($item['doc'][0]['url']);
                } else {
                    $sites[] = Str::lower($item['doc']['url']);
                }
            }

            return $sites;

        } else {

            foreach ($result['response']['results']['grouping']['group'] as $key => $item) {
                if (array_key_exists(0, $item['doc'])) {
                    $result['response']['results']['grouping']['group'][$key]['doc'] = $item['doc'][0];
                }
            }

            return $result;
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
