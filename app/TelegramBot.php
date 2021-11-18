<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TelegramBot extends Model
{
    protected $guarded = [];

    protected $table = 'telegram_bot';

    public static function brokenDomenNotification($project, $chatId)
    {
        TelegramBot::prepareBreakdownMessage($project, $chatId);
    }

    public static function repairedDomenNotification($project, $chatId)
    {
        TelegramBot::PrepareRecoveryMessage($project, $chatId);
    }

    public static function prepareDomainInformationMessage($project, $chatId)
    {
        $text =
            'Domain ' . $project->domain
            . "\n"
            . $project->domain_information
            . "\n"
            . __('Go to the service:')
            . " <a href='https://lk.redbox.su/domain-information' target='_blank'>https://lk.redbox.su/domain-information</a>";

        TelegramBot::sendMessage($text, $chatId);
    }

    /**
     * @param false $offset
     * @return array
     */
    public static function getUpdates($offset = null): array
    {
        $data = [];
        if (isset($offset)) {
            $data = ['offset' => $offset];
        }
        $updates = json_decode(file_get_contents('https://api.telegram.org/bot'
            . env('TELEGRAM_BOT_TOKEN', '') .
            '/getUpdates?' . http_build_query($data)), true);

        return $updates['result'];
    }

    /**
     * @param $token
     * @return bool
     */
    public static function searchToken($token): bool
    {
        $find = true;
        $updates = TelegramBot::getUpdates();
        while ($find) {
            foreach ($updates as $key => $element) {
                if (isset($element['message']) &&
                    isset($element['message']['text']) &&
                    $element['message']['text'] === $token
                ) {
                    User::where('telegram_token', '=', $token)->update([
                        'telegram_bot_active' => 1,
                        'chat_id' => $element['message']['chat']['id'],
                    ]);
                    TelegramBot::sendSuccessMessage($element['message']['chat']['id']);
                    return true;
                }
                if (count($updates) === 1) {
                    return false;
                }
                if ($key === array_key_last($updates)) {
                    $updates = TelegramBot::getUpdates($element['update_id']);
                }
            }
        }
    }

    public static function sendSuccessMessage($chatId)
    {
        $data = [
            'text' => __('You have successfully subscribed to the notification newsletter'),
            'chat_id' => $chatId,
            'parse_mode' => 'HTML'
        ];

        file_get_contents('https://api.telegram.org/bot'
            . env('TELEGRAM_BOT_TOKEN', '') .
            '/sendMessage?' . http_build_query($data));
    }

    public static function prepareBreakdownMessage($project, $chatId)
    {
        $link = preg_replace('#^https?://#', '', rtrim($project->link, '/'));
        $link = preg_replace('/^www\./', '', $link);
        $uptimePercent = round($project->uptime_percent, 2);

        $text = __('Project') . " <code>$project->project_name</code>  " . __('broken') . "
" . __('Check time:') . " <code>$project->last_check</code>
" . __('http code:') . " <code>$project->code</code>
" . __('Condition:') . " <code>" . __($project->status) . "</code>
" . __('Current uptime:') . " <code>$uptimePercent%</code>
" . __('Go to the website') . "
<a href='$link' target='_blank'>" . $link . "</a>
" . __('Go to the service:') . "
<a href='https://lk.redbox.su/domain-monitoring' target='_blank'>https://lk.redbox.su/domain-monitoring</a>";

        TelegramBot::sendMessage($text, $chatId);
    }

    public static function prepareRecoveryMessage($project, $chatId)
    {
        $link = preg_replace('#^https?://#', '', rtrim($project->link, '/'));
        $link = preg_replace('/^www\./', '', $link);
        $uptimePercent = round($project->uptime_percent, 2);

        $text = __('Project') . " <code>$project->project_name</code>  " . __('repair') . "
" . __('Check time:') . " <code>$project->last_check</code>
" . __('Condition:') . " <code>" . __($project->status) . "</code>
" . __('Current uptime:') . " <code>$uptimePercent%</code>
" . __('Total time of the last breakdown:') . " <code>$project->total_time_last_breakdown</code> " . __('min') . "
" . __('Go to the website') . "
<a href='$link' target='_blank'>" . $link . "</a>
" . __('Go to the service:') . "
<a href='https://lk.redbox.su/domain-monitoring' target='_blank'>https://lk.redbox.su/domain-monitoring</a>";

        TelegramBot::sendMessage($text, $chatId);
    }

    /**
     * @param $text
     * @param $chatId
     */
    public static function sendMessage($text, $chatId)
    {
        $data = [
            'text' => $text,
            'chat_id' => $chatId,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ];

        file_get_contents('https://api.telegram.org/bot'
            . env('TELEGRAM_BOT_TOKEN', '') .
            '/sendMessage?' . http_build_query($data));
    }
}
