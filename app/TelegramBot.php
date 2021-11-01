<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramBot extends Model
{
    protected $guarded = [];

    protected $table = 'telegram_bot';

    public static function brokenDomenNotification($project)
    {
        $uptimePercent = round($project->uptime_percent, 2);

        Telegram::sendMessage([
            'chat_id' => $project->telegramBot->chat_id,
            'text' => "Внимание: проект <code>{$project->project_name}</code> стал <code>недоступен</code>
<code>{$project->last_check}</code>
Состояние: <code>{$project->status}</code>
Текущий uptime: <code>{$uptimePercent}%</code>
Перейти в сервис:
<a href='https://lk.redbox.su/domain-monitoring'>https://lk.redbox.su/domain-monitoring</a>",
            'parse_mode' => 'HTML',
        ]);
    }

    public static function repairedDomenNotification($project)
    {
        $uptimePercent = round($project->uptime_percent, 2);

        Telegram::sendMessage([
            'chat_id' => $project->telegramBot->chat_id,
            'text' => "Внимание: проект <code>{$project->project_name}</code> стал <code>доступен</code>
В <code>{$project->last_check}</code>
Состояние: <code>{$project->status}</code>
Текущий uptime: <code>{$uptimePercent}%</code>
Перейти в сервис:
<a href='https://lk.redbox.su/domain-monitoring'>https://lk.redbox.su/domain-monitoring</a>",
            'parse_mode' => 'HTML',
        ]);
    }
}
