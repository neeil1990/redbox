<?php

namespace App\Http\Controllers;

use App\DomainInformation;
use App\DomainMonitoring;
use App\LinkTracking;
use App\ProjectTracking;
use Illuminate\Support\Facades\Log;
use PHPUnit\Exception;

class CroneController extends Controller
{

    /**
     * @var array $result
     */
    public $result;

    /**
     * @param $timing
     * @return void
     */
    public function checkLinkCrone($timing)
    {
        Log::debug('start monitoring with timing', [$timing]);
        try {
            $projects = DomainMonitoring::where('timing', '=', $timing)->get();
            foreach ($projects as $project) {
                DomainMonitoring::httpCheck($project);
            }
        } catch (Exception $exception) {
            Log::debug('scan error', [$exception->getMessage()]);
        }
    }

    /**
     * method for cron
     */
    public function checkDomains()
    {
        $projects = DomainInformation::all();
        foreach ($projects as $project) {
            DomainInformation::checkDomain($project);
        }
    }

    /**
     * api method for cron
     */
    public function scanBrokenLinks()
    {
        try {
            $links = LinkTracking::where('broken', '=', true)->get();
            foreach ($links->chunk(5) as $links) {
                foreach ($links as $link) {
                    $this->analyseLink(
                        $link->site_donor,
                        $link->link,
                        $link->anchor,
                        (boolean)$link->nofollow,
                        (boolean)$link->noindex
                    );
                    if (isset($this->result['error'])) {
                        if (!(boolean)$link->mail_sent) {
                            $link->project->user->sendBrokenLinkNotification($this->result['error'], $link);
                            $this->saveResult($link, true, true);
                        } else {
                            $this->saveResult($link, true);
                        }
                    } else {
                        $this->saveResult($link, false, false);
                    }
                }
            }
        } catch (\Exception $exception) {
            Log::debug('scan broken link', [$exception]);
        }
    }


    /**
     * api method for cron
     */
    public function scanLinks()
    {
        try {
            $links = LinkTracking::all();
            foreach ($links->chunk(5) as $links) {
                foreach ($links as $link) {
                    $this->analyseLink(
                        $link->site_donor,
                        $link->link,
                        $link->anchor,
                        (boolean)$link->nofollow,
                        (boolean)$link->noindex
                    );
                    if (isset($this->result['error'])) {
                        $this->saveResult($link, true);
                    } else {
                        $this->saveResult($link, false, false);
                    }
                }
            }
        } catch (\Exception $exception) {
            Log::debug('scan link', [$exception]);
        }
    }

    /**
     * @param $page_url
     * @param $link_url
     * @param $anchor
     * @param bool $nofollow
     * @param bool $noindex
     * @return void
     */
    public function analyseLink($page_url, $link_url, $anchor, bool $nofollow = false, bool $noindex = false)
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
        } elseif ((boolean)$target->broken != $broken) {
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
}
