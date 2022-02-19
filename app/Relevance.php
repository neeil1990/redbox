<?php

namespace App;


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

    public function __construct()
    {
        $this->mainPage = [];
        $this->domains = [];
        $this->pages = [];
        $this->competitorsLinks = '';
        $this->competitorsText = '';
        $this->competitorsTextAndLinks = '';
    }

    /**
     * @param $link
     * @return $this
     */
    public function getMainPageHtml($link): Relevance
    {
        $this->mainPage['html'] = TextAnalyzer::curlInit($link);
        $this->mainPage['html'] = mb_strtolower(TextAnalyzer::removeHeaders($this->mainPage['html']));

        return $this;
    }

    /**
     * @return $this
     */
    public function parseXmlResponse(): Relevance
    {
        foreach ($this->domains as $item) {
            $result = mb_strtolower(TextAnalyzer::removeHeaders(
                TextAnalyzer::curlInit($item['doc']['url'])
            ));
            $this->pages[$item['doc']['url']]['html'] = $result;
            //for scaned table
            if ($result == "" || $result == null) {
                $this->sites[] = [
                    'site' => $item['doc']['url'],
                    'danger' => true,
                ];
            } else {
                $this->sites[] = [
                    'site' => $item['doc']['url'],
                    'danger' => false,
                ];
            }
        }

        return $this;
    }

    public function analyse($request)
    {
        $this->removeNoIndex($request);

        $this->getTextWithoutLinks();

        $this->getHiddenData($request);


        $this->removeConjunctionsPrepositionsPronouns($request);

        $this->removeListWords($request);

        //Вся информация с сайтов конкурентов с сайтов конкурентов
        foreach ($this->pages as $key => $page) {
            $this->competitorsLinks .= ' ' . $this->pages[$key]['linkText'] . ' ';
            $this->competitorsText .= ' ' . $this->pages[$key]['hiddenText'] . ' ' . $this->pages[$key]['html'] . ' ';
            $this->competitorsTextAndLinks .= ' ' . $this->pages[$key]['hiddenText'] . ' ' . $this->pages[$key]['html'] . ' ' . $this->pages[$key]['linkText'] . ' ';
        }

        Log::debug('compet',[
            $this->competitorsLinks,
            $this->competitorsText,
            $this->competitorsTextAndLinks,
        ]);
        $this->prepareClouds();

        $this->searchWordForms();

        $this->processingOfGeneralInformation($request->count);

        $this->prepareUnigramTable();
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

    public function removeNoIndex($request)
    {
        if ($request->noIndex == "false") {
            $this->mainPage['html'] = mb_strtolower(TextAnalyzer::removeNoindexText($this->mainPage['html']));
            foreach ($this->pages as $key => $page) {
                $this->pages[$key]['html'] = mb_strtolower(TextAnalyzer::removeNoindexText($page['html']));
            }
        }
    }

    public function getTextWithoutLinks()
    {
        $this->mainPage['linkText'] = TextAnalyzer::getLinkText($this->mainPage['html']);
        $this->mainPage['html'] = TextAnalyzer::clearHTMLFromLinks($this->mainPage['html']);
        $this->mainPage['hiddenText'] = '';
        foreach ($this->pages as $key => $page) {
            $this->pages[$key]['linkText'] = TextAnalyzer::getLinkText($page['html']);
            $this->pages[$key]['html'] = TextAnalyzer::clearHTMLFromLinks($page['html']);
            $this->pages[$key]['hiddenText'] = '';
        }
    }

    public function getHiddenData($request)
    {
        if ($request->hiddenText == 'true') {
            $this->mainPage['hiddenText'] = Relevance::getHiddenText($this->mainPage['html']);
            foreach ($this->pages as $key => $page) {
                $this->pages[$key]['hiddenText'] = Relevance::getHiddenText($page['html']);
            }
        }
    }

    public function getHtml()
    {
        $this->mainPage['html'] = TextAnalyzer::deleteEverythingExceptCharacters($this->mainPage['html']);
        foreach ($this->pages as $key => $page) {
            $this->pages[$key]['html'] = TextAnalyzer::deleteEverythingExceptCharacters($page['html']);
        }
    }

    public function removeConjunctionsPrepositionsPronouns($request)
    {
        if ($request->conjunctionsPrepositionsPronouns == 'false') {
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

    public function removeListWords($request)
    {
        if ($request->switchMyListWords == 'true') {
            $listWords = str_replace(["\r\n", "\n\r"], "\n", $request->listWords);
            $arList = explode("\n", $listWords);
            Log::debug('arList', $arList);
            $this->mainPage['html'] = Relevance::mbStrReplace($arList, '', $this->mainPage['html']);
            $this->mainPage['linkText'] = Relevance::mbStrReplace($arList, '', $this->mainPage['linkText']);
            $this->mainPage['hiddenText'] = Relevance::mbStrReplace($arList, '', $this->mainPage['hiddenText']);
            foreach ($this->pages as $key => $page) {
                $this->pages[$key]['html'] = Relevance::mbStrReplace($arList, '', $this->pages[$key]['html']);
                $this->pages[$key]['linkText'] = Relevance::mbStrReplace($arList, '', $this->pages[$key]['linkText']);
                $this->pages[$key]['hiddenText'] = Relevance::mbStrReplace($arList, '', $this->pages[$key]['hiddenText']);
            }
        }
    }

    public static function mbStrReplace($search, $replace, $string)
    {
        $charset = mb_detect_encoding($string);

        $unicodeString = iconv($charset, "UTF-8", $string);

        return str_replace($search, $replace, $unicodeString);
    }

    /**
     * @return void
     */
    public function prepareClouds()
    {
        $this->mainPage['textCloud'] = TextAnalyzer::prepareCloud(
            $this->mainPage['html'] . ' ' . $this->mainPage['hiddenText']
        );
        $this->mainPage['textWithLinksCloud'] = TextAnalyzer::prepareCloud(
            $this->mainPage['html'] . ' ' . $this->mainPage['hiddenText'] . ' ' . $this->mainPage['linkText']
        );
        $this->mainPage['linksCloud'] = TextAnalyzer::prepareCloud($this->mainPage['linkText']);

        $this->competitorsTextAndLinksCloud = TextAnalyzer::prepareCloud($this->competitorsTextAndLinks);
        $this->competitorsTextCloud = TextAnalyzer::prepareCloud($this->competitorsText);
        $this->competitorsLinksCloud = TextAnalyzer::prepareCloud($this->competitorsLinks);
    }

    public function processingOfGeneralInformation($countSites)
    {
        $mainPage = ' ' . $this->mainPage['html'] . ' ' .
            $this->mainPage['linkText'] . ' ' .
            $this->mainPage['hiddenText'] . ' ';
        $strLen = str_word_count($this->competitorsTextAndLinks);

        foreach ($this->wordForms as $root => $wordForm) {
            foreach ($wordForm as $word => $item) {
                $reSpam = 0;
                $numberTextOccurrences = 0;
                $numberLinkOccurrences = 0;
                $numberOccurrences = 0;
                foreach ($this->pages as $page) {
                    if (preg_match("/($word)/", $page['html'])) {
                        $count = substr_count($page['html'], " $word ");
                        $numberTextOccurrences += $count;
                        if ($reSpam < $count) {
                            $reSpam = $count;
                        }
                    }
                    if (preg_match("/($word)/", $page['hiddenText'])) {
                        $count = substr_count($page['hiddenText'], " $word ");
                        $numberTextOccurrences += $count;
                        if ($reSpam < $count) {
                            $reSpam = $count;
                        }
                    }
                    if (preg_match("/($word)/", $page['linkText'])) {
                        $count = substr_count($page['linkText'], " $word ");
                        $numberLinkOccurrences += $count;
                        if ($reSpam < $count) {
                            $reSpam = $count;
                        }
                    }

                    if (preg_match("/($word)/", $page['html']) ||
                        preg_match("/($word)/", $page['linkText']) ||
                        preg_match("/($word)/", $page['hiddenText'])) {
                        $numberOccurrences++;
                    }
                }

                $tf = round($item / $strLen, 4);
                $idf = round(log10($strLen / $item), 4);

                $repeatInTextMainPage = substr_count($mainPage, " $word ");
                $repeatLinkInMainPage = substr_count($this->mainPage['linkText'], " $word ");
                $this->wordForms[$root][$word] = [
                    'tf' => $tf,
                    'idf' => $idf,
                    'numberOccurrences' => $numberOccurrences,
                    'reSpam' => $reSpam,
                    'avgInLink' => $numberLinkOccurrences / $countSites,
                    'repeatInLinkMainPage' => $repeatLinkInMainPage,
                    'avgInText' => $numberTextOccurrences / $countSites,
                    'repeatInTextMainPage' => $repeatInTextMainPage,
                ];
            }
        }
    }

    public function searchWordForms()
    {
        $will = [];
        $array = explode(' ', $this->competitorsTextAndLinks);
        $stemmer = new LinguaStem();

        $array = array_count_values($array);
        asort($array);
        $array = array_reverse($array);

        // удаляем все слова в которых кол-во символов меньше 3
        foreach ($array as $key => $item) {
            if (mb_strlen($key) <= 3) {
                unset($array[$key]);
            }
        }

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
                            $this->wordForms[$key1][$key2] = $item2;
                            $will[] = $key2;
                            $will[] = $key1;
                        }
                    }
                }
            }
            if (count($this->wordForms) >= 600) {
                break;
            }
        }
    }

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
            $danger = false;
            foreach ($wordForm as $word) {
                if ($word['repeatInTextMainPage'] == 0 || $word['repeatInLinkMainPage'] == 0) {
                    $danger = true;
                }
                $tf += $word['tf'];
                $idf += $word['idf'];
                $avgInText += $word['avgInText'];
                $avgInLink += $word['avgInLink'];
                $repeatInText += $word['repeatInTextMainPage'];
                $repeatInLink += $word['repeatInLinkMainPage'];
                if ($word['numberOccurrences'] > $occurrences) {
                    $occurrences = $word['numberOccurrences'];
                }
                if ($word['reSpam'] > $reSpam) {
                    $reSpam = $word['reSpam'];
                }
            }
            $this->wordForms[$key]['total'] = [
                'tf' => $tf,
                'idf' => $idf,
                'avgInText' => $avgInText,
                'avgInLink' => $avgInLink,
                'repeatInTextMainPage' => $repeatInText,
                'repeatInLinkMainPage' => $repeatInLink,
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
     */
    public function removeIgnoredDomains($count, $ignoredDomains, $xmlResponse)
    {
        if (isset($ignoredDomains)) {
            $ignoredDomains = str_replace("\r\n", "\n", $ignoredDomains);
            $ignoredDomains = explode("\n", $ignoredDomains);
            foreach ($xmlResponse as $item) {
                if (!in_array($item['doc']['domain'], $ignoredDomains)) {
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
}
