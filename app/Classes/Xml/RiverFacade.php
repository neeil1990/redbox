<?php

namespace App\Classes\Xml;

use App\TelegramBot;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RiverFacade
{
    protected $user = '6602';

    protected $key = '8c0d8e659c4ba2240e791fb3e6b4f172556be01f';

    protected $region;

    protected $query;

    protected $xmlRiverPath;

    protected $countAttempts;

    public function __construct($region)
    {
        $this->region = $region;

        $this->xmlRiverPath = "https://xmlriver.com/wordstat/json?user=$this->user&key=$this->key&regions=$this->region&query=";

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
            $url = str_replace(' ', '%20', $this->xmlRiverPath . $this->query);
            $riverResponse = [];

            $attempt = 1;
            while (!isset($riverResponse['content']['includingPhrases']['items']) && $attempt <= $this->countAttempts) {
                $riverResponse = json_decode(file_get_contents(html_entity_decode($url)), true);
                $attempt = $attempt + 1;
                if ($attempt === $this->countAttempts && isset($riverResponse['error'])) {
                    TelegramBot::sendMessage('xmlRiver: ' . $riverResponse['error'] . "\nPhrase: " . $this->getQuery(), 938341087);
                    TelegramBot::sendMessage('xmlRiver: ' . $riverResponse['error'] . ' ' . $this->getQuery(), 169011279);
                    return [
                        'number' => 0,
                        'phrase' => $this->getQuery()
                    ];
                }
            }

            if (
                $searchInItems &&
                isset($riverResponse['content']['includingPhrases']['items'][0]['phrase']) &&
                Str::length($riverResponse['content']['includingPhrases']['items'][0]['phrase']) === Str::length($this->getQuery())
            ) {
                $number = htmlentities($riverResponse['content']['includingPhrases']['items'][0]['number']);

                return [
                    'number' => str_replace("&nbsp;", '', $number),
                    'phrase' => $riverResponse['content']['includingPhrases']['items'][0]['phrase']
                ];
            } elseif (isset($riverResponse['content']['includingPhrases']['info'][2])) {
                return [
                    'number' => $this->removeExtraSymbols($riverResponse['content']['includingPhrases']['info'][2]),
                    'phrase' => $this->getQuery()
                ];
            } else {
                return [
                    'number' => 0,
                    'phrase' => $this->getQuery()
                ];
            }
        } catch (\Throwable $e) {
            Log::debug('river request error', [
                $e->getMessage(),
                $e->getLine(),
                $e->getFile(),
                $this->getQuery(),
                $riverResponse,
            ]);
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
