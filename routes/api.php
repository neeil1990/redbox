<?php

use Illuminate\Http\Request;
use \App\Classes\Locations\Yandex;
use App\Classes\Position\Engine\Yandex as Engine;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/backlink/scan-links', 'CroneController@scanLinks');
Route::get('/backlink/scan-broken-links', 'CroneController@scanBrokenLinks');
Route::get('/domain-monitoring/check-link-crone/{timing}', 'CroneController@checkLinkCrone');
Route::get('/domain-information/check-domain-crone/', 'CroneController@checkDomains');

Route::get('location', function(Request $request){

    $name = $request->get('name', '');
    $site = $request->get('site', 'yandex');

    if(!$name)
        return '';

    $location = null;

    switch ($site) {
        case "yandex":
            $location = new Yandex();
            break;

        default:
            throw new ErrorException('Location site is not exist.');
    }

    return $location->get($name) ?: '';
});

Route::get('search/{query?}', function($query){

    dump('site: lorshop.ru');
    dump('lr: 193');
    dump('query: ' . $query);

    $position = new Engine('lorshop.ru', $query, '193');
    $position->handle();
});
