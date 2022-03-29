<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Robokassa Driver
    |--------------------------------------------------------------------------
    |
    */

    'robokassa' => [
        'url' => "https://auth.robokassa.ru/Merchant/Index.aspx",
        'login' => env('ROBOKASSA_LOGIN', ''),
        'password' => env('ROBOKASSA_PASSWORD', ''),
        'password2' => env('ROBOKASSA_PASSWORD2', ''),
    ],
];
