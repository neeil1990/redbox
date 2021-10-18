<?php

namespace App\Http\Controllers;

use App\BrokenLink;
use App\LinkTracking;
use App\ProjectTracking;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use mysql_xdevapi\Exception;
use Symfony\Component\VarDumper\VarDumper;

class BacklinkController extends Controller
{
    /**
     * @var array $result
     */
    public $result;

    /**
     * BacklinkController constructor.
     */
    public function __construct()
    {
        $this->result = [];
    }

    /**
     * method for cron
     */
    public function scanLinks()
    {
        try {
            $projects = ProjectTracking::all();
            $brokenFlag = false;
            foreach ($projects as $project) {
                foreach ($project->link as $link) {
                    $brokenLink = BrokenLink::find($link->id);
                    if (isset($brokenLink)) {
                        continue;
                    }
                    $this->containsLink($link->site_donor, $link->link, $link->anchor, (boolean)$link->nofollow, (boolean)$link->noindex);
                    if (isset($this->result['error'])) {
                        $this->saveBrokenLink($link->id);
                        $brokenFlag = true;
                    }
                    $this->saveResult($link, $brokenFlag);
                    unset($this->result);
                    $brokenFlag = false;
                    sleep(1);
                }
            }
        } catch (\Exception $exception) {
            Log::debug('scan link error', [$exception]);
        }
    }

    /**
     * @return array|Application|Factory|View|mixed
     */
    public function index()
    {
        $tracking = ProjectTracking::where('user_id', '=', Auth::id())->get();
        $totalBrokenLinks = 0;
        foreach ($tracking as $track) {
            foreach ($track->link as $link) {
                if ((boolean)$link->broken) {
                    $totalBrokenLinks++;
                }
            }
        }

        return view('backlink.index', compact('tracking', 'totalBrokenLinks'));
    }

    /**
     * @return array|Application|Factory|View|mixed
     */
    public function createView()
    {
        return view('backlink.create');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $phrases = $this->getParams($request->params);
        if ($this->isParserError($phrases)) {
            flash()->overlay(__('Format in invalid'), ' ')->error();
            return Redirect::refresh();
        }

        $projectTracking = new ProjectTracking();
        $projectTracking->user_id = Auth::id();
        $projectTracking->project_name = $request->project_name;
        $projectTracking->total_link = count($phrases);
        if ($projectTracking->save()) {
            foreach ($phrases as $phrase) {
                $linkParams = explode("::", $phrase);
                $tracking = new LinkTracking([
                    'project_tracking_id' => $projectTracking->id,
                    'site_donor' => $linkParams[0],
                    'link' => $linkParams[1],
                    'anchor' => $linkParams[2],
                    'nofollow' => $linkParams[3],
                    'noindex' => $linkParams[4],
                    'yandex' => $linkParams[5],
                    'google' => $linkParams[6],
                ]);
                $tracking->save();
            }
        }
        flash()->overlay(__('Tracking was successfully created'), ' ')->success();

        return Redirect::route('backlink');
    }

    /**
     * @param $params
     * @return false|string[]
     */
    public function getParams($params)
    {
        return explode("\r\n", $params);
    }

    /**
     * @param $phrases
     * @return bool
     */
    public function isParserError($phrases): bool
    {
        foreach ($phrases as $phrase) {
            $linkParams = explode("::", $phrase);
            if (count($linkParams) !== 7) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function remove($id): RedirectResponse
    {
        ProjectTracking::destroy($id);
        flash()->overlay(__('Tracking was successfully deleted'), ' ')->success();

        return Redirect::route('backlink');
    }

    /**
     * @param $id
     * @return array|Application|Factory|View|mixed
     */
    public function show($id)
    {
        $project = ProjectTracking::findOrFail($id);

        return view('backlink.show', compact('project'));
    }

    /**
     * @param $id
     * @return array|Application|Factory|View|mixed
     */
    public function checkLink($id)
    {
        $brokenLink = BrokenLink::find($id);
        $target = LinkTracking::findOrFail($id);
        $brokenFlag = false;
        if (isset($brokenLink)) {
            flash()->overlay('Эта ссылка уже была проверена и она содержит ошибку', ' ')->error();
            return Redirect::route('show.backlink', $target->project_tracking_id);
        }
        $this->containsLink($target->site_donor, $target->link, $target->anchor, $target->nofollow, $target->noindex);
        if (isset($this->result['error'])) {
            flash()->overlay($this->result['error'], ' ')->error();
            $this->saveBrokenLink($id);
            $brokenFlag = true;
        }
        $this->saveResult($target, $brokenFlag);

        return Redirect::route('show.backlink', $target->project_tracking_id);
    }

    /**
     * @param $page_url
     * @param $link_url
     * @param $anchor
     * @param false $nofollow
     * @param false $noindex
     */
    public function containsLink($page_url, $link_url, $anchor, $nofollow = false, $noindex = false)
    {
        $html = $this->curlInit($page_url);
        if ($html == false) {
            $this->result['error'] = 'сайт донор не существует';
        } else {
            if ((boolean)$noindex) {
                $this->searchNoindex($html, $link_url, $anchor);
            }
            if (!isset($this->result['error'])) {
                $link = $this->searchLinksOnPage($html, $link_url, $anchor);
                if ((boolean)$nofollow && isset($link)) {
                    $this->searchNofollow($link[0]);
                }
            }
        }
    }

    /**
     * @param $page_url
     * @return bool|string
     */
    public function curlInit($page_url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $page_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
//        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        $html = curl_exec($curl);
        curl_close($curl);
        if (!$html) {
            return false;
        }
        $html = preg_replace('//i', '', $html);

        return $html;
    }

    /**
     * @param $html
     * @param $link_url
     * @param $anchor
     * @return null
     */
    public function searchLinksOnPage($html, $link_url, $anchor)
    {
        if (preg_match_all('(<a *href*=*["\']?(' . addslashes($link_url) . ')([\'"]+[^<>]*>*' . addslashes($anchor) . '</a>))', $html, $matches, PREG_SET_ORDER)) {
            $this->result['link'] = 'ссылка найдена, anchor совпадает';
            return array_unique($matches, SORT_REGULAR);
        }
        $this->result['error'] = 'ссылка не найдена или anchor совпадает';
        return null;
    }

    /**
     * @param $match
     * @param $anchor
     */
    public function searchAnchor($match, $anchor)
    {
        if (strpos($match, $anchor) === 'false') {
            $this->result['error'] = 'anchor не совпадает';
        }
        $this->result['anchor'] = 'anchor совпадает';
    }

    /**
     * @param $link
     */
    public function searchNofollow($link)
    {
        if (preg_match('/rel*=*[\'"]?nofollow[\'"]?/i ', $link[0])) {
            $this->result['error'] = 'в атрибуте rel присутствует свойство nofollow ';
        } else {
            $this->result['nofollow'] = 'nofollow отсутствует';
        }
    }

    /**
     * @param $html
     * @param $link_url
     * @param $anchor
     */
    public function searchNoindex($html, $link_url, $anchor)
    {
        if (preg_match_all('(<(!--)?(noindex)(--)?(>)(<a *href*=*["\']?(' . addslashes($link_url) . ')([\'"]+[^<>]*>*' . addslashes($anchor) . '</a>))(<)(!--)(/noindex)(--)?>)', $html, $matches, PREG_SET_ORDER)) {
            $this->result['error'] = 'ссылка помещена в noindex';
        }
    }

    /**
     * @param $target
     * @param false $broken
     */
    public function saveResult($target, $broken = false)
    {
        $target->status = implode(', ', $this->result);
        $target->last_check = date("Y-m-d H:i:s");
        if ($broken) {
            $target->broken = true;
        } else {
            $target->broken = false;
        }
        $target->save();
    }

    /**
     * @param $brokenLinkId
     */
    public function saveBrokenLink($brokenLinkId)
    {
        $brokenLink = new BrokenLink();
        $brokenLink->link_tracking_id = $brokenLinkId;
        $brokenLink->status = $this->result['error'];
        $brokenLink->save();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function storeLink(Request $request): RedirectResponse
    {
        $phrases = $this->getParams($request->params);
        if ($this->isParserError($phrases)) {
            flash()->overlay(__('Format in invalid'), ' ')->error();
            return Redirect::route('add.link.view', $request->id);
        }

        foreach ($phrases as $phrase) {
            $linkParams = explode("::", $phrase);
            $tracking = new LinkTracking([
                'project_tracking_id' => $request->id,
                'site_donor' => $linkParams[0],
                'link' => $linkParams[1],
                'anchor' => $linkParams[2],
                'nofollow' => $linkParams[3],
                'noindex' => $linkParams[4],
                'yandex' => $linkParams[5],
                'google' => $linkParams[6],
            ]);
            $tracking->save();
            $project = ProjectTracking::find($request->id);
            $project->increment('total_link');
        }
        flash()->overlay(__('Tracking was successfully created'), ' ')->success();

        return Redirect::route('backlink');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function editLink(Request $request): JsonResponse
    {
        if (strlen($request->option) > 0) {
            $link = LinkTracking::where('id', $request->id)->update([
                $request->name => $request->option,
            ]);
            BrokenLink::where('link_tracking_id', $request->id)->delete();
            return response()->json([], 200);
        }
        return response()->json([], 400);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function editBacklink(Request $request): JsonResponse
    {
        if (strlen($request->option) > 0) {
            ProjectTracking::where('id', $request->id)->update([
                $request->name => $request->option
            ]);
            return response()->json([], 200);
        }
        return response()->json([], 400);
    }

    /**
     * @param $id
     * @return array|Application|Factory|View|mixed
     */
    public function addLinkView($id)
    {
        return view('backlink.add-link', compact('id'));
    }

    /**
     * @param $id
     * @return array|Application|Factory|View|mixed
     */
    public function removeLink($id)
    {
        $link = LinkTracking::findOrFail($id);
        $project = ProjectTracking::findOrFail($link->project_tracking_id);
        $project->decrement('total_link');
        LinkTracking::destroy($id);
        flash()->overlay(__('Link was successfully deleted'), ' ')->success();

        return Redirect::route('show.backlink', $link->project_tracking_id);
    }
}
