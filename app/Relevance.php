<?php

namespace App;

use App\Classes\Xml\SimplifiedXmlFacade;
use App\Http\Controllers\TextLengthController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Relevance
{
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

    public $countSymbols;

    public $countSymbolsInMyPage;

    public $countWords;

    public $countWordsInMyPage;

    public $params;

    public $pages;

    public $sites;

    public $countNotIgnoredSites = 0;

    public $recommendations = [];

    public $phrase;

    public $queue;

    public $request;

    /**
     * @param $request
     * @param bool $queue
     */
    public function __construct($request, bool $queue = false)
    {
        $this->pages = [];
        $this->domains = [];
        $this->mainPage = [];
        $this->wordForms = [];
        $this->ignoredWords = [];
        $this->competitorsText = '';
        $this->competitorsLinks = '';
        $this->competitorsTextAndLinks = '';
        $this->countSymbols = 0;
        $this->countWords = 0;
        $this->queue = $queue;
        $this->request = $request;

        $this->maxWordLength = $request['separator'];
        $this->phrase = $request['phrase'] ?? '';


        if ($queue) {
            $params = [
                'user_id' => Auth::id(),
            ];
        } else {
            $params = [
                'user_id' => Auth::id(),
                'page_hash' => $request['pageHash']
            ];
        }

        $this->params = RelevanceAnalyseResults::firstOrNew($params);

        $this->params['main_page_link'] = $request['link'];
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
     * @param bool|array $xmlResponse
     * @return void
     */
    public function parseSites($xmlResponse = false)
    {
        $mainUrl = parse_url($this->params['main_page_link']);
        $host = Str::lower($mainUrl['host']);

        foreach ($this->domains as $item) {
            $domain = strtolower($item['item']);
            $result = TextAnalyzer::removeStylesAndScripts(TextAnalyzer::curlInit($domain));

            $this->sites[$domain]['danger'] = $result == '' || $result == null;
            $this->sites[$domain]['html'] = $result;
            $this->sites[$domain]['defaultHtml'] = $result;
            $this->sites[$domain]['site'] = $domain;
            $this->sites[$domain]['position'] = $item['position'];

            $compUrl = parse_url($domain);
            if ($host == Str::lower($compUrl['host'])) {
                $this->sites[$domain]['equallyHost'] = true;
            } else {
                $this->sites[$domain]['equallyHost'] = false;
            }

            if (Str::lower($domain) == Str::lower($this->params['main_page_link'])) {
                $this->mainPageIsRelevance = true;
                $this->sites[$domain]['mainPage'] = true;
                $this->sites[$domain]['inRelevance'] = $item['inRelevance'] ?? true;
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
            $this->sites[$this->params['main_page_link']]['defaultHtml'] = $this->mainPage['html'];
            $this->sites[$this->params['main_page_link']]['html'] = $this->mainPage['html'];
            if ($xmlResponse) {
                $this->sites[$this->params['main_page_link']]['position'] = array_search('https://almamed.su/category/laringoskopy/', $xmlResponse);
            } else {
                $this->sites[$this->params['main_page_link']]['position'] = count($this->domains) + 1;
            }
        }
    }

    /**
     * @param $userId
     * @param int|boolean $historyId
     * @return void
     */
    public function analysis($userId, $historyId = false)
    {
        try {
            $this->removeNoIndex();
            RelevanceProgress::editProgress(20, $this->request);
            $this->getHiddenData();
            $this->separateLinksFromText();
            $this->removePartsOfSpeech();
            RelevanceProgress::editProgress(50, $this->request);
            $this->removeListWords();
            $this->getTextFromCompetitors();
            RelevanceProgress::editProgress(70, $this->request);
            $this->separateAllText();
            $this->preparePhrasesTable();
            $this->searchWordForms();
            RelevanceProgress::editProgress(80, $this->request);
            $this->processingOfGeneralInformation();
            $this->prepareUnigramTable();
            $this->analyzeRecommendations();
            $this->prepareAnalysedSitesTable();
            RelevanceProgress::editProgress(90, $this->request);
            $this->prepareClouds();
            $this->saveHistory($userId, $historyId);
        } catch (\Throwable $e) {
            $this->saveError();
            Log::debug('Relevance Error', [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);
        }
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
     * @return void
     */
    public function removeNoIndex()
    {
        if ($this->request['noIndex'] == 'false') {
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
        $this->mainPage['html'] = TextAnalyzer::deleteEverythingExceptCharacters(Relevance::clearHTMLFromLinks($this->mainPage['html']));
        foreach ($this->sites as $key => $page) {
            $this->sites[$key]['linkText'] = TextAnalyzer::getLinkText($this->sites[$key]['html']);
            $this->sites[$key]['html'] = TextAnalyzer::deleteEverythingExceptCharacters(Relevance::clearHTMLFromLinks($this->sites[$key]['html']));
        }
    }

    /**
     * @return void
     */
    public function getHiddenData()
    {
        if ($this->request['hiddenText'] == 'true') {
            $this->mainPage['hiddenText'] = Relevance::getHiddenText($this->mainPage['html']);
            foreach ($this->sites as $key => $page) {
                $this->sites[$key]['hiddenText'] = Relevance::getHiddenText($this->sites[$key]['html']);
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
        }
    }

    /**
     * @return void
     */
    public function calculateTextInfo()
    {
        foreach ($this->sites as $key => $site) {
            $this->countNotIgnoredSites++;
            $countSymbols = Str::length($site['html']) + Str::length($site['linkText']) + Str::length($site['hiddenText']);
            $countWords = TextLengthController::countingWord($site['html'] . ' ' . $site['linkText'] . ' ' . $site['hiddenText']);

            if ($this->sites[$key]['mainPage']) {
                $this->countSymbolsInMyPage = $countSymbols;
                $this->countWordsInMyPage = $countWords;
            }

            if (!$site['ignored']) {
                $this->countSymbols += $countSymbols;
                $this->countWords += $countWords;
            }

            $this->sites[$key]['countSymbols'] = max($countSymbols, 0);
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
     * @return void
     */
    public function prepareAnalysedSitesTable()
    {
        $this->calculateDensity();
        $this->calculateCoveragePoints();
        $this->calculateWidthPoints();
        $this->calculateTotalPoints();
        $this->calculateTextInfo();
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
     * @return void
     */
    public function removePartsOfSpeech()
    {
        if ($this->request['conjunctionsPrepositionsPronouns'] == 'false') {
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
    public function removeListWords()
    {
        if ($this->request['switchMyListWords'] == 'true') {
            $listWords = str_replace(["\r\n", "\n\r"], "\n", $this->request['listWords']);
            $this->ignoredWords = explode("\n", $listWords);
            $this->mainPage['html'] = Relevance::mbStrReplace($this->ignoredWords, '', $this->mainPage['html']);
            $this->mainPage['linkText'] = Relevance::mbStrReplace($this->ignoredWords, '', $this->mainPage['linkText']);
            $this->mainPage['hiddenText'] = Relevance::mbStrReplace($this->ignoredWords, '', $this->mainPage['hiddenText']);
            foreach ($this->sites as $key => $page) {
                $this->sites[$key]['html'] = Relevance::mbStrReplace($this->ignoredWords, '', $this->sites[$key]['html']);
                $this->sites[$key]['linkText'] = Relevance::mbStrReplace($this->ignoredWords, '', $this->sites[$key]['linkText']);
                $this->sites[$key]['hiddenText'] = Relevance::mbStrReplace($this->ignoredWords, '', $this->sites[$key]['hiddenText']);
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

        foreach ($this->sites as $key => $page) {
            $this->tfCompClouds[$key] = $this->prepareTfCloud($this->separateText($page['html'] . ' ' . $page['linkText']));
        }
    }

    /**
     * @param $request
     * @param $sites
     * @param $exp
     * @return void
     */
    public function removeIgnoredDomains($request, $sites, $exp)
    {
        $ignoredDomains = str_replace("\r\n", "\n", $request['ignoredDomains']);
        $ignoredDomains = explode("\n", $ignoredDomains);
        $ignoredDomains = array_map("mb_strtolower", $ignoredDomains);
        $iterator = 0;

        foreach ($sites as $key => $item) {
            $domain = parse_url($item);
            $domain = str_replace('www.', "", mb_strtolower($domain['host']));

            if ($iterator < $request['count']) {
                $this->domains[$key] = [
                    'item' => $item,
                    'position' => $key + 1,
                ];

                if (in_array($domain, $ignoredDomains)) {
                    $this->domains[$key]['ignored'] = true;
                } else {
                    $this->domains[$key]['ignored'] = false;
                    $iterator++;
                }

            } else {
                if ($exp && $key < 50) {
                    $this->domains[$key] = [
                        'exp' => true,
                        'ignored' => true,
                        'item' => $item,
                        'position' => $key + 1,
                    ];
                } else {
                    break;
                }
            }
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
     * @return void
     */
    public function setSites($sites)
    {
        $this->params['sites'] = $sites;

        foreach (json_decode($sites, true) as $key => $site) {
            $this->sites[$key] = [
                'danger' => $site['danger'],
                'html' => gzuncompress(base64_decode($site['defaultHtml'])),
                'defaultHtml' => gzuncompress(base64_decode($site['defaultHtml'])),
                'ignored' => $site['ignored'],
                'mainPage' => $site['mainPage'],
                'equallyHost' => $site['equallyHost'] ?? false,
                'site' => $key,
                'position' => $site['position'],
            ];
        }
    }

    /**
     * @param $domains
     * @return void
     */
    public function setDomains($domains)
    {
        $array = json_decode($domains, true);

        foreach ($array as $key => $item) {
            $this->domains[$key] = [
                'item' => $item['site'],
                'ignored' => $item['ignored'],
                'position' => $item['position'],
            ];

            if (isset($item['inRelevance']) && $item['inRelevance'] == false) {
                $this->domains[$key]['inRelevance'] = false;
            }
        }

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

                    $repeatInTextMainPage = mb_substr_count(Relevance::concatenation([$this->mainPage['html'], $this->mainPage['hiddenText']]), "$phrase");
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
            $allText = Relevance::concatenation([$page['html'], $page['linkText'], $page['hiddenText']]);

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
        if (!$this->queue) {
            $saveObject = [];
            //кодируем и сжимаем html, удаляем не нужную информацию для экономии ресурсов бд
            foreach ($this->sites as $key => $site) {
                if (!array_key_exists('exp', $this->sites[$key])) {
                    unset($this->sites[$key]['html']);
                    unset($this->sites[$key]['linkText']);
                    unset($this->sites[$key]['hiddenText']);
                    $this->sites[$key]['defaultHtml'] = base64_encode(gzcompress($this->sites[$key]['defaultHtml'], 9));

                    $saveObject[$key] = $this->sites[$key];
                }

            }

            $this->params['sites'] = json_encode($saveObject);
            $this->params['page_hash'] = $this->request['pageHash'];
            $this->params->save();
        }
    }

    /**
     * @param $userId
     * @param $historyId
     * @return void
     */
    public function saveHistory($userId, $historyId)
    {
        $this->saveResults();
        $this->saveStatistic();

        $time = Carbon::now()->toDateTimeString();
        $link = parse_url($this->params['main_page_link']);

        $mainHistory = ProjectRelevanceHistory::createOrUpdate($link['host'], $time, $userId);

        foreach ($this->sites as $site) {
            if ($site['mainPage']) {
                $site = [
                    'mainPoints' => $site['mainPoints'],
                    'coverage' => $site['coverage'],
                    'coverageTf' => $site['coverageTf'],
                    'width' => $site['width'],
                    'density' => $site['density']['densityMainPercent'],
                    'position' => $site['position']
                ];

                $id = RelevanceHistory::createOrUpdate(
                    $this->phrase,
                    $this->params['main_page_link'],
                    $this->request,
                    $site,
                    $time,
                    $mainHistory,
                    true,
                    $historyId
                );

                $info = ProjectRelevanceHistory::calculateInfo($mainHistory->stories);

                $mainHistory->total_points = $info['points'];
                $mainHistory->count_sites = $info['count'];
                $mainHistory->save();

                $this->saveHistoryResult($id);
                return;
            }
        }

    }

    /**
     * @param $id
     * @return void
     */
    public function saveHistoryResult($id)
    {
        $result = RelevanceHistoryResult::firstOrNew(['project_id' => $id]);

        $result->clouds_competitors = json_encode([
            'totalTf' => json_encode($this->competitorsCloud['totalTf']),
            'textTf' => json_encode($this->competitorsCloud['textTf']),
            'linkTf' => json_encode($this->competitorsCloud['linkTf']),

            'textAndLinks' => json_encode($this->competitorsTextAndLinksCloud),
            'links' => json_encode($this->competitorsLinksCloud),
            'text' => json_encode($this->competitorsTextCloud),
        ]);

        $result->clouds_main_page = json_encode([
            'totalTf' => json_encode($this->mainPage['totalTf']),
            'textTf' => json_encode($this->mainPage['textTf']),
            'linkTf' => json_encode($this->mainPage['linkTf']),
            'textWithLinks' => json_encode($this->mainPage['textWithLinks']),
            'links' => json_encode($this->mainPage['links']),
            'text' => json_encode($this->mainPage['text']),
        ]);

        $result->avg = json_encode([
            'countWords' => $this->countWords / $this->countNotIgnoredSites,
            'countSymbols' => $this->countSymbols / $this->countNotIgnoredSites,
        ]);

        $result->main_page = json_encode([
            'countWords' => $this->countWordsInMyPage,
            'countSymbols' => $this->countSymbolsInMyPage,
        ]);

        $result->unigram_table = json_encode($this->wordForms);
        $result->sites = json_encode($this->sites);
        $result->tf_comp_clouds = json_encode($this->tfCompClouds);
        $result->phrases = json_encode($this->phrases);
        $result->avg_coverage_percent = json_encode($this->avgCoveragePercent);
        $result->recommendations = json_encode($this->recommendations);

        $result->save();
    }

    /**
     * @param $request
     * @return void
     */
    public function analysisByPhrase($request)
    {
        RelevanceProgress::editProgress(10, $request);
        $xml = new SimplifiedXmlFacade($request['region']);
        $xml->setQuery($request['phrase']);
        $xmlResponse = $xml->getXMLResponse();

        $this->removeIgnoredDomains(
            $request,
            $xmlResponse,
            false
        );
        RelevanceProgress::editProgress(15, $request);

        $this->parseSites($xmlResponse);
    }

    /**
     * @param $request
     * @return void
     */
    public function analysisByList($request)
    {
        RelevanceProgress::editProgress(10, $request);
        $this->prepareDomains($request['siteList']);
        $this->parseSites();
    }

    /**
     * @param $siteList
     * @return void
     */
    public function prepareDomains($siteList)
    {
        $sitesList = str_replace("\r\n", "\n", $siteList);
        $sitesList = explode("\n", $sitesList);

        foreach ($sitesList as $item) {
            $this->domains[] = [
                'item' => str_replace('www.', '', mb_strtolower(trim($item))),
                'ignored' => false,
                'position' => count($this->domains) + 1
            ];
        }
    }

    /**
     * @return void
     */
    public function saveStatistic()
    {
        $toDay = RelevanceStatistics::firstOrNew(['date' => Carbon::now()->toDateString()]);
        if ($toDay->id) {
            $toDay->count_checks += 1;
        } else {
            $toDay->count_checks = 1;
        }
        $toDay->save();

        $page = RelevanceUniquePages::where('name', '=', $this->params['main_page_link'])
            ->first();
        if (empty($page)) {
            $page = new RelevanceAllUniquePages();
            $page->name = $this->params['main_page_link'];
            $page->save();
        }

        $url = parse_url($this->params['main_page_link']);
        $domain = RelevanceUniqueDomains::where('name', '=', $url['host'])->first();
        if (empty($domain)) {
            $domain = new RelevanceUniqueDomains();
            $domain->name = $url['host'];
            $domain->save();
        }

        foreach ($this->sites as $page => $item) {
            $url = parse_url($page);
            $uniquePage = RelevanceAllUniquePages::where('name', '=', $page)
                ->first();
            if (empty($uniquePage)) {
                $uniquePage = new RelevanceAllUniquePages();
                $uniquePage->name = $page;
                $uniquePage->save();
            }

            $uniqueDomain = RelevanceAllUniqueDomains::where('name', '=', $url['host'])
                ->first();
            if (empty($uniqueDomain)) {
                $uniqueDomain = new RelevanceAllUniqueDomains();
                $uniqueDomain->name = $page;
                $uniqueDomain->save();
            }
        }
    }

    /**
     * @return void
     */
    public function saveError()
    {
        $toDay = RelevanceStatistics::firstOrNew(['date' => Carbon::now()->toDateString()]);
        if ($toDay->id) {
            $toDay->count_fails += 1;
        } else {
            $toDay->count_fails = 1;
        }
        $toDay->save();
    }
}
