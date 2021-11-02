<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redirect;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramBot extends Model
{
    protected $guarded = [];

    protected $table = 'telegram_bot';

    public static function brokenDomenNotification($project)
    {

        TelegramBot::sendMessage($project, 'недоступен');
    }

    public static function repairedDomenNotification($project)
    {
        TelegramBot::sendMessage($project, 'доступен');
    }

    /**
     * @return mixed
     */
    public static function getUpdates(): array
    {
        $updates = json_decode(file_get_contents('https://api.telegram.org/bot'
            . env('TELEGRAM_BOT_TOKEN', '2073017935:AAHgwY7d0TBAAUzNUyvsmH6QLH14nESQhOc') .
            '/getUpdates'), true);

        return $updates['result'];
    }

    /**
     * @param $token
     * @return bool
     */
    public static function searchToken($token): bool
    {
        $updates = TelegramBot::getUpdates();
        foreach ($updates as $update) {
            if (isset($update['message']) && $update['message']['text'] === $token) {
                TelegramBot::where('token', '=', $token)->update([
                    'active' => 1,
                    'chat_id' => $update['message']['chat']['id']
                ]);
                return true;
            }
        }
        return false;
    }

    public static function sendMessage($project, $status)
    {
        $uptimePercent = round($project->uptime_percent, 2);

        $data = [
            'text' => "Внимание: проект <code>$project->project_name</code> $status <code>недоступен</code>
<code>$project->last_check</code>
Состояние: <code>$project->status</code>
Текущий uptime: <code>$uptimePercent%</code>
Перейти в сервис:
<a href='https://lk.redbox.su/domain-monitoring'>https://lk.redbox.su/domain-monitoring</a>",
            'chat_id' => $project->telegramBot->chat_id,
            'parse_mode' => 'HTML'
        ];

        file_get_contents('https://api.telegram.org/bot'
            . env('TELEGRAM_BOT_TOKEN', '2073017935:AAHgwY7d0TBAAUzNUyvsmH6QLH14nESQhOc') .
            '/sendMessage?' . http_build_query($data));
    }
}
