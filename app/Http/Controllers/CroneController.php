<?php

namespace App\Http\Controllers;

use App\DomainInformation;
use App\DomainMonitoring;
use App\LinkTracking;
use Illuminate\Support\Facades\Log;
use PHPUnit\Exception;

class CroneController extends Controller
{
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
            DomainInformation::checkDomainSock($project);
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
                    $this->containsLink($link->site_donor,
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
                    unset($this->result);
                    sleep(1);
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
                    $this->containsLink(
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
                    unset($this->result);
                    sleep(1);
                }
            }
        } catch (\Exception $exception) {
            Log::debug('scan link', [$exception]);
        }
    }
}
