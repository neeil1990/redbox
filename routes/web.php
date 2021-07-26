<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('info', function (){
    phpinfo();
});

Auth::routes(['verify' => true]);
Route::post('email/verify/code', 'Auth\VerificationController@verifyCode')->name('verification.code');

Route::middleware(['verified'])->group(function () {
    Route::get('/', 'HomeController@index')->name('home');
    Route::resource('users', 'UsersController');

    $arPages = [
        'keyword-generator' => 'keywordGenerator',
        'duplicates' => 'duplicates',
        'utm-marks' => 'utmMarks',
        'roi-calculator' => 'roiCalculator',
        'http-headers/{url?}' => 'httpHeaders'
    ];
    foreach ($arPages as $url => $page)
        Route::get($url, "PagesController@{$page}")->name($page);

});





