<?php

namespace App;

use App\Classes\Xml\SimplifiedXmlFacade;
use Illuminate\Database\Eloquent\Model;

class SearchCompetitors extends Model
{
    protected $guarded = [];

    protected $table = 'competitor_analysis_count_checks';

    protected $metaTags = [];

    protected $sites = [];

    protected $analysedSites = [];

    protected $pagesCounter = [];

    protected $totalMetaTags = [];

    protected $domainsPosition = [];

    protected $region;

    protected $phrases;

    protected $count;

    public $pageHash;

    /**
     * @param string $pageHash
     * @return void
     */
    public function setPageHash(string $pageHash)
    {
        $this->pageHash = $pageHash;
    }

    public function setPhrases(string $string)
    {
        $phrases = explode("\n", $string);

        $this->phrases = array_diff($phrases, ['']);
    }

    public function setRegion(string $region)
    {
        $this->region = $region;
    }

    public function setCount(int $count)
    {
        $this->count = $count;
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        return [
            'analysedSites' => $this->analysedSites,
            'pagesCounter' => $this->pagesCounter,
            'totalMetaTags' => $this->totalMetaTags,
            'domainsPosition' => $this->domainsPosition,
        ];
    }

    /**
     * @return void
     */
    public function analyzeList()
    {
        $xml = new SimplifiedXmlFacade($this->region, $this->count);

        foreach ($this->phrases as $phrase) {
            $xml->setQuery($phrase);
            $this->sites[$phrase] = $xml->getXMLResponse();
        }

        CompetitorsProgressBar::where('page_hash', '=', $this->pageHash)->update([
            'percent' => 25
        ]);
        $this->scanSites();
    }

    /**
     * @return void
     */
    public function scanSites()
    {
        foreach ($this->sites as $key => $items) {
            foreach ($items as $item) {
                $site = SearchCompetitors::curlInit($item);
                try {
                    $contentType = $site[1]['content_type'];
                    if (preg_match('(.*?charset=(.*))', $contentType, $contentType, PREG_OFFSET_CAPTURE)) {
                        $contentType = str_replace(["\r", "\n"], '', $contentType[1][0]);
                        $site = mb_convert_encoding($site, 'utf8', str_replace('"', '', $contentType));
                    }
                } catch (\Exception $exception) {
                }

                $description = SearchCompetitors::getText($site[0], "/<meta name=\"description\" content=\"(.*?)\"/");
                $title = SearchCompetitors::getText($site[0], "/<title.*?>(.*?)<\/title>/");
                $h1 = SearchCompetitors::getText($site[0], "/<h1.*?>(.*?)<\/h1>/");
                $h2 = SearchCompetitors::getText($site[0], "/<h2.*?>(.*?)<\/h2>/");
                $h3 = SearchCompetitors::getText($site[0], "/<h3.*?>(.*?)<\/h3>/");
                $h4 = SearchCompetitors::getText($site[0], "/<h4.*?>(.*?)<\/h4>/");
                $h5 = SearchCompetitors::getText($site[0], "/<h5.*?>(.*?)<\/h5>/");
                $h6 = SearchCompetitors::getText($site[0], "/<h6.*?>(.*?)<\/h6>/");

                $this->analysedSites[$key][$item]['meta'] = [
                    'title' => $title,
                    'h1' => $h1,
                    'h2' => $h2,
                    'h3' => $h3,
                    'h4' => $h4,
                    'h5' => $h5,
                    'h6' => $h6,
                    'description' => $description,
                ];

                //Если все теги пустые, значит не получилось получить данные со страницы
                $this->analysedSites[$key][$item]['danger'] = array_merge($title, $h1, $h2, $h3, $h4, $h5, $h6, $description) === [];
            }
        }

        CompetitorsProgressBar::where('page_hash', '=', $this->pageHash)->update([
            'percent' => 50
        ]);
        $this->analysisPageNesting();
    }

    /**
     * @return void
     */
    public function analysisPageNesting()
    {
        $this->pagesCounter = [
            'mainPageCounter' => 0,
            'nestedPageCounter' => 0
        ];

        $counter = 0;
        foreach ($this->sites as $items) {
            foreach ($items as $item) {
                $url = parse_url($item);
                if ($url['path'] === '/' || $url['path'] === 'index.html' || $url['path'] === 'index.php') {
                    $this->pagesCounter['mainPageCounter']++;
                } else {
                    $this->pagesCounter['nestedPageCounter']++;
                }
                $counter++;
            }
        }

        $this->pagesCounter['mainPagePercent'] = round((100 / $counter) * $this->pagesCounter['mainPageCounter'], 1);
        $this->pagesCounter['nestedPagePercent'] = round((100 / $counter) * $this->pagesCounter['nestedPageCounter'], 1);

        CompetitorsProgressBar::where('page_hash', '=', $this->pageHash)->update([
            'percent' => 75
        ]);

        $this->scanTags();
    }

    /**
     * @return void
     */
    public function scanTags()
    {
        foreach ($this->analysedSites as $phrase => $sites) {
            foreach ($sites as $link => $site) {
                $this->metaTags[$phrase][] = $site['meta'];
            }
        }

        foreach ($this->metaTags as $phrase => $metaTags) {
            foreach ($metaTags as $metaTag) {
                $this->totalMetaTags[$phrase]['title'][] = TextAnalyzer::deleteEverythingExceptCharacters(implode(' ', $metaTag['title']));
                $this->totalMetaTags[$phrase]['h1'][] = TextAnalyzer::deleteEverythingExceptCharacters(implode(' ', $metaTag['h1']));
                $this->totalMetaTags[$phrase]['h2'][] = TextAnalyzer::deleteEverythingExceptCharacters(implode(' ', $metaTag['h2']));
                $this->totalMetaTags[$phrase]['h3'][] = TextAnalyzer::deleteEverythingExceptCharacters(implode(' ', $metaTag['h3']));
                $this->totalMetaTags[$phrase]['h4'][] = TextAnalyzer::deleteEverythingExceptCharacters(implode(' ', $metaTag['h4']));
                $this->totalMetaTags[$phrase]['h5'][] = TextAnalyzer::deleteEverythingExceptCharacters(implode(' ', $metaTag['h5']));
                $this->totalMetaTags[$phrase]['h6'][] = TextAnalyzer::deleteEverythingExceptCharacters(implode(' ', $metaTag['h6']));
            }
        }

        foreach ($this->metaTags as $phrase => $metaTags) {
            $this->totalMetaTags[$phrase]['title'] = array_count_values(explode(' ', mb_strtolower(implode(' ', $this->totalMetaTags[$phrase]['title']))));
            $this->totalMetaTags[$phrase]['h1'] = array_count_values(explode(' ', mb_strtolower(implode(' ', $this->totalMetaTags[$phrase]['h1']))));
            $this->totalMetaTags[$phrase]['h2'] = array_count_values(explode(' ', mb_strtolower(implode(' ', $this->totalMetaTags[$phrase]['h2']))));
            $this->totalMetaTags[$phrase]['h3'] = array_count_values(explode(' ', mb_strtolower(implode(' ', $this->totalMetaTags[$phrase]['h3']))));
            $this->totalMetaTags[$phrase]['h4'] = array_count_values(explode(' ', mb_strtolower(implode(' ', $this->totalMetaTags[$phrase]['h4']))));
            $this->totalMetaTags[$phrase]['h5'] = array_count_values(explode(' ', mb_strtolower(implode(' ', $this->totalMetaTags[$phrase]['h5']))));
            $this->totalMetaTags[$phrase]['h6'] = array_count_values(explode(' ', mb_strtolower(implode(' ', $this->totalMetaTags[$phrase]['h6']))));

            arsort($this->totalMetaTags[$phrase]['title']);
            arsort($this->totalMetaTags[$phrase]['h1']);
            arsort($this->totalMetaTags[$phrase]['h2']);
            arsort($this->totalMetaTags[$phrase]['h3']);
            arsort($this->totalMetaTags[$phrase]['h4']);
            arsort($this->totalMetaTags[$phrase]['h5']);
            arsort($this->totalMetaTags[$phrase]['h6']);
        }

        CompetitorsProgressBar::where('page_hash', '=', $this->pageHash)->update([
            'percent' => 90
        ]);
        $this->calculatePositions();
    }

    /**
     * @return void
     */
    public function calculatePositions()
    {
        $domains = [];

        foreach ($this->analysedSites as $phrase => $sites) {
            $position = 1;
            foreach ($sites as $link => $item) {
                $domains[parse_url($link)['host']][] = $position;
                $position++;
            }
        }

        $countPhrases = count($this->phrases);

        foreach ($domains as $key => $positions) {
            $countPositions = count($positions);
            $sum = array_sum($positions);


            $percent = $countPhrases / 100;
            $this->domainsPosition[$key]['topPercent'] = min(100, $countPositions / $percent);
            $this->domainsPosition[$key]['text'] = "($countPositions/$countPhrases)";
            if ($countPhrases === $countPositions || $countPhrases < $countPositions) {
                $this->domainsPosition[$key]['avg'] = $sum / $countPositions;
            } else {
                $this->domainsPosition[$key]['avg'] = ((($countPhrases - $countPositions) * $this->count + 1) + $sum) / $countPositions;
            }
        }

        TariffSetting::saveStatistics(SearchCompetitors::class, count($this->phrases));
        CompetitorsProgressBar::where('page_hash', '=', $this->pageHash)->update([
            'percent' => 100
        ]);
    }

    /**
     * @param $html
     * @param $regex
     * @return array
     */
    public static function getText($html, $regex): array
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
            if ($headers['http_code'] == 200 && $html !== false) {
                $html = preg_replace('//i', '', $html);
                break;
            }
        }
        curl_close($curl);
        return [$html, $headers];
    }
}
