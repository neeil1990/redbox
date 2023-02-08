<?php

return [

    /*
    |--------------------------------------------------------------------------
    | XML River Driver
    |--------------------------------------------------------------------------
    */

    'url' => "https://xmlriver.com/wordstat/json",

    'user' => env('XML_RIVER_USER', ''),
    'key' => env('XML_RIVER_KEY', ''),

    'proxyUser' => env('XML_PROXY_USER', ''),
    'proxyKey' => env('XML_PROXY_KEY', ''),

    'stockUser' => env('XML_STOCK_USER', ''),
    'stockKey' => env('XML_STOCK_KEY', ''),

];
