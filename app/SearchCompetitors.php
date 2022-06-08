<?php

namespace App;

use App\Classes\Xml\SimplifiedXmlFacade;

class SearchCompetitors
{
    /**
     * @param $request
     * @return array
     */
    public static function analyzeList($request): array
    {
        $array = explode("\n", $request['phrases']);
        $phrases = array_diff($array, ['']);
        $resultArray = [];
        $xml = new SimplifiedXmlFacade($request['region'], $request['count']);

        foreach ($phrases as $phrase) {
            $xml->setQuery($phrase);
            $result = $xml->getXMLResponse(true);
            $resultArray[$phrase] = $result['response']['results']['grouping']['group'];
        }

        return $resultArray;
    }

    /**
     * @param $scanResult
     * @return array
     */
    public static function scanSites($scanResult): array
    {
        $metaTags = [];
        $result = $scanResult;
        foreach ($result as $key => $items) {
            foreach ($items as $key1 => $item) {
                $site = SearchCompetitors::curlInit($item['doc']['url']);
                try {
                    $contentType = $site[1]['content_type'];
                    if (preg_match('(.*?charset=(.*))', $contentType, $contentType, PREG_OFFSET_CAPTURE)) {
                        $contentType = str_replace(["\r", "\n"], '', $contentType[1][0]);
                        $site = mb_convert_encoding($site, 'utf8', str_replace('"', '', $contentType));
                    }
                } catch (\Exception $exception) {
                }

                $description = SearchCompetitors::getHiddenText($site[0], "/<meta name=\"description\" content=\"(.*?)\"/");
                $title = SearchCompetitors::getHiddenText($site[0], "/<title.*?>(.*?)<\/title>/");
                $h1 = SearchCompetitors::getHiddenText($site[0], "/<h1.*?>(.*?)<\/h1>/");
                $h2 = SearchCompetitors::getHiddenText($site[0], "/<h2.*?>(.*?)<\/h2>/");
                $h3 = SearchCompetitors::getHiddenText($site[0], "/<h3.*?>(.*?)<\/h3>/");
                $h4 = SearchCompetitors::getHiddenText($site[0], "/<h4.*?>(.*?)<\/h4>/");
                $h5 = SearchCompetitors::getHiddenText($site[0], "/<h5.*?>(.*?)<\/h5>/");
                $h6 = SearchCompetitors::getHiddenText($site[0], "/<h6.*?>(.*?)<\/h6>/");

                $metaTags[$key]['title'][] = $title;
                $metaTags[$key]['h1'][] = $h1;
                $metaTags[$key]['h2'][] = $h2;
                $metaTags[$key]['h3'][] = $h3;
                $metaTags[$key]['h4'][] = $h4;
                $metaTags[$key]['h5'][] = $h5;
                $metaTags[$key]['h6'][] = $h6;

                $result[$key][$key1]['meta'] = [
                    'title' => $title,
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

        return [
            'sites' => $result,
            'metaTags' => $metaTags
        ];
    }

    /**
     * @param $scanResult
     * @return int[]
     */
    public static function analysisPageNesting($scanResult): array
    {
        $pagesCounter = [
            'mainPageCounter' => 0,
            'nestedPageCounter' => 0
        ];

        $counter = 0;
        foreach ($scanResult as $items) {
            foreach ($items as $item) {
                $url = parse_url($item['doc']['url']);
                $domain = parse_url($item['doc']['domain']);
                if ($url['host'] . $url['path'] === $domain['path'] . '/') {
                    $pagesCounter['mainPageCounter']++;
                } else {
                    $pagesCounter['nestedPageCounter']++;
                }
                $counter++;
            }
        }
        $pagesCounter['mainPagePercent'] = round((100 / $counter) * $pagesCounter['mainPageCounter'], 1);
        $pagesCounter['nestedPagePercent'] = round((100 / $counter) * $pagesCounter['nestedPageCounter'], 1);

        return $pagesCounter;
    }

    /**
     * @param $metaTagsArray
     * @return array
     */
    public static function scanTags($metaTagsArray): array
    {
        $tags = [];
        $wordForms = [];
        $result = [];

        foreach ($metaTagsArray as $key => $metaTags) {
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
     * @param $request
     * @return array
     */
    public static function calculatePositions($request): array
    {
        $array = explode("\n", $request['phrases']);
        $phrases = array_diff($array, ['']);
        $countPhrases = count($phrases);
        $sites = $request->sites;
        foreach ($request->scanResult as $item) {
            for ($i = 0; $i < count($item); $i++) {
                $sites[$item[$i]['doc']['domain']][] = $i + 1;
            }
        }
        $positions = 0;
        foreach ($sites as $key => $site) {
            $sites[$key]['count'] = 0;
            $avg = 0.0;
            for ($i = 0; $i < $countPhrases; $i++) {
                if (isset($site[$i])) {
                    $avg += $positions++;
                    $sites[$key]['count'] += 1;
                } else {
                    $avg += 11;
                }
            }
            $sites[$key]['percent'] = round(100 / $countPhrases * $sites[$key]['count'], 1);
            $sites[$key]['count'] .= '/' . $countPhrases;
            $sites[$key]['avg'] = round($avg / $countPhrases, 1);

            if ($positions == $request['conut']) {
                $positions = 0;
            }
        }

        foreach ($phrases as $phrase) {
            unset($sites[$phrase]);
        }
        return $sites;
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
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
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
            'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',
            'Mozilla/5.0 (Windows NT 10.0; rv:87.0) Gecko/20100101 Firefox/87.0',
            //opera
            'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.43 (KHTML, like Gecko) Chrome/93.0.4577.82 Safari/537.36 OPR/79.0.4143.72',
            'Mozilla/5.0 (Windows NT 6.3) AppleWebKit/537.43 (KHTML, like Gecko) Chrome/93.0.4577.82 Safari/537.36 OPR/79.0.4143.72',
            // chrome
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.63 Safari/537.36',
            'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.159 Safari/537.36'
        ];

        for ($i = 0; $i < count($userAgents); $i++) {
            curl_setopt($curl, CURLOPT_USERAGENT, $userAgents[$i]);
            $html = curl_exec($curl);
            $headers = curl_getinfo($curl);
            if ($headers['http_code'] == 200 && $html != false) {
                $html = preg_replace('//i', '', $html);
                break;
            }
        }
        curl_close($curl);
        return [$html, $headers];
    }
}
