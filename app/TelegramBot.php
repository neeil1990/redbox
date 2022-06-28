<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TelegramBot extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var string
     */
    protected $table = 'telegram_bot';

    /**
     * @param $project
     * @param $chatId
     * @return void
     */
    public static function brokenDomainNotification($project, $chatId)
    {
        TelegramBot::prepareBreakdownMessage($project, $chatId);
    }

    /**
     * @param $project
     * @param $chatId
     * @return void
     */
    public static function repairedDomenNotification($project, $chatId)
    {
        TelegramBot::PrepareRecoveryMessage($project, $chatId);
    }

    /**
     * @param $project
     * @param $chatId
     * @return void
     */
    public static function sendNotificationAboutChangeStateProject($project, $chatId)
    {
        $text =
            __('Domain') .
            ' ' . $project->domain
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
        $updates = json_decode(
            file_get_contents(
                'https://api.telegram.org/bot2073017935:AAF5OJbt74xrX8W7kR_O4NhSMWncpTiwflo/getUpdates?'
                . http_build_query($data)
            ), true);

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

    /**
     * @param $chatId
     * @return void
     */
    public static function sendSuccessMessage($chatId)
    {
        $text = __('You have successfully subscribed to the notification newsletter');

        TelegramBot::sendMessage($text, $chatId);
    }

    /**
     * @param $project
     * @param $chatId
     * @return void
     */
    public static function prepareBreakdownMessage($project, $chatId)
    {
        $link = TelegramBot::removeProtocol($project);
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

    /**
     * @param $project
     * @param $chatId
     * @return void
     */
    public static function prepareRecoveryMessage($project, $chatId)
    {
        $link = TelegramBot::removeProtocol($project);
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
     * @param $project
     * @return array|string|string[]|null
     */
    public static function removeProtocol($project)
    {
        $link = preg_replace('#^https?://#', '', rtrim($project->link, '/'));
        return preg_replace('/^www\./', '', $link);
    }

    /**
     * @param $project
     * @param $chatId
     * @param $dns
     * @return void
     */
    public static function sendNotificationAboutChangeDNS($project, $chatId, $dns)
    {
        $text = __('Domain') . ' ' . $project->domain
            . "\n"
            . __('DNS CHANGED')
            . "\n"
            . __('old') . " " . $dns
            . "\n"
            . __('new') . " " . $project->dns
            . "\n"
            . "\n"
            . __('Go to the service:')
            . " <a href='https://lk.redbox.su/domain-information' target='_blank'>https://lk.redbox.su/domain-information</a>";

        TelegramBot::sendMessage($text, $chatId);

    }

    /**
     * @param $project
     * @param $chatId
     * @param $diffInDays
     * @return void
     */
    public static function sendNotificationAboutExpirationRegistrationPeriod($project, $chatId, $diffInDays)
    {
        $text = __('Domain') . ' ' . $project->domain
            . "\n"
            . __('Notification of the expiration of the registration period')
            . "\n"
            . __('Registration ends after') . " $diffInDays " . __('days')
            . "\n"
            . "\n"
            . __('Go to the service:')
            . " <a href='https://lk.redbox.su/domain-information' target='_blank'>https://lk.redbox.su/domain-information</a>";

        TelegramBot::sendMessage($text, $chatId);
    }

    /**
     * @param $text
     * @param $chatId
     * @return bool|int
     */
    public static function sendMessage($text, $chatId)
    {
        if ($chatId) {
            $data = [
                'text' => $text,
                'chat_id' => $chatId,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ];

            file_get_contents("https://api.telegram.org/bot2073017935:AAF5OJbt74xrX8W7kR_O4NhSMWncpTiwflo/sendMessage?"
                . http_build_query($data)
            );
        }

        return http_response_code(200);
    }
}
