<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;
use Iodev\Whois\Factory;

class DomainInformation extends Model
{
    protected $table = 'domain_information';

    protected $guarded = [];

    /**
     * @param $project
     */
    public static function checkDomain($project)
    {
        $oldState = $project->broken;
        $oldDNS = $project->dns;
        $whois = Factory::get()->createWhois();
        $project->last_check = Carbon::now();

        try {
            $info = $whois->loadDomainInfo($project->domain);
            if (isset($info)) {
                $project->broken = false;
                $project->dns = "DNS:\n" . implode("\n", $info->nameServers);
                $registrationDate = __('Registration date') . ' ' . date('Y-m-d', $info->creationDate);
                $freeDate = date('Y-m-d', $info->expirationDate);
                $project->domain_information = DomainInformation::prepareStatus($project->dns, $registrationDate, $freeDate);
                DomainInformation::sendNotifications($project, $oldState, $oldDNS, $freeDate);
            } else {
                $project->broken = true;
                $project->domain_information = __('This domain has been removed from delegation(is free) and it can be registered.');
                DomainInformation::sendNotifications($project, $oldState);
            }
        } catch (\Exception $exception) {
            $project->broken = true;
            $project->domain_information = __('This domain has been removed from delegation(is free) and it can be registered.');
            DomainInformation::sendNotifications($project, $oldState);
        }

        $project->save();
    }

    public static function prepareStatus($dns, $registrationDate, $freeDate): string
    {
        $date = new Carbon($freeDate);
        return $dns . "\n\n"
            . $registrationDate . "\n"
            . __('Registration expires')
            . $freeDate
            . ' '
            . __('through')
            . ' '
            . $date->diffInDays(Carbon::now())
            . ' '
            . __('days');
    }

    /**
     * @param $project
     * @param $oldState
     * @param $oldDNS
     * @param $freeDate
     */
    public static function sendNotifications($project, $oldState, $oldDNS = null, $freeDate = null)
    {
        $user = User::find($project->user_id);

        if ($project->dns !== $oldDNS && isset($oldDNS)) {
            if ($user->telegram_bot_active and $project->check_dns) {
                TelegramBot::sendNotificationAboutChangeDNS($project, $user->chat_id, $oldDNS);
            }

            if ($project->check_dns_email) {
                $user->sendNotificationAboutChangeDNS($project);
            }
        }

        if (isset($freeDate)) {
            $freeDate = new Carbon($freeDate);
            $diffInDays = $freeDate->diffInDays(Carbon::now());

            if ($diffInDays < 20) {
                if ($user->telegram_bot_active and $project->check_registration_date) {
                    TelegramBot::sendNotificationAboutExpirationRegistrationPeriod($project, $user->chat_id, $diffInDays);
                }

                if ($project->check_registration_date_email) {
                    $user->sendNotificationAboutExpirationRegistrationPeriod($project, $diffInDays);
                }
            }
        }
    }

    /**
     * @param $domain
     * @return bool
     */
    public static function isValidDomain($domain): bool
    {
        return (
            preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain) &&
            preg_match("/^.{1,253}$/", $domain) &&
            preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain)
        );
    }

    /**
     * @param $link
     * @return string
     */
    public static function getDomain($link): string
    {
        $information = parse_url($link);

        return $information['host'] ?? $link;
    }

}
