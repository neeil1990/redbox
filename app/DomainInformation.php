<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DomainInformation extends Model
{
    protected $table = 'domain_information';

    protected $guarded = [];

    /**
     * @param $project
     */
    public static function checkDomainSock($project)
    {
        $oldState = $project->broken;
        $dns = '';
        $registrationDate = '';
        $socket = fsockopen('whois.tcinet.ru', 43);
        $project->last_check = Carbon::now();
        if ($socket) {
            fputs($socket, $project->domain . PHP_EOL);
            $text = '';
            while (!feof($socket)) {
                $text .= fgets($socket, 128);
            }
            fclose($socket);
            if (preg_match('/(No entries found for the selected source\(s\).)/', $text, $matches, PREG_OFFSET_CAPTURE)) {
                $project->domain_information = __("No records were found for the selected source");
                $project->broken = true;
                DomainInformation::sendNotification($project, $oldState);
                return;
            }
            if ($project->check_dns) {
                $dns = DomainInformation::prepareDNS($text);
            }
            if ($project->check_registration_date) {
                preg_match('/(created:)(\s\s\s\s\s\s\s)(.*)(\n)/', $text, $matches, PREG_OFFSET_CAPTURE);
                $registrationDate = __('Domain created') . ' ' . $matches[3][0];
            }
            $freeDate = DomainInformation::checkDate($text, $project);
            $project->domain_information = DomainInformation::prepareStatus($dns, $registrationDate, $freeDate);
            DomainInformation::sendNotification($project, $oldState);
        }
    }

    public static function prepareDNS($text)
    {
        $dns = '';
        preg_match_all('/(nserver:)(\s\s\s\s\s\s\s)(ns.*)(\n)/', $text, $matches, PREG_OFFSET_CAPTURE);
        if (empty($matches[0])) {
            $dns = __('not found');
        } else {
            foreach ($matches[0] as $item) {
                $dns .= $item[0];
            }
        }

        return $dns;
    }

    public static function prepareStatus($dns, $registrationDate, $freeDate): string
    {
        $dns = str_replace('nserver:       ', '', $dns);
        $dns = "DNS:\n" . $dns;
        return $dns . "\n"
            . $registrationDate . "\n"
            . __('Registration expires')
            . $freeDate
            . ' '
            . __('through')
            . ' '
            . $freeDate->diffInDays(Carbon::now())
            . ' '
            . __('days');
    }

    /**
     * @param $text
     * @param $project
     * @return Carbon
     */
    public static function checkDate($text, $project): Carbon
    {
        preg_match('/(free-date:)(\s\s\s\s\s)(.*)(\n)/', $text, $matches, PREG_OFFSET_CAPTURE);
        $freeDate = new Carbon($matches[3][0]);
        $countDays = $freeDate->diffInDays(Carbon::now());
        if ($countDays <= 10) {
            $project->broken = true;
        } else {
            $project->broken = false;
        }

        return $freeDate;
    }

    /**
     * @param $project
     * @param $oldState
     */
    public static function sendNotification($project, $oldState)
    {
        $user = User::find($project->user_id);
        if ($project->broken != $oldState) {
            if ($user->telegram_bot_active) {
                TelegramBot::prepareDomainInformationMessage($project, $user->chat_id);
            }
            $user->DomainInformationNotification($project);
        }
    }

    /**
     * @param $domain
     * @return bool
     */
    public static function isValidDomain($domain): bool
    {
        return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain) //valid chars check
            && preg_match("/^.{1,253}$/", $domain) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain)); //length of each label
    }
}
