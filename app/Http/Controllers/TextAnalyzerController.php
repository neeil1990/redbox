<?php

namespace App\Http\Controllers;

use App\Common;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\VarDumper\VarDumper;


class TextAnalyzerController extends Controller
{

    public function index()
    {
        return view('text-analyzer.index');
    }

    /**
     * @param Request $request
     * @return array|Application|Factory|RedirectResponse|View|mixed
     */
    public function analyze(Request $request)
    {
        if (isset($request->link)) {
            $html = Common::curlInit($request->link);
            if ($html == false) {
                flash()->overlay('connection attempt failed', ' ')->error();
            } else {
                $text = Common::deleteEverythingExceptCharacters($html);
                if (isset($request->listWords)) {
                    $text = TextAnalyzerController::removeWords($request->listWords, $text);
                }
                if (isset($request->conjunctionsPrepositionsPronouns)) {
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
                        'без', 'безо', 'близ', 'в', 'во', 'вместо', 'вне', 'для', 'до', 'за', 'из',
                        'изо', 'из-за', 'из-под', 'к', 'ко', 'кроме', 'между', 'меж', 'на', 'над',
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
                    $union = [
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
                        'нежели', 'не столько…, сколько', 'не то…, не то', 'не только не…, но и',
                        'не только…, но и', 'не только…., а и', 'не только…, но даже', 'невзирая на то что',
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
                }

                dd($text);
                $response = [];
                $response['textLength'] = Str::length($text);
                $response['countSpaces'] = substr_count($text, ' ');
                $response['lengthWithOutSpaces'] = $response['textLength'] - $response['countSpaces'];
                $response['countWords'] = count(
                    str_word_count(
                        $text, 1, "аАбБвВгГдДеЕёЁжЖзЗиИйЙкКлЛмМнНоОпПрРсСтТуУфФхХцЦчЧшШщЩъыЫьэЭюЮяЯ"
                    )
                );

                return view('text-analyzer.index', ['response' => $response]);
            }
        }

        return Redirect::back();
    }

    /**
     * @param $listWords
     * @param $text
     * @return mixed|string|string[]
     */
    public static function removeWords($listWords, $text)
    {
        $listWords = explode("\r\n", $listWords);
        foreach ($listWords as $listWord) {
            $text = str_replace([$listWord, mb_strtolower($listWord)], '', $text);
        }

        return trim($text);
    }

}
