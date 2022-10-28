<?php

namespace App\Classes\Xml;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RiverFacade
{
    protected $user = '6602';

    protected $key = '8c0d8e659c4ba2240e791fb3e6b4f172556be01f';

    protected $region;

    protected $query;

    protected $xmlRiwerPath;

    protected $countAttempts;

    public function __construct($region)
    {
        $this->region = $region;

        $this->xmlRiwerPath = "https://xmlriver.com/wordstat/json?user=$this->user&key=$this->key&regions=$this->region&query=";

        $this->countAttempts = 3;
    }

    public function setQuery($query)
    {
        $this->query = $query;
    }

    public function getQuery()
    {
        return $this->query;
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
     * @param bool $searchInItems
     * @return array
     */
    public function riverRequest(bool $searchInItems = true): array
    {
        try {
            $url = str_replace(' ', '%20', $this->xmlRiwerPath . $this->query);
            $riwerResponse = [];

            $attempt = 1;
            while (!isset($riwerResponse['content']['includingPhrases']['items']) && $attempt <= $this->countAttempts) {
                $riwerResponse = json_decode(file_get_contents(htmlspecialchars_decode($url)), true);
                $attempt++;
            }

            if ($searchInItems) {
                if (Str::length($riwerResponse['content']['includingPhrases']['items'][0]['phrase']) === Str::length($this->getQuery())) {
                    $number = htmlentities($riwerResponse['content']['includingPhrases']['items'][0]['number']);

                    return [
                        'number' => str_replace("&nbsp;", '', $number),
                        'phrase' => $riwerResponse['content']['includingPhrases']['items'][0]['phrase']
                    ];
                } else {
                    return [
                        'number' => $this->removeExtraSymbols($riwerResponse['content']['includingPhrases']['info'][2]),
                        'phrase' => $this->getQuery()
                    ];
                }
            } else {

                return [
                    'number' => $this->removeExtraSymbols($riwerResponse['content']['includingPhrases']['info'][2]),
                    'phrase' => $this->getQuery()
                ];
            }
        } catch (\Throwable $e) {
            return [
                'number' => 0,
                'phrase' => $this->getQuery()
            ];
        }
    }

    /**
     * @param string $string
     * @return int
     */
    protected function removeExtraSymbols(string $string): int
    {
        $number = preg_replace('/[^0-9]/', '', $string);
        $number = htmlentities($number);

        return (int)str_replace("&nbsp;", '', $number);
    }
}
