<?php

namespace App\Classes\Xml;

use App\TelegramBot;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class SimplifiedXmlFacade extends XmlFacade
{
    protected int $count;

    protected int $attempt;

    protected string $url;

    public function __construct($region, int $count = 100)
    {
        $this->count = $count;
        $this->lr = $region;
        $this->attempt = 0;
    }

    public function setCount($count)
    {
        $this->count = $count;
    }

    public function setAttempt($attempt = 0)
    {
        $this->attempt = $attempt;
    }

    public function getXMLResponse(string $searchEngine = 'yandex')
    {
        $this->attempt += 1;
        if ($this->attempt >= 7) {
            return 'Превышен лимит попыток';
        }

        try {
            $result = $this->sendRequest($searchEngine);
            Log::debug($this->attempt, [$result]);

            if (isset($result['response']['results']['grouping']['group'])) {
                return $this->parseResult($result['response']['results']['grouping']['group']);
            }

        } catch (Throwable $e) {
            Log::debug('XML Response error', [
                $e->getMessage(),
                $e->getLine(),
                $e->getFile(),
            ]);

            $this->getXMLResponse($searchEngine);
        }

        return $this->getXMLResponse($searchEngine);
    }

    protected function sendRequest($searchEngine)
    {
        if ($searchEngine === 'yandex') {
            $this->url = $this->prepareYandexRequest();
        } else {
            $this->url = $this->prepareGoogleRequest();
        }

        $response = file_get_contents($this->url);
        $xml = $this->load($response);

        return json_decode(json_encode($xml), true);
    }

    protected function prepareGoogleRequest(): ?string
    {
        $query = str_replace(' ', '%20', $this->query);

        if ($this->attempt <= 2) {
            $this->setPath('https://xmlstock.com/google/xml/');
            $this->setUser(config('xmlstock.user'));
            $this->setKey(config('xmlstock.key'));

            return "$this->path?user=$this->user&key=$this->key&query=$query&groupby=$this->count&lr=$this->lr&sortby=$this->sortby";

        } elseif ($this->attempt <= 4) {
            $this->setPath('https://xmlriver.com/search/xml');
            $this->setUser(config('xmlriver.user'));
            $this->setKey(config('xmlriver.key'));
            $loc = $this->getRiverLocation();

            return "$this->path?user=$this->user&key=$this->key&query=$query&groupby=$this->count&loc=$loc";
        }

        return null;
    }

    protected function prepareYandexRequest(): ?string
    {
        $query = str_replace(' ', '%20', $this->query);

        if ($this->attempt <= 2) {
            $this->setPath('https://xmlstock.com/yandex/xml/');
            $this->setUser(config('xmlstock.user'));
            $this->setKey(config('xmlstock.key'));

            return "$this->path?user=$this->user&key=$this->key&query=$query&groupby=attr=d.mode%3Ddeep.groups-on-page%3D"
                . "$this->count.docs-in-group%3D1&lr=$this->lr&sortby=$this->sortby&page=$this->page";
        } elseif ($this->attempt <= 4) {
            $this->setPath('https://xmlproxy.ru/search/xml');
            $this->setUser(config('xmlproxy.user'));
            $this->setKey(config('xmlproxy.key'));

            return "$this->path?user=$this->user&key=$this->key&query=$query&groupby=attr=d.mode%3Ddeep.groups-on-page%3D"
                . "$this->count.docs-in-group%3D1&lr=$this->lr&sortby=$this->sortby&page=$this->page";
        } elseif ($this->attempt <= 6) {
            $this->setPath('https://xmlriver.com/yandex/xml');
            $this->setUser(config('xmlriver.user'));
            $this->setKey(config('xmlriver.key'));
            $loc = $this->getRiverLocation();

            return "$this->path?user=$this->user&key=$this->key&query=$query&groupby=attr=d.mode%3Ddeep.groups-on-page%3D"
                . "$this->count.docs-in-group%3D1&loc=$loc";
        }

        return null;
    }

    protected function parseResult($xmlResult): array
    {
        $result = [];
        if (isset($xmlResult['doc']['url'])) {
            return [$xmlResult['doc']['url']];
        } else {
            foreach ($xmlResult as $item) {
                $result[] = Str::lower($item['doc']['url']);
            }
        }

        return $result;
    }

    public static function getPosition($request)
    {
        $xml = new SimplifiedXmlFacade($request['region']);
        $xml->setQuery($request['phrase']);
        $xmlResponse = $xml->getXMLResponse();

        $position = array_search(Str::lower($request['link']), $xmlResponse);
        if ($position === false) {
            $position = array_search(Str::lower($request['link'] . '/'), $xmlResponse);
        }

        return $position;
    }

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
