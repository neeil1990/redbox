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
        ['id' => 2, 'method' => 'duplicates', 'url' => 'duplicates', 'name' => 'pages.duplicates'],
        ['id' => 3, 'method' => 'utmMarks', 'url' => 'utm-marks', 'name' => 'pages.utm'],
        ['id' => 4, 'method' => 'roiCalculator', 'url' => 'roi-calculator', 'name' => 'pages.roi'],
        ['id' => 5, 'method' => 'httpHeaders', 'url' => 'http-headers/{url?}', 'name' => 'pages.headers'],
        ['id' => 6, 'method' => 'passwordGenerator', 'url' => 'password-generator', 'name' => 'pages.password'],
        ['id' => 7, 'method' => 'countingTextLength', 'url' => 'counting-text-length', 'name' => 'pages.length'],
    ],

];
