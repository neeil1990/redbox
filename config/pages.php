<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pages Application
    |--------------------------------------------------------------------------
    |
    | Here you may create static page for PagesController
    |
    */

    'link' => [
        ['id' => 1, 'method' => 'keywordGenerator', 'url' => 'keyword-generator', 'name' => 'pages.keyword'],
        ['id' => 4, 'method' => 'duplicates', 'url' => 'duplicates', 'name' => 'pages.duplicates'],
        ['id' => 5, 'method' => 'utmMarks', 'url' => 'utm-marks', 'name' => 'pages.utm'],
        ['id' => 6, 'method' => 'roiCalculator', 'url' => 'roi-calculator', 'name' => 'pages.roi'],
        ['id' => 7, 'method' => 'httpHeaders', 'url' => 'http-headers/{url?}', 'name' => 'pages.headers'],
    ],

];
