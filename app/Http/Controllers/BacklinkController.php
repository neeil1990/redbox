<?php

namespace App\Http\Controllers;

use App\LinkTracking;
use App\ProjectTracking;
use App\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

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
        $this->middleware(['permission:Backlink']);

        $this->result = [];
    }

    /**
     * @return array|Application|Factory|View|mixed
     */
    public function index()
    {
        $backlinks = ProjectTracking::where('user_id', '=', Auth::id())->get();
        if (count($backlinks) === 0) {
            return $this->createView();
        }

        return view('backlink.index', compact('backlinks'));
    }

    /**
     * @return array|Application|Factory|View|mixed
     */
    public function createView()
    {
        /** @var User $user */
        $user = Auth::user();
        if($tariff = $user->tariff()){
            $count = ProjectTracking::where('user_id', '=', $user->id)->count();
            $tariff = $tariff->getAsArray();
            if (array_key_exists('BacklinkProject', $tariff['settings'])) {

                if($count >= $tariff['settings']['BacklinkProject']['value']){
                    if($tariff['settings']['BacklinkProject']['message'])
                        flash()->overlay($tariff['settings']['BacklinkProject']['message'], __('Error'))->error();

                    return redirect('backlink');
                }
            }
        }

        return view('backlink.create');
    }

    /**
     * @param $id
     * @return array|Application|Factory|View|mixed
     */
    public function addLinkView($id)
    {
        return view('backlink.add-backlink', compact('id'));
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
     * @param Request $request
     * @return RedirectResponse
     */
    public function storeLink(Request $request): RedirectResponse
    {
        if (isset($request->countRows)) {
            $this->simplifiedCreate($request);
        } else {
            $phrases = $this->getParams($request->params);
            if ($this->isParserError($phrases)) {
                flash()->overlay(__('Invalid format'), ' ')->error();
                return Redirect::back();
            }
            $this->expressCreate($request, $phrases);
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
            LinkTracking::where('id', $request->id)->update([
                $request->name => $request->option,
            ]);
            return response()->json([]);
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
            return response()->json([]);
        }
        return response()->json([], 400);
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
        if ($link->broken) {
            $project->decrement('total_broken_link');
        }
        LinkTracking::destroy($id);
        flash()->overlay(__('Link was successfully deleted'), ' ')->success();

        return Redirect::route('show.backlink', $link->project_tracking_id);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        if (isset($request->countRows)) {

            if($this->checkLinks((int)$request->countRows))
                return redirect()->route('backlink');

            $this->simplifiedCreate($request);
        } else {

            $phrases = $this->getParams($request->params);

            if ($this->isParserError($phrases)) {
                flash()->overlay(__('Invalid format'), ' ')->error();
                return Redirect::refresh();
            }

            if($this->checkLinks(count($phrases)))
                return redirect()->route('backlink');

            $this->expressCreate($request, $phrases);
        }

        flash()->overlay(__('Tracking was successfully created'), ' ')->success();
        return Redirect::route('backlink');
    }

    protected function checkLinks(int $count)
    {
        /** @var User $user */
        $user = Auth::user();
        if($tariff = $user->tariff()){
            $tariff = $tariff->getAsArray();
            if (array_key_exists('BacklinkLinks', $tariff['settings'])) {

                if($count > $tariff['settings']['BacklinkLinks']['value']){

                    if($tariff['settings']['BacklinkLinks']['message'])
                        flash()->overlay($tariff['settings']['BacklinkLinks']['message'], __('Error'))->error();

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $request
     * @param $phrases
     */
    public function expressCreate($request, $phrases)
    {
        if (empty($request->id)) {
            $projectTracking = new ProjectTracking();
            $projectTracking->user_id = Auth::id();
            $projectTracking->project_name = $request->project_name;
            $projectTracking->total_link = count($phrases);
            $projectTracking->save();
        } else {
            $project = ProjectTracking::find($request->id);
            $project->increment('total_link');
        }

        foreach ($phrases as $phrase) {
            $params = explode("::", $phrase);
            $tracking = new LinkTracking([
                'project_tracking_id' => empty($request->id)
                    ? $projectTracking->id
                    : $request->id,
                'site_donor' => $params[0],
                'link' => $params[1],
                'anchor' => $params[2],
                'nofollow' => $params[3],
                'noindex' => $params[4],
                'yandex' => $params[5],
                'google' => $params[6],
            ]);
            $tracking->save();
        }
    }

    /**
     * @param $request
     */
    public function simplifiedCreate($request)
    {
        $request = $request->all();
        if (empty($request['id'])) {
            $projectTracking = new ProjectTracking();
            $projectTracking->user_id = Auth::id();
            $projectTracking->project_name = $request['project_name'];
            $projectTracking->total_link = (integer)$request['countRows'];
            $projectTracking->save();
        } else {
            $project = ProjectTracking::find($request['id']);
            $project->increment('total_link');
        }
        for ($i = 1; $i <= (integer)$request['countRows']; $i++) {
            $tracking = new LinkTracking([
                'project_tracking_id' => empty($request['id'])
                    ? $projectTracking->id
                    : $request['id'],
                'site_donor' => $request['site_donor_' . $i],
                'link' => $request['link_' . $i],
                'anchor' => $request['anchor_' . $i],
                'nofollow' => $request['nofollow_' . $i],
                'noindex' => $request['noindex_' . $i],
                'yandex' => $request['yandex_' . $i],
                'google' => $request['google_' . $i],
            ]);
            $tracking->save();
        }
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
     * @return array|Application|Factory|View|mixed
     */
    public function checkLink($id)
    {
        $target = LinkTracking::findOrFail($id);
        $this->analyseLink(
            $target->site_donor,
            $target->link,
            $target->anchor,
            $target->nofollow,
            $target->noindex
        );
        if (isset($this->result['error'])) {
            flash()->overlay($this->result['error'], ' ')->error();
            $this->saveResult($target, true);
        } else {
            $this->saveResult($target, false, false);
        }

        return Redirect::route('show.backlink', $target->project_tracking_id);
    }

    /**
     * @param $page_url
     * @param $link_url
     * @param $anchor
     * @param false $nofollow
     * @param false $noindex
     */
    public function analyseLink($page_url, $link_url, $anchor, $nofollow = false, $noindex = false)
    {
        $html = $this->curlInit($page_url);
        if ($html == false) {
            $this->result['error'] = __('the donor site does not exist');
        } else {
            if ($noindex) {
                $this->searchNoindex($html, $link_url, $anchor);
            }
            if (!isset($this->result['error'])) {
                $link = $this->searchLinksOnPage($html, $link_url, $anchor);
                if ($nofollow && isset($link)) {
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
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        $html = curl_exec($curl);
        curl_close($curl);
        if (!$html) {
            return false;
        }
        return preg_replace('//i', '', $html);
    }

    /**
     * @param $html
     * @param $link_url
     * @param $anchor
     * @return null|array
     */
    public function searchLinksOnPage($html, $link_url, $anchor): ?array
    {
        if (preg_match_all('(<a *href=["\']?(' . addslashes($link_url) . ')([\'"]+[^<>]*>' . addslashes($anchor) . '</a>))', $html, $matches, PREG_SET_ORDER)) {
            $this->result['link'] = __('link found, anchor matches');
            return array_unique($matches, SORT_REGULAR);
        }
        $this->result['error'] = __('link not found or anchor does not match');
        return null;
    }

    /**
     * @param $match
     * @param $anchor
     */
    public function searchAnchor($match, $anchor)
    {
        if (strpos($match, $anchor) === 'false') {
            $this->result['error'] = __('anchor does not match');
        }
        $this->result['anchor'] = __('anchor matches');
    }

    /**
     * @param $link
     */
    public function searchNofollow($link)
    {
        if (preg_match('/rel*=*[\'"]?nofollow[\'"]?/i ', $link[0])) {
            $this->result['error'] = __('the nofollow property is present in the rel attribute');
        } else {
            $this->result['nofollow'] = __('nofollow is missing');
        }
    }

    /**
     * @param $html
     * @param $link_url
     * @param $anchor
     */
    public function searchNoindex($html, $link_url, $anchor)
    {
        if (preg_match_all('(<!--noindex-->(<a *href=*["\']?(' . addslashes($link_url) . ')([\'"]+[^<>]*>' . addslashes($anchor) . '</a>))<!--/noindex-->)',
            $html,
            $matches,
            PREG_SET_ORDER)) {
            $this->result['error'] = __('the link is placed in noindex');
        } elseif (preg_match_all('(<noindex>(<a *href=*["\']?(' . addslashes($link_url) . ')([\'"]+[^<>]*>' . addslashes($anchor) . '</a>))</noindex>)',
            $html,
            $matches,
            PREG_SET_ORDER)) {
            $this->result['error'] = __('the link is placed in noindex');
        } else {
            $this->result['noindex'] = __('the link is not placed in noindex');
        }
    }

    /**
     * @param $target
     * @param $broken
     * @param $sendMail
     */
    public function saveResult($target, $broken, $sendMail = null)
    {
        $target->status = implode(', ', $this->result);
        $target->last_check = date("Y-m-d H:i:s");
        if ($target->broken == null) {
            if ($broken) {
                $this->increment($target->project_tracking_id);
            } else {
                $this->decrement($target->project_tracking_id);
            }
        } elseif ($target->broken != $broken) {
            if ($broken) {
                $this->increment($target->project_tracking_id);
            } else {
                $this->decrement($target->project_tracking_id);
            }
        }
        if (isset($sendMail)) {
            $target->mail_sent = $sendMail;
        }
        $target->broken = $broken;
        $target->save();
    }

    /**
     * @param $project_tracking_id
     * @return void
     */
    public function increment($project_tracking_id)
    {
        $article = ProjectTracking::find($project_tracking_id);
        $article->increment('total_broken_link');
    }

    /**
     * @param $project_tracking_id
     * @return void
     */
    public function decrement($project_tracking_id)
    {
        $article = ProjectTracking::find($project_tracking_id);
        if ($article->total_broken_link != 0) {
            $article->decrement('total_broken_link');
        }
    }

}
