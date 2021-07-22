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

    $arPages = ['keywordGenerator', 'duplicates', 'utmMarks', 'roiCalculator'];
    foreach ($arPages as $page)
        Route::get($page, "PagesController@{$page}")->name($page);

});





