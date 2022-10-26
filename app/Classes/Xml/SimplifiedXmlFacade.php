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
    public function getXMLResponse(int $try = 1)
    {
        if ($try === 1) {
            $this->setPath('https://xmlstock.com/yandex/xml/');
            $this->setUser('9371');
            $this->setKey('660fb3c4c831f41ac36637cf3b69031e');
        } elseif ($try === 2) {
            $this->setPath('https://xmlproxy.ru/search/xml');
            $this->setUser('sv@prime-ltd.su');
            $this->setKey('2fdf7f2b218748ea34cf1afb8b6f8bbb');
        } elseif ($try === 3) {
            $this->setPath('https://xmlriver.com/search/xml');
            $this->setUser('6602');
            $this->setKey('8c0d8e659c4ba2240e791fb3e6b4f172556be01f');
        }

        $xml = $this->sendRequest();

        if (isset($xml['response']['error'])) {
            Log::debug("$this->path: " . $xml['response']['error']);
            TelegramBot::sendMessage("$this->path: " . $xml['response']['error'], 938341087);
            TelegramBot::sendMessage("$this->path: " . $xml['response']['error'], 169011279);

            if ($try === 3) {
                return new Exception($xml['response']['error']);
            }

            return $this->getXMLResponse(++$try);

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

        if ($this->path === 'https://xmlriver.com/search/xml') {
            $loc = $this->getRiverLocation();
            $url = "$this->path?user=$this->user&key=$this->key&query=$query&groupby=attr=d.mode%3Ddeep.groups-on-page%3D"
                . "$this->count.docs-in-group%3D1&loc=$loc";
        } else {
            $url = "$this->path?user=$this->user&key=$this->key&query=$query&groupby=attr=d.mode%3Ddeep.groups-on-page%3D"
                . "$this->count.docs-in-group%3D1&lr=$this->lr&sortby=$this->sortby&page=$this->page";
        }


        $config = file_get_contents(str_replace('&amp;', '&', $url), false, stream_context_create([
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
            $result[] = Str::lower($item['doc']['url']);
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

    /**
     * @return string
     */
    protected function getRiverLocation(): string
    {
        $array = [
            '213' => '1015930',
            '1' => '20949',
            '20' => '1011859',
            '37' => '1011862',
            '197' => '1011853',
            '4' => '1011868',
            '77' => '1011858',
            '191' => '1011869',
            '24' => '1011977',
            '75' => '1012008',
            '33' => '1012037',
            '192' => '1012073',
            '38' => '1012068',
            '21' => '1012075',
            '193' => '1012077',
            '1106' => '9040919',
            '54' => '1012052',
            '5' => '1011898',
            '63' => '1011896',
            '41' => '1011949',
            '43' => '1012054',
            '22' => '1011914',
            '64' => '1011909',
            '7' => '1011934',
            '35' => '1011905',
            '62' => '1011941',
            '53' => '1011916',
            '8' => '20943',
            '9' => '1011947',
            '28' => '1011892',
            '23' => '1011976',
            '1092' => '9051420',
            '30' => '1011901',
            '47' => '1011981',
            '65' => '1011984',
            '66' => '1011985',
            '10' => '1014494',
            '48' => '1011987',
            '49' => '1011996',
            '50' => '1011993',
            '25' => '1012010',
            '39' => '1012013',
            '11' => '1012017',
            '51' => '1012029',
            '42' => '1011950',
            '2' => '1012040',
            '12' => '1012038',
            '239' => '1011907',
            '36' => '1012043',
            '10649' => '9040930',
            '973' => '1011924',
            '13' => '1011924',
            '14' => '1012061',
            '67' => '1012059',
            '15' => '1012060',
            '195' => '1012067',
            '172' => '1011867',
            '76' => '1011918',
            '45' => '9040951',
            '56' => '1011874',
            '1104' => '1011902',
            '16' => '1012084',
        ];

        return $array[$this->lr];
    }
}
