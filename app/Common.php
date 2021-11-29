<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Common extends Model
{
    /**
     * @param $link
     * @return array|null
     */
    public static function curlInit($link)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl, CURLOPT_TIMEOUT, 2);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);

        return Common::tryConnect($curl);
    }

    /**
     * @param $curl
     * @return bool|string|string[]|null
     */
    public static function tryConnect($curl)
    {
        $userAgents = [
            //Mozilla Firefox
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:84.0) Gecko/20100101 Firefox/84.0',
            //opera
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.43 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36 OPR/77.0.4054.146',
            'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.43 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36 OPR/77.0.4054.60',
            // chrome
            'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.106 Safari/537.36',
            'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36'
        ];

        foreach ($userAgents as $agent) {
            curl_setopt($curl, CURLOPT_USERAGENT, $agent);
            $html = curl_exec($curl);
            $headers = curl_getinfo($curl);
            if ($headers['http_code'] == 200 && $html != false) {
                $html = preg_replace('//i', '', $html);
                break 1;
            }
        }

        curl_close($curl);
        return $html;
    }

    public static function deleteEverythingExceptCharacters($text)
    {
        $withoutSubject = explode("\n\r", $text);
        unset($withoutSubject[0]);
        $withoutSubject = implode("\n", $withoutSubject);
        $text = preg_replace("'<style[^>]*?>.*?</style>'si", "", $withoutSubject);
        $text = preg_replace("'<script[^>]*?>.*?</script>'si", "", $text);
        $text = trim(strip_tags($text));
        $text = html_entity_decode($text);
        $text = str_replace(["\n", "\t", "\r"], '  ', $text);
        $text = preg_replace("/[0-9]/", "", $text);
        $text = preg_replace("#[[:punct:]]#", "", $text);
        $text = preg_replace('| +|', ' ', $text);
        return preg_replace('| +|', ' ', $text);
    }
}
