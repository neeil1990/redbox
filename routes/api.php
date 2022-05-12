<?php

use Illuminate\Http\Request;
use \App\Classes\Locations\Searches\Yandex;
use \App\Classes\Locations\Searches\Google;

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
    $search = $request->get('searchEngine', '');

    if(!$name)
        return '';

    $location = null;

    switch ($search) {
        case "yandex":
            $location = new Yandex();
            break;

        case "google":
            $location = new Google();
            break;

        default:
            throw new ErrorException('Location site is not exist.');
    }

    if($location)
        return $location->get($name) ?: '';
    else
        return '';
});
