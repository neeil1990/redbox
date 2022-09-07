<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use JavaScript;

class TextAnalyzer extends Model
{
    protected $guarded = [];

    protected $table = 'text_analyser_count_checks';

    /**
     * @param $link
     * @return array|null
     */
    public static function curlInit($link)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
        curl_setopt($curl, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
        curl_setopt($curl, CURLOPT_COOKIE, 'realauth=SvBD85dINu3; expires=Sat, 25 Feb 2030 02:16:43 GMT; path=/; SameSite=Lax');
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 4);
        curl_setopt($curl, CURLOPT_TIMEOUT, 4);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);

        return TextAnalyzer::curlConnect($curl);
    }

    /**
     * @param $curl
     * @return bool|string|string[]|null
     */
    public static function curlConnect($curl)
    {
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

        foreach ($userAgents as $agent) {
            curl_setopt($curl, CURLOPT_USERAGENT, $agent);
            $html = curl_exec($curl);
            $headers = curl_getinfo($curl);
            if ($headers['http_code'] == 200 && $html != false) {
                $html = preg_replace('//i', '', $html);
                break;
            }
//            else {
//                Log::debug('curl relevance error', [
//                    curl_error($curl),
//                    curl_getinfo($curl, CURLINFO_EFFECTIVE_URL)
//                ]);
//            }
        }

        curl_close($curl);
        try {
            $contentType = trim(str_replace('text/html;', '', $headers['content_type']));
            $contentType = trim(str_replace('charset=', '', $contentType));
            $html = mb_convert_encoding($html, "utf-8", $contentType);
        } catch (\Exception $exception) {

        }

        return $html;
    }

    /**
     * @param $string
     * @param $request
     * @return array
     */
    public static function analyze($string, $request): array
    {
        $data = '';
        $alt = '';
        $title = '';
        $html = mb_strtolower($string);
        if (isset($request->noIndex)) {
            $html = TextAnalyzer::removeNoindexText($html);
        } else {
            $response['noIndex'] = true;
        }
        $link = TextAnalyzer::getLinkText($html);
        if (isset($request->hiddenText)) {
            $title = TextAnalyzer::getHiddenText($html, "<.*?title=\"(.*?)\".*>");
            $alt = TextAnalyzer::getHiddenText($html, "<.*?alt=\"(.*?)\".*>");
            $data = TextAnalyzer::getHiddenText($html, "<.*?data-text=\"(.*?)\".*>");
            $response['hiddenText'] = true;
        }

        $html = TextAnalyzer::clearHTMLFromLinks($html);
        $text = TextAnalyzer::deleteEverythingExceptCharacters($html);

        if (isset($request->conjunctionsPrepositionsPronouns)) {
            $text = TextAnalyzer::removeConjunctionsPrepositionsPronouns($text);
            $title = TextAnalyzer::removeConjunctionsPrepositionsPronouns($title);
            $alt = TextAnalyzer::removeConjunctionsPrepositionsPronouns($alt);
            $data = TextAnalyzer::removeConjunctionsPrepositionsPronouns($data);
            $link = TextAnalyzer::removeConjunctionsPrepositionsPronouns($link);
        } else {
            $response['conjunctionsPrepositionsPronouns'] = true;
        }

        if (isset($request->switchMyListWords)) {
            $text = TextAnalyzer::removeWords($request->listWords, $text);
            $title = TextAnalyzer::removeWords($request->listWords, $title);
            $alt = TextAnalyzer::removeWords($request->listWords, $alt);
            $data = TextAnalyzer::removeWords($request->listWords, $data);
            $link = TextAnalyzer::removeWords($request->listWords, $link);
            $response['listWords'] = $request->listWords;
        }

        $total = trim($text . ' ' . $alt . ' ' . $title . ' ' . $data);

        $countSpaces = substr_count(trim($total . ' ' . $link), ' ');
        $totalWords = TextAnalyzer::deleteEverythingExceptCharacters($string);
        $length = mb_strlen($totalWords);
        $response['general'] = [
            'textLength' => $length,
            'countSpaces' => $countSpaces,
            'lengthWithOutSpaces' => $length - $countSpaces,
            'countWords' => count(explode(' ', $totalWords)),
        ];

        $textWithoutLinks = TextAnalyzer::prepareCloud($total);
        $linksText = TextAnalyzer::prepareCloud($link);
        $textWithLinks = TextAnalyzer::prepareCloud(trim($total . ' ' . $link));

        $response['totalWords'] = TextAnalyzer::AnalyzeWords($total, $link);
        $response['phrases'] = TextAnalyzer::searchPhrases(trim($total . ' ' . $link));

        JavaScript::put([
            'textWithoutLinks' => $textWithoutLinks,
            'textWithLinks' => $textWithLinks,
            'linksText' => $linksText,
            'graph' => TextAnalyzer::prepareDataGraph($response['totalWords']),
        ]);

        TariffSetting::saveStatistics(TextAnalyzer::class);

        return $response;
    }

    /**
     * @param $html
     * @return array|string|string[]|null
     */
    public static function deleteEverythingExceptCharacters($html)
    {
        $html = preg_replace([
            "'<style[^>]*?>.*?</style>'si",
            "'<script[^>]*?>.*?</script>'si",
            "'<head[^>]*?>.*?</head>'si",
            "'<i [^>]*?>.*?</i>'si",
            "'array\n\(\n.*?\n\)\n'si",
            "'array.*?\(.*?\)'si",
            "'<div.*?class=\"js_img-for-color hidden\">.*?</div>'si",
        ], "", $html);

        $html = str_replace('>', '> ', $html);
        $text = trim(strip_tags($html));

        $text = trim(str_replace(chr(194) . chr(160), ' ', html_entity_decode($text)));
        $text = preg_replace('/[^a-zа-яё\w\s]/ui', ' ', $text);
        $text = preg_replace("/&#?[a-z]+;/i", "", $text);
        $text = str_replace([
            "\n", "\t", "\r",
            "»", "«", ".", ",", "!", "?",
            "(", ")", "+", ";", ":", "-",
            "₽", "$", "/", "[", "]", "“"
        ], ' ', $text);
        $text = preg_replace("/\d/", "", $text);
        $text = str_replace("ё", "е", $text);

        return preg_replace('| +|', ' ', $text);
    }

    /**
     * @param $html
     * @return string
     */
    public static function removeStylesAndScripts($html): string
    {
        $text = preg_replace([
            "'<style[^>]*?>.*?</style>'si",
            "'<script[^>]*?>.*?</script>'si",
            "'<head[^>]*?>.*?</head>'si",
            "'<i [^>]*?>.*?</i>'si",
            "'array\n\(\n.*?\n\)\n'si",
            "'array.*?\(.*?\)'si",
            "'<div.*?class=\"js_img-for-color hidden\">.*?</div>'si",
        ], "", $html);

        return mb_strtolower($text);
    }

    /**
     * @param $html
     * @return array|false|string|string[]|null
     */
    public static function removeNoindexText($html)
    {
        preg_match_all('#<noindex>(.+?)</noindex>#su', $html, $matches, PREG_SET_ORDER);
        foreach ($matches as $item) {
            $html = str_replace($item[0], "", $html);
        }

        return mb_strtolower($html);
    }

    /**
     * @param $text
     * @return mixed|string|string[]
     */
    public static function removeConjunctionsPrepositionsPronouns($text)
    {
        $pronouns = [
            'я', 'мы', 'ты', 'вы', 'он', 'она', 'оно', 'они', 'себя', 'мой', 'наш', 'твой', 'ваш', 'свой',
            'кто', 'что', 'какой', 'каков', 'который', 'чей', 'сколько', 'этот', 'тот', 'такой', 'таков',
            'столько', 'сам', 'самый', 'весь', 'вся', 'всё', 'все', 'всякий', 'каждый', 'любой', 'иной',
            'никто', 'ничто', 'никакой', 'ничей', 'никоторый', 'некого', 'нечего', 'некто', 'нечто',
            'некоторый', 'кто-то', 'сколько-то', 'что-либо', 'кое-кто', 'какой-то', 'какой-либо',
            'кое-какой', 'чей-то', 'чей-нибудь',
            'i', 'you', 'she', 'he', 'it', 'we ', 'you', 'they', 'me', 'you', 'her',
            'him', 'it', 'us', 'you', 'them', 'my', 'your', 'her', 'his', 'its', 'our',
            'your', 'their', 'mine', 'yours', 'hers', 'his', 'its', 'our', 'your',
            'their', 'yours', 'hers', 'ours', 'yours', 'theirs', 'myself', 'yourself',
            'herself', 'himself', 'itself', 'ourselves', 'yourselves', 'themselves',
        ];
        $preposition = [
            'без', 'безо', 'близ', 'в', 'во', 'вместо', 'вне', 'для', 'до', 'за', 'из', 'по',
            'изо', 'из-за', 'из-под', 'к', 'не', 'ко', 'кроме', 'между', 'меж', 'на', 'над',
            'о', 'об', 'обо', 'от', 'ото', 'перед', 'передо', 'пред', 'пред', 'пo', 'под',
            'подо', 'при', 'про', 'ради', 'с', 'со', 'сквозь', 'среди', 'у', 'через', 'чрез',
            'aboard', 'about', 'above', 'absent', 'across', 'before', 'after', 'against', 'along',
            'amid', 'amidst', 'among', 'amongst', 'around', 'as', 'aside', 'aslant', 'astride', 'at',
            'athwart', 'atop', 'bar', 'before', 'behind', 'below', 'beneath', 'beside', 'besides', 'between',
            'betwixt', 'beyond', 'but', 'by', 'circa', 'despite', 'down', 'except', 'for', 'from', 'given',
            'in', 'inside', 'into', 'like', 'minus', 'near', 'neath', 'next', 'notwithstanding', 'of', 'off',
            'on', 'opposite', 'out', 'outside', 'over', 'pace', 'per', 'plus', 'post', 'pro', 'qua', 'round',
            'save', 'since', 'than', 'through', 'till', 'times', 'to', 'toward', 'towards', 'under',
            'underneath', 'unlike', 'until', 'up', 'versus', 'via', 'vice', 'with', 'without', 'barring',
            'concerning', 'considering', 'depending', 'during', 'granted', 'excepting', 'excluding', 'failing',
            'following', 'including', 'past', 'pending', 'regarding', 'alongside', 'within', 'outside', 'upon',
            'onto', 'throughout', 'wherewith', 'according to', 'ahead of', 'apart from', 'as far as', 'as for',
            'as of', 'as per', 'as regards', 'aside from', 'as well as', 'away from',
            'because of', 'by force of', 'by means of', 'by virtue of', 'close to', 'contrary to', 'due to',
            'except for', 'far from', 'for the sake of', 'in accordance with', 'in addition to', 'in case of',
            'in connection with', 'in consequence of', 'in front of', 'in spite of', 'in the back of',
            'in the course of', 'in the event of', 'in the middle of', 'inside of', 'instead of', 'in view of',
            'near to', 'next to', 'on account of', 'on top of', 'opposite to', 'out of	из,', 'outside of',
            'owing to', 'thanks to', 'up to', 'with regard to', 'with respect to',
        ];
        $conjunctions = [
            'а', 'а вдобавок', 'а именно', 'а также', 'а то', 'благодаря тому что', 'благо', 'буде',
            'будто', 'вдобавок', 'в результате чего', 'в результате того что', 'в связи с тем что',
            'в силу того что', 'в случае если', 'в то время как', 'в том случае если', 'в силу чего',
            'ввиду того что', 'вопреки тому что', 'вроде того как', 'вследствие чего', 'вследствие того что',
            'да вдобавок', 'да еще', 'да и', 'да и то', 'дабы', 'даже', 'даром что', 'для того чтобы',
            'же', 'едва', 'ежели', 'если', 'если бы', 'затем чтобы', 'затем что', 'зато', 'зачем', 'и',
            'и все же', 'и значит', 'а именно', 'и поэтому', 'и притом',
            'и все-таки', 'и следовательно', 'и то', 'и тогда времени', 'и еще', 'ибо', 'и вдобавок',
            'из-за того что', 'или', 'или, или', 'кабы', 'как', 'Как скоро', 'как будто',
            'как если бы', 'как словно', 'как только', 'кактак и', 'как-то?', 'когда',
            'коли', 'к тому же', 'кроме того', 'либо', 'лишь', 'лишь бы', 'лишь только', 'между тем как',
            'нежели', 'не столько, сколько', 'не то, не то', 'не только не, но и',
            'не только, но и', 'не только., а и', 'не только, но даже', 'невзирая на то что',
            'независимо от того что', 'несмотря на то что', 'но', 'однако', 'особенно',
            'оттого', 'оттого что', 'отчего', 'перед тем как', 'по мере того как', 'по причине того что',
            'подобно тому как', 'пока', 'покамест', 'покуда', 'пока не', 'после того как',
            'поскольку', 'потому', 'потому что', 'почему', 'прежде чем', 'при всем том что',
            'при условии что', 'притом', 'причем', 'пускай', 'пусть', 'ради того чтобы', 'раз',
            'раньше чем', 'с тем чтобы', 'с тех пор как', 'словно', 'так как', 'так что', 'также',
            'тем более что', 'тогда как', 'то есть', 'тоже', 'только', 'только бы', 'только что',
            'только лишь', 'только чуть', 'точно', 'хотя', 'хотя и, но', 'чем', 'что', 'чтоб', 'чтобы',
            'also', 'and', 'as', 'as far as', 'as long as', 'as soon as', 'as well as', 'because',
            'because of', 'but', 'however', 'if', 'in case', 'in order', 'moreover', 'nevertheless',
            'no matter where', 'no matter how', 'no matter when', 'no matter who', 'no matter why',
            'now that', 'once', 'on the contrary', 'on the other hand', 'or', 'otherwise', 'not so as',
            'still', 'than', 'that', 'therefore', 'although', 'thus', 'unless', 'what', 'while', 'yet',
            'not', 'for', 'against', 'like', 'unlike', 'with', 'without', 'within', 'owing to', 'meanwhile',
            'from time to time', 'beyond', 'whereas', 'at least', 'at last', 'as if, as though', 'on condition',
        ];
        $listWords = array_merge($pronouns, $preposition, $conjunctions);

        foreach ($listWords as $listWord) {
            $text = str_replace(' ' . $listWord . ' ', ' ', $text);
        }

        return $text;
    }

    /**
     * @param $listWords
     * @param $text
     * @return string
     */
    public static function removeWords($listWords, $text): string
    {
        $listWords = str_replace("\r\n", "\n", $listWords);
        $listWords = explode("\n", $listWords);
        foreach ($listWords as $listWord) {
            $text = TextAnalyzer::mbStrReplace([' ' . $listWord . ' '], ' ', $text);
            $text = preg_replace('| +|', ' ', $text);
        }

        return trim($text);
    }

    /**
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
     * @param $string
     * @param int $separator
     * @return array
     */
    public static function prepareCloud($string, int $separator = 2): array
    {
        $words = [];
        $was = [];
        $array = explode(" ", $string);
        $countWords = count($array);
        foreach ($array as $item) {
            if (mb_strlen($item) > $separator) {
                $item = addslashes($item);
                preg_match_all("/.*?\s($item)\s.*?/",
                    $string,
                    $matches,
                    PREG_SET_ORDER);
                if (!in_array($item, $was) && $item != "") {
                    $weight = count($matches);
                    $words[] = [
                        'text' => $item,
                        'weight' => $weight,
                        'html' => [
                            'title' => (1 / $countWords) * $weight
                        ],
                    ];
                    $was[] = $item;
                }
            }
            // максимальное кол-во слов в облаке - 200
            if (count($words) == 200) {
                break;
            }
        }

        $words['count'] = 199;
        $collection = collect($words);

        return $collection->sortByDesc('weight')->toArray();
    }

    /**
     * @param $textWords
     * @param $linkWords
     * @return array
     */
    public static function AnalyzeWords($textWords, $linkWords): array
    {
        $linkWords = explode(' ', $linkWords);
        $textWords = explode(' ', $textWords);
        $totalWords = array_merge($linkWords, $textWords);
        $text = TextAnalyzer::countWordsInText($textWords);
        $link = TextAnalyzer::countWordsInLink($linkWords);
        $result = TextAnalyzer::mergeTextAndLinks($text, $link);

        $result = TextAnalyzer::calculateTFIDF($result, $totalWords, 'inLink');

        return TextAnalyzer::calculateTFIDF($result, $totalWords, 'inText');
    }

    /**
     * @param $string
     * @return \Illuminate\Support\Collection
     */
    public static function searchPhrases($string)
    {
        $phrases = [];
        $array = explode(' ', $string);
        $generalCount = count($array);
        for ($i = 1; $i < count($array); $i++) {
            $phrases[] = [
                'phrase' => $array[$i - 1] . ' ' . $array[$i]
            ];
        }
        $result = [];
        foreach ($phrases as $phrase) {
            $count = 0;
            for ($i = 0; $i < count($phrases); $i++) {
                if ($phrases[$i]['phrase'] === $phrase['phrase']) {
                    $count++;
                }
            }
            $result[] = [
                'phrase' => $phrase['phrase'],
                'count' => $count,
                'density' => round((100 / $generalCount) * $count, 2),
            ];
        }

        $collection = collect($result);
        $collection = $collection->unique();
        $collection = $collection->sortByDesc('count')->toArray();

        return array_slice($collection, 0, 26);
    }

    /**
     * @param $html
     * @param $regex
     * @return string|string[]|null
     */
    public static function getHiddenText($html, $regex)
    {
        $hiddenText = '';
        preg_match_all($regex, $html, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            if ($match[1] != "") {
                $hiddenText .= $match[1] . ' ';
            }
        }
        return TextAnalyzer::deleteEverythingExceptCharacters($hiddenText);
    }

    /**
     * @param $html
     * @return string|string[]|null
     */
    public static function getLinkText($html)
    {
        $linkText = '';
        $html = preg_replace('| +|', ' ', $html);
        $html = str_replace("\n", " ", $html);
        preg_match_all('(<a.*?href=["\']?(.*?)([\'"].*?>(.*?)</a>))', $html, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $match = strip_tags($match[3]);
            if ($match !== "") {
                $linkText .= $match . " ";
            }
        }

        return TextAnalyzer::deleteEverythingExceptCharacters($linkText);
    }

    /**
     * @param $array
     * @return array
     */
    public static function prepareDataGraph($array): array
    {
        $result = [];
        $i = 0;
        foreach ($array as $item) {
            $result[] = [
                'x' => $i + 5,
                'y' => $item['total'],
                'label' => $item['text'],
            ];
            if ($i == 20) {
                break;
            }
            $i++;
        }

        return $result;
    }

    /**
     * @param $html
     * @return string
     */
    public static function clearHTMLFromLinks($html): string
    {
        $html = str_replace(["\n", "\r", "\t"], " ", $html);
        $html = preg_replace("| +|", ' ', $html);

        preg_match_all('(<a.*?>.*?</a>)', $html, $matches, PREG_SET_ORDER);
        foreach ($matches as $items) {
            $html = str_replace($items[0], "", $html);
        }

        return trim($html);
    }

    /**
     * @param $text
     * @return array
     */
    public static function countWordsInText($text): array
    {
        $wordForms = TextAnalyzer::searchWordForms($text);
        $textAr = array_count_values($text);
        arsort($textAr);
        $result = [];

        foreach ($wordForms as $key => $wordForm) {
            $extra = $textAr[$key];
            $result[$key] = [
                'text' => $key,
                'inText' => $textAr[$key],
                'inLink' => 0,
                'total' => $textAr[$key],
                'wordForms' => ['inText' => $wordForm]
            ];
            foreach ($wordForm as $item) {
                $count = array_shift($item);
                $result[$key]['total'] += $count;
                $result[$key]['inText'] += $count;
            }
            $result[$key]['total'] -= $extra;
            $result[$key]['inText'] -= $extra;
        }

        return $result;
    }

    /**
     * @param $array
     * @return array
     */
    public static function searchWordForms($array): array
    {
        $will = [];
        $array = array_count_values($array);
        asort($array);
        $array = array_reverse($array);
        $morphy = new Morphy();
        $result = [];
        $wordForms = [];

        foreach ($array as $key => $item) {
            if (!in_array($key, $will)) {
                $will[] = $key;

                $root = $morphy->base($key);

                if ($root == null) {
                    continue;
                }

                $result[$root][] = [
                    $key => $item
                ];
            }
        }

        foreach ($result as $wordForm) {
            $wordForms[array_key_first($wordForm[0])] = $wordForm;
        }

        return $wordForms;
    }

    /**
     * @param $link
     * @return array
     */
    public static function countWordsInLink($link): array
    {
        $wordForms = TextAnalyzer::searchWordForms($link);
        $linkAr = array_count_values($link);
        asort($linkAr);
        $linkAr = array_reverse($linkAr);
        $links = [];

        foreach ($wordForms as $key => $wordForm) {
            $extra = $linkAr[$key];
            $links[$key] = [
                'text' => $key,
                'inLink' => $linkAr[$key],
                'inText' => 0,
                'total' => $linkAr[$key],
                'wordForms' => ['inLink' => $wordForm]
            ];
            foreach ($wordForm as $item) {
                $count = array_shift($item);
                $links[$key]['inLink'] += $count;
                $links[$key]['total'] += $count;
            }
            $links[$key]['inLink'] -= $extra;
            $links[$key]['total'] -= $extra;
        }

        return $links;
    }

    /**
     * @param $text
     * @param $link
     * @return array
     */
    public static function mergeTextAndLinks($text, $link): array
    {
        $result = [];
        $resultWithDensity = [];
        $density = 0;
        foreach ($text as $key1 => $item1) {
            foreach ($link as $key2 => $item2) {
                similar_text($key1, $key2, $percent);
                if ($percent > 82) {
                    $wordForms = [
                        'inLink' => array_shift($item2['wordForms']),
                        'inText' => array_shift($item1['wordForms'])
                    ];
                    $result[$key1] = [
                        'text' => $key1,
                        'inText' => $item1['inText'],
                        'inLink' => $item2['inLink'],
                        'total' => $item1['inText'] + $item2['inLink'],
                        'wordForms' => $wordForms
                    ];
                    unset($link[$key2]);
                    unset($text[$key1]);
                    break;
                }
            }
        }

        $result = array_merge($link, $text, $result);

        foreach ($result as $item) {
            $density += $item['total'];
        }

        foreach ($result as $item) {
            $resultWithDensity[] = array_merge($item, [
                'density' => round(100 / $density * $item['total'], 2)
            ]);
        }

        $collect = collect($resultWithDensity);
        return $collect->sortByDesc('total')->toArray();
    }

    /**
     * @param $array
     * @param $textAr
     * @param $type
     * @return array
     */
    public static function calculateTFIDF($array, $textAr, $type): array
    {
        for ($i = 0; $i < count($array); $i++) {
            if (isset($array[$i]['wordForms'][$type])) {
                for ($j = 0; $j < count($array[$i]['wordForms'][$type]); $j++) {
                    $word = array_key_first($array[$i]['wordForms'][$type][$j]);
                    $count = array_shift($array[$i]['wordForms'][$type][$j]);
                    $array[$i]['wordForms'][$type][$j] = [
                        $word => $count
                    ];
                    $array[$i]['wordForms'][$type][$j]['tf'] = round($count / count($textAr), 4);
                    $array[$i]['wordForms'][$type][$j]['idf'] = round(log10(count($textAr) / $count), 4);
                }
            }
        }

        return $array;
    }
}
