<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

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
            $user->repairDomenNotification($project);
            if ($user->telegram_bot_active) {
                TelegramBot::repairedDomenNotification($project, $user->chat_id);
                $project->time_last_notification = Carbon::now();
            }
        }

        if ((boolean)$oldState == false && (boolean)$project->broken == true) {
            $user->brokenDomenNotification($project);
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

    public static function httpCheck($project)
    {
        try {
            $oldState = $project->broken;
            $startConnect = Carbon::now();
            $request = self::curlInit($project->link);
            if (isset($request) && $request[1] === 200) {
                if (isset($project->phrase)) {
                    DomainMonitoring::searchPhrase($request[0], $project->phrase, $project);
                } else {
                    $project->status = __('Everything all right');
                    $project->code = $request[1];
                    $project->broken = false;
                }
            } else {
                Log::debug('connect time', [$startConnect->diffInSeconds(Carbon::now())]);
                $project->status = __('The response code is not 200');
                $project->code = $request[1];
                $project->broken = true;
            }
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

    /**
     * @param $url
     * @return array|false
     */
    public static function curlInit($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 6);
        curl_setopt($curl, CURLOPT_TIMEOUT, 6);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        $html = curl_exec($curl);
        $code = curl_getinfo($curl);
        curl_close($curl);
        if (!$html) {
            return null;
        }

        $html = preg_replace('//i', '', $html);

        return [$html, $code['http_code']];
    }

    /**
     * @param $body
     * @param $phrase
     * @param $project
     */
    public static function searchPhrase($body, $phrase, $project)
    {
        if (preg_match('<meta http-equiv="Content-Type" content="text/html; charset=(.*)"*/>', $body, $matches, PREG_OFFSET_CAPTURE)) {
            $phrase = mb_convert_encoding($project->phrase, substr($matches[1][0], 0, -2));
        }

        if (preg_match_all('(' . $phrase . ')', $body, $matches, PREG_SET_ORDER)) {
            $project->status = __('Everything all right');
            $project->broken = false;
        } else {
            $project->status = __('Keyword not found');
            $project->broken = true;
        }
    }
}
