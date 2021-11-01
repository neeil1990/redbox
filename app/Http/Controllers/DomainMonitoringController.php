<?php

namespace App\Http\Controllers;

use App\DomainMonitoring;
use App\TelegramBot;
use App\User;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class DomainMonitoringController extends Controller
{
    public function index()
    {
        $projects = DomainMonitoring::where('user_id', '=', Auth::id())->get();
        if (count($projects) === 0) {
            return $this->createView();
        }

        return view('domain-monitoring.index', compact('projects'));
    }

    public function createView()
    {
        return view('domain-monitoring.create');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $monitoring = new DomainMonitoring($request->all());
        $monitoring->user_id = Auth::id();
        $monitoring->save();

        $bot = new TelegramBot();
        $bot->domain_monitoring_id = $monitoring->id;
        $bot->token = Str::random(40);
        $bot->save();

        return Redirect::route('domain.monitoring');
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function remove($id): RedirectResponse
    {
        DomainMonitoring::destroy($id);
        flash()->overlay(__('Monitoring was successfully deleted'), ' ')->success();

        return Redirect::route('domain.monitoring');
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function checkLink($id): RedirectResponse
    {
        $project = DomainMonitoring::findOrFail($id);
        $this->httpCheck($project);

        return Redirect::back();
    }

    public function httpCheck($project)
    {
        $oldState = $project->broken;
        try {
            $client = new Client();
            $res = $client->request('get', $project->link);
            if ($res->getStatusCode() === 200) {
                if (isset($project->phrase)) {
                    $this->searchPhrase($res->getBody()->getContents(), $project);
                } else {
                    $project->status = 'Всё в порядке';
                    $project->broken = false;
                    $project->send_notification = false;
                }
            } else {
                $project->status = 'Домен не отвечает';
                $project->broken = true;
            }
            $project->code = $res->getStatusCode();
        } catch (Exception $e) {
            $project->code = $e->getCode();
            $project->status = 'Домен не отвечает';
            $project->broken = true;
        }
        $this->sendNotifications($project, $oldState);
        $project->uptime_percent = $this->calculateUpTime($project);
        $project->last_check = Carbon::now();
        $project->save();
    }

    public function sendNotifications($project, $oldState)
    {
        if ($oldState && !$project->broken && $project->telegramBot->active) {
            TelegramBot::repairedDomenNotification($project);
        }

        if ($project->broken && !$project->send_notification) {
            $project->send_notification = true;
            User::find(Auth::id())->brokenDomenNotification($project);
            if ($project->telegramBot->active) {
                TelegramBot::brokenDomenNotification($project);
            }
        }
    }

    /**
     * @param $project
     * @return float
     */
    public function calculateUpTime($project): float
    {
        $created = new Carbon($project->created_at);
        $lastCheck = new Carbon($project->last_check);
        $totalTime = $created->diffInSeconds(Carbon::now());
        if ($project->last_check === null) {
            if ($project->broken) {
                return 0;
            } else {
                $project->up_time = $totalTime;
                return 100;
            }
        }
        if ($project->broken) {
            return $project->up_time / ($totalTime / 100);
        }

        $project->up_time += $lastCheck->diffInSeconds(Carbon::now());
        return $project->up_time / ($totalTime / 100);
    }

    /**
     * @param $body
     * @param $project
     */
    public function searchPhrase($body, $project)
    {
        if (preg_match_all('(' . $project->phrase . ')', $body, $matches, PREG_SET_ORDER)) {
            if (count($matches) > 0) {
                $project->status = 'Всё в порядке';
                $project->broken = false;
                $project->send_notification = false;
            }
        } else {
            $project->status = 'Ключевая фраза не найдена';
            $project->broken = true;
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function edit(Request $request): JsonResponse
    {
        if (strlen($request->option) > 0) {
            DomainMonitoring::where('id', $request->id)->update([
                $request->name => $request->option,
            ]);
            return response()->json([]);
        }
        return response()->json([], 400);
    }
}
