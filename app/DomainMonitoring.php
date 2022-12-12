<?php

namespace App;

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
     * @return float|int|void
     */
    public static function calculateUpTime($project)
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
        $project->uptime_percent = $project->up_time / ($totalTime / 100);
        $project->save();
    }

    /**
     * @param $project
     * @param $oldState
     */
    public static function calculateTotalTimeLastBreakdown($project, $oldState)
    {
        if ($oldState && !$project->broken) {
            $timeLastBreakdown = new Carbon($project->time_last_breakdown);
            $project->total_time_last_breakdown = $timeLastBreakdown->diffInMinutes(Carbon::now());
        }

        if (!$oldState && $project->broken) {
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
            if ($oldState && !$project->broken) {
                $user->repairDomainNotification($project);
                if ($user->telegram_bot_active) {
                    TelegramBot::repairedDomainNotification($project, $user->chat_id);
                    $project->time_last_notification = Carbon::now();
                }
            }

            if (!$oldState && $project->broken) {
                $user->brokenDomainNotification($project);
                if ($user->telegram_bot_active) {
                    TelegramBot::brokenDomainNotification($project, $user->chat_id);
                    $project->time_last_notification = Carbon::now();
                }
            }

            $lastNotification = new Carbon($project->time_last_notification);
            if ($oldState && $project->broken && $lastNotification->diffInMinutes(Carbon::now()) >= 360) {
                $user->brokenDomainNotification($project);
                if ($user->telegram_bot_active) {
                    TelegramBot::brokenDomainNotification($project, $user->chat_id);
                }
                $project->time_last_notification = Carbon::now();
            }
        }
    }

    public static function httpCheck($project)
    {
        $oldState = $project->broken;
        $curl = DomainMonitoring::curlInit($project);
        if (isset($curl) && $curl[1]['http_code'] === 200) {
            if (isset($project->phrase)) {
                DomainMonitoring::searchPhrase($curl, $project);
            } else {
                $project->status = 'Everything all right';
                $project->broken = false;
            }
            $project->code = 200;
        } else {
            $project->status = 'Unexpected response code';
            $project->code = $curl[1]['http_code'];
            $project->broken = true;
        }

        $project->last_check = Carbon::now();
        DomainMonitoring::calculateTotalTimeLastBreakdown($project, $oldState);
        DomainMonitoring::calculateUpTime($project);
        $project->save();

        DomainMonitoring::sendNotifications($project, $oldState);
    }

    public static function curlInit($project): ?array
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $project->link);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_COOKIE, 'realauth=SvBD85dINu3; expires=Sat, 25 Feb 2030 02:16:43 GMT; path=/; SameSite=Lax');
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $project->waiting_time);
        curl_setopt($curl, CURLOPT_TIMEOUT, $project->waiting_time);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);

        return DomainMonitoring::tryConnect($curl);
    }

    /**
     * @param $curl
     * @return array|null
     */
    public static function tryConnect($curl): ?array
    {
        $html = null;
        $headers = null;
        $userAgents = [
            //Mozilla Firefox
            'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',
            'Mozilla/5.0 (Windows NT 10.0; rv:87.0) Gecko/20100101 Firefox/87.0',
//            opera
            'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.43 (KHTML, like Gecko) Chrome/93.0.4577.82 Safari/537.36 OPR/79.0.4143.72',
            'Mozilla/5.0 (Windows NT 6.3) AppleWebKit/537.43 (KHTML, like Gecko) Chrome/93.0.4577.82 Safari/537.36 OPR/79.0.4143.72',
            // chrome
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.63 Safari/537.36',
            'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.159 Safari/537.36'
        ];

        for ($i = 0; $i < count($userAgents); $i++) {
            curl_setopt($curl, CURLOPT_USERAGENT, $userAgents[$i]);
            $html = curl_exec($curl);
            $headers = curl_getinfo($curl);
            if (curl_error($curl) == "transfer closed with outstanding read data remaining") {
                curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            }

            if ($headers['http_code'] == 200 && $html) {
                $html = preg_replace('//i', '', $html);
                break;
            }
        }
        curl_close($curl);
        return [$html, $headers];
    }

    /**
     * @param $curl
     * @param $project
     */
    public static function searchPhrase($curl, $project)
    {
        $contentType = $curl[1]['content_type'];
        if (preg_match('(.*?charset=(.*))', $contentType, $contentType, PREG_OFFSET_CAPTURE)) {
            $contentType = str_replace(["\r", "\n"], '', $contentType[1][0]);
            $phrase = mb_convert_encoding($project->phrase, $contentType);
        } else {
            $phrase = $project->phrase;
        }

        $html = str_replace('>', '> ', $curl[0]);
        $text = trim(str_replace(chr(194) . chr(160), ' ', html_entity_decode($html)));
        $text = preg_replace('/[^a-zа-яё\w\s]/ui', ' ', $text);
        $text = preg_replace("/&#?[a-z]+;/i", "", $text);
        $text = str_replace([
            "\n", "\t", "\r",
            "»", "«", ".", ",", "!", "?",
            "(", ")", "+", ";", ":", "-",
            "₽", "$", "/", "[", "]", "“"
        ], ' ', $text);
        $text = preg_replace("/\d/", "", $text);
        $text = str_replace("ё", "е", $text);
        $text = preg_replace('| +|', ' ', $text);

        try {
            if (preg_match_all('(' . $phrase . ')', $text, $matches, PREG_SET_ORDER)) {
                $project->status = 'Everything all right';
                $project->broken = false;
            } else {
                $project->status = 'Keyword not found';
                $project->broken = true;
            }
        } catch (\Throwable $e) {
            Log::debug('domain monitoring search phrase error', [$project]);
            $project->status = 'Keyword not found';
            $project->broken = true;
        }
    }
}
