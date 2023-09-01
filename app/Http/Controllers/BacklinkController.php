<?php

namespace App\Http\Controllers;

use App\Classes\SimpleHtmlDom\HtmlDocument;
use App\LinkTracking;
use App\ProjectTracking;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class BacklinkController extends Controller
{
    protected $result;

    protected $error;

    protected $node = false;

    protected $noIndex = null;

    protected $noFollow = null;

    public function __construct()
    {
        $this->middleware(['permission:Backlink']);
    }

    public function index()
    {
        $backlinks = ProjectTracking::where('user_id', '=', Auth::id())->get();
        if (count($backlinks) === 0) {
            return $this->createView();
        }

        return view('backlink.index', compact('backlinks'));
    }

    public function createView()
    {
        /** @var User $user */
        $user = Auth::user();
        if ($tariff = $user->tariff()) {
            $count = ProjectTracking::where('user_id', '=', $user->id)->count();
            $tariff = $tariff->getAsArray();
            if (array_key_exists('BacklinkProject', $tariff['settings'])) {

                if ($count >= $tariff['settings']['BacklinkProject']['value']) {
                    if ($tariff['settings']['BacklinkProject']['message'])
                        flash()->overlay($tariff['settings']['BacklinkProject']['message'], __('Error'))->error();

                    return redirect('backlink');
                }
            }
        }

        $monitoring = $this->getMonitoringOptions();

        return view('backlink.create', compact('monitoring'));
    }

    public function addLinkView($id)
    {
        return view('backlink.add-backlink', compact('id'));
    }

    public function remove($id): RedirectResponse
    {
        ProjectTracking::destroy($id);
        flash()->overlay(__('Tracking was successfully deleted'), ' ')->success();

        return Redirect::route('backlink');
    }

    public function show($id)
    {
        $project = ProjectTracking::findOrFail($id);
        $monitoring = $this->getMonitoringOptions();

        return view('backlink.show', compact('project', 'monitoring'));
    }

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

    public function editBacklink(Request $request): JsonResponse
    {
        ProjectTracking::where('id', $request->id)->update([
            $request->name => $request->option
        ]);

        return response()->json([]);
    }

    public function removeLink($id): RedirectResponse
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

    public function store(Request $request): RedirectResponse
    {
        if (isset($request->countRows)) {

            if ($this->checkLinks((int)$request->countRows))
                return redirect()->route('backlink');

            $this->simplifiedCreate($request);
        } else {

            $phrases = $this->getParams($request->params);

            if ($this->isParserError($phrases)) {
                flash()->overlay(__('Invalid format'), ' ')->error();
                return Redirect::refresh();
            }

            if ($this->checkLinks(count($phrases)))
                return redirect()->route('backlink');

            $this->expressCreate($request, $phrases);
        }

        flash()->overlay(__('Tracking was successfully created'), ' ')->success();

        return Redirect::route('backlink');
    }

    protected function checkLinks(int $count): bool
    {
        /** @var User $user */
        $user = Auth::user();
        if ($tariff = $user->tariff()) {
            $tariff = $tariff->getAsArray();
            if (array_key_exists('BacklinkLinks', $tariff['settings'])) {

                if ($count > $tariff['settings']['BacklinkLinks']['value']) {

                    if ($tariff['settings']['BacklinkLinks']['message'])
                        flash()->overlay($tariff['settings']['BacklinkLinks']['message'], __('Error'))->error();

                    return true;
                }
            }
        }

        return false;
    }

    public function expressCreate($request, $phrases)
    {
        if (empty($request->id)) {
            $projectTracking = new ProjectTracking();
            $projectTracking->user_id = Auth::id();
            $projectTracking->monitoring_project_id = $request->input('monitoring_project_id', null);
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
            ]);

            $tracking->save();
        }
    }

    public function simplifiedCreate($request)
    {
        $request = $request->all();
        if (empty($request['id'])) {
            $projectTracking = new ProjectTracking();
            $projectTracking->user_id = Auth::id();
            $projectTracking->monitoring_project_id = $request['monitoring_project_id'];
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
            ]);

            $tracking->save();
        }
    }

    public function getParams($params)
    {
        return explode("\r\n", $params);
    }

    public function isParserError($phrases): bool
    {
        foreach ($phrases as $phrase) {
            $linkParams = explode("::", $phrase);
            if (count($linkParams) !== 5) {
                return true;
            }
        }
        return false;
    }

    public function checkLink($id): RedirectResponse
    {
        $site = LinkTracking::findOrFail($id);
        $this->analyseLink($site);

        if (isset($this->error)) {
            flash()->overlay($this->error, ' ')->error();
            $this->saveResult($site, true);
            //add tg message
        } else {
//            flash()->overlay($this->result['result'], ' ')->success();
            $this->saveResult($site, false);
        }

        return Redirect::route('show.backlink', $site->project_tracking_id);
    }

    public function analyseLink($project)
    {
        $html = $this->curlInit($project->site_donor);

        if (!$html) {
            $this->error = 'the donor site does not exist';
        } else {
            $this->searchLink($html, $project);
        }
    }

    private function searchLink($html, $project)
    {
        $document = new HtmlDocument();
        $document->load(mb_strtolower($html));

        $this->searchNoIndex($document, $project);

        if ($this->node === false) {
            $elem = $document->find('a[href="' . $project->link . '"]');

            if ($elem !== []) {
                foreach ($elem as $node) {
                    foreach ($node->_ as $text) {
                        if ($text === mb_strtolower($project->anchor)) {
                            $this->result = 'Link found, anchor matches.';
                            $this->node = $node;
                            break 2;
                        }
                    }

                    foreach ($node->children as $child) {
                        foreach ($child->_ as $text) {
                            if ($text === mb_strtolower($project->anchor)) {
                                $this->node = $child;
                                $this->result = 'Link found, anchor matches.';
                                break 3;
                            }
                        }
                    }
                }
            } else {
                $this->error = 'Link not found.';
            }
        }

        $issetNofollow = false;
        if ($project->nofollow && $this->node !== false) {
            foreach ($this->node->attr as $attribute => $value) {
                if ($attribute === 'rel' && $value === 'nofollow') {
                    $issetNofollow = true;
                    break;
                }
            }

            if ($issetNofollow) {
                $this->noFollow = 'Link have attribute nofollow.';
            } else {
                $this->noFollow = 'Link dont have attribute nofollow.';
            }
        }
    }

    private function searchNoIndex($document, $project)
    {
        $elem = $document->find('noindex');

        if ($elem !== []) {
            foreach ($elem as $node) {
                $this->searchNoIndexLink($node->children[0], $project);
            }
        }

        if ($this->noIndex === null) {
            $this->noIndex = 'Link dont placed in noindex.';
        }
    }

    private function searchNoIndexLink($node, $project)
    {
        if ($node->tag === 'a') {
            foreach ($node->_ as $text) {
                if ($text === mb_strtolower($project->anchor)) {
                    $this->noIndex = $project->noindex ? 'Link found, anchor matches, link placed in noindex.' : 'Link found, anchor matches.';
                    $this->node = $node;
                    return;
                }
            }
        } else {
            $this->searchNoIndexLink($node->children[0], $project);
        }
    }

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

    public function saveResult($target, $broken, $sendNotification = false)
    {
        $target->status = preg_replace('/\s+/u', ' ', "$this->result $this->noIndex $this->noFollow");

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

        if ($sendNotification) {
            $target->mail_sent = $sendNotification;
        }

        $target->broken = $broken;
        $target->save();
    }

    public function increment($project_tracking_id)
    {
        $article = ProjectTracking::find($project_tracking_id);
        $article->increment('total_broken_link');
    }

    public function decrement($project_tracking_id)
    {
        $article = ProjectTracking::find($project_tracking_id);
        if ($article->total_broken_link != 0) {
            $article->decrement('total_broken_link');
        }
    }

    private function getMonitoringOptions(): array
    {
        /** @var User $user */
        $user = Auth::user();
        $options = [null => ' ' . __('Select an option')];

        foreach ($user->monitoringProjects as $item)
            $options[$item['id']] = $item['name'];

        return $options;
    }
}
