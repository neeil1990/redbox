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
        ['id' => 2, 'method' => 'passwordGenerator', 'url' => 'password-generator', 'name' => 'pages.password'],
        ['id' => 3, 'method' => 'duplicates', 'url' => 'duplicates', 'name' => 'pages.duplicates'],
        ['id' => 4, 'method' => 'utmMarks', 'url' => 'utm-marks', 'name' => 'pages.utm'],
        ['id' => 5, 'method' => 'roiCalculator', 'url' => 'roi-calculator', 'name' => 'pages.roi'],
        ['id' => 6, 'method' => 'httpHeaders', 'url' => 'http-headers/{url?}', 'name' => 'pages.headers'],
    ],

];
