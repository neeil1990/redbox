<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Relevance
{
    public $mainPage;

    public $pages;

    public $competitorsText;

    public $competitorsTextCloud;

    public $competitorsLinks;

    public $competitorsLinksCloud;

    public $competitorsTextAndLinks;

    public $competitorsTextAndLinksCloud;

    public $wordForms;

    public $domains;

    public $sites;

    public $ignoredWords;

    public $params;

    public $separator = "\n\nseparator\n\n";

    public $competitorsTfCloud;

    public $mainPageTfCloud;

    public function __construct($request)
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
        $this->params['main_page_link'] = $request->link;
        $this->params['sites'] = '';
        $this->params['html_relevance'] = '';
        $this->params['html_main_page'] = '';
    }

    /**
     * @param $link
     * @return $this
     */
    public function getMainPageHtml($link): Relevance
    {
        $response = TextAnalyzer::curlInit($link);
        $html = TextAnalyzer::removeHeaders($response);
        $this->setMainPage($html);

        return $this;
    }

    /**
     * @param $link
     * @return $this
     */
    public function parseSites($link): Relevance
    {
        foreach ($this->domains as $item) {
            $domain = isset($item['doc']['url'])
                ? strtolower($item['doc']['url'])
                : $item;
            $result = mb_strtolower(TextAnalyzer::removeHeaders(
                TextAnalyzer::curlInit($domain)
            ));
            $this->pages[$domain]['html'] = $result;
            $this->params['html_relevance'] .= $result . $this->separator;
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
            $this->sites[array_key_last($this->sites)]['mainPage'] = $domain == $link;
        }

        $this->params['sites'] = json_encode($this->sites);

        return $this;
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
        $this->searchWordForms($request->separator);
        $this->processingOfGeneralInformation();
        $this->prepareClouds($request->separator);
        $this->prepareUnigramTable();
        $this->params->save();
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
        $this->competitorsTextAndLinks .= ' ' . $this->competitorsLinks . ' ' . $this->competitorsText . ' ';

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
     * Подготовка облаков (http://cavaliercoder.com/jclouds)
     * @return void
     */
    public function prepareClouds($separator)
    {
        $mainPageText = Relevance::concatenation([
            $this->mainPage['html'],
            $this->mainPage['hiddenText'],
            $this->mainPage['linkText']
        ]);
        $this->mainPageTfCloud = Relevance::prepareMainPageCloud($mainPageText, $separator);
        $this->competitorsTfCloud = Relevance::prepareTFCloud($separator);
        $this->mainPage['textCloud'] = TextAnalyzer::prepareCloud(
            Relevance::concatenation([
                $this->mainPage['html'],
                $this->mainPage['hiddenText']
            ]), $separator);
        $this->mainPage['textWithLinksCloud'] = TextAnalyzer::prepareCloud($mainPageText, $separator);
        $this->mainPage['linksCloud'] = TextAnalyzer::prepareCloud($this->mainPage['linkText'], $separator);
        $this->competitorsTextAndLinksCloud = TextAnalyzer::prepareCloud($this->competitorsTextAndLinks, $separator);
        $this->competitorsTextCloud = TextAnalyzer::prepareCloud($this->competitorsText, $separator);
        $this->competitorsLinksCloud = TextAnalyzer::prepareCloud($this->competitorsLinks, $separator);

    }

    /**
     * Обработка информации для таблицы unigram
     * @return void
     */
    public function processingOfGeneralInformation()
    {
        $countSites = count($this->sites);
        $wordCount = str_word_count($this->competitorsTextAndLinks);
        foreach ($this->wordForms as $root => $wordForm) {
            foreach ($wordForm as $word => $item) {
                $reSpam = 0;
                $numberTextOccurrences = 0;
                $numberLinkOccurrences = 0;
                $numberOccurrences = 0;
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
                    'totalRepeatMainPage' => $repeatLinkInMainPage + $repeatInTextMainPage
                ];
            }
        }
    }

    /**
     * @param $separator
     * @return void
     */
    public function searchWordForms($separator)
    {
        $this->getTextFromCompetitors();
        $array = explode(' ', $this->competitorsTextAndLinks);
        $stemmer = new LinguaStem();

        $array = array_count_values($array);
        asort($array);
        $array = array_reverse($array);

        // удаляем все слова в которых кол-во символов меньше $separator
        foreach ($array as $key => $item) {
            if (mb_strlen($key) <= $separator) {
                unset($array[$key]);
            }
        }

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
     * @return void
     */
    public function prepareUnigramTable()
    {
        foreach ($this->wordForms as $key => $wordForm) {
            $tf = 0;
            $idf = 0;
            $reSpam = 0;
            $occurrences = 0;
            $repeatInText = 0;
            $repeatInLink = 0;
            $avgInText = 0;
            $avgInLink = 0;
            $avgInTotalCompetitors = 0;
            $totalRepeatMainPage = 0;
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
                if ($word['numberOccurrences'] > $occurrences) {
                    $occurrences = $word['numberOccurrences'];
                }
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
                'numberOccurrences' => $occurrences,
                'reSpam' => $reSpam,
                'danger' => $danger,
            ];
        }
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
     * @return $this
     */
    public function setMainPage($html): Relevance
    {
        $this->mainPage['html'] = $html;
        $this->params['html_main_page'] = $html;

        return $this;
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
     * @param $separator
     * @return array
     */
    public function prepareTFCloud($separator): array
    {
        $cloud = [];
        $was = [];
        $tfCloud = [];

        foreach ($this->wordForms as $key => $wordForm) {
            $tfCloud[$key]['tf'] = 0;
            foreach ($wordForm as $item) {
                $tfCloud[$key]['tf'] += $item['tf'];
                if (count($tfCloud) >= 200) {
                    break 2;
                }
            }
        }

        foreach ($tfCloud as $key => $item) {
            if (mb_strlen($key) > $separator) {
                if (!in_array($key, $was) && $key != "") {
                    $cloud[] = [
                        'text' => $key,
                        'weight' => $item['tf'],
                        'html' => [
                            'title' => $item['tf']
                        ],
                    ];
                    $was[] = $key;
                }
            }
        }

        $cloud['count'] = count($cloud) - 1;
        $collection = collect($cloud);

        return $collection->sortByDesc('weight')->toArray();
    }

    /**
     * @param $mainPageText
     * @param $separator
     * @return array
     */
    public static function prepareMainPageCloud($mainPageText, $separator): array
    {
        $wordCount = str_word_count($mainPageText);
        $array = array_count_values(explode(' ', $mainPageText));
        $cloud = [];
        arsort($array);

        foreach ($array as $key => $item) {
            if (mb_strlen($key) > $separator) {
                $tf = round($item / $wordCount, 4);
                $cloud[] = [
                    'text' => $key,
                    'weight' => $tf,
                    'html' => [
                        'title' => $tf
                    ]
                ];
                if (count($cloud) > 200) {
                    break;
                }
            }
        }
        $cloud['count'] = count($cloud) - 1;
        $collection = collect($cloud);

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
}
