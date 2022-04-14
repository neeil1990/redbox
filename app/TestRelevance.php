<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TestRelevance
{

    public $separator = "\n\nseparator\n\n";

    public $competitorsTextAndLinksCloud;

    public $mainPageIsRelevance = false;

    public $competitorsTextAndLinks;

    public $competitorsLinksCloud;

    public $competitorsCloud = [];

    public $competitorsTextCloud;

    public $tfCompClouds = [];

    public $competitorsLinks;

    public $competitorsText;

    public $maxWordLength;

    public $ignoredWords;

    public $coverageInfo;

    public $wordForms;

    public $mainPage;

    public $domains;

    public $phrases;

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
     * @return void
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
            $this->sites[$domain]['site'] = $domain;
            if ($result == '' || $result == null) {
                $this->sites[$domain]['danger'] = true;
            } else {
                $this->sites[$domain]['danger'] = false;
            }

            $this->pages[$domain]['html'] = $result;
            $this->params['html_relevance'] .= $result . $this->separator;

            //Если проанализированный домен является посадочной страницей
            $lastItem = array_key_last($this->sites);
            if ($domain == $this->params['main_page_link']) {
                $this->mainPageIsRelevance =
                $this->sites[$lastItem]['mainPage'] =
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
        $this->preparePhrasesTable();
        $this->searchWordForms();
        $this->processingOfGeneralInformation();
        $this->prepareUnigramTable();
        $this->prepareClouds();
        $this->calculateCoveragePoints();
        $this->calculatePoints();
        $this->calculateDensity();
        $this->params['sites'] = json_encode($this->sites);
        $this->params->save();
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
        $this->mainPage['html'] = TestRelevance::clearHTMLFromLinks($this->mainPage['html']);
        foreach ($this->pages as $key => $page) {
            $this->pages[$key]['linkText'] = TextAnalyzer::getLinkText($this->pages[$key]['html']);
            $this->pages[$key]['html'] = TestRelevance::clearHTMLFromLinks($this->pages[$key]['html']);
        }
    }

    /**
     * @param $hiddenText
     * @return void
     */
    public function getHiddenData($hiddenText)
    {
        if ($hiddenText == 'true') {
            $this->mainPage['hiddenText'] = TestRelevance::getHiddenText($this->mainPage['html']);
            foreach ($this->pages as $key => $page) {
                $this->pages[$key]['hiddenText'] = TestRelevance::getHiddenText($this->pages[$key]['html']);
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
            $this->pages[$key]['coverage'] = 0;
            $this->pages[$key]['coverageTf'] = 0;
        }
    }

    /**
     * @return void
     */
    public function calculateCoveragePoints()
    {
        //расчёт общей суммы tf
        $totalTf = 0;
        foreach ($this->wordForms as $wordForm) {
            $totalTf += $wordForm['total']['tf'];
        }

        foreach ($this->pages as $pageKey => $page) {
            $object = $page['html'] . ' ' . $page['linkText'] . ' ' . $page['hiddenText'];
            $this->calculateCoverage($object, $pageKey);
        }

        foreach ($this->pages as $pageKey => $page) {
            $this->sites[$pageKey]['coverage'] = round($this->pages[$pageKey]['coverage'] / 6, 2);
            $this->sites[$pageKey]['coverageTf'] = round($this->pages[$pageKey]['coverageTf'] / ($totalTf / 100), 2);
        }

        if (!$this->mainPageIsRelevance) {
            $totalCount = 0;
            $mainPageTf = 0;
            $mainPageText = $this->mainPage['html'] . ' ' . $this->mainPage['linkText'] . ' ' . $this->mainPage['hiddenText'];
            foreach ($this->wordForms as $wordForm) {
                foreach ($wordForm as $word => $form) {
                    $count = mb_substr_count($mainPageText, "$word ");
                    if ($count > 0) {
                        $totalCount++;
                        break;
                    }
                }
                foreach ($wordForm as $word => $form) {
                    if ($word != 'total') {
                        $count = mb_substr_count($mainPageText, "$word ");
                        if ($count > 0) {
                            $mainPageTf += $wordForm['total']['tf'];
                        }
                    }
                }
            }

            $this->sites[$this->params['main_page_link']] = [
                'site' => $this->params['main_page_link'],
                'danger' => false,
                'mainPage' => true,
                'inRelevance' => false,
                'coverage' => round($totalCount / 6, 2),
                'coverageTf' => round($mainPageTf / ($totalTf / 100), 2),
            ];
        }
    }

    public function calculateCoverage($object, $pageKey)
    {
        foreach ($this->wordForms as $wordForm) {
            foreach ($wordForm as $word => $form) {
                $count = mb_substr_count($object, "$word ");
                if ($count > 0) {
                    $this->pages[$pageKey]['coverage']++;
                    break;
                }
            }
            foreach ($wordForm as $word => $form) {
                if ($word != 'total') {
                    $count = mb_substr_count($object, "$word ");
                    if ($count > 0) {
                        $this->pages[$pageKey]['coverageTf'] += $form['tf'];
                    }
                }
            }
        }
    }

    /**
     * Расчёт баллов для таблицы "Проанализированные сайты"
     * @return void
     */
    public function calculatePoints()
    {
        $avgCoveragePercent = $iterator = 0;
        foreach ($this->sites as $site) {
            if ($iterator == 10) {
                break;
            }
            $avgCoveragePercent += $site['coverage'];
            $iterator++;
        }
        $avgCoveragePercent /= 10;
        foreach ($this->sites as $key => $site) {
            $points = $this->sites[$key]['coverage'] / ($avgCoveragePercent / 100);
            $points = min($points, 100);
            $this->sites[$key]['width'] = round($points, 2);
        }
    }

    /**
     * @param $wordsInText
     * @return float
     */
    public function calculateCoverageTF($wordsInText): float
    {
        $sum = 0;
        foreach ($wordsInText as $key => $value) {
            if (array_key_exists($key, $this->coverageInfo['words'])) {
                $sum += $this->coverageInfo['words'][$key];
            }
        }

        $percent = $this->coverageInfo['sum'] / 100;

        return $sum / $percent;
    }

    /**
     * @param $pageText
     * @return void
     */
    public function calculateCoveragePercent($pageText)
    {
//        $totalCount = 0;
//        foreach ($this->wordForms as $key => $wordForm) {
//            $count = mb_substr_count($pageText, "$key ");
//            if ($count > 0) {
//                $totalCount++;
//            }
//        }
//
//        return round($totalCount / 6, 2);
        foreach ($this->wordForms as $keyWord => $wordForm) {
//            Log::debug('$wordForm', [$wordForm]);
//            Log::debug('$keyWord', [$keyWord]);
            foreach ($wordForm as $word => $form) {
//                Log::debug('$word', [$word]);
//                Log::debug('$form', [$form]);
                $count = mb_substr_count($this->mainPage['html'] . ' ' . $this->mainPage['linkText'] . ' ' . $this->mainPage['hiddenText'], "$word ");
                if ($count > 0) {
                    $this->sites[$this]['coverage']++;
                }
            }
        }
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
            $this->mainPage['html'] = TestRelevance::mbStrReplace($this->ignoredWords, '', $this->mainPage['html']);
            $this->mainPage['linkText'] = TestRelevance::mbStrReplace($this->ignoredWords, '', $this->mainPage['linkText']);
            $this->mainPage['hiddenText'] = TestRelevance::mbStrReplace($this->ignoredWords, '', $this->mainPage['hiddenText']);
            foreach ($this->pages as $key => $page) {
                $this->pages[$key]['html'] = TestRelevance::mbStrReplace($this->ignoredWords, '', $this->pages[$key]['html']);
                $this->pages[$key]['linkText'] = TestRelevance::mbStrReplace($this->ignoredWords, '', $this->pages[$key]['linkText']);
                $this->pages[$key]['hiddenText'] = TestRelevance::mbStrReplace($this->ignoredWords, '', $this->pages[$key]['hiddenText']);
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
        $wordCount = count(explode(' ', $this->competitorsTextAndLinks));
        foreach ($this->wordForms as $root => $wordForm) {
            foreach ($wordForm as $word => $item) {
                $reSpam = $numberTextOccurrences = $numberLinkOccurrences = $numberOccurrences = 0;
                $occurrences = [];
                foreach ($this->pages as $key => $page) {

                    $htmlCount = mb_substr_count($this->pages[$key]['html'], "$word ");
                    if ($htmlCount > 0) {
                        $numberTextOccurrences += $htmlCount;
                        if ($reSpam < $htmlCount) {
                            $reSpam = $htmlCount;
                        }
                    }

                    $hiddenTextCount = mb_substr_count($this->pages[$key]['hiddenText'], "$word ");
                    if ($hiddenTextCount > 0) {
                        $numberTextOccurrences += $hiddenTextCount;
                        if ($reSpam < $hiddenTextCount) {
                            $reSpam = $hiddenTextCount;
                        }
                    }

                    $linkTextCount = mb_substr_count($this->pages[$key]['linkText'], "$word ");
                    if ($linkTextCount > 0) {
                        $numberLinkOccurrences += $linkTextCount;
                        if ($reSpam < $linkTextCount) {
                            $reSpam = $linkTextCount;
                        }
                    }

                    if ($htmlCount > 0 || $hiddenTextCount > 0 || $linkTextCount > 0) {
                        $numberOccurrences++;
                        $occurrences[] = $key;
                    }
                }

                $tf = round($item / $wordCount, 5);
                $idf = round(log10($wordCount / $item), 5);

                $repeatInTextMainPage = mb_substr_count($this->mainPage['html'] . ' ' . $this->mainPage['hiddenText'], "$word ");
                $repeatLinkInMainPage = mb_substr_count($this->mainPage['linkText'], "$word ");

                $this->wordForms[$root][$word] = [
                    'tf' => $tf,
                    'idf' => $idf,
                    'numberOccurrences' => $numberOccurrences,
                    'reSpam' => $reSpam,
                    'avgInTotalCompetitors' => (int)ceil(($numberLinkOccurrences + $numberTextOccurrences) / $countSites),
                    'avgInLink' => (int)ceil($numberLinkOccurrences / $countSites),
                    'avgInText' => (int)ceil($numberTextOccurrences / $countSites),
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
        $this->coverageInfo['sum'] = 0;

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

            /** @var $danger */
            $this->wordForms[$key]['total'] = [
                'tf' => $tf,
                'idf' => $idf,
                'avgInTotalCompetitors' => (int)ceil($avgInTotalCompetitors),
                'avgInText' => (int)ceil($avgInText),
                'avgInLink' => (int)ceil($avgInLink),
                'repeatInTextMainPage' => $repeatInText,
                'repeatInLinkMainPage' => $repeatInLink,
                'totalRepeatMainPage' => $totalRepeatMainPage,
                'numberOccurrences' => $numberOccurrences,
                'reSpam' => $reSpam,
                'danger' => $danger,
                'occurrences' => array_values(array_unique($occurrences)),
            ];

            $this->density[$key] = [
                'count' => $avgInTotalCompetitors,
                'tf' => $tf
            ];
        }

        $collection = collect($this->density);
        $this->density = $collection->sortByDesc('tf')->toArray();
    }

    /**
     * Подготовка облаков (http://cavaliercoder.com/jclouds)
     * @return void
     */
    public function prepareClouds()
    {
        $mainPage = TestRelevance::concatenation([
            $this->mainPage['html'],
            $this->mainPage['hiddenText'],
            $this->mainPage['linkText']
        ]);
        $textMainPage = TestRelevance::concatenation([
            $this->mainPage['html'],
            $this->mainPage['hiddenText']
        ]);
        $this->mainPage['totalTf'] = TestRelevance::prepareTfCloud($mainPage);
        $this->mainPage['textTf'] = TestRelevance::prepareTfCloud($textMainPage);
        $this->mainPage['linkTf'] = TestRelevance::prepareTfCloud($this->mainPage['linkText']);

        $this->mainPage['textWithLinks'] = TextAnalyzer::prepareCloud($mainPage);
        $this->mainPage['text'] = TextAnalyzer::prepareCloud($textMainPage);
        $this->mainPage['links'] = TextAnalyzer::prepareCloud($this->mainPage['linkText']);

        $this->competitorsCloud['totalTf'] = TestRelevance::prepareTFCloud($this->competitorsTextAndLinks);
        $this->competitorsCloud['textTf'] = TestRelevance::prepareTFCloud($this->competitorsText);
        $this->competitorsCloud['linkTf'] = TestRelevance::prepareTFCloud($this->competitorsLinks);

        $this->competitorsTextAndLinksCloud = TextAnalyzer::prepareCloud($this->competitorsTextAndLinks);
        $this->competitorsTextCloud = TextAnalyzer::prepareCloud($this->competitorsText);
        $this->competitorsLinksCloud = TextAnalyzer::prepareCloud($this->competitorsLinks);

        foreach ($this->pages as $key => $page) {
            $this->tfCompClouds[$key] = $this->prepareTfCloud($this->separateText($page['html'] . ' ' . $page['linkText']));
        }
    }

    /**
     * @param $count
     * @param $ignoredDomains
     * @param $sites
     * @return void
     */
    public function removeIgnoredDomains($count, $ignoredDomains, $sites)
    {
        if (isset($ignoredDomains)) {
            $ignoredDomains = str_replace("\r\n", "\n", $ignoredDomains);
            $ignoredDomains = explode("\n", $ignoredDomains);
            $ignoredDomains = array_map("mb_strtolower", $ignoredDomains);
            foreach ($sites as $item) {
                $domain = str_replace('www.', "", mb_strtolower($item['doc']['domain']));
                if (!in_array($domain, $ignoredDomains)) {
                    $this->domains[] = $item;
                }
                if (count($this->domains) == $count) {
                    break;
                }
            }
        } else {
            $this->domains = array_slice($sites, 0, $count - 1);
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
    public function setSites($sites): TestRelevance
    {
        $this->params['sites'] = $sites;
        $this->sites = json_decode($sites, true);

        return $this;
    }

    /**
     * @param $html_relevance
     * @return $this
     */
    public function setPages($html_relevance): TestRelevance
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
     * @return void
     */
    public function setDomains($sites)
    {
        $array = json_decode($sites, true);
        foreach ($array as $item) {
            $this->domains[] = $item['site'];
        }
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

    /**
     * @return void
     */
    public function preparePhrasesTable()
    {
        $result = [];
        $phrases = $this->searchPhrases();
        $totalCount = count($phrases);
        foreach ($phrases as $phrase) {
            if ($phrase != "") {
                $reSpam = $numberTextOccurrences = $numberLinkOccurrences = $numberOccurrences = 0;
                $occurrences = [];
                foreach ($this->pages as $key => $page) {
                    if (preg_match("/($phrase)/", $page['html']) ||
                        preg_match("/($phrase)/", $page['linkText']) ||
                        preg_match("/($phrase)/", $page['hiddenText'])) {
                        $numberOccurrences++;
                        $occurrences[] = $key;
                    }

                    if (preg_match("/($phrase)/", $page['html'])) {
                        $count = mb_substr_count($this->pages[$key]['html'], "$phrase");
                        $numberTextOccurrences += $count;
                        if ($reSpam < $count) {
                            $reSpam = $count;
                        }
                    }

                    if (preg_match("/($phrase)/", $page['hiddenText'])) {
                        $count = mb_substr_count($this->pages[$key]['hiddenText'], "$phrase");
                        $numberTextOccurrences += $count;
                        if ($reSpam < $count) {
                            $reSpam = $count;
                        }
                    }

                    if (preg_match("/($phrase)/", $page['linkText'])) {
                        $count = mb_substr_count($this->pages[$key]['linkText'], "$phrase");
                        $numberLinkOccurrences += $count;
                        if ($reSpam < $count) {
                            $reSpam = $count;
                        }
                    }
                }
                if ($numberOccurrences > 0) {
                    $countOccurrences = $numberTextOccurrences + $numberLinkOccurrences;
                    $tf = round($countOccurrences / $totalCount, 6);
                    $idf = round(log10($totalCount / $countOccurrences), 6);
                    $repeatInTextMainPage = mb_substr_count(
                        TestRelevance::concatenation([$this->mainPage['html'], $this->mainPage['hiddenText']]),
                        "$phrase");
                    $repeatLinkInMainPage = mb_substr_count($this->mainPage['linkText'], "$phrase");
                    $countSites = count($this->sites);
                    $result[$phrase] = [
                        'tf' => $tf,
                        'idf' => $idf,
                        'numberOccurrences' => $numberOccurrences,
                        'reSpam' => $reSpam,
                        'avgInTotalCompetitors' => (int)ceil(($numberLinkOccurrences + $numberTextOccurrences) / $countSites),
                        'avgInLink' => (int)ceil($numberLinkOccurrences / $countSites),
                        'avgInText' => (int)ceil($numberTextOccurrences / $countSites),
                        'repeatInLinkMainPage' => $repeatLinkInMainPage,
                        'repeatInTextMainPage' => $repeatInTextMainPage,
                        'totalRepeatMainPage' => $repeatLinkInMainPage + $repeatInTextMainPage,
                        'occurrences' => $occurrences
                    ];
                }
            }
        }

        $collection = collect($result);
        $collection = $collection->unique();
        $collection = $collection->sortByDesc('tf');
        $this->phrases = $collection->slice(0, 600)->toArray();
    }

    /**
     * из строки "купить много хлеба" получает фразы (купить много, много хлеба)
     *
     * @return array
     */
    public function searchPhrases(): array
    {
        $phrases = [];
        $array = explode(' ', $this->competitorsTextAndLinks);

        $grouped = array_chunk($array, 2);
        foreach ($grouped as $two_words) {
            $phrases[] = implode(' ', $two_words);
        }
        unset($array[0]);
        $grouped = array_chunk($array, 2);
        foreach ($grouped as $two_words) {
            $phrases[] = implode(' ', $two_words);
        }

        $phrases = collect($phrases);
        return $phrases->unique()->toArray();
    }

    /**
     * @return void
     */
    public function calculateDensity()
    {
        foreach ($this->pages as $keyPage => $page) {
            $allText = TestRelevance::concatenation([$page['html'], $page['linkText'], $page['hiddenText']]);
            $density = $this->calculateDensityPoints($allText);
            $this->sites[$keyPage]['density'] = $density[600]['percentPoints'];
            $this->sites[$keyPage]['densityPoints'] = $density[600]['totalPoints'];
            $this->sites[$keyPage]['density100'] = $density[100]['percentPoints'];
            $this->sites[$keyPage]['density100Points'] = $density[100]['totalPoints'];
            $this->sites[$keyPage]['density200'] = $density[200]['percentPoints'];
            $this->sites[$keyPage]['density200Points'] = $density[200]['totalPoints'];
        }

        if (!$this->mainPageIsRelevance) {
            $mainPageText = TestRelevance::concatenation([
                $this->mainPage['html'],
                $this->mainPage['linkText'],
                $this->mainPage['hiddenText']
            ]);
            $density = $this->calculateDensityPoints($mainPageText);
            $this->sites[$this->params['main_page_link']]['density'] = $density[600]['percentPoints'];
            $this->sites[$this->params['main_page_link']]['densityPoints'] = $density[600]['totalPoints'];
            $this->sites[$this->params['main_page_link']]['density100'] = $density[100]['percentPoints'];
            $this->sites[$this->params['main_page_link']]['density100Points'] = $density[100]['totalPoints'];
            $this->sites[$this->params['main_page_link']]['density200'] = $density[200]['percentPoints'];
            $this->sites[$this->params['main_page_link']]['density200Points'] = $density[200]['totalPoints'];
        }
    }

    /**
     * @param $text
     * @return array
     */
    public function calculateDensityPoints($text): array
    {
        $result = [];
        $allPoints = 0;
        $iterator = 0;
        foreach ($this->density as $word => $value) {
            if (preg_match("/($word)/", $text)) {
                $count = substr_count($text, " $word ");
                $points = min($count / ($value['count'] / 100), 100);
                $allPoints += $points;
            }
            $iterator++;
            if ($iterator == 100) {
                $result[100] = [
                    'percentPoints' => round($allPoints * 2 / 600),
                    'totalPoints' => round($allPoints),
                ];
            }
            if ($iterator == 200) {
                $result[200] = [
                    'percentPoints' => round($allPoints * 2 / 600),
                    'totalPoints' => round($allPoints),
                ];
            }
            if ($iterator == 600) {
                $result[600] = [
                    'percentPoints' => round($allPoints / 600),
                    'totalPoints' => round($allPoints),
                ];
                break;
            }
        }

        return $result;
    }

}
