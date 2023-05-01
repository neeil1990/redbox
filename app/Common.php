<?php

namespace App;

use Carbon\Carbon;

class Common
{
    public static function fileExport($file, string $type, string $name = '')
    {
        if (!$name) {
            $name = md5(microtime());
        }

        $fileName = $file->getFile()->getFilename();

        $filePath = storage_path('framework/laravel-excel/' . $fileName);
        $newFileName = storage_path('framework/laravel-excel/' . $name) . '.' . $type;

        rename($filePath, $newFileName);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($newFileName));
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($newFileName));

        readfile($newFileName);

        unlink($newFileName);
    }

    public static function uncompressArray($string, $decode = true)
    {
        if ($decode) {
            return json_decode(gzuncompress(base64_decode($string)), true);
        }

        return gzuncompress(base64_decode($string));
    }

    public static function getRegionName(string $id): string
    {
        switch ($id) {
            case '1' :
                return __('Moscow');
            case '20' :
                return __('Arkhangelsk');
            case '37' :
                return __('Astrakhan');
            case '197' :
                return __('Barnaul');
            case '4' :
                return __('Belgorod');
            case '77' :
                return __('Blagoveshchensk');
            case '191' :
                return __('Bryansk');
            case '24' :
                return __('Veliky Novgorod');
            case '75' :
                return __('Vladivostok');
            case '33' :
                return __('Vladikavkaz');
            case '192' :
                return __('Vladimir');
            case '38' :
                return __('Volgograd');
            case '21' :
                return __('Vologda');
            case '193' :
                return __('Voronezh');
            case '1106' :
                return __('Grozny');
            case '54' :
                return __('Ekaterinburg');
            case '5' :
                return __('Ivanovo');
            case '63' :
                return __('Irkutsk');
            case '41' :
                return __('Yoshkar-ola');
            case '43' :
                return __('Kazan');
            case '22' :
                return __('Kaliningrad');
            case '64' :
                return __('Kemerovo');
            case '7' :
                return __('Kostroma');
            case '35' :
                return __('Krasnodar');
            case '62' :
                return __('Krasnoyarsk');
            case '53' :
                return __('Kurgan');
            case '8' :
                return __('Kursk');
            case '9' :
                return __('Lipetsk');
            case '28' :
                return __('Makhachkala');
            case '213' :
                return __('Moscow');
            case '23' :
                return __('Murmansk');
            case '1092' :
                return __('Nazran');
            case '30' :
                return __('Nalchik');
            case '47' :
                return __('Nizhniy Novgorod');
            case '65' :
                return __('Novosibirsk');
            case '66' :
                return __('Omsk');
            case '10' :
                return __('Eagle');
            case '48' :
                return __('Orenburg');
            case '49' :
                return __('Penza');
            case '50' :
                return __('Perm');
            case '25' :
                return __('Pskov');
            case '39' :
                return __('Rostov-on');
            case '11' :
                return __('Ryazan');
            case '51' :
                return __('Samara');
            case '42' :
                return __('Saransk');
            case '2' :
                return __('Saint-Petersburg');
            case '12' :
                return __('Smolensk');
            case '239' :
                return __('Sochi');
            case '36' :
                return __('Stavropol');
            case '973' :
                return __('Surgut');
            case '13' :
                return __('Tambov');
            case '14' :
                return __('Tver');
            case '67' :
                return __('Tomsk');
            case '15' :
                return __('Tula');
            case '195' :
                return __('Ulyanovsk');
            case '172' :
                return __('Ufa');
            case '76' :
                return __('Khabarovsk');
            case '45' :
                return __('Cheboksary');
            case '56' :
                return __('Chelyabinsk');
            case '1104' :
                return __('Cherkessk');
            case '16' :
                return __('Yaroslavl');
            default:
                return 'Регион не опознан';
        }
    }

    public static function domainFilter($domain): string
    {
        return urldecode(str_replace(['www.'], '', strtolower($domain)));
    }

    public static function percentHitIn($top, $positions, $additional = false): int
    {
        $count = 0;
        foreach ($positions as $position) {
            if ($position <= $top) {
                $count++;
            }
        }
        if ($additional && $count !== 0) {
            return round((100 / count($positions)) * $count, 2);
        }

        return $count;
    }

    public static function secondsToDate($seconds): string
    {
        $carbon1 = Carbon::now();
        $carbon2 = $carbon1->copy()->addSeconds($seconds);
        $diff = $carbon1->diff($carbon2);

        return $diff->h . ':' . $diff->i . ':' . $diff->s;
    }
}
