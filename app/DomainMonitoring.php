<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\VarDumper\VarDumper;

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
        if ($project->send_notification) {
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
                $lastNotification = new Carbon($project->time_last_notification);
                if ($lastNotification->diffInMinutes(Carbon::now()) >= 360) {
                    $user->brokenDomenNotification($project);
                    if ($user->telegram_bot_active) {
                        TelegramBot::brokenDomenNotification($project, $user->chat_id);
                    }
                    $project->time_last_notification = Carbon::now();
                }
            }
        }
    }

    public static function httpCheck($project)
    {
        $curl = DomainMonitoring::curlInit($project);
        try {
            $oldState = $project->broken;
            if (isset($curl) && $curl[1]['http_code'] === 200) {
                if (isset($project->phrase)) {
                    DomainMonitoring::searchPhrase($curl, $project->phrase, $project);
                } else {
                    $project->status = 'Everything all right';
                    $project->broken = false;
                }
                $project->code = 200;
            } else {
                $project->status = 'unexpected response code';
                $project->code = $curl[1]['http_code'];
                $project->broken = true;
                Log::debug('broken project', [$project]);
                Log::debug('broken project', [$curl]);
            }
        } catch (\Exception $e) {
            Log::debug('broken project', [$project]);
            Log::debug('broken project', [$curl]);
            $project->status = 'the domain did not respond';
            $project->code = 0;
            $project->broken = true;
        }
        DomainMonitoring::calculateTotalTimeLastBreakdown($project, $oldState);
        DomainMonitoring::calculateUpTime($project);
        DomainMonitoring::sendNotifications($project, $oldState);
        $project->last_check = Carbon::now();
        $project->save();
    }

    /**
     * @param $project
     * @return array|false
     */
    public static function curlInit($project)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $project->link);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $project->waiting_time);
        curl_setopt($curl, CURLOPT_TIMEOUT, $project->waiting_time);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        $html = curl_exec($curl);
        $headers = curl_getinfo($curl);
        curl_close($curl);
        if (!$html) {
            return null;
        }
        $html = preg_replace('//i', '', $html);
        return [$html, $headers];
    }

    /**
     * @param $curl
     * @param $phrase
     * @param $project
     */
    public static function searchPhrase($curl, $phrase, $project)
    {
        $body = $curl[0];
        $contentType = $curl[1]['content_type'];
        if (preg_match('(.*?charset=(.*))', $contentType, $contentType, PREG_OFFSET_CAPTURE)) {
            $contentType = str_replace(array("\r", "\n"), '', $contentType[1][0]);
            $phrase = mb_convert_encoding($project->phrase, $contentType);
        }

        if (preg_match_all('(' . $phrase . ')', $body, $matches, PREG_SET_ORDER)) {
            $project->status = 'Everything all right';
            $project->broken = false;
        } else {
            $project->status = 'Keyword not found';
            $project->broken = true;
            Log::debug('keyword not found', [$project]);
        }
    }
}
