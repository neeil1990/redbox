<?php

namespace App;

use App\Classes\Xml\XmlFacade;

class SearchCompetitors
{
    public $phrases;

    public $result;

    public $xml;

    public $sites;

    public $metaTags;

    /**
     * @param $request
     * @return mixed
     */
    public function analyzeList($request)
    {
        $array = explode("\r\n", $request['keywords']);
        $this->phrases = array_diff($array, ['']);
        $this->xml = new XmlFacade();

        $this->xml->setPath('https://xmlproxy.ru/search/xml');
        $this->xml->setUser('sv@prime-ltd.su');
        $this->xml->setKey('2fdf7f2b218748ea34cf1afb8b6f8bbb');
        $this->xml->setLr($request['region']);

        foreach ($this->phrases as $keyword) {
            $this->xml->setQuery($keyword);
            $result = $this->xml->getByArray();
            $this->result[$keyword] = $result['response']['results']['grouping']['group'];
        }

        return $this->result;
    }

    /**
     * @return array
     */
    public function scanSites(): array
    {
        $result = $this->result;
        foreach ($result as $key => $items) {
            foreach ($items as $key1 => $item) {
                $site = SearchCompetitors::curlInit($item['doc']['url']);

                $contentType = $site[1]['content_type'];
                if (preg_match('(.*?charset=(.*))', $contentType, $contentType, PREG_OFFSET_CAPTURE)) {
                    $contentType = str_replace(["\r", "\n"], '', $contentType[1][0]);
                    $site = mb_convert_encoding($site, str_replace('"', '', $contentType));
                }

                $description = SearchCompetitors::getHiddenText($site[0], "/<meta name=\"description\" content=\"(.*?)\"/");
                $title = SearchCompetitors::getHiddenText($site[0], "/<title.*?>(.*?)<\/title>/");
                $h1 = SearchCompetitors::getHiddenText($site[0], "/<h1.*?>(.*?)<\/h1>/");
                $h2 = SearchCompetitors::getHiddenText($site[0], "/<h2.*?>(.*?)<\/h2>/");
                $h3 = SearchCompetitors::getHiddenText($site[0], "/<h3.*?>(.*?)<\/h3>/");
                $h4 = SearchCompetitors::getHiddenText($site[0], "/<h4.*?>(.*?)<\/h4>/");
                $h5 = SearchCompetitors::getHiddenText($site[0], "/<h5.*?>(.*?)<\/h5>/");
                $h6 = SearchCompetitors::getHiddenText($site[0], "/<h6.*?>(.*?)<\/h6>/");

                $this->metaTags[$key]['title'][] = $title;
                $this->metaTags[$key]['h1'][] = $h1;
                $this->metaTags[$key]['h2'][] = $h2;
                $this->metaTags[$key]['h3'][] = $h3;
                $this->metaTags[$key]['h4'][] = $h4;
                $this->metaTags[$key]['h5'][] = $h5;
                $this->metaTags[$key]['h6'][] = $h6;

                $result[$key][$key1]['meta'] = [
                    'h1' => $h1,
                    'h2' => $h2,
                    'h3' => $h3,
                    'h4' => $h4,
                    'h5' => $h5,
                    'h6' => $h6,
                    'description' => $description,
                ];
            }
        }

        return $result;
    }


    /**
     * @return array
     */
    public function scanTags(): array
    {
        $tags = [];
        $wordForms = [];
        $result = [];

        foreach ($this->metaTags as $key => $metaTags) {
            foreach ($metaTags as $key1 => $metaTag) {
                foreach ($metaTag as $items) {
                    foreach ($items as $item) {
                        $tags[$key][$key1][] = $item;
                    }
                }
            }
        }

        foreach ($tags as $key => $elems) {
            if (isset($elems['title'])) {
                $wordForms[$key]['title'] = SearchCompetitors::searchWordForms(implode(' ', $elems['title']));
            }
            if (isset($elems['h1'])) {
                $wordForms[$key]['h1'] = SearchCompetitors::searchWordForms(implode(' ', $elems['h1']));
            }
            if (isset($elems['h2'])) {
                $wordForms[$key]['h2'] = SearchCompetitors::searchWordForms(implode(' ', $elems['h2']));
            }
            if (isset($elems['h3'])) {
                $wordForms[$key]['h3'] = SearchCompetitors::searchWordForms(implode(' ', $elems['h3']));
            }
            if (isset($elems['h4'])) {
                $wordForms[$key]['h4'] = SearchCompetitors::searchWordForms(implode(' ', $elems['h4']));
            }
            if (isset($elems['h5'])) {
                $wordForms[$key]['h5'] = SearchCompetitors::searchWordForms(implode(' ', $elems['h5']));
            }
            if (isset($elems['h6'])) {
                $wordForms[$key]['h6'] = SearchCompetitors::searchWordForms(implode(' ', $elems['h6']));
            }
        }

        foreach ($wordForms as $key => $tags) {
            foreach ($tags as $key1 => $words) {
                foreach ($words as $key2 => $word) {
                    foreach ($word as $item) {
                        if (isset($result[$key][$key1][$key2])) {
                            $result[$key][$key1][$key2] += $item;
                        } else {
                            $result[$key][$key1][$key2] = $item;
                        }
                    }
                }
            }
        }
        foreach ($result as $key1 => $elems) {
            foreach ($elems as $key2 => $elem) {
                arsort($result[$key1][$key2]);
            }
        }

        return $result;
    }

    /**
     * @param $site
     * @return array|null
     */
    public static function curlInit($site): ?array
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $site);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 1);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);

        return SearchCompetitors::tryConnect($curl);
    }

    /**
     * @param $curl
     * @return array|null
     */
    public static function tryConnect($curl): ?array
    {
        $html = null;
        $headers = null;
        $userAgents = [
            //Mozilla Firefox
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:84.0) Gecko/20100101 Firefox/84.0',
            //opera
            'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.43 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36 OPR/77.0.4054.60',
            // chrome
            'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36'
        ];

        for ($i = 0; $i < count($userAgents); $i++) {
            curl_setopt($curl, CURLOPT_USERAGENT, $userAgents[$i]);
            $html = curl_exec($curl);
            $headers = curl_getinfo($curl);
            if ($headers['http_code'] == 200 && $html != false) {
                $html = preg_replace('//i', '', $html);
                break 1;
            }
        }
        curl_close($curl);
        return [$html, $headers];
    }

    /**
     * @param $wordForm
     * @return array
     */
    public static function sortWordForm($wordForm): array
    {
        $wordForms = [];
        foreach ($wordForm as $key => $item) {
            if (mb_strlen($key) >= 3) {
                $wordForms[$key] = count($item);
            }
        }
        arsort($wordForms);

        return $wordForms;
    }

    /**
     * @param $html
     * @param $regex
     * @return array
     */
    public static function getHiddenText($html, $regex): array
    {
        $hiddenText = [];
        preg_match_all($regex, $html, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            if ($match[1] != "") {
                $hiddenText[] = htmlspecialchars_decode(strip_tags($match[1]));
            }
        }
        return $hiddenText;
    }

    /**
     * @return array
     */
    public function calculatePositions(): array
    {
        $countKeyWords = count($this->phrases);
        $percent = 100 / $countKeyWords;
        foreach ($this->result as $item) {
            for ($i = 0; $i < count($item); $i++) {
                $this->sites[$item[$i]['doc']['domain']][] = $i + 1;
            }
        }

        foreach ($this->sites as $key => $site) {
            $count = count($site);
            $this->sites[$key]['percent'] = $percent * $count;
            $avg = 0.0;
            for ($i = 0; $i < $countKeyWords; $i++) {
                if (isset($site[$i])) {
                    $avg += $site[$i];
                    unset($site[$i]);
                } else {
                    $avg += 11;
                }
            }
            $this->sites[$key]['avg'] = $avg / $countKeyWords;
        }

        return $this->sites;
    }

    /**
     * @param $string
     * @return array
     */
    public static function searchWordForms($string): array
    {
        $stemmer = new LinguaStem();
        $wordForms = [];
        $will = [];

        $string = mb_strtolower($string);
        $string = str_replace([
            "\n", "\t", "\r", "nbsp",
            "»", "«", ".", ",", "!", "?",
            "(", ")", "+", ";", ":", "-",
            "₽", "$", "/", "[", "]", "“", '—', ""
        ], ' ', $string);

        $array = explode(' ', $string);
        $array = array_count_values($array);
        arsort($array);

        foreach ($array as $key1 => $item1) {
            if (!in_array($key1, $will)) {
                foreach ($array as $key2 => $item2) {
                    if (!in_array($key2, $will)) {
                        similar_text($key1, $key2, $percent);
                        if (
                            preg_match("/[А-Яа-я]/", $key1)
                            && $stemmer->getRootWord($key2) == $stemmer->getRootWord($key1)
                            || preg_match("/[A-Za-z]/", $key1)
                            && $percent >= 82
                        ) {
                            $wordForms[$key1][$key2] = $item2;
                            $will[] = $key2;
                            $will[] = $key1;
                        }
                    }
                }
            }
        }

        return $wordForms;
    }
}
