<?php

namespace App;

use App\Classes\Xml\SimplifiedXmlFacade;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

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

    protected $urls = [];

    protected $region;

    protected $phrases;

    protected $count;

    protected $duplicates = [];

    protected $savedHtml = [];

    protected $countPhrases;

    public $pageHash;

    public function getCountPhrases()
    {
        return $this->countPhrases;
    }

    public function setPageHash(string $pageHash)
    {
        $this->pageHash = $pageHash;
    }

    public function setPhrases(string $string)
    {
        $phrases = explode("\n", $string);

        $this->phrases = array_unique(array_diff($phrases, ['']));
    }

    public function getPhrases()
    {
        return $this->phrases;
    }

    public function setRegion(string $region)
    {
        $this->region = $region;
    }

    public function setCount(int $count)
    {
        $this->count = $count;
    }

    public function getResult()
    {
        return json_encode([
            'analysedSites' => $this->tryConvertEncoding($this->analysedSites),
            'pagesCounter' => $this->tryConvertEncoding($this->pagesCounter),
            'totalMetaTags' => $this->tryConvertEncoding($this->totalMetaTags),
            'domainsPosition' => $this->tryConvertEncoding($this->domainsPosition),
            'urls' => $this->tryConvertEncoding($this->urls),
        ]);
    }

    protected function tryConvertEncoding($object)
    {
        try {
            return mb_convert_encoding($object, 'UTF-8', 'auto');
        } catch (Throwable $e) {
            return $object;
        }
    }

    /**
     * @return Exception|void
     */
    public function analyseList()
    {
        $xml = new SimplifiedXmlFacade($this->region, $this->count);

        foreach ($this->phrases as $phrase) {
            $phrase = trim($phrase);
            if ($phrase != '') {
                $xml->setQuery($phrase);
                $this->sites[$phrase] = $xml->getXMLResponse();
            }
        }

        $this->countPhrases = 0;
        foreach ($this->sites as $key => $site) {
            if (is_array($site)) {
                $this->countPhrases++;
            } else {
                unset($this->sites[$key]);
            }
        }

        TariffSetting::saveStatistics(SearchCompetitors::class, $this->countPhrases);

        try {
            $this->searchDuplicates();
            $this->scanSites();
        } catch (Throwable $e) {
            $now = Carbon::now();

            SearchCompetitors::where('user_id', '=', Auth::id())
                ->where('month', '=', $now->year . '-' . $now->month)
                ->decriment('counter', $this->getCountPhrases());

            return new Exception('competitors error');
        }

    }

    /**
     * @return void
     */
    public function scanSites()
    {
        $iterator = 0;
        $total = ($this->count * count($this->phrases)) / 100;
        foreach ($this->sites as $phrase => $items) {
            foreach ($items as $link) {
                if ($this->duplicates[$link]) {
                    if (array_key_exists($link, $this->savedHtml)) {
                        $this->analysedSites[$phrase][$link] = $this->savedHtml[$link];
                    } else {
                        $this->savedHtml[$link] = $this->analyseSite(
                            $this->encodingContent(SearchCompetitors::curlInit($link)),
                            $phrase,
                            $link
                        );
                    }
                } else {
                    $this->analyseSite(
                        $this->encodingContent(SearchCompetitors::curlInit($link)),
                        $phrase,
                        $link
                    );
                }


                $iterator++;
                if ($iterator % 3 === 0) {
                    $percent = $iterator / $total;
                    CompetitorsProgressBar::where('page_hash', '=', $this->pageHash)->update([
                        'percent' => ceil($percent)
                    ]);
                }
            }

        }

        $this->analysisNestingDomains();
    }

    /**
     * @param $site
     * @param $phrase
     * @param $link
     * @return mixed
     */
    protected function analyseSite($site, $phrase, $link)
    {
        $description = SearchCompetitors::getText($site[0], "/<meta name=\"description\" content=\"(.*?)\"/");
        $title = SearchCompetitors::getText($site[0], "/<title.*?>(.*?)<\/title>/");
        $h1 = SearchCompetitors::getText($site[0], "/<h1.*?>(.*?)<\/h1>/");
        $h2 = SearchCompetitors::getText($site[0], "/<h2.*?>(.*?)<\/h2>/");
        $h3 = SearchCompetitors::getText($site[0], "/<h3.*?>(.*?)<\/h3>/");
        $h4 = SearchCompetitors::getText($site[0], "/<h4.*?>(.*?)<\/h4>/");
        $h5 = SearchCompetitors::getText($site[0], "/<h5.*?>(.*?)<\/h5>/");
        $h6 = SearchCompetitors::getText($site[0], "/<h6.*?>(.*?)<\/h6>/");

        $this->analysedSites[$phrase][$link]['meta'] = [
            'title' => $title,
            'h1' => $h1,
            'h2' => $h2,
            'h3' => $h3,
            'h4' => $h4,
            'h5' => $h5,
            'h6' => $h6,
            'description' => $description,
        ];

        $this->analysedSites[$phrase][$link]['danger'] = array_merge($title, $h1, $h2, $h3, $h4, $h5, $h6, $description) === [];

        $this->analysedSites[$phrase][$link]['mainPage'] = SearchCompetitors::isLinkMainPage($link);

        return $this->analysedSites[$phrase][$link];
    }

    /**
     * @param array $site
     * @return array|false|string|string[]|null
     */
    protected function encodingContent(array $site)
    {
        try {
            $contentType = $site[1]['content_type'];
            if (preg_match('(.*?charset=(.*))', $contentType, $contentType, PREG_OFFSET_CAPTURE)) {
                $contentType = str_replace(["\r", "\n"], '', $contentType[1][0]);
                return mb_convert_encoding($site, 'utf8', str_replace('"', '', $contentType));
            }

        } catch (Throwable $e) {
            return $site;
        }

        return $site;
    }

    /**
     * @return void
     */
    protected function searchDuplicates()
    {
        foreach ($this->sites as $phrase => $links) {
            foreach ($links as $link) {
                if (isset($this->duplicates[$link])) {
                    $this->duplicates[$link] = true;
                } else {
                    $this->duplicates[$link] = false;
                }
            }
        }
    }

    /**
     * @return void
     */
    protected function analysisNestingDomains()
    {
        $this->pagesCounter = [
            'mainPageCounter' => 0,
            'nestedPageCounter' => 0
        ];

        $counter = 0;
        foreach ($this->sites as $items) {
            foreach ($items as $item) {
                if (SearchCompetitors::isLinkMainPage($item)) {
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
            'percent' => 93
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

        $metaTagsArray = ['title', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'description'];

        foreach ($metaTagsArray as $metaTag) {
            $this->searchMetaTag($metaTag);
        }

        CompetitorsProgressBar::where('page_hash', '=', $this->pageHash)->update([
            'percent' => 95
        ]);
        $this->calculatePositions();
    }

    /**
     * @param $key
     * @return void
     */
    protected function searchMetaTag($key)
    {
        foreach ($this->metaTags as $phrase => $metaTags) {
            foreach ($metaTags as $metaTag) {
                $this->totalMetaTags[$phrase][$key][] = TextAnalyzer::deleteEverythingExceptCharacters(implode(' ', $metaTag[$key]));
            }
        }

        foreach ($this->metaTags as $phrase => $metaTags) {
            $this->totalMetaTags[$phrase][$key] = array_count_values(explode(' ', mb_strtolower(implode(' ', $this->totalMetaTags[$phrase][$key]))));

            arsort($this->totalMetaTags[$phrase][$key]);
        }
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
                $host = parse_url($link)['host'];
                $domains[$host]['position'][] = $position;
                $domains[$host]['phrases'][] = $phrase;
                $domains[$host]['phrases'] = array_unique($domains[$host]['phrases']);
                $position++;
            }
        }

        $countPhrases = count($this->phrases);

        foreach ($domains as $domain => $info) {
            $countPositions = count($info['position']);
            $sum = array_sum($info['position']);
            $percent = $countPhrases / 100;

            $this->domainsPosition[$domain]['phrases'] = $info['phrases'];
            $this->domainsPosition[$domain]['topPercent'] = ceil(min(100, $countPositions / $percent));
            $this->domainsPosition[$domain]['text'] = "($countPositions/$countPhrases)";

            if ($countPhrases === $countPositions || $countPhrases < $countPositions) {
                $this->domainsPosition[$domain]['avg'] = ceil($sum / $countPositions);
            } else {
                $this->domainsPosition[$domain]['avg'] = ceil(((($countPhrases - $countPositions) * $this->count + 1) + $sum) / $countPositions);
            }
        }

        $this->analysisRepeatUrl();
    }

    /**
     * @return void
     */
    protected function analysisRepeatUrl()
    {
        $this->urls = [];
        foreach ($this->analysedSites as $phrase => $urls) {
            foreach ($urls as $url => $info) {
                if (isset($this->urls[$url])) {
                    $this->urls[$url]['count'] += 1;
                } else {
                    $this->urls[$url]['count'] = 1;
                }

                $this->urls[$url]['phrases'][] = $phrase;
            }
        }

        foreach ($this->urls as $url => $info) {
            $this->urls[$url]['phrases'] = array_unique($this->urls[$url]['phrases']);
        }

        CompetitorsProgressBar::where('page_hash', '=', $this->pageHash)->update([
            'percent' => 100,
            'result' => $this->getResult()
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

    /**
     * @param $link
     * @return bool
     */
    public static function isLinkMainPage($link): bool
    {
        $url = parse_url($link);

        return $url['path'] === '/' || $url['path'] === 'index.html' || $url['path'] === 'index.php';
    }

    /**
     * @param $phrases
     * @param $tags
     * @param $metaTags
     * @param $countPhrases
     * @param $count
     * @return array[]
     */
    public static function getRecommendations($phrases, $tags, $metaTags, $countPhrases, $count): array
    {
        $config = CompetitorConfig::first();

        if ($count === "10") {
            $minimumValue = $config->count_repeat_top_10;
        } else {
            $minimumValue = $config->count_repeat_top_20;
        }

        $information = [];
        foreach ($phrases as $phrase) {
            foreach ($metaTags[$phrase] as $tag => $values) {
                if (in_array($tag, $tags)) {
                    $information[$tag][] = $values;
                }
            }
        }

        $result = [];
        foreach ($information as $tag => $values) {
            foreach ($values as $value) {
                foreach ($value as $word => $count) {
                    if (isset($result[$tag][$word])) {
                        $result[$tag][$word] += $count;
                    } else {
                        $result[$tag][$word] = $count;
                    }
                }
            }
        }

        foreach ($result as $tag => $values) {
            foreach ($values as $word => $count) {
                if (($count / $countPhrases) < $minimumValue || $word === 0 || $word === "") {
                    unset($result[$tag][$word]);
                }
            }
        }

        return $result;
    }
}
