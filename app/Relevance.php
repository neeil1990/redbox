<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Relevance
{
    public $separator = "\n\nseparator\n\n";

    public $competitorsTextAndLinksCloud;

    public $mainPageIsRelevance = false;

    public $competitorsTextAndLinks;

    public $competitorsLinksCloud;

    public $competitorsCloud = [];

    public $competitorsTextCloud;

    public $competitorsLinks;

    public $competitorsText;

    public $maxWordLength;

    public $ignoredWords;

    public $coverageInfo;

    public $wordForms;

    public $mainPage;

    public $domains;

    public $density = [];

    public $params;

    public $pages;

    public $sites;

    /**
     * @param $link
     */
    public function __construct($link)
    {
        $this->pages = [];
        $this->domains = [];
        $this->mainPage = [];
        $this->wordForms = [];
        $this->ignoredWords = [];
        $this->competitorsText = '';
        $this->competitorsLinks = '';
        $this->competitorsTextAndLinks = '';

        $this->params = RelevanceAnalyseResults::firstOrNew(['user_id' => Auth::id()]);
        $this->params['main_page_link'] = $link;
        $this->params['sites'] = '';
        $this->params['html_relevance'] = '';
        $this->params['html_main_page'] = '';
    }

    /**
     * @param $link
     */
    public function getMainPageHtml()
    {
        $html = TextAnalyzer::removeStylesAndScripts(TextAnalyzer::curlInit($this->params['main_page_link']));
        $this->setMainPage($html);
    }

    /**
     * @return void
     */
    public function parseSites()
    {
        foreach ($this->domains as $item) {
            $domain = isset($item['doc']['url'])
                ? strtolower($item['doc']['url'])
                : $item;
            $result = TextAnalyzer::removeStylesAndScripts(TextAnalyzer::curlInit($domain));
            // если ответ от сервера не был получен
            if ($result == '' || $result == null) {
                $this->sites[] = [
                    'site' => $domain,
                    'danger' => true,
                ];
            } else {
                $this->sites[] = [
                    'site' => $domain,
                    'danger' => false,
                ];
            }
            $this->pages[$domain]['html'] = $result;
            $this->params['html_relevance'] .= $result . $this->separator;
            //Если проанализированный домен является посадочной страницей
            $lastItem = array_key_last($this->sites);
            if ($domain == $this->params['main_page_link']) {
                $this->mainPageIsRelevance = true;
                $this->sites[$lastItem]['mainPage'] = true;
                $this->sites[$lastItem]['inRelevance'] = true;
            } else {
                $this->sites[$lastItem]['mainPage'] = false;
            }
        }
    }

    /**
     * @param $request
     * @return void
     */
    public function analysis($request)
    {
        $this->maxWordLength = $request->separator;
        $this->removeNoIndex($request->noIndex);
        $this->getHiddenData($request->hiddenText);
        $this->separateLinksFromText();
        $this->removePartsOfSpeech($request->conjunctionsPrepositionsPronouns);
        $this->removeListWords($request);
        $this->deleteEverythingExceptCharacters();
        $this->getTextFromCompetitors();
        $this->separateAllText();
        $this->searchWordForms();
        $this->processingOfGeneralInformation();
        $this->prepareUnigramTable();
        $this->prepareClouds();
        $this->calculateCoverage();
        $this->calculatePoints();
        $this->calculateDensity();
        $this->params->save();
    }

    public function calculateDensity()
    {
        $iterator = 0;
        foreach ($this->pages as $page) {
            $allText = Relevance::concatenation([$page['html'], $page['linkText'], $page['hiddenText']]);
            $this->sites[$iterator]['density'] = 0;
            foreach ($this->density as $word => $value) {
                if (preg_match("/($word)/", $allText)) {
                    $count = substr_count($allText, " $word ");
                    $points = min($count / ($value / 100), 100);
                    $this->sites[$iterator]['density'] += $points;
                }
            }

            $iterator++;
        }

        for ($i = 0; $i < count($this->sites); $i++) {
            $this->sites[$i]['density'] = round($this->sites[$i]['density'] / 600);
        }

        Log::debug('sites', $this->sites);
    }

    /**
     * @return void
     */
    public function separateAllText()
    {
        $this->competitorsLinks = $this->separateText($this->competitorsLinks);
        $this->competitorsText = $this->separateText($this->competitorsText);
        $this->mainPage['html'] = $this->separateText($this->mainPage['html']);
        $this->mainPage['linkText'] = $this->separateText($this->mainPage['linkText']);
        $this->mainPage['hiddenText'] = $this->separateText($this->mainPage['hiddenText']);
        $this->competitorsTextAndLinks = ' ' . $this->competitorsLinks . ' ' . $this->competitorsText . ' ';
    }

    /**
     * @return void
     */
    public function deleteEverythingExceptCharacters()
    {
        $this->mainPage['html'] = TextAnalyzer::deleteEverythingExceptCharacters($this->mainPage['html']);
        foreach ($this->pages as $key => $page) {
            $this->pages[$key]['html'] = TextAnalyzer::deleteEverythingExceptCharacters($this->pages[$key]['html']);
        }
    }

    /**
     * Удалить текст, который помечен <noindex>
     * @param $noIndex
     * @return void
     */
    public function removeNoIndex($noIndex)
    {
        if ($noIndex == 'false') {
            $this->mainPage['html'] = TextAnalyzer::removeNoindexText($this->mainPage['html']);
            foreach ($this->pages as $key => $page) {
                $this->pages[$key]['html'] = TextAnalyzer::removeNoindexText($page['html']);
            }
        }
    }

    /**
     * Разделение ссылок и текста
     * @return void
     */
    public function separateLinksFromText()
    {
        $this->mainPage['linkText'] = TextAnalyzer::getLinkText($this->mainPage['html']);
        $this->mainPage['html'] = Relevance::clearHTMLFromLinks($this->mainPage['html']);
        foreach ($this->pages as $key => $page) {
            $this->pages[$key]['linkText'] = TextAnalyzer::getLinkText($this->pages[$key]['html']);
            $this->pages[$key]['html'] = Relevance::clearHTMLFromLinks($this->pages[$key]['html']);
        }
    }

    /**
     * @param $hiddenText
     * @return void
     */
    public function getHiddenData($hiddenText)
    {
        if ($hiddenText == 'true') {
            $this->mainPage['hiddenText'] = Relevance::getHiddenText($this->mainPage['html']);
            foreach ($this->pages as $key => $page) {
                $this->pages[$key]['hiddenText'] = Relevance::getHiddenText($this->pages[$key]['html']);
            }
        } else {
            $this->mainPage['hiddenText'] = '';
            foreach ($this->pages as $key => $page) {
                $this->pages[$key]['hiddenText'] = '';
            }
        }
    }

    /**
     * Вся информация с сайтов конкурентов с сайтов конкурентов
     * @return void
     */
    public function getTextFromCompetitors()
    {
        foreach ($this->pages as $key => $page) {
            $this->competitorsLinks .= ' ' . $this->pages[$key]['linkText'] . ' ';
            $this->competitorsText .= ' ' . $this->pages[$key]['hiddenText'] . ' ' . $this->pages[$key]['html'] . ' ';
        }
    }

    /**
     * @return void
     */
    public function calculateCoverage()
    {
        $competitorsText = Relevance::searchWords($this->competitorsTextAndLinks);
        $iterator = 0;
        foreach ($this->pages as $page) {
            $allText = Relevance::concatenation([$page['html'], $page['linkText'], $page['hiddenText']]);
            $wordsInText = Relevance::searchWords($allText);
            $this->sites[$iterator]['coverage'] = $this->calculateCoveragePercent($wordsInText, $competitorsText);
            $this->sites[$iterator]['tf'] = $this->calculateCoverageTF($wordsInText);
            $iterator++;
        }
        $this->isMainPageInRelevanceResponse($competitorsText);
    }

    /**
     * @param $competitorsText
     * @return void
     */
    public function isMainPageInRelevanceResponse($competitorsText)
    {
        if (!$this->mainPageIsRelevance) {
            $mainPageText = Relevance::searchWords(
                Relevance::concatenation([
                    $this->mainPage['html'],
                    $this->mainPage['linkText'],
                    $this->mainPage['hiddenText']
                ])
            );
            $this->sites[] = [
                'site' => $this->params['main_page_link'],
                'danger' => false,
                'mainPage' => true,
                'inRelevance' => false,
                'coverage' => $this->calculateCoveragePercent($mainPageText, $competitorsText),
                'tf' => $this->calculateCoverageTF($mainPageText),
            ];
        }

        $this->params['sites'] = json_encode($this->sites);
    }

    /**
     * Расчёт баллов для таблицы "Проанализированные сайты"
     * @return void
     */
    public function calculatePoints()
    {
        $avgCoveragePercent = 0;
        for ($i = 0; $i < 10; $i++) {
            $avgCoveragePercent += $this->sites[$i]['coverage'] / 10;
        }
        foreach ($this->sites as $key => $site) {
            $points = $this->sites[$key]['coverage'] / ($avgCoveragePercent / 100);
            $points = min($points, 100);
            $this->sites[$key]['points'] = round($points, 2);
        }
    }

    /**
     * @param $wordsInText
     * @return float
     */
    public function calculateCoverageTF($wordsInText): float
    {
        $result = 0;
        foreach ($this->coverageInfo['600'] as $word => $value) {
            if ($word != 'total') {
                if (in_array($word, $wordsInText)) {
                    $result += $value;
                }
            }
        }

        return $result;
    }

    /**
     * @param $text
     * @param $competitorsText
     * @return float
     */
    public function calculateCoveragePercent($text, $competitorsText): float
    {
        $percent = count($competitorsText) / 100;
        return round(100 - count(array_diff($competitorsText, $text)) / $percent, 2);
    }

    /**
     * ищем все уникальные слова в предоставленой строке/тексте
     * @param $string
     * @return array
     */
    public static function searchWords($string): array
    {
        $array = array_count_values(explode(" ", $string));
        $newArray = [];
        foreach ($array as $key => $item) {
            $newArray[] = $key;
        }

        return $newArray;
    }

    /**
     * Получить скрытый текст из alt,title,data-text атрибутов
     * @param $html
     * @return array|string|string[]|null
     */
    public static function getHiddenText($html)
    {
        $hiddenText = '';
        $regex = ["<.*?title=\"(.*?)\".*>", "<.*?alt=\"(.*?)\".*>", "<.*?data-text=\"(.*?)\".*>"];
        foreach ($regex as $reg) {
            preg_match_all($reg, $html, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                if ($match[1] != "") {
                    $hiddenText .= $match[1] . ' ';
                }
            }
        }

        return TextAnalyzer::deleteEverythingExceptCharacters($hiddenText);
    }

    /**
     * Удаляем союзы, предлоги, местоимения
     * @param $conjunctionsPrepositionsPronouns
     * @return void
     */
    public function removePartsOfSpeech($conjunctionsPrepositionsPronouns)
    {
        if ($conjunctionsPrepositionsPronouns == 'false') {
            $this->mainPage['html'] = TextAnalyzer::removeConjunctionsPrepositionsPronouns($this->mainPage['html']);
            $this->mainPage['linkText'] = TextAnalyzer::removeConjunctionsPrepositionsPronouns($this->mainPage['linkText']);
            $this->mainPage['hiddenText'] = TextAnalyzer::removeConjunctionsPrepositionsPronouns($this->mainPage['hiddenText']);
            foreach ($this->pages as $key => $page) {
                $this->pages[$key]['html'] = TextAnalyzer::removeConjunctionsPrepositionsPronouns($this->pages[$key]['html']);
                $this->pages[$key]['linkText'] = TextAnalyzer::removeConjunctionsPrepositionsPronouns($this->pages[$key]['linkText']);
                $this->pages[$key]['hiddenText'] = TextAnalyzer::removeConjunctionsPrepositionsPronouns($this->pages[$key]['hiddenText']);
            }
        }
    }

    /**
     * Удаляем полученного текста слова
     * @param $request
     * @return void
     */
    public function removeListWords($request)
    {
        if ($request->switchMyListWords == 'true') {
            $listWords = str_replace(["\r\n", "\n\r"], "\n", $request->listWords);
            $this->ignoredWords = explode("\n", $listWords);
            $this->mainPage['html'] = Relevance::mbStrReplace($this->ignoredWords, '', $this->mainPage['html']);
            $this->mainPage['linkText'] = Relevance::mbStrReplace($this->ignoredWords, '', $this->mainPage['linkText']);
            $this->mainPage['hiddenText'] = Relevance::mbStrReplace($this->ignoredWords, '', $this->mainPage['hiddenText']);
            foreach ($this->pages as $key => $page) {
                $this->pages[$key]['html'] = Relevance::mbStrReplace($this->ignoredWords, '', $this->pages[$key]['html']);
                $this->pages[$key]['linkText'] = Relevance::mbStrReplace($this->ignoredWords, '', $this->pages[$key]['linkText']);
                $this->pages[$key]['hiddenText'] = Relevance::mbStrReplace($this->ignoredWords, '', $this->pages[$key]['hiddenText']);
            }
        }
    }

    /**
     * Преобразование слова
     *
     * @param $search
     * @param $replace
     * @param $string
     * @return array|false|string|string[]
     */
    public static function mbStrReplace($search, $replace, $string)
    {
        $charset = mb_detect_encoding($string);

        $unicodeString = iconv($charset, "UTF-8", $string);

        return str_replace($search, $replace, $unicodeString);
    }

    /**
     * @return void
     */
    public function searchWordForms()
    {
        $array = explode(' ', $this->competitorsTextAndLinks);
        $stemmer = new LinguaStem();

        $array = array_count_values($array);
        asort($array);
        $array = array_reverse($array);

        foreach ($array as $key1 => $item1) {
            if (!in_array($key1, $this->ignoredWords)) {
                foreach ($array as $key2 => $item2) {
                    if (!in_array($key2, $this->ignoredWords)) {
                        similar_text($key1, $key2, $percent);
                        if (
                            preg_match("/[А-Яа-я]/", $key1) &&
                            $stemmer->getRootWord($key2) == $stemmer->getRootWord($key1) ||
                            preg_match("/[A-Za-z]/", $key1) &&
                            $percent >= 82
                        ) {
                            $this->wordForms[$key1][$key2] = $item2;
                            $this->ignoredWords[] = $key2;
                            $this->ignoredWords[] = $key1;
                        }
                    }
                }
            }
            if (count($this->wordForms) >= 600) {
                break;
            }
        }
    }

    /**
     * Обработка информации для таблицы unigram
     * @return void
     */
    public function processingOfGeneralInformation()
    {
        //TODO САМЫЙ РЕСУРСОЗАТРАТНЫЙ МОМЕНТ - НУЖНО ОПТИМИЗИРОВАТЬ
        $countSites = count($this->sites);
        $wordCount = str_word_count($this->competitorsTextAndLinks);
        foreach ($this->wordForms as $root => $wordForm) {
            foreach ($wordForm as $word => $item) {
                $reSpam = $numberTextOccurrences = $numberLinkOccurrences = $numberOccurrences = 0;
                $occurrences = [];
                foreach ($this->pages as $key => $page) {
                    if (preg_match("/($word)/", $page['html'])) {
                        $count = substr_count($this->pages[$key]['html'], " $word ");
                        $numberTextOccurrences += $count;
                        if ($reSpam < $count) {
                            $reSpam = $count;
                        }
                    }
                    if (preg_match("/($word)/", $this->pages[$key]['hiddenText'])) {
                        $count = substr_count($this->pages[$key]['hiddenText'], " $word ");
                        $numberTextOccurrences += $count;
                        if ($reSpam < $count) {
                            $reSpam = $count;
                        }
                    }
                    if (preg_match("/($word)/", $this->pages[$key]['linkText'])) {
                        $count = substr_count($this->pages[$key]['linkText'], " $word ");
                        $numberLinkOccurrences += $count;
                        if ($reSpam < $count) {
                            $reSpam = $count;
                        }
                    }

                    if (preg_match("/($word)/", $this->pages[$key]['html']) ||
                        preg_match("/($word)/", $this->pages[$key]['linkText']) ||
                        preg_match("/($word)/", $this->pages[$key]['hiddenText'])) {
                        $numberOccurrences++;
                        $occurrences[] = $key;
                    }
                }

                $tf = round($item / $wordCount, 4);
                $idf = round(log10($wordCount / $item), 4);

                $repeatInTextMainPage = mb_substr_count($this->mainPage['html'] . ' ' . $this->mainPage['hiddenText'], "$word ");
                $repeatLinkInMainPage = mb_substr_count($this->mainPage['linkText'], "$word ");
                $this->wordForms[$root][$word] = [
                    'tf' => $tf,
                    'idf' => $idf,
                    'numberOccurrences' => $numberOccurrences,
                    'reSpam' => $reSpam,
                    'avgInTotalCompetitors' => ($numberLinkOccurrences + $numberTextOccurrences) / $countSites,
                    'avgInLink' => $numberLinkOccurrences / $countSites,
                    'avgInText' => $numberTextOccurrences / $countSites,
                    'repeatInLinkMainPage' => $repeatLinkInMainPage,
                    'repeatInTextMainPage' => $repeatInTextMainPage,
                    'totalRepeatMainPage' => $repeatLinkInMainPage + $repeatInTextMainPage,
                    'occurrences' => $occurrences
                ];
            }
        }
    }

    /**
     * @return void
     */
    public function prepareUnigramTable()
    {
        $this->coverageInfo['total'] = $iterator = 0;
        foreach ($this->wordForms as $key => $wordForm) {
            $tf = $idf = $reSpam = $numberOccurrences = $repeatInText = $repeatInLink = $avgInText = 0;
            $avgInLink = $avgInTotalCompetitors = $totalRepeatMainPage = 0;
            $occurrences = [];
            foreach ($wordForm as $word) {
                $danger = $word['repeatInTextMainPage'] == 0 || $word['repeatInLinkMainPage'] == 0;
                $tf += $word['tf'];
                $idf += $word['idf'];
                $avgInTotalCompetitors += $word['avgInTotalCompetitors'];
                $totalRepeatMainPage += $word['totalRepeatMainPage'];
                $avgInText += $word['avgInText'];
                $avgInLink += $word['avgInLink'];
                $repeatInText += $word['repeatInTextMainPage'];
                $repeatInLink += $word['repeatInLinkMainPage'];
                $reSpam += $word['reSpam'];
                if ($word['numberOccurrences'] > $numberOccurrences) {
                    $numberOccurrences = $word['numberOccurrences'];
                }
                $occurrences = array_merge($occurrences, $word['occurrences']);
            }
            $this->wordForms[$key]['total'] = [
                'tf' => $tf,
                'idf' => $idf,
                'avgInTotalCompetitors' => $avgInTotalCompetitors,
                'avgInText' => $avgInText,
                'avgInLink' => $avgInLink,
                'repeatInTextMainPage' => $repeatInText,
                'repeatInLinkMainPage' => $repeatInLink,
                'totalRepeatMainPage' => $totalRepeatMainPage,
                'numberOccurrences' => $numberOccurrences,
                'reSpam' => $reSpam,
                'danger' => $danger,
                'occurrences' => array_values(array_unique($occurrences)),
            ];
            $this->density[$key] = $avgInTotalCompetitors;
            if ($iterator < 600) {
                $this->coverageInfo['total'] = round($this->coverageInfo['total'] + $tf, 4);
                $this->coverageInfo['600'][$key] = $tf;
            }
            $iterator++;
        }
    }

    /**
     * Подготовка облаков (http://cavaliercoder.com/jclouds)
     * @return void
     */
    public function prepareClouds()
    {
        $mainPage = Relevance::concatenation([
            $this->mainPage['html'],
            $this->mainPage['hiddenText'],
            $this->mainPage['linkText']
        ]);
        $textMainPage = Relevance::concatenation([
            $this->mainPage['html'],
            $this->mainPage['hiddenText']
        ]);
        $this->mainPage['totalTf'] = Relevance::prepareTfCloud($mainPage);
        $this->mainPage['textTf'] = Relevance::prepareTfCloud($textMainPage);
        $this->mainPage['linkTf'] = Relevance::prepareTfCloud($this->mainPage['linkText']);

        $this->mainPage['textWithLinks'] = TextAnalyzer::prepareCloud($mainPage);
        $this->mainPage['text'] = TextAnalyzer::prepareCloud($textMainPage);
        $this->mainPage['links'] = TextAnalyzer::prepareCloud($this->mainPage['linkText']);

        $this->competitorsCloud['totalTf'] = Relevance::prepareTFCloud($this->competitorsTextAndLinks);
        $this->competitorsCloud['textTf'] = Relevance::prepareTFCloud($this->competitorsText);
        $this->competitorsCloud['linkTf'] = Relevance::prepareTFCloud($this->competitorsLinks);

        $this->competitorsTextAndLinksCloud = TextAnalyzer::prepareCloud($this->competitorsTextAndLinks);
        $this->competitorsTextCloud = TextAnalyzer::prepareCloud($this->competitorsText);
        $this->competitorsLinksCloud = TextAnalyzer::prepareCloud($this->competitorsLinks);
    }

    /**
     * @param $count
     * @param $ignoredDomains
     * @param $xmlResponse
     * @return void
     */
    public function removeIgnoredDomains($count, $ignoredDomains, $xmlResponse)
    {
        if (isset($ignoredDomains)) {
            $ignoredDomains = str_replace("\r\n", "\n", $ignoredDomains);
            $ignoredDomains = explode("\n", $ignoredDomains);
            $ignoredDomains = array_map("mb_strtolower", $ignoredDomains);
            foreach ($xmlResponse as $item) {
                $domain = str_replace('www.', "", mb_strtolower($item['doc']['domain']));
                if (!in_array($domain, $ignoredDomains)) {
                    $this->domains[] = $item;
                }
                if (count($this->domains) == $count) {
                    break;
                }
            }
        } else {
            $this->domains = array_slice($xmlResponse, 0, $count - 1);
        }

    }

    /**
     * @param $html
     */
    public function setMainPage($html)
    {
        $this->mainPage['html'] = $html;
        $this->params['html_main_page'] = $html;
    }

    /**
     * @param $sites
     * @return $this
     */
    public function setSites($sites): Relevance
    {
        $this->params['sites'] = $sites;
        $this->sites = json_decode($sites, true);

        return $this;
    }

    /**
     * @param $html_relevance
     * @return $this
     */
    public function setPages($html_relevance): Relevance
    {
        $this->params['html_relevance'] = $html_relevance;
        $html = explode($this->separator, $this->params['html_relevance']);
        unset($html[count($html) - 1]);
        for ($i = 0; $i < 10; $i++) {
            $this->pages[$this->sites[$i]['site']]['html'] = $html[$i];
        }

        return $this;
    }

    /**
     * @param array $array
     * @return string
     */
    public static function concatenation(array $array): string
    {
        return implode(' ', $array);
    }

    /**
     * @param $sites
     * @return $this
     */
    public function setDomains($sites): Relevance
    {
        $array = json_decode($sites, true);
        foreach ($array as $item) {
            $this->domains[] = $item['site'];
        }

        return $this;
    }

    /**
     * @param $text
     * @return array
     */
    public function prepareTfCloud($text): array
    {
        $wordForms = $cloud = [];
        $lingua = new LinguaStem();
        $wordCount = count(explode(" ", $text));
        $array = array_count_values(explode(' ', $text));
        arsort($array);
        $array = array_slice($array, 0, 199);
        foreach ($array as $key => $item) {
            $tf = round($item / $wordCount, 4);
            $cloud[] = [
                'text' => $key,
                'weight' => $tf,
            ];
        }

        foreach ($cloud as $key1 => $item1) {
            $weight = 0;
            foreach ($cloud as $key2 => $item2) {
                similar_text($item1['text'], $item2['text'], $percent);
                if (
                    preg_match("/[А-Яа-я]/", $item1['text']) &&
                    $lingua->getRootWord($item1['text']) == $lingua->getRootWord($item2['text']) ||
                    preg_match("/[A-Za-z]/", $item2['text']) &&
                    $percent >= 82
                ) {
                    $weight += $item2['weight'];
                    unset($cloud[$key1]);
                    unset($cloud[$key2]);
                }
            }
            $totalWeight = $item1['weight'] + $weight;
            $wordForms[] = [
                'text' => $item1['text'],
                'weight' => $totalWeight,
                'html' => [
                    'title' => $totalWeight
                ]
            ];

            if (count($wordForms) == 200) {
                break;
            }
        }
        $wordForms['count'] = count($wordForms) - 1;
        $collection = collect($wordForms);

        return $collection->sortByDesc('weight')->toArray();
    }

    /**
     * @param $html
     * @return string
     */
    public static function clearHTMLFromLinks($html): string
    {
        $html = preg_replace('| +|', ' ', $html);
        $html = str_replace("\n", " ", $html);
        preg_match_all('(<a.*?href=["\']?(.*?)([\'"].*?>(.*?)</a>))', $html, $matches, PREG_SET_ORDER);
        foreach ($matches as $items) {
            $html = str_replace($items[0], "", $html);
        }
        return $html;
    }

    /**
     * Обрезать все слова короче N символов
     * @param $text
     * @return string
     */
    public function separateText($text): string
    {
        $text = explode(" ", $text);
        foreach ($text as $key => $item) {
            if (Str::length($item) < $this->maxWordLength) {
                unset($text[$key]);
            }
        }
        return implode(" ", $text);
    }
}
