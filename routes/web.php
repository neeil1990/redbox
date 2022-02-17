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

use App\ Bot;

Route::get('info', function () {
    phpinfo();
});

Route::get('telegram', function () {

    $user = \Illuminate\Support\Facades\Auth::user();
    TelegramBot::sendMessage("test", $user->chat_id);
});

Auth::routes(['verify' => true]);
Route::post('email/verify/code', 'Auth\VerificationController@verifyCode')->name('verification.code');

//Public method
Route::get('public/http-headers/{id}', 'PublicController@httpHeaders');
Route::get('public/behavior/{id}/check', 'PublicController@checkBehavior')->name('behavior.check');
Route::post('public/behavior/verify', 'PublicController@verifyBehavior')->name('behavior.verify');
Route::get('public/behavior/{site}/code', 'PublicController@codeBehavior')->name('behavior.code');

Route::middleware(['verified'])->group(function () {

    Route::get('test', 'TestController@index')->name('test');

    Route::get('/', 'HomeController@index')->name('home');
    Route::post('project-sortable', 'HomeController@projectSort');
    Route::post('menu-item-sortable', 'HomeController@menuItemSort');
    Route::post('/get-description-projects', 'HomeController@getDescriptionProjects')->name('get.description.projects');

    Route::resource('main-projects', 'DescriptionProjectForAdminController');

    Route::resource('users', 'UsersController');

    Route::post('/manage-access/assignPermission', 'ManageAccessController@assignPermission');
    Route::get('manage-access/destroy/{id}/where/{type}', 'ManageAccessController@destroy');
    Route::resource('manage-access', 'ManageAccessController')->only([
        'index', 'store', 'update'
    ]);

    Route::resource('tariff-settings', 'TariffSettingsController');
    Route::get('tariff-setting-values/{id}/create', 'TariffSettingValuesController@create')->name('tariff-setting-values.create');
    Route::resource('tariff-setting-values', 'TariffSettingValuesController')->except([
        'create', 'index', 'show', 'edit', 'update'
    ]);

    Route::delete('/meta-tags/history/{id}', 'MetaTagsController@destroyHistory')->name('meta.history.delete');
    Route::get('/meta-tags/history/{id}/compare/{id_compare}/export/', 'MetaTagsController@exportCompare')->name('meta.history.export_compare');
    Route::get('/meta-tags/history/{id}/export/', 'MetaTagsController@export')->name('meta.history.export');
    Route::post('/meta-tags/get', 'MetaTagsController@getMetaTags');
    Route::put('/meta-tags/histories/ideal/{id}', 'MetaTagsController@updateHistoriesIdeal');
    Route::patch('/meta-tags/histories/{id}', 'MetaTagsController@storeHistories');
    Route::get('/meta-tags/histories/{id}', 'MetaTagsController@showHistories');
    Route::get('/meta-tags/history/{id}/compare/{id_compare}', 'MetaTagsController@showHistoryCompare')->name('meta.history.compare');;
    Route::get('/meta-tags/history/{id}', 'MetaTagsController@showHistory');
    Route::resource('meta-tags', 'MetaTagsController');

    Route::get('behavior/{behavior}/edit-project', 'BehaviorController@editProject')->name('behavior.edit_project');
    Route::patch('behavior/{behavior}/update-project', 'BehaviorController@updateProject')->name('behavior.update_project');

    Route::delete('behavior/phrase/{phrase}', 'BehaviorController@phraseDestroy')->name('behavior.phrase.destroy');
    Route::resource('behavior', 'BehaviorController');

    Route::get('profile/', 'ProfilesController@index')->name('profile.index');
    Route::post('profile/', 'ProfilesController@update')->name('profile.update');
    Route::patch('profile/', 'ProfilesController@password')->name('profile.password');

    Route::get('description/{description}/edit/{position?}', 'DescriptionController@edit')->name('description.edit');
    Route::patch('description/{description}', 'DescriptionController@update')->name('description.update');

    Route::get('http-headers/{object}/export', 'PagesController@httpHeadersExport');

    Route::get('duplicates/{quantity?}', "PagesController@duplicates")->name('pages.duplicates')->middleware('permission:Duplicates');
    Route::get('keyword-generator', "PagesController@keywordGenerator")->name('pages.keyword')->middleware('permission:Keyword generator');
    Route::get('utm-marks', "PagesController@utmMarks")->name('pages.utm')->middleware('permission:Utm marks');
    Route::get('roi-calculator', "PagesController@roiCalculator")->name('pages.roi')->middleware('permission:Roi calculator');
    Route::get('http-headers/{url?}', "PagesController@httpHeaders")->name('pages.headers')->middleware('permission:Http headers');

    Route::post('generate-password', 'PasswordGeneratorController@createPassword')->name('generate.password');
    Route::get('password-generator', 'PasswordGeneratorController@index')->name('pages.password');

    Route::post('counting-text-length', 'TextLengthController@countingTextLength')->name('counting.text.length');
    Route::get('counting-text-length', 'TextLengthController@index')->name('pages.length');

    Route::get('list-comparison', 'ListComparisonController@index')->name('list.comparison');
    Route::post('list-comparison', 'ListComparisonController@listComparison')->name('counting.list.comparison');
    Route::post('download-comparison-file', 'ListComparisonController@downloadComparisonFile')->name('download.comparison.file');

    Route::get('unique-words', 'UniqueWordsController@index')->name('unique.words');
    Route::post('unique-words', 'UniqueWordsController@countingUniqueWords')->name('unique.words');
    Route::post('download-unique-words', 'UniqueWordsController@createFile')->name('create.file.unique.words');
    Route::post('download-unique-phrases', 'UniqueWordsController@createFile')->name('create.file.unique.phrases');
    Route::post('download-file', 'UniqueWordsController@downloadFile')->name('download-file');

    Route::get('html-editor', 'TextEditorController@index')->name('HTML.editor');
    Route::get('create-project', 'TextEditorController@createView')->name('create.project');
    Route::get('edit-project{id}', 'TextEditorController@editProjectView')->name('edit.project');
    Route::post('edit-project', 'TextEditorController@editProject')->name('save.edit.project');
    Route::post('save-project', 'TextEditorController@saveProject')->name('save.project');
    Route::get('project/delete{id}', 'TextEditorController@destroyProject')->name('delete.project');

    Route::get('edit-description{id}', 'TextEditorController@editDescriptionView')->name('edit.description');
    Route::post('edit-description', 'TextEditorController@editDescription')->name('save.edit.description');
    Route::delete('description/delete{id}', 'TextEditorController@destroyDescription')->name('delete.description');
    Route::get('create-description', 'TextEditorController@createDescriptionView')->name('create.description');
    Route::post('save-description', 'TextEditorController@createDescription')->name('save.description');

    Route::get('backlink', 'BacklinkController@index')->name('backlink');
    Route::get('add-backlink', 'BacklinkController@createView')->name('add.backlink.view');
    Route::post('add-backlink', 'BacklinkController@store')->name('add.backlink');
    Route::delete('delete-backlink/{id}', 'BacklinkController@remove')->name('delete.backlink');
    Route::get('show-backlink/{id}', 'BacklinkController@show')->name('show.backlink');
    Route::post('edit-backlink', 'BacklinkController@edit')->name('save.changes.backlink');
    Route::get('check-link/{id}', 'BacklinkController@checkLink')->name('check.link');
    Route::delete('delete-link/{id}', 'BacklinkController@removeLink')->name('delete.link');
    Route::post('edit-link', 'BacklinkController@editLink')->name('edit.link');
    Route::get('add-link/{id}', 'BacklinkController@addLinkView')->name('add.link.view');
    Route::post('edit-backlink', 'BacklinkController@editBacklink')->name('edit.backlink');
    Route::post('add-link', 'BacklinkController@storeLink');

    Route::get('domain-monitoring', 'DomainMonitoringController@index')->name('domain.monitoring');
    Route::get('add-domain-monitoring', 'DomainMonitoringController@createView')->name('add.domain.monitoring.view');
    Route::post('add-domain-monitoring', 'DomainMonitoringController@store')->name('add.domain.monitoring');
    Route::get('delete-domain-monitoring/{id}', 'DomainMonitoringController@remove')->name('delete.domain.monitoring');
    Route::get('check-domain-monitoring/{id}', 'DomainMonitoringController@checkLink')->name('check.domain');
    Route::post('edit-domain-monitoring', 'DomainMonitoringController@edit')->name('edit.domain');
    Route::post('delete-domains-monitoring', 'DomainMonitoringController@removeDomains')->name('delete.domains');

    Route::get('verification-token/{token}', 'TelegramBotController@verificationToken')->name('verification.token');
    Route::get('reset-notification/{token}', 'TelegramBotController@resetNotification')->name('reset.notification');

    Route::get('domain-information', 'DomainInformationController@index')->name('domain.information');
    Route::get('add-domain-information', 'DomainInformationController@createView')->name('add.domain.information.view');
    Route::get('delete-domain-information/{id}', 'DomainInformationController@remove')->name('delete.domain.information');
    Route::post('add-domain-information', 'DomainInformationController@store')->name('add.domain.information.view');
    Route::post('edit-domain-information', 'DomainInformationController@edit')->name('edit.domain.information');
    Route::post('delete-domains-information', 'DomainInformationController@removeDomains')->name('delete.domain-information');
    Route::get('check-domain-information/{id}', 'DomainInformationController@checkDomain')->name('check.domain.information');

    Route::get('text-analyzer', 'TextAnalyzerController@index')->name('text.analyzer.view');
    Route::get('text-analyzer/{url}', 'TextAnalyzerController@redirectToAnalyse')->name('text.analyzer.redirect');
    Route::post('text-analyzer', 'TextAnalyzerController@analyze')->name('text.analyzer');

    Route::get('news', 'NewsController@index')->name('news');
    Route::get('/create-news', 'NewsController@createView')->name('create.news');
    Route::post('/save-news', 'NewsController@store')->name('save.news');
    Route::post('/remove-news', 'NewsController@remove')->name('remove.news');
    Route::post('/create-comment', 'NewsController@storeComment')->name('create.comment');
    Route::post('/remove-comment', 'NewsController@removeComment')->name('remove.comment');
    Route::post('/like-news', 'NewsController@likeNews')->name('like');
    Route::get('/edit-news/{id}', 'NewsController@editNewsView')->name('edit.news');
    Route::post('/save-edit-news', 'NewsController@editNews')->name('save.edit.news');
    Route::post('/edit-comment', 'NewsController@editComment')->name('edit.comment');
    Route::post('/get-count-new-news', 'NewsController@calculateCountNewNews')->name('get.count.new.news');

    Route::get('/competitor-analysis', 'SearchCompetitorsController@index');
    Route::post('/competitor-analysis', 'SearchCompetitorsController@analyzeSites')->name('analyze.sites');
    Route::post('/analyze-nesting', 'SearchCompetitorsController@analyseNesting')->name('analyze.nesting');
    Route::post('/analyze-positions', 'SearchCompetitorsController@analysePositions')->name('analyze.positions');
    Route::post('/analyze-tags', 'SearchCompetitorsController@analyseTags')->name('analyze.tags');
});
