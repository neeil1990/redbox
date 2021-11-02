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
     * @param $project
     * @param $oldState
     */
    public static function sendNotifications($project, $oldState)
    {
        if ($oldState && !$project->broken) {
//            User::find($project->user_id)->repairDomenNotification($project);
            if ($project->telegramBot->active) {
                TelegramBot::repairedDomenNotification($project);
            }
        }

        if (!$oldState && $project->broken) {
//            User::find($project->user_id)->brokenDomenNotification($project);
            if ($project->telegramBot->active) {
                TelegramBot::brokenDomenNotification($project);
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
            if (count($matches) > 0) {
                $project->status = 'Всё в порядке';
                $project->broken = false;
            }
        } else {
            $project->status = 'Ключевая фраза не найдена';
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
                    $project->status = 'Всё в порядке';
                    $project->broken = false;
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
        DomainMonitoring::sendNotifications($project, $oldState);
        $project->uptime_percent = DomainMonitoring::calculateUpTime($project);
        $project->last_check = Carbon::now();
        $project->save();
    }
}
