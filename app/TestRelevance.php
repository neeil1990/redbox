<?php

namespace App;

use Illuminate\Support\Facades\Auth;
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

    public $avgCoveragePercent;

    public $ignoredWords;

    public $coverageInfo;

    public $wordForms;

    public $mainPage;

    public $domains;

    public $phrases;

    public $params;

    public $pages;

    public $sites;

    public $recommendations = [];

    /**
     * @param $link
     * @param $separator
     */
    public function __construct($link, $separator)
    {
        $this->pages = [];
        $this->domains = [];
        $this->mainPage = [];
        $this->wordForms = [];
        $this->ignoredWords = [];
        $this->competitorsText = '';
        $this->competitorsLinks = '';
        $this->competitorsTextAndLinks = '';
        $this->maxWordLength = $separator;

        $this->params = RelevanceAnalyseResults::firstOrNew(['user_id' => Auth::id()]);
        $this->params['main_page_link'] = $link;
        $this->params['sites'] = '';
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
        $mainUrl = parse_url($this->params['main_page_link']);

        foreach ($this->domains as $item) {
            $domain = strtolower($item['item']);
            $result = TextAnalyzer::removeStylesAndScripts(TextAnalyzer::curlInit($domain));

            $compUrl = parse_url($domain);

            $this->sites[$domain]['danger'] = $result == '' || $result == null;
            $this->sites[$domain]['html'] = $result;
            $this->sites[$domain]['site'] = $domain;

            if (Str::lower($mainUrl['host']) == Str::lower($compUrl['host'])) {
                $this->sites[$domain]['equallyHost'] = true;
            }
            //Если проанализированный домен является посадочной страницей
            if ($domain == $this->params['main_page_link']) {
                $this->mainPageIsRelevance = true;
                $this->sites[$domain]['mainPage'] = true;
                $this->sites[$domain]['inRelevance'] = true;
                $this->sites[$domain]['ignored'] = false;
            } else {
                $this->sites[$domain]['mainPage'] = false;
                $this->sites[$domain]['ignored'] = $item['ignored'];
            }
        }

        if (!$this->mainPageIsRelevance) {
            $this->sites[$this->params['main_page_link']]['inRelevance'] = false;
            $this->sites[$this->params['main_page_link']]['danger'] = false;
            $this->sites[$this->params['main_page_link']]['ignored'] = false;
            $this->sites[$this->params['main_page_link']]['site'] = $this->params['main_page_link'];
            $this->sites[$this->params['main_page_link']]['mainPage'] = true;
            $this->sites[$this->params['main_page_link']]['html'] = $this->mainPage['html'];;
        }
    }

    /**
     * @param $request
     * @return void
     */
    public function analysis($request)
    {
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
        $this->analyzeRecommendations();
        $this->calculateDensity();
        $this->calculateCoveragePoints();
        $this->calculateWidthPoints();
        $this->calculateTotalPoints();
        $this->prepareClouds();
//        $this->saveResults();
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
        foreach ($this->sites as $key => $page) {
            $this->sites[$key]['html'] = TextAnalyzer::deleteEverythingExceptCharacters($this->sites[$key]['html']);
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
            foreach ($this->sites as $key => $page) {
                $this->sites[$key]['html'] = TextAnalyzer::removeNoindexText($page['html']);
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
        foreach ($this->sites as $key => $page) {
            $this->sites[$key]['linkText'] = TextAnalyzer::getLinkText($this->sites[$key]['html']);
            $this->sites[$key]['html'] = TestRelevance::clearHTMLFromLinks($this->sites[$key]['html']);
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
            foreach ($this->sites as $key => $page) {
                $this->sites[$key]['hiddenText'] = TestRelevance::getHiddenText($this->sites[$key]['html']);
            }
        } else {
            $this->mainPage['hiddenText'] = '';
            foreach ($this->sites as $key => $page) {
                $this->sites[$key]['hiddenText'] = '';
            }
        }
    }

    /**
     * Вся информация с сайтов конкурентов с сайтов конкурентов
     * @return void
     */
    public function getTextFromCompetitors()
    {
        foreach ($this->sites as $key => $page) {
            if (!$this->sites[$key]['ignored']) {
                $this->competitorsLinks .= ' ' . $this->sites[$key]['linkText'] . ' ';
                $this->competitorsText .= ' ' . $this->sites[$key]['hiddenText'] . ' ' . $this->sites[$key]['html'] . ' ';
            }

            $this->sites[$key]['coverage'] = 0;
            $this->sites[$key]['coverageTf'] = 0;
        }
    }

    /**
     * @return void
     */
    public function calculateCoveragePoints()
    {
        $totalTf = 0;
        foreach ($this->wordForms as $wordForm) {
            $totalTf += $wordForm['total']['tf'];
        }

        foreach ($this->sites as $pageKey => $page) {
            $object = $page['html'] . ' ' . $page['linkText'] . ' ' . $page['hiddenText'];
            $coverage = $this->calculateCoverage($object);

            $this->sites[$pageKey]['coverage'] = round($coverage['text'] / 6, 2);
            $this->sites[$pageKey]['coverageTf'] = round($coverage['tf'] / ($totalTf / 100), 2);
        }
    }

    /**
     * @param $object
     * @return array
     */
    public function calculateCoverage($object): array
    {
        $text = 0;
        $tf = 0;
        foreach ($this->wordForms as $wordForm) {
            foreach ($wordForm as $word => $form) {
                if ($word != 'total') {
                    if (strpos($object, " $word ") !== false) {
                        $text++;
                        break;
                    }
                }
            }
        }
        foreach ($this->wordForms as $wordForm) {
            foreach ($wordForm as $word => $form) {
                if ($word != 'total') {
                    if (strpos($object, " $word ") !== false) {
                        $tf += $form['tf'];
                    }
                }
            }
        }

        return [
            'text' => $text,
            'tf' => $tf,
        ];
    }

    /**
     * Расчёт баллов для таблицы "Проанализированные сайты"
     * @return void
     */
    public function calculateWidthPoints()
    {
        // высчитываем 100%, игнорируя игнорируемые домены
        $this->avgCoveragePercent = $iterator = 0;
        foreach ($this->sites as $site) {
            if (!$site['ignored']) {
                if ($iterator == 10) {
                    break;
                }
                $this->avgCoveragePercent += $site['coverage'];
                $iterator++;
            }
        }

        $this->avgCoveragePercent /= 10;
        foreach ($this->sites as $key => $site) {
            $points = $this->sites[$key]['coverage'] / ($this->avgCoveragePercent / 100);
            $points = min($points, 100);
            $this->sites[$key]['width'] = round($points, 2);
        }
    }

    /**
     * @return void
     */
    public function calculateTotalPoints()
    {
        foreach ($this->sites as $key => $site) {
            $points = $site['coverage'] + $site['coverageTf'] + $site['density']['densityMainPercent'];
            $this->sites[$key]['mainPoints'] = min(round(($points / 3) * 2, 2), 100);
//            $this->sites[$key]['mainWithGainFixPoints'] = round(($site['coverage'] + $site['coverageTf'] + $site['density']['densityMainWithGainFixPercent']) / 3, 2);
//            $this->sites[$key]['mainWithGainPoints'] = round(($site['coverage'] + $site['coverageTf'] + $site['density']['densityMainWithGainPercent']) / 3, 2);
        }
    }

    /**
     * @return void
     */
    public function analyzeRecommendations()
    {
        foreach ($this->wordForms as $wordForm) {
            foreach ($wordForm as $word => $form) {
                if ($wordForm['total']['avgInTotalCompetitors'] >= 10) {
                    $recommendationMin = ceil($wordForm['total']['avgInTotalCompetitors'] * 0.9);
                    $recommendationMax = ceil($wordForm['total']['avgInTotalCompetitors'] * 1.1);
                } else if ($wordForm['total']['avgInTotalCompetitors'] >= 2) {
                    $recommendationMin = $wordForm['total']['avgInTotalCompetitors'] - 1;
                    $recommendationMax = $wordForm['total']['avgInTotalCompetitors'] + 1;
                } else {
                    $recommendationMin = 1;
                    $recommendationMax = 2;
                }

                if ($wordForm['total']['totalRepeatMainPage'] < $recommendationMin) {
                    $this->recommendations[$word] = [
                        'onPage' => $wordForm['total']['totalRepeatMainPage'],
                        'tf' => round($wordForm['total']['tf'], 5),
                        'avg' => $wordForm['total']['avgInTotalCompetitors'],
                        'diapason' => $recommendationMin . ' - ' . $recommendationMax,
                        'spam' => 0,
                        'add' => ($recommendationMin - $wordForm['total']['totalRepeatMainPage']) . ' - ' . ($recommendationMax - $wordForm['total']['totalRepeatMainPage']),
                        'remove' => 0,
                    ];
                    break;
                } else if ($wordForm['total']['totalRepeatMainPage'] > $recommendationMax) {
                    $this->recommendations[$word] = [
                        'onPage' => $wordForm['total']['totalRepeatMainPage'],
                        'tf' => round($wordForm['total']['tf'], 5),
                        'avg' => $wordForm['total']['avgInTotalCompetitors'],
                        'diapason' => $recommendationMin . ' - ' . $recommendationMax,
                        'spam' => round(($wordForm['total']['totalRepeatMainPage'] - $recommendationMax) / ($recommendationMax / 100)) . '%',
                        'add' => 0,
                        'remove' => ($wordForm['total']['totalRepeatMainPage'] - $recommendationMax) . ' - ' . ($wordForm['total']['totalRepeatMainPage'] - $recommendationMin),
                    ];
                    break;
                }
            }
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
            foreach ($this->sites as $key => $page) {
                $this->sites[$key]['html'] = TextAnalyzer::removeConjunctionsPrepositionsPronouns($this->sites[$key]['html']);
                $this->sites[$key]['linkText'] = TextAnalyzer::removeConjunctionsPrepositionsPronouns($this->sites[$key]['linkText']);
                $this->sites[$key]['hiddenText'] = TextAnalyzer::removeConjunctionsPrepositionsPronouns($this->sites[$key]['hiddenText']);
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
            foreach ($this->sites as $key => $page) {
                $this->sites[$key]['html'] = TestRelevance::mbStrReplace($this->ignoredWords, '', $this->sites[$key]['html']);
                $this->sites[$key]['linkText'] = TestRelevance::mbStrReplace($this->ignoredWords, '', $this->sites[$key]['linkText']);
                $this->sites[$key]['hiddenText'] = TestRelevance::mbStrReplace($this->ignoredWords, '', $this->sites[$key]['hiddenText']);
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
     * Обработка информации для таблицы LTP
     * @return void
     */
    public function processingOfGeneralInformation()
    {
        $countSites = 0;
        // считаем количество сайтов, которые не в списке игнорируемых
        foreach ($this->sites as $site) {
            if (!$site['ignored']) {
                $countSites++;
            }
        }

        $myText = $this->mainPage['html'] . ' ' . $this->mainPage['hiddenText'];
        $myText = explode(" ", $myText);
        $myText = array_count_values($myText);

        $myLink = $this->mainPage['linkText'];
        $myLink = explode(" ", $myLink);
        $myLink = array_count_values($myLink);

        $wordCount = count(explode(' ', $this->competitorsTextAndLinks));
        foreach ($this->wordForms as $root => $wordForm) {
            foreach ($wordForm as $word => $item) {
                $reSpam = $numberTextOccurrences = $numberLinkOccurrences = $numberOccurrences = 0;
                $occurrences = [];
                foreach ($this->sites as $key => $page) {
                    if (!$page['ignored']) {
                        $htmlCount = preg_match_all("( $word )", ' ' . $this->sites[$key]['html'] . ' ');
                        if ($htmlCount > 0) {
                            $numberTextOccurrences += $htmlCount;
                        }

                        $hiddenTextCount = preg_match_all("( $word )", ' ' . $this->sites[$key]['hiddenText'] . ' ');
                        if ($hiddenTextCount > 0) {
                            $numberTextOccurrences += $hiddenTextCount;
                        }

                        $linkTextCount = preg_match_all("( $word )", ' ' . $this->sites[$key]['linkText'] . ' ');
                        if ($linkTextCount > 0) {
                            $numberLinkOccurrences += $linkTextCount;
                        }

                        if ($htmlCount > 0 || $hiddenTextCount > 0 || $linkTextCount > 0) {
                            $countRepeat = $htmlCount + $hiddenTextCount + $linkTextCount;
                            $numberOccurrences++;
                            $occurrences[$key] = $countRepeat;
                            if ($reSpam < $countRepeat) {
                                $reSpam = $countRepeat;
                            }
                        }
                    }
                }

                arsort($occurrences);
                $repeatInTextMainPage = $myText[$word] ?? 0;
                $repeatLinkInMainPage = $myLink[$word] ?? 0;

                $tf = round($item / $wordCount, 5);
                $idf = round(log10($wordCount / $item), 5);

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
                if ($reSpam < $word['reSpam']) {
                    $reSpam = $word['reSpam'];
                }

                if ($word['numberOccurrences'] > $numberOccurrences) {
                    $numberOccurrences = $word['numberOccurrences'];
                }

                foreach ($word['occurrences'] as $key2 => $value) {
                    if (key_exists($key2, $occurrences)) {
                        $occurrences[$key2] += $value;
                    } else {
                        $occurrences[$key2] = $value;
                    }
                }
            }
            arsort($occurrences);

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
                'occurrences' => $occurrences,
            ];
        }

        $collection = collect($this->wordForms);

        $this->wordForms = $collection->sortBy(function ($key, $value) {
        }, SORT_REGULAR, true)->toArray();
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

        foreach ($this->sites as $key => $page) {
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
            $iterator = 0;

            foreach ($sites as $item) {

                $domain = parse_url($item);
                $domain = str_replace('www.', "", mb_strtolower($domain['host']));


                if (in_array($domain, $ignoredDomains)) {
                    $this->domains[] = [
                        'item' => $item,
                        'ignored' => true,
                    ];
                } else {
                    $this->domains[] = [
                        'item' => $item,
                        'ignored' => false,
                    ];
                    $iterator++;
                }

                if ($iterator == $count) {
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
    public function setSites($sites): Relevance
    {
        $this->params['sites'] = $sites;
        $this->sites = json_decode($sites, true);

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
            $this->domains[] = [
                'item' => $item['site'],
                'ignored' => $item['ignored'],
            ];
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

        $array = array_count_values(explode(' ', $text));
        arsort($array);
        $array = array_slice($array, 0, 199);

        $wordCount = count(explode(" ", $text));
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
                foreach ($this->sites as $key => $page) {
                    if (!$page['ignored']) {
                        $htmlCount = preg_match_all("/($phrase)/", ' ' . $page['html'] . ' ');
                        if ($htmlCount > 0) {
                            $numberTextOccurrences += $htmlCount;
                        }

                        $hiddenTextCount = preg_match_all("/($phrase)/", ' ' . $page['hiddenText'] . ' ');
                        if ($hiddenTextCount > 0) {
                            $numberTextOccurrences += $hiddenTextCount;
                        }

                        $linkTextCount = preg_match_all("/($phrase)/", ' ' . $page['linkText'] . ' ');
                        if ($linkTextCount > 0) {
                            $numberLinkOccurrences += $linkTextCount;
                        }

                        if ($linkTextCount > 0 || $hiddenTextCount > 0 || $htmlCount > 0) {
                            $countRepeat = $linkTextCount + $hiddenTextCount + $htmlCount;
                            $numberOccurrences++;
                            $occurrences[$key] = $countRepeat;
                            if ($reSpam < $countRepeat) {
                                $reSpam = $countRepeat;
                            }
                        }
                    }
                }
                if ($numberOccurrences > 0) {
                    $countOccurrences = $numberTextOccurrences + $numberLinkOccurrences;
                    $tf = round($countOccurrences / $totalCount, 6);
                    $idf = round(log10($totalCount / $countOccurrences), 6);

                    $repeatInTextMainPage = mb_substr_count(TestRelevance::concatenation([$this->mainPage['html'], $this->mainPage['hiddenText']]), "$phrase");
                    $repeatLinkInMainPage = mb_substr_count($this->mainPage['linkText'], "$phrase");
                    $countSites = count($this->sites);
                    arsort($occurrences);

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
        foreach ($this->sites as $keyPage => $page) {
            $allText = TestRelevance::concatenation([$page['html'], $page['linkText'], $page['hiddenText']]);

            $this->sites[$keyPage]['density'] = $this->calculateDensityPoints($allText);
        }
    }

    /**
     * @param $text
     * @return array
     */
    public function calculateDensityPoints($text): array
    {
        $result = [];
        $array = explode(' ', $text);
        $array = array_count_values($array);
        $densityMain = 0;
        $testMainIterator = 0;
        foreach ($this->wordForms as $wordForm) {
            $countRepeatInPage = 0;
            foreach ($wordForm as $word => $form) {
                if ($word == 'total') {
                    continue;
                }
                if (array_key_exists($word, $array)) {
                    foreach ($wordForm as $w => $info) {
                        if ($w == 'total') {
                            continue;
                        }
                        $countRepeatInPage += $array[$w] ?? 0;
                    }
                    $points = min($countRepeatInPage / ($wordForm['total']['avgInTotalCompetitors'] / 100), 100);
                    $densityMain += $points;

                    break;
                }
            }
            $testMainIterator++;
        }

        $result['densityMain'] = round($densityMain);
        $result['densityMainPercent'] = round($densityMain / 600);
        return $result;
    }

    /**
     * @return void
     */
    public function saveResults()
    {
        //кодируем и сжимаем html, удаляем не нужную информацию для экономии ресурсов бд
        foreach ($this->sites as $key => $site) {
            if (isset($this->sites[$key]['defaultHtml'])) {
                $encode = base64_encode(gzcompress($this->sites[$key]['defaultHtml'], 9));
            } else {
                $encode = base64_encode(gzcompress($this->sites[$key]['html'], 9));
            }

            $this->sites[$key]['html'] = $encode;
            unset($this->sites[$key]['defaultHtml']);
            unset($this->sites[$key]['linkText']);
            unset($this->sites[$key]['hiddenText']);

        }
        $this->params['sites'] = json_encode($this->sites);
        $this->params->save();
    }

}
