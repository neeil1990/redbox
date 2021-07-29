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
Route::get('public/http-headers/{id}', 'PublicController@httpHeaders');

Route::middleware(['verified'])->group(function () {

    Route::get('/', 'HomeController@index')->name('home');

    Route::resource('users', 'UsersController');

    Route::get('description/{description}/edit/{position?}', 'DescriptionController@edit')->name('description.edit');
    Route::patch('description/{description}', 'DescriptionController@update')->name('description.update');

    $arPages = config('pages.link');
    foreach ($arPages as $page)
        Route::get($page['url'], "PagesController@{$page['method']}")->name($page['name']);

});





