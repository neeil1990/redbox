<?php

namespace App;

use App\Classes\Xml\SimplifiedXmlFacade;
use App\Jobs\Relevance\RemoveRelevanceProgress;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Relevance
{
    public $countWords = 0;

    public $countSymbols = 0;

    public $countNotIgnoredSites = 0;

    public $mainPageIsRelevance = false;

    public $competitorsTextAndLinks = '';

    public $competitorsLinks = '';

    public $competitorsText = '';

    public $competitorsCloud = [];

    public $recommendations = [];

    public $ignoredWords = [];

    public $tfCompClouds = [];

    public $wordForms = [];

    public $mainPage = [];

    public $domains = [];

    public $pages = [];

    public $avg = [];

    public $competitorsTextAndLinksCloud;

    public $competitorsLinksCloud;

    public $competitorsTextCloud;

    public $countSymbolsInMyPage;

    public $countWordsInMyPage;

    public $avgCoveragePercent;

    public $maxWordLength;

    public $coverageInfo;

    public $phrases;

    public $request;

    public $phrase;

    public $params;

    public $sites;

    public $queue;

    public $userId;

    public $scanHash;

    public function __construct($request, $userId, bool $queue = false)
    {
        $this->queue = $queue;
        $this->request = $request;
        $this->userId = $userId;
        $this->scanHash = $request['hash'] ?? 'no hash';

        $this->maxWordLength = $request['separator'];
        $this->phrase = $request['phrase'] ?? '';
        $this->request['searchPassages'] = isset($this->request['searchPassages'])
            ? filter_var($this->request['searchPassages'], FILTER_VALIDATE_BOOLEAN)
            : false;

        $params = [
            'user_id' => $this->userId,
            'page_hash' => $this->queue ? null : $request['pageHash']
        ];

        $this->params = RelevanceAnalyseResults::firstOrNew($params);

        $this->params['main_page_link'] = $request['link'];
        $this->params['sites'] = '';
        $this->params['html_main_page'] = '';
    }

    public function getMainPageHtml()
    {
        $html = TextAnalyzer::removeStylesAndScripts(TextAnalyzer::curlInit($this->params['main_page_link']));
        $this->setMainPage($html);
    }

    public function parseSites($xmlResponse = false, $searchPosition = false)
    {
        $mainUrl = parse_url($this->params['main_page_link']);
        $host = Str::lower($mainUrl['host']);

        foreach ($this->domains as $item) {
            $domain = Str::lower($item['item']);
            $result = TextAnalyzer::removeStylesAndScripts(TextAnalyzer::curlInit($domain));

            $this->sites[$domain]['danger'] = $result == '' || $result == null;
            $this->sites[$domain]['html'] = $result;
            $this->sites[$domain]['defaultHtml'] = $result;
            $this->sites[$domain]['site'] = $domain;
            $this->sites[$domain]['position'] = $item['position'];

            $compUrl = parse_url($domain);

            $this->sites[$domain]['equallyHost'] = isset($compUrl['host']) && $host === $compUrl['host'];

            if ($domain === Str::lower($this->params['main_page_link']) ||
                $domain === Str::lower($this->params['main_page_link']) . '/' ||
                $domain . '/' === Str::lower($this->params['main_page_link'])) {
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
            if ($xmlResponse) {
                $position = array_search(Str::lower($this->params['main_page_link']), $xmlResponse);
                if ($position === false) {
                    $position = array_search(Str::lower($this->params['main_page_link'] . '/'), $xmlResponse);
                }
            } elseif ($searchPosition) {
                $position = SimplifiedXmlFacade::getPosition($this->request);
            } else {
                $position = count($this->domains) + 1;
            }

            $this->sites[$this->params['main_page_link']] = [
                'inRelevance' => false,
                'danger' => $this->mainPage['html'] === '',
                'ignored' => false,
                'mainPage' => true,
                'defaultHtml' => $this->mainPage['html'],
                'html' => $this->mainPage['html'],
                'site' => $this->params['main_page_link'],
                'position' => $position
            ];
        }
    }

    public function analysis($historyId = false)
    {
        try {
            $this->removeNoIndex();
            Log::debug($this->scanHash, ['removeNoIndex']);
            $this->getHiddenData();
            Log::debug($this->scanHash, ['getHiddenData']);
            $this->separateLinksFromText();
            Log::debug($this->scanHash, ['separateLinksFromText']);
            $this->removePartsOfSpeech();
            Log::debug($this->scanHash, ['removePartsOfSpeech']);
            $this->removeListWords();
            Log::debug($this->scanHash, ['removeListWords']);
            $this->getTextFromCompetitors();
            Log::debug($this->scanHash, ['getTextFromCompetitors']);
            $this->separateAllText();
            Log::debug($this->scanHash, ['separateAllText']);
            $this->preparePhrasesTable();
            Log::debug($this->scanHash, ['preparePhrasesTable']);
            $this->searchWordForms();
            Log::debug($this->scanHash, ['searchWordForms']);
            $this->processingOfGeneralInformation();
            Log::debug($this->scanHash, ['processingOfGeneralInformation']);
            $this->prepareUnigramTable();
            Log::debug($this->scanHash, ['prepareUnigramTable']);
            $this->analyseRecommendations();
            Log::debug($this->scanHash, ['analyseRecommendations']);
            $this->prepareAnalysedSitesTable();
            Log::debug($this->scanHash, ['prepareAnalysedSitesTable']);
            $this->prepareClouds();
            Log::debug($this->scanHash, ['prepareClouds']);
            $this->saveHistory($historyId);

            UsersJobs::where('user_id', '=', $this->params['user_id'])->decrement('count_jobs');

            RemoveRelevanceProgress::dispatch($this->scanHash)
                ->onQueue('default')
                ->delay(now()->addSeconds(100));

        } catch (\Throwable $exception) {
            $this->saveError($exception);

            if ($historyId !== false) {
                RelevanceHistory::where('id', '=', $historyId)->update([
                    'state' => '-1'
                ]);
            }
        }
    }

    /**
     * Удалить текст, который помечен <noindex>
     * @return void
     */
    public function removeNoIndex()
    {
        Log::info(1123);
        RelevanceProgress::editProgress(20, $this->request);

        if (isset($this->request['noIndex']) && $this->request['noIndex'] == 'false') {
            $this->mainPage['html'] = TextAnalyzer::removeNoindexText($this->mainPage['html']);
            foreach ($this->sites as $key => $page) {
                Log::info($key);
                $this->sites[$key]['html'] = TextAnalyzer::removeNoindexText($page['html']);
            }
        }
    }

    public function separateAllText()
    {
        $this->competitorsLinks = $this->separateText($this->competitorsLinks);
        $this->competitorsText = $this->separateText($this->competitorsText);
        $this->mainPage['html'] = $this->separateText($this->mainPage['html']);
        $this->mainPage['linkText'] = $this->separateText($this->mainPage['linkText']);
        $this->mainPage['hiddenText'] = $this->separateText($this->mainPage['hiddenText']);
        $this->competitorsTextAndLinks = ' ' . $this->competitorsLinks . ' ' . $this->competitorsText . ' ';
    }

    public function separateLinksFromText()
    {
        foreach ($this->sites as $key => $page) {
            $this->sites[$key]['linkText'] = TextAnalyzer::getLinkText($this->sites[$key]['html']);
            $this->sites[$key]['html'] = TextAnalyzer::deleteEverythingExceptCharacters(TextAnalyzer::clearHTMLFromLinks($this->sites[$key]['html']));

            if ($this->request['searchPassages']) {

                $this->sites[$key]['passages'] = Relevance::searchPassages($this->sites[$key]['defaultHtml']);

                $passagesArray = explode(' ', $this->sites[$key]['passages']);
                $html = ' ' . $this->sites[$key]['html'] . ' ';
                foreach ($passagesArray as $item) {
                    $search = " $item ";
                    $pos = strpos($html, $search);
                    if ($pos !== false) {
                        $html = substr_replace($html, " ", $pos, strlen($search));
                    }
                }

                $this->sites[$key]['html'] = trim($html);

            } else {
                $this->sites[$key]['passages'] = '';
            }

            if ($this->sites[$key]['mainPage']) {
                $this->mainPage['linkText'] = $this->sites[$key]['linkText'];
                $this->mainPage['html'] = $this->sites[$key]['html'];
                $this->mainPage['passages'] = $this->sites[$key]['passages'];
            }
        }
    }

    public static function searchPassages($html): string
    {
        $passages = '';
        preg_match_all('(<li.*?>(.*?)</li>)', $html, $li, PREG_SET_ORDER);

        foreach ($li as $item) {
            $ul = str_replace('>', '> ', $item[1]);
            $ul = TextAnalyzer::clearHTMLFromLinks($ul);

            $text = trim(strip_tags($ul));
            $text = preg_replace('| +|', ' ', $text);
            $text = trim(TextAnalyzer::deleteEverythingExceptCharacters($text));
            if (mb_strlen($text) < 200 && $text != "") {
                $passages .= ' ' . $text;
            }
        }

        return trim($passages);
    }

    public function getHiddenData()
    {
        if (isset($this->request['hiddenText']) && $this->request['hiddenText'] == 'true') {
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

    public function getTextFromCompetitors()
    {
        RelevanceProgress::editProgress(40, $this->request);
        foreach ($this->sites as $key => $page) {
            if (!$this->sites[$key]['ignored']) {
                $this->competitorsLinks .= ' ' . $this->sites[$key]['linkText'] . ' ';
                $this->competitorsText .= ' ' . $this->sites[$key]['hiddenText'] . ' ' . $this->sites[$key]['html'] . ' ';
            }

            $this->sites[$key]['coverage'] = 0;
            $this->sites[$key]['coverageTf'] = 0;
        }
    }

    public function calculateCoveragePoints()
    {
        $totalTf = 0;
        foreach ($this->wordForms as $wordForm) {
            $totalTf += $wordForm['total']['tf'];
        }

        foreach ($this->sites as $pageKey => $page) {
            $object = $page['html'] . ' ' . $page['linkText'] . ' ' . $page['hiddenText'];
            $coverage = $this->calculateCoverage($object);

            $this->sites[$pageKey]['coverage'] = round($coverage['text'] / 10, 2);
            $this->sites[$pageKey]['coverageTf'] = round($coverage['tf'] / ($totalTf / 100), 2);
        }
    }

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

    public function calculateWidthPoints()
    {
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

    public function calculateTotalPoints()
    {
        foreach ($this->sites as $key => $site) {
            $points = $site['coverage'] + $site['coverageTf'] + $site['density']['densityMainPercent'];
            $this->sites[$key]['mainPoints'] = min(round(($points / 3) * 2, 2), 100);
        }
    }

    public function calculateTextInfo()
    {
        foreach ($this->sites as $key => $site) {
            $totalWords = TextAnalyzer::deleteEverythingExceptCharacters($site['defaultHtml']);
            $countSymbols = Str::length($totalWords);
            $countWords = count(explode(' ', $totalWords));

            if ($this->sites[$key]['mainPage']) {
                $this->countSymbolsInMyPage = $countSymbols;
                $this->countWordsInMyPage = $countWords;
            } else if (!$site['ignored']) {
                $this->countNotIgnoredSites++;
                $this->countSymbols += $countSymbols;
                $this->countWords += $countWords;
            }

            $this->sites[$key]['countSymbols'] = max($countSymbols, 0);
        }
    }

    public function analyseRecommendations()
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

    public function prepareAnalysedSitesTable()
    {
        $this->calculateDensity();
        $this->calculateCoveragePoints();
        $this->calculateWidthPoints();
        $this->calculateTotalPoints();
        $this->calculateTextInfo();
        $this->calculateAvg();
    }

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

    public function removeListWords()
    {
        if (filter_var($this->request['switchMyListWords'], FILTER_VALIDATE_BOOLEAN)) {
            $listWords = str_replace(["\r\n", "\n\r"], "\n", $this->request['listWords']);
            $this->ignoredWords = explode("\n", $listWords);

            foreach ($this->ignoredWords as $key => $word) {
                $this->ignoredWords[$key] = " $word ";
            }

            foreach ($this->sites as $key => $page) {
                $this->sites[$key]['html'] = Relevance::mbStrReplace($this->ignoredWords, ' ', $this->sites[$key]['html']);
                $this->sites[$key]['linkText'] = Relevance::mbStrReplace($this->ignoredWords, ' ', $this->sites[$key]['linkText']);
                $this->sites[$key]['hiddenText'] = Relevance::mbStrReplace($this->ignoredWords, ' ', $this->sites[$key]['hiddenText']);

                if ($this->sites[$key]['mainPage']) {
                    $this->mainPage['html'] = $this->sites[$key]['html'];
                    $this->mainPage['linkText'] = $this->sites[$key]['linkText'];
                    $this->mainPage['hiddenText'] = $this->sites[$key]['hiddenText'];
                }
            }
        }
    }

    public static function mbStrReplace($search, $replace, $string): string
    {
        $charset = mb_detect_encoding($string);

        $unicodeString = iconv($charset, "UTF-8", $string);

        return preg_replace('| +|', ' ', str_replace($search, $replace, $unicodeString));
    }

    public function searchWordForms()
    {
        $m = new Morphy();
        $wordWorms = [];

        $array = explode(' ', $this->competitorsTextAndLinks);
        $array = array_count_values($array);
        arsort($array);

        foreach ($array as $key => $item) {
            if (!in_array($key, $this->ignoredWords)) {
                $this->ignoredWords[] = $key;

                $root = $m->base($key);
                if (empty($root)) {
                    $root = $key;
                }

                $wordWorms[$root][$key] = $item;

                if (count($wordWorms) >= 3500) {
                    break;
                }
            }
        }

        foreach ($wordWorms as $wordWorm) {
            $this->wordForms[array_key_first($wordWorm)] = $wordWorm;
        }

        uasort($this->wordForms, function ($l, $r) {
            $first = array_sum($r);
            $second = array_sum($l);

            if ($first == $second) return 0;
            return ($first < $second) ? -1 : 1;
        });


        $this->wordForms = array_slice($this->wordForms, 0, 1000);
    }

    public function processingOfGeneralInformation()
    {
        RelevanceProgress::editProgress(80, $this->request);
        $countSites = 0;
        foreach ($this->sites as $site) {
            if (!$site['ignored']) {
                $countSites++;
            }
        }

        $myText = $this->mainPage['html'] . ' ' . $this->mainPage['hiddenText'];
        $myText = explode(" ", $myText);
        $myText = array_count_values($myText);

        $myLink = explode(" ", $this->mainPage['linkText']);
        $myLink = array_count_values($myLink);

        $myPassages = explode(" ", $this->mainPage['passages']);
        $myPassages = array_count_values($myPassages);

        $wordCount = count(explode(' ', $this->competitorsTextAndLinks));
        foreach ($this->wordForms as $root => $wordForm) {
            foreach ($wordForm as $word => $item) {
                $reSpam = $numberTextOccurrences = $numberLinkOccurrences = $numberOccurrences = $numberPassageOccurrences = 0;
                $occurrences = [];
                foreach ($this->sites as $key => $page) {
                    if (!$page['ignored']) {
                        $htmlCount = substr_count(' ' . $this->sites[$key]['html'] . ' ', " $word ");
                        if ($htmlCount > 0) {
                            $numberTextOccurrences += $htmlCount;
                        }

                        $hiddenTextCount = substr_count(' ' . $this->sites[$key]['hiddenText'] . ' ', " $word ");
                        if ($hiddenTextCount > 0) {
                            $numberTextOccurrences += $hiddenTextCount;
                        }

                        $linkTextCount = substr_count(' ' . $this->sites[$key]['linkText'] . ' ', " $word ");
                        if ($linkTextCount > 0) {
                            $numberLinkOccurrences += $linkTextCount;
                        }

                        $passagesCount = substr_count(' ' . $this->sites[$key]['passages'] . ' ', " $word ");
                        if ($passagesCount > 0) {
                            $numberPassageOccurrences += $passagesCount;
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
                $repeatInPassagesMainPage = $myPassages[$word] ?? 0;

                $tf = round($item / $wordCount, 7);
                $idf = round(log10($wordCount / $item), 7);

                $this->wordForms[$root][$word] = [
                    'tf' => $tf,
                    'idf' => $idf,
                    'numberOccurrences' => $numberOccurrences,
                    'reSpam' => $reSpam,
                    'avgInTotalCompetitors' => (int)ceil(($numberLinkOccurrences + $numberTextOccurrences) / $countSites),
                    'avgInLink' => (int)ceil($numberLinkOccurrences / $countSites),
                    'avgInText' => (int)ceil($numberTextOccurrences / $countSites),
                    'avgInPassages' => (int)ceil($numberPassageOccurrences / $countSites),
                    'repeatInLinkMainPage' => $repeatLinkInMainPage,
                    'repeatInTextMainPage' => $repeatInTextMainPage,
                    'repeatInPassagesMainPage' => $repeatInPassagesMainPage,
                    'totalRepeatMainPage' => $repeatLinkInMainPage + $repeatInTextMainPage + $repeatInPassagesMainPage,
                    'occurrences' => $occurrences,
                ];
            }
        }
    }

    public function prepareUnigramTable()
    {
        $this->coverageInfo['sum'] = 0;

        foreach ($this->wordForms as $key => $wordForm) {
            $tf = $idf = $reSpam = $repeatInPassages = $repeatInText = $repeatInLink = $avgInText = $avgInPassages = 0;
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

                $avgInPassages += $word['avgInPassages'];
                $repeatInPassages += $word['repeatInPassagesMainPage'];

                if ($reSpam < $word['reSpam']) {
                    $reSpam = $word['reSpam'];
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
                'numberOccurrences' => count($occurrences),
                'reSpam' => $reSpam,
                'danger' => $danger,
                'occurrences' => $occurrences,
            ];

            if ($this->request['searchPassages']) {
                $this->wordForms[$key]['total']['avgInPassages'] = $avgInPassages;
                $this->wordForms[$key]['total']['repeatInPassagesMainPage'] = $repeatInPassages;
            }
        }

        $collection = collect($this->wordForms);

        $this->wordForms = $collection->sortBy(
            function ($key, $value) {
            },
            SORT_REGULAR,
            true
        )->toArray();
    }

    public function prepareClouds()
    {
        RelevanceProgress::editProgress(90, $this->request);
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

    public function removeIgnoredDomains($request, $sites, $exp)
    {
        $ignoredDomains = str_replace("\r\n", "\n", $request['ignoredDomains']);
        $ignoredDomains = explode("\n", $ignoredDomains);
        $ignoredDomains = array_map("mb_strtolower", $ignoredDomains);
        $iterator = 0;

        foreach ($sites as $key => $item) {
            Log::info($item);
            Log::info(str_contains($item, '.pdf'));

            if (str_contains($item, '.pdf')) {
                continue;
            }
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
                if (filter_var($exp, FILTER_VALIDATE_BOOLEAN) && $key < 50) {
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

    public function setMainPage($html)
    {
        $this->mainPage['html'] = $html;
        $this->params['html_main_page'] = $html;
    }

    public function setSites($sites)
    {
        $mainPageInRelevance = false;
        $this->params['sites'] = $sites;

        foreach (json_decode($sites, true) as $key => $site) {
            if (isset($this->sites[$key]['mainPage']) && $this->sites[$key]['mainPage']) {
                $this->sites[$key] = [
                    'danger' => false,
                    'html' => $this->mainPage['html'],
                    'defaultHtml' => $this->mainPage['html'],
                    'ignored' => false,
                    'mainPage' => true,
                    'equallyHost' => false,
                    'site' => $key,
                    'position' => $site['position'],
                ];

                $mainPageInRelevance = true;
            } else {
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

            if (!$mainPageInRelevance) {
                $this->sites[$this->params['main_page_link']] = [
                    'danger' => false,
                    'html' => $this->mainPage['html'],
                    'defaultHtml' => $this->mainPage['html'],
                    'ignored' => false,
                    'mainPage' => true,
                    'equallyHost' => false,
                    'site' => $this->params['main_page_link'],
                    'position' => 0,
                ];
            }
        }
    }

    public function setDomains($domains)
    {
        $array = json_decode($domains, true);

        foreach ($array as $key => $item) {
            $this->domains[$key] = [
                'item' => $item['site'],
                'ignored' => $item['ignored'],
                'position' => $item['position'],
            ];

            if (isset($item['inRelevance']) && !$item['inRelevance']) {
                $this->domains[$key]['inRelevance'] = false;
            }
        }

    }

    public static function concatenation(array $array): string
    {
        return implode(' ', $array);
    }

    public function prepareTfCloud($text): array
    {
        $wordForms = $cloud = [];
        $m = new Morphy();

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
                    preg_match("/[А-я]/", $item1['text']) &&
                    $m->base($item1['text']) == $m->base($item2['text']) ||
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

    public function preparePhrasesTable()
    {
        $result = [];
        $phrases = $this->searchPhrases();
        $totalCount = count($phrases);
        foreach ($phrases as $phrase) {

            if ($phrase === "") {
                continue;
            }

            $reSpam = $numberTextOccurrences = $numberLinkOccurrences = $numberOccurrences = 0;
            $occurrences = [];

            foreach ($this->sites as $key => $page) {
                if (!$page['ignored']) {
                    $htmlCount = preg_match_all("/ ($phrase) /", ' ' . $page['html'] . ' ');
                    if ($htmlCount > 0) {
                        $numberTextOccurrences += $htmlCount;
                    }

                    $hiddenTextCount = preg_match_all("/ ($phrase) /", ' ' . $page['hiddenText'] . ' ');
                    if ($hiddenTextCount > 0) {
                        $numberTextOccurrences += $hiddenTextCount;
                    }

                    $linkTextCount = preg_match_all("/ ($phrase) /", ' ' . $page['linkText'] . ' ');
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

        $collection = collect($result);
        $collection = $collection->unique();
        $collection = $collection->sortByDesc('tf');
        $this->phrases = $collection->slice(0, 600)->toArray();
    }

    public function searchPhrases(): array
    {
        $phrases = [];
        $array = explode(' ', $this->competitorsTextAndLinks);

        $grouped = array_chunk($array, 2);
        foreach ($grouped as $two_words) {
            $phrases[] = implode(' ', $two_words);
        }

        return array_unique($phrases);
    }

    public function calculateDensity()
    {
        foreach ($this->sites as $keyPage => $page) {
            $allText = Relevance::concatenation([$page['html'], $page['linkText'], $page['hiddenText']]);

            $this->sites[$keyPage]['density'] = $this->calculateDensityPoints($allText);
        }
    }

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

        $result['densityMain'] = min(round($densityMain), 100);
        $result['densityMainPercent'] = round($densityMain / 1000);

        return $result;
    }

    public function saveResults()
    {
        $saveObject = [];
        foreach ($this->sites as $key => $site) {
            if (!array_key_exists('exp', $this->sites[$key])) {
                unset($this->sites[$key]['html']);
                unset($this->sites[$key]['linkText']);
                unset($this->sites[$key]['hiddenText']);
                $this->sites[$key]['defaultHtml'] = base64_encode(gzcompress($this->sites[$key]['defaultHtml'], 9));

                $saveObject[$key] = $this->sites[$key];
            }
        }

        if (!$this->queue) {
            $this->params['sites'] = json_encode($saveObject);
            $this->params->save();
        }
    }

    public function saveHistory($historyId)
    {
        RelevanceProgress::editProgress(100, $this->request);
        $this->saveResults();
        $this->saveStatistic();

        $time = Carbon::now()->toDateTimeString();
        $link = parse_url($this->params['main_page_link']);

        $main = ProjectRelevanceHistory::createOrUpdate($link['host'], $time, $this->userId);

        foreach ($this->sites as $site) {
            if ($site['mainPage']) {
                $stat = [
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
                    $stat,
                    $time,
                    $main,
                    true,
                    $historyId,
                    base64_encode(gzcompress($this->params['html_main_page'], 9)),
                    json_encode($this->sites)
                );

                RelevanceHistory::where('user_id', '=', $this->userId)
                    ->where('phrase', '=', $this->request['phrase'])
                    ->where('main_link', '=', $this->request['link'])
                    ->where('position', '=', 0)
                    ->where('points', '=', 0)
                    ->where('coverage', '=', 0)
                    ->where('density', '=', 0)
                    ->where('html_main_page', '=', '')
                    ->delete();

                ProjectRelevanceHistory::calculateInfo($main);

                $this->saveHistoryResult($id);
            }
        }
    }

    public function saveHistoryResult($id)
    {
        $result = RelevanceHistoryResult::firstOrNew(['project_id' => $id]);

        $result->clouds_competitors = base64_encode(gzcompress(json_encode([
            'totalTf' => json_encode($this->competitorsCloud['totalTf']),
            'textTf' => json_encode($this->competitorsCloud['textTf']),
            'linkTf' => json_encode($this->competitorsCloud['linkTf']),

            'textAndLinks' => json_encode($this->competitorsTextAndLinksCloud),
            'links' => json_encode($this->competitorsLinksCloud),
            'text' => json_encode($this->competitorsTextCloud),
        ]), 9));


        $result->clouds_main_page = base64_encode(gzcompress(json_encode([
            'totalTf' => json_encode($this->mainPage['totalTf']),
            'textTf' => json_encode($this->mainPage['textTf']),
            'linkTf' => json_encode($this->mainPage['linkTf']),
            'textWithLinks' => json_encode($this->mainPage['textWithLinks']),
            'links' => json_encode($this->mainPage['links']),
            'text' => json_encode($this->mainPage['text']),
        ]), 9));

        $result->avg = base64_encode(gzcompress(json_encode([
            'countWords' => $this->countWords / $this->countNotIgnoredSites,
            'countSymbols' => $this->countSymbols / $this->countNotIgnoredSites,
        ]), 9));

        $result->main_page = base64_encode(gzcompress(json_encode([
            'countWords' => $this->countWordsInMyPage,
            'countSymbols' => $this->countSymbolsInMyPage,
        ]), 9));

        $result->average_values = json_encode($this->avg);
        $result->unigram_table = base64_encode(gzcompress(json_encode($this->wordForms), 9));
        $result->sites = base64_encode(gzcompress(json_encode($this->sites), 9));
        $result->tf_comp_clouds = base64_encode(gzcompress(json_encode($this->tfCompClouds), 9));
        $result->phrases = base64_encode(gzcompress(json_encode($this->phrases), 9));
        $result->avg_coverage_percent = base64_encode(gzcompress(json_encode($this->avgCoveragePercent), 9));
        $result->recommendations = base64_encode(gzcompress(json_encode($this->recommendations), 9));
        $result->hash = $this->scanHash;

        $result->compressed = true;
        $result->save();
    }

    public function analysisByPhrase($request, $exp)
    {
        try {
            RelevanceProgress::editProgress(10, $request);
            $xml = new SimplifiedXmlFacade($request['region']);
            $xml->setQuery($request['phrase']);
            $xmlResponse = $xml->getXMLResponse();

            $this->removeIgnoredDomains($request, $xmlResponse, $exp);
            $this->parseSites($xmlResponse);
        } catch (\Throwable $exception) {
            $this->saveError($exception);
        }
    }

    public function analysisByList($request)
    {
        RelevanceProgress::editProgress(10, $request);
        $this->prepareDomains($request['siteList']);
        $this->parseSites(false, true);
    }

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

    public function saveStatistic()
    {
        $toDay = RelevanceStatistics::firstOrNew(['date' => Carbon::now()->toDateString()]);
        if ($toDay->id) {
            $toDay->count_checks += 1;
        } else {
            $toDay->count_checks = 1;
        }
        $toDay->save();

        RelevanceUniquePages::firstOrCreate(['name' => Str::lower($this->params['main_page_link'])]);

        $mainUrl = parse_url($this->params['main_page_link']);
        RelevanceUniqueDomains::firstOrCreate(['name' => Str::lower($mainUrl['host'])]);

        foreach ($this->sites as $url => $item) {
            RelevanceAllUniquePages::firstOrCreate(['name' => Str::lower($url)]);

            $link = parse_url($url);
            RelevanceAllUniqueDomains::firstOrCreate(['name' => Str::lower($link['host'])]);
        }
    }

    public function calculateAvg()
    {
        $coverage = $coverageTf = $density = $width = $points = $countSymbols = [];
        foreach ($this->sites as $site) {
            $coverage[] = $site['coverage'];
            $coverageTf[] = $site['coverageTf'];
            $density[] = $site['density']['densityMainPercent'];
            $width[] = $site['width'];
            $points[] = $site['mainPoints'];
            $countSymbols[] = $site['countSymbols'];
        }

        rsort($coverage);
        rsort($coverageTf);
        rsort($density);
        rsort($width);
        rsort($points);
        rsort($countSymbols);

        for ($i = 0; $i <= 4; $i++) {
            $this->calculate('coverage', $coverage[$i] / 5);
            $this->calculate('coverageTf', $coverageTf[$i] / 5);
            $this->calculate('densityPercent', $density[$i] / 5);
            $this->calculate('width', $width[$i] / 5);
            $this->calculate('points', $points[$i] / 5);
            $this->calculate('countSymbols', $countSymbols[$i] / 5);
        }
    }

    public function calculate($key, $elem)
    {
        if (isset($this->avg[$key])) {
            $this->avg[$key] += $elem;
        } else {
            $this->avg[$key] = $elem;
        }
    }

    public function saveError($exception)
    {
        Log::debug('Relevance Error', [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'message' => $exception->getMessage(),
        ]);

        $toDay = RelevanceStatistics::firstOrNew(['date' => date('Y-m-d')]);
        $toDay->increment('count_fails');
        $toDay->save();

        UsersJobs::where('user_id', '=', $this->params['user_id'])->decrement('count_jobs');
        RelevanceProgress::where('hash', $this->scanHash)->update(['error' => 1]);
    }

    public static function uncompress($history)
    {
        if (isset($history)) {
            $history = json_decode($history, true);

            if (!$history['cleaning']) {
                $clouds_competitors = json_decode(gzuncompress(base64_decode($history['clouds_competitors'])), true);
                $clouds_main_page = json_decode(gzuncompress(base64_decode($history['clouds_main_page'])), true);
                $avg = json_decode(gzuncompress(base64_decode($history['avg'])), true);
                $main_page = json_decode(gzuncompress(base64_decode($history['main_page'])), true);

                $data = [
                    'clouds_competitors' => [
                        'totalTf' => json_decode($clouds_competitors['totalTf'], true),
                        'textTf' => json_decode($clouds_competitors['textTf'], true),
                        'linkTf' => json_decode($clouds_competitors['linkTf'], true),

                        'textAndLinks' => json_decode($clouds_competitors['textAndLinks'], true),
                        'links' => json_decode($clouds_competitors['links'], true),
                        'text' => json_decode($clouds_competitors['text'], true),
                    ],
                    'clouds_main_page' => [
                        'totalTf' => json_decode($clouds_main_page['totalTf'], true),
                        'textTf' => json_decode($clouds_main_page['textTf'], true),
                        'linkTf' => json_decode($clouds_main_page['linkTf'], true),
                        'textWithLinks' => json_decode($clouds_main_page['textWithLinks'], true),
                        'links' => json_decode($clouds_main_page['links'], true),
                        'text' => json_decode($clouds_main_page['text'], true),
                    ],
                    'avg' => [
                        'countWords' => json_decode($avg['countWords'], true),
                        'countSymbols' => json_decode($avg['countSymbols'], true),
                    ],
                    'main_page' => [
                        'countWords' => json_decode($main_page['countWords'], true),
                        'countSymbols' => json_decode($main_page['countSymbols'], true),
                    ],

                    'unigram_table' => json_decode(gzuncompress(base64_decode($history['unigram_table'])), true),
                    'history_id' => $history['id'],
                    'sites' => json_decode(gzuncompress(base64_decode($history['sites'])), true),
                    'tf_comp_clouds' => json_decode(gzuncompress(base64_decode($history['tf_comp_clouds'])), true),
                    'phrases' => json_decode(gzuncompress(base64_decode($history['phrases'])), true),
                    'avg_coverage_percent' => json_decode(gzuncompress(base64_decode($history['avg_coverage_percent'])), true),
                    'recommendations' => json_decode(gzuncompress(base64_decode($history['recommendations'])), true),
                    'cleaning' => false
                ];
            } else {
                $data = [
                    'sites' => json_decode(gzuncompress(base64_decode($history['sites'])), true),
                    'avg_coverage_percent' => json_decode(gzuncompress(base64_decode($history['avg_coverage_percent'])), true),
                    'cleaning' => true
                ];
            }

            $data['average_values'] = json_decode($history['average_values'], true);

            return $data;
        }
    }
}
