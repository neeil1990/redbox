<?php

namespace App\Classes\Xml;

class RiverFacade
{
    public $user = '6602';

    public $key = '8c0d8e659c4ba2240e791fb3e6b4f172556be01f';

    public $region;

    public $query;

    public $xmlRiwerPath;

    public function __construct($region)
    {
        $this->region = $region;

        $this->xmlRiwerPath = "https://xmlriver.com/wordstat/json?user=$this->user&key=$this->key&regions=$this->region&query=";
    }

    public function setQuery($query)
    {
        $this->query = $query;
    }

    public function setRegions($region)
    {
        $this->region = $region;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return array
     */
    public function riverRequest(): array
    {
        $url = str_replace(' ', '%20', $this->xmlRiwerPath . $this->query);
        $riwerResponse = [];

        $attempt = 1;
        while (!isset($riwerResponse['content']['includingPhrases']['items']) && $attempt <= 3) {
            $riwerResponse = json_decode(file_get_contents($url), true);
            $attempt++;
        }

        return [
            'number' => $riwerResponse['content']['includingPhrases']['items'][0]['number'],
            'phrase' => $riwerResponse['content']['includingPhrases']['items'][0]['phrase']
        ];
    }
}
