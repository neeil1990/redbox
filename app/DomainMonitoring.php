<?php

namespace App;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class DomainMonitoring extends Model
{
    protected $guarded = [];

    protected $table = 'domain_monitoring';

    /**
     * @return HasOne
     */
    public function telegramBot(): HasOne
    {
        return $this->hasOne(TelegramBot::class);
    }

    /**
     * @param $project
     * @return float
     */
    public static function calculateUpTime($project): float
    {
        $created = new Carbon($project->created_at);
        $lastCheck = new Carbon($project->last_check);
        $totalTime = $created->diffInSeconds(Carbon::now());
        if ($project->last_check === null) {
            if ($project->broken) {
                return $project->uptime_percent = 0;
            } else {
                $project->up_time = $totalTime;
                return $project->uptime_percent = 100;
            }
        }
        if ($project->broken) {
            return $project->uptime_percent = $project->up_time / ($totalTime / 100);
        }

        $project->up_time += $lastCheck->diffInSeconds(Carbon::now());
        return $project->uptime_percent = $project->up_time / ($totalTime / 100);
    }

    /**
     * @param $project
     * @param $oldState
     */
    public static function calculateTotalTimeLastBreakdown($project, $oldState)
    {
        if ((boolean)$oldState == true && (boolean)$project->broken == false) {
            $timeLastBreakdown = new Carbon($project->time_last_breakdown);
            $project->total_time_last_breakdown = $timeLastBreakdown->diffInMinutes(Carbon::now());
        }

        if ((boolean)$oldState == false && (boolean)$project->broken == true) {
            $project->time_last_breakdown = Carbon::now();
        }
    }

    /**
     * @param $project
     * @param $oldState
     */
    public static function sendNotifications($project, $oldState)
    {
        $user = User::where('id', '=', $project->user_id)->first();

        if ((boolean)$oldState == true && (boolean)$project->broken == false) {
//            $user->repairDomenNotification($project);
            if ($user->telegram_bot_active) {
                TelegramBot::repairedDomenNotification($project, $user->chat_id);
                $project->time_last_notification = Carbon::now();
            }
        }

        if ((boolean)$oldState == false && (boolean)$project->broken == true) {
//            $user->brokenDomenNotification($project);
            if ($user->telegram_bot_active) {
                TelegramBot::brokenDomenNotification($project, $user->chat_id);
                $project->time_last_notification = Carbon::now();
            }
        }

        if ((boolean)$oldState == true && (boolean)$project->broken == true) {
            $user->brokenDomenNotification($project);
            $lastNotification = new Carbon($project->time_last_notification);
            if ($lastNotification->diffInMinutes(Carbon::now()) > 60) {
                TelegramBot::brokenDomenNotification($project, $user->chat_id);
                $project->time_last_notification = Carbon::now();
            }
        }

    }

    /**
     * @param $body
     * @param $project
     */
    public static function searchPhrase($body, $project)
    {
        if (preg_match_all('(' . $project->phrase . ')', $body, $matches, PREG_SET_ORDER)) {
            $project->status = __('Everything all right');
            $project->broken = false;
        } else {
            $project->status = __('Keyword not found');
            $project->broken = true;
        }
    }

    public static function httpCheck($project)
    {
        $oldState = $project->broken;
        try {
            $client = new Client();
            $res = $client->request('get', $project->link);
            if ($res->getStatusCode() === 200) {
                if (isset($project->phrase)) {
                    DomainMonitoring::searchPhrase($res->getBody()->getContents(), $project);
                } else {
                    $project->status = __('Everything all right');
                    $project->broken = false;
                }
            } else {
                $project->status = __('The response code is not 200');
                $project->broken = true;
            }
            $project->code = $res->getStatusCode();
        } catch (Exception $e) {
            $project->code = $e->getCode();
            $project->status = __('The domain is not responding');
            $project->broken = true;
        }
        DomainMonitoring::calculateTotalTimeLastBreakdown($project, $oldState);
        DomainMonitoring::sendNotifications($project, $oldState);
        DomainMonitoring::calculateUpTime($project);
        $project->last_check = Carbon::now();
        $project->save();
    }
}
