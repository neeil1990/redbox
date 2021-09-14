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

Route::get('info', function () {
    phpinfo();
});

Auth::routes(['verify' => true]);
Route::post('email/verify/code', 'Auth\VerificationController@verifyCode')->name('verification.code');
Route::get('public/http-headers/{id}', 'PublicController@httpHeaders');

Route::get('public/behavior/{site}/code', 'BehaviorController@code')->name('behavior.code');
Route::get('public/behavior/{id}/check', 'BehaviorController@check')->name('behavior.check');
Route::post('public/behavior/verify', 'BehaviorController@verify')->name('behavior.verify');

Route::middleware(['verified'])->group(function () {

    Route::get('/', 'HomeController@index')->name('home');

    Route::resource('users', 'UsersController');

    Route::resource('behavior', 'BehaviorController');
    Route::delete('behavior/phrase/{phrase}', 'BehaviorController@phraseDestroy')->name('behavior.phrase.destroy');

    Route::get('profile/', 'ProfilesController@index')->name('profile.index');
    Route::post('profile/', 'ProfilesController@update')->name('profile.update');
    Route::patch('profile/', 'ProfilesController@password')->name('profile.password');

    Route::get('description/{description}/edit/{position?}', 'DescriptionController@edit')->name('description.edit');
    Route::patch('description/{description}', 'DescriptionController@update')->name('description.update');

    $arPages = config('pages.link');
    foreach ($arPages as $page)
        Route::get($page['url'], "PagesController@{$page['method']}")->name($page['name']);

    Route::post('generate-password', 'PasswordGeneratorController@createPassword')->name('generate.password');
    Route::get('password-generator', 'PasswordGeneratorController@index')->name('pages.password');

    Route::post('counting-text-length', 'TextLengthController@countingTextLength')->name('counting.text.length');
    Route::get('counting-text-length', 'TextLengthController@index')->name('pages.length');

    Route::get('list-comparison', 'ListComparisonController@index')->name('list.comparison');
    Route::post('list-comparison', 'ListComparisonController@listComparison')->name('counting.list.comparison');
    Route::post('download-comparison-file', 'ListComparisonController@downloadComparisonFile')->name('download.comparison.file');

    Route::get('unique-words', 'UniqueWordsController@index')->name('unique.words');
    Route::post('unique-words', 'UniqueWordsController@countingUniqueWords')->name('unique.words');
    Route::post('download-unique-words', 'UniqueWordsController@downloadUniqueWords')->name('download.unique.words');
    Route::post('download-unique-phrases', 'UniqueWordsController@downloadUniquePhrases')->name('download.unique.phrases');

    Route::get('html-editor', 'TextEditorController@index')->name('HTML.editor');
    Route::get('create-project', 'TextEditorController@createView')->name('create.project');
    Route::get('edit-project{id}', 'TextEditorController@editProjectView')->name('edit.project');
    Route::post('edit-project', 'TextEditorController@editProject')->name('save.edit.project');
    Route::post('save-project', 'TextEditorController@saveProject')->name('save.project');
    Route::get('project/delete{id}', 'TextEditorController@destroyProject')->name('delete.project');

    Route::get('edit-description{id}', 'TextEditorController@editDescriptionView')->name('edit.description');
    Route::post('edit-description', 'TextEditorController@editDescription')->name('save.edit.description');
    Route::get('description/delete{id}', 'TextEditorController@destroyDescription')->name('delete.description');
    Route::get('create-description', 'TextEditorController@createDescriptionView')->name('create.description');
    Route::post('save-description', 'TextEditorController@createDescription')->name('save.description');
});
