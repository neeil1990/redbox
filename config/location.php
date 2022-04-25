<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Location Yandex, Google
    |--------------------------------------------------------------------------
    |
    */

    'yandex' => [
        'url' => 'https://api.partner.market.yandex.ru/v2/regions.json',
        'token' => env('YANDEX_TOKEN', ''),
        'client_id' => env('YANDEX_CLIENT_ID', ''),
    ],

    'google' => []
];
