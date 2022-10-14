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
     * @var mixed
     */
    protected $result;

    /**
     * @param $region
     * @param int $count
     */
    public function __construct($region, int $count = 100)
    {
        $this->count = $count;
        $this->lr = $region;

        return $this;
    }

    /**
     * @return array|Exception
     */
    public function getXMLResponse(bool $lastTry = false)
    {
        if ($lastTry) {
            $this->setPath('https://xmlproxy.ru/search/xml');
            $this->setUser('sv@prime-ltd.su');
            $this->setKey('2fdf7f2b218748ea34cf1afb8b6f8bbb');
        } else {
            $this->setPath('https://xmlstock.com/yandex/xml/');
            $this->setUser('9371');
            $this->setKey('660fb3c4c831f41ac36637cf3b69031e');
        }

        $xml = $this->sendRequest();

        if (isset($xml['response']['error'])) {
            Log::debug("$this->path: " . $xml['response']['error']);
            TelegramBot::sendMessage("$this->path: " . $xml['response']['error'], 938341087);
            TelegramBot::sendMessage("$this->path: " . $xml['response']['error'], 169011279);

            if ($lastTry) {
                return new Exception($xml['response']['error']);
            }

            return $this->getXMLResponse(true);

        } else {
            return $this->parseResult($xml['response']['results']['grouping']['group']);
        }
    }

    /**
     * @return array|Exception
     */
    protected function sendRequest()
    {
        $query = str_replace(' ', '%20', $this->query);
        $url = "$this->path?user=$this->user&key=$this->key&query=$query&groupby=attr%3Dd.mode%3Ddeep.groups-on-page%3D"
            . $this->count . ".docs-in-group%3D3&lr=$this->lr&sortby=$this->sortby&page=>$this->page";

        $config = file_get_contents($url, false, stream_context_create([
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ]));
        $xml = $this->load($config);
        $json = json_encode($xml);

        return json_decode($json, true);
    }

    /**
     * @param $xmlResult
     * @return array
     */
    protected function parseResult($xmlResult): array
    {
        $result = [];
        foreach ($xmlResult as $item) {
            if (array_key_exists(0, $item['doc'])) {
                $result[] = Str::lower($item['doc'][0]['url']);
            } else {
                $result[] = Str::lower($item['doc']['url']);
            }
        }

        return $result;
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
