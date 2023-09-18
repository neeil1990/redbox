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

use App\CheckLists;
use App\ChecklistTasks;
use App\Classes\SimpleHtmlDom\HtmlDocument;
use App\Common;
use App\MonitoringCompetitor;
use App\MonitoringHelper;
use App\MonitoringKeyword;
use App\MonitoringSearchengine;
use App\ProjectRelevanceHistory;
use App\VisitStatistic;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

Route::get('test-create', function () {

});

Route::get('info', function () {
    phpinfo();
});

Route::get('jobs', function () {
    set_time_limit(0);

    $job = App\Jobs::find(7373087);

    dd(unserialize($job->payload['data']['command'])->handle());
});

Route::get('monitoring_projects-to-monitoring_project_user-copy', function () {
    $projects = App\MonitoringProject::all();
    foreach($projects as $p)
    {
        try {
            \DB::table('monitoring_project_user')->insert([
                'user_id' => $p['user_id'],
                'monitoring_project_id' => $p['id'],
                'admin' => 1,
                'approved' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            echo 'User ID: ',  $p['user_id'], "\n";
        }
    }

    dump("done");
});

Auth::routes(['verify' => true]);
Route::post('/validate-registration-form', 'Auth\RegisterController@validateData')->name('validate.registration.form');
Route::post('/validate-verify-code', 'Auth\VerificationController@validateVerifyCode')->name('validate.verify.code');
Route::post('email/verify/code', 'Auth\VerificationController@verifyCode')->name('verification.code');

//Public method
Route::get('public/http-headers/{id}', 'PublicController@httpHeaders');
Route::get('public/behavior/{id}/check', 'PublicController@checkBehavior')->name('behavior.check');
Route::post('public/behavior/verify', 'PublicController@verifyBehavior')->name('behavior.verify');
Route::get('public/behavior/{site}/code', 'PublicController@codeBehavior')->name('behavior.code');
Route::post('/balance-add/result', 'BalanceAddController@result')->name('balance.add.result');
Route::get('/personal-data/ru', 'AccessController@getRuPersonalData');
Route::get('/personal-data/en', 'AccessController@getEnPersonalData');
Route::get('/privacy-policy/ru', 'AccessController@getRuPrivacyPolicy');
Route::get('/privacy-policy/en', 'AccessController@getEnPrivacyPolicy');

Route::middleware(['verified'])->group(function () {
    Route::get('test', 'TestController@index')->name('test');

    Route::get('/', 'HomeController@index')->name('home');

    Route::resource('main-projects', 'MainProjectsController');
    Route::get('/main-projects/statistics/{project}', 'MainProjectsController@statistics')->name('main-projects.statistics');
    Route::get('/get-click-actions/{id}', 'MainProjectsController@actions');
    Route::post('/get-statistic-modules', 'MainProjectsController@statisticsModules')->name('get.statistics.modules');
    Route::get('/modules-statistics/', 'MainProjectsController@moduleVisitStatistics')->name('statistics.modules');
    Route::post('/update-statistics', 'PublicController@updateStatistics')->name('update.statistics');

    Route::get('users/{id}/login', 'UsersController@login')->name('users.login');
    Route::get('/get-verified-users/{type}', 'UsersController@getFile')->name('get.verified.users');
    Route::get('/visit-statistics/{user}', 'UsersController@visitStatistics')->name('visit.statistics');
    Route::post('/user-actions-history', 'UsersController@userActionsHistory')->name('user.actions.history');
    Route::post('/project-actions-history', 'MainProjectsController@actionsHistory')->name('module.actions.history');
    Route::get('/get-data-range-visit-statistics/{user}', 'UsersController@getDateRangeVisitStatistics')->name('visit.statistics.date.range');
    Route::get('/get-data-range-module-statistics/{project}', 'MainProjectsController@getDateRangeModuleStatistics');
    Route::post('/get-filtered-users', 'UsersController@filterExportsUsers')->name('filter.exports.users');
    Route::get('/visits-statistics/', 'UsersController@userVisitStatistics')->name('users.statistics');
    Route::post('users/tariff', 'UsersController@storeTariff')->name('users.tariff');
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
    Route::get('/meta-tags/history/{id}/compare/{id_compare}', 'MetaTagsController@showHistoryCompare')->name('meta.history.compare');
    Route::get('/meta-tags/history/{id}', 'MetaTagsController@showHistory');
    Route::get('/meta-tags/getTariffMetaTagsPages', 'MetaTagsController@getTariffMetaTagsPages');
    Route::resource('meta-tags', 'MetaTagsController');

    Route::get('behavior/{behavior}/unique', 'BehaviorController@uniquePhrases')->name('behavior.unique.phrases');
    Route::get('behavior/{behavior}/sort-mixed', 'BehaviorController@sortMixed')->name('behavior.sort.mixed');
    Route::get('behavior/{behavior}/edit-project', 'BehaviorController@editProject')->name('behavior.edit_project');
    Route::patch('behavior/{behavior}/update-project', 'BehaviorController@updateProject')->name('behavior.update_project');

    Route::post('behavior/phrase/{phrase}/sort', 'BehaviorController@phraseSortUpdate')->name('behavior.phrase.sort.update');
    Route::delete('behavior/phrase/{phrase}', 'BehaviorController@phraseDestroy')->name('behavior.phrase.destroy');
    Route::delete('behavior/phrases/{behavior}', 'BehaviorController@destroyPhrases')->name('behavior.phrases.destroy');

    Route::resource('behavior', 'BehaviorController');

    Route::get('profile/', 'ProfilesController@index')->name('profile.index');
    Route::post('profile/', 'ProfilesController@update')->name('profile.update');
    Route::patch('profile/', 'ProfilesController@password')->name('profile.password');

    Route::get('description/{description}/edit/{position?}', 'DescriptionController@edit')->name('description.edit');
    Route::patch('description/{description}', 'DescriptionController@update')->name('description.update');

    Route::get('duplicates/{quantity?}', "PagesController@duplicates")->name('pages.duplicates')->middleware('permission:Duplicates');
    Route::get('keyword-generator', "PagesController@keywordGenerator")->name('pages.keyword')->middleware('permission:Keyword generator');
    Route::get('utm-marks', "PagesController@utmMarks")->name('pages.utm')->middleware('permission:Utm marks');
    Route::get('roi-calculator', "PagesController@roiCalculator")->name('pages.roi')->middleware('permission:Roi calculator');
    Route::get('http-headers/{url?}', "PagesController@httpHeaders")->name('pages.headers')->middleware('permission:Http headers');

    Route::post('/generate-password', 'PasswordGeneratorController@createPassword')->name('generate.password');
    Route::get('/password-generator', 'PasswordGeneratorController@index')->name('pages.password');
    Route::post('/edit-password-comment', 'PasswordGeneratorController@editComment')->name('edit.password.comment');
    Route::post('/remove-password', 'PasswordGeneratorController@remove')->name('remove.password');

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
    Route::get('edit-project/{id}', 'TextEditorController@editProjectView')->name('edit.project');
    Route::post('edit-project', 'TextEditorController@editProject')->name('save.edit.project');
    Route::post('save-project', 'TextEditorController@storeProject')->name('store.project');
    Route::get('project/delete{id}', 'TextEditorController@destroyProject')->name('delete.project');

    Route::get('edit-description/{id}', 'TextEditorController@editDescriptionView')->name('edit.description');
    Route::post('edit-description', 'TextEditorController@editDescription')->name('save.edit.description');
    Route::delete('description/delete/{id}', 'TextEditorController@destroyDescription')->name('delete.description');
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

    Route::get('site-monitoring', 'MonitoringDomainController@index')->name('site.monitoring');
    Route::get('add-site-monitoring', 'MonitoringDomainController@createView')->name('add.site.monitoring.view');
    Route::post('add-site-monitoring', 'MonitoringDomainController@store')->name('add.site.monitoring');
    Route::get('delete-site-monitoring/{id}', 'MonitoringDomainController@remove')->name('delete.site.monitoring');
    Route::post('check-site-monitoring', 'MonitoringDomainController@checkLink')->name('check.domain');
    Route::post('edit-site-monitoring', 'MonitoringDomainController@edit')->name('edit.domain');
    Route::post('delete-domains-monitoring', 'MonitoringDomainController@removeDomains')->name('delete.sites.monitoring');

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
    Route::get('/redirect-to-text-analyzer/{url}', 'TextAnalyzerController@redirectToAnalyse')->name('text.analyzer.redirect');
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

    Route::get('/competitor-analysis', 'SearchCompetitorsController@index')->name('competitor.analysis');
    Route::post('/competitor-analysis', 'SearchCompetitorsController@analyseSites')->name('analysis.sites');
    Route::post('/analyze-nesting', 'SearchCompetitorsController@analyseNesting')->name('analysis.nesting');
    Route::post('/analyze-positions', 'SearchCompetitorsController@analysePositions')->name('analysis.positions');
    Route::post('/analyze-tags', 'SearchCompetitorsController@analyseTags')->name('analysis.tags');
    Route::post('/start-competitor-progress', 'SearchCompetitorsController@startProgressBar')->name('start.competitor.progress');
    Route::post('/get-competitor-progress', 'SearchCompetitorsController@getProgressBar')->name('get.competitor.progress');
    Route::post('/remove-competitor-progress', 'SearchCompetitorsController@removeProgressBar')->name('remove.competitor.progress');
    Route::get('/competitors-config', 'SearchCompetitorsController@config')->name('competitor.config');
    Route::post('/competitors-config', 'SearchCompetitorsController@editConfig')->name('competitor.edit.config');
    Route::post('/get-recommendations', 'SearchCompetitorsController@getRecommendations')->name('competitor.get.recommendations');

    Route::post('/start-relevance-progress-percent', 'RelevanceProgressController@startProgress')->name('start.relevance.progress');
    Route::post('/get-relevance-progress-percent', 'RelevanceProgressController@getProgress')->name('get.relevance.progress');
    Route::post('/end-relevance-progress-percent', 'RelevanceProgressController@endProgress')->name('end.relevance.progress');
    Route::post('/create-link-project-with-tag', 'ProjectRelevanceHistoryTagsController@store')->name('create.link.project.with.tag');
    Route::post('/destroy-link-project-with-tag', 'ProjectRelevanceHistoryTagsController@destroy')->name('destroy.link.project.with.tag');

    Route::post('/remove-page-history', 'RelevanceController@removePageHistory')->name('remove.page.history');
    Route::get('/create-queue', 'RelevanceController@createQueue')->name('create.queue.view');
    Route::post('/create-queue', 'RelevanceController@createTaskQueue')->name('create.queue');
    Route::get('/analyze-relevance', 'RelevanceController@index')->name('relevance-analysis');
    Route::post('/analyze-relevance', 'RelevanceController@analyse')->name('analysis.relevance');
    Route::post('/repeat-analyze-main-page', 'RelevanceController@repeatMainPageAnalysis')->name('repeat.main.page.analysis');
    Route::post('/repeat-analyze-relevance', 'RelevanceController@repeatRelevanceAnalysis')->name('repeat.relevance.analysis');

    Route::get('/history', 'HistoryRelevanceController@index')->name('relevance.history');
    Route::post('/edit-group-name', 'HistoryRelevanceController@editGroupName')->name('edit.group.name');
    Route::post('/edit-history-comment', 'HistoryRelevanceController@editComment')->name('edit.history.comment');
    Route::post('/change-state', 'HistoryRelevanceController@changeCalculateState')->name('change.state');
    Route::get('/show-history/{id}', 'HistoryRelevanceController@show')->name('show.history');
    Route::post('/get-details-history', 'HistoryRelevanceController@getDetailsInfo')->name('get.details.info');
    Route::post('/get-stories', 'HistoryRelevanceController@getStories')->name('get.stories');
    Route::post('/get-stories-v2', 'HistoryRelevanceController@getHistoryInfoV2')->name('get.stories.v2');
    Route::get('/get-file/{id}/{type}', 'HistoryRelevanceController@getFile')->name('get.relevance.file');

    Route::get('/get-history-info/{object}', 'HistoryRelevanceController@getHistoryInfo')->name('get.history.info');
    Route::post('/repeat-scan', 'HistoryRelevanceController@repeatScan')->name('repeat.scan');
    Route::post('/repeat-queue-competitors-scan', 'HistoryRelevanceController@repeatQueueCompetitorsScan')->name('repeat.queue.competitors.scan');
    Route::post('/repeat-queue-main-page-scan', 'HistoryRelevanceController@repeatQueueMainPageScan')->name('repeat.queue.main.page.scan');
    Route::post('/remove-scan-results', 'HistoryRelevanceController@removeEmptyResults')->name('remove.empty.results');
    Route::post('/remove-scan-results-with-filters', 'HistoryRelevanceController@removeEmptyResultsFilters')->name('remove.with.filters');
    Route::post('/repeat-scan-unique-sites', 'HistoryRelevanceController@repeatScanUniqueSites')->name('repeat.scan.unique.sites');
    Route::post('/check-queue-scan-state', 'HistoryRelevanceController@checkQueueScanState')->name('check.queue.scan.state');
    Route::post('/rescan-projects', 'HistoryRelevanceController@rescanProjects')->name('rescan.projects');
    Route::post('/check-state', 'HistoryRelevanceController@checkAnalyseProgress')->name('check.state');
    Route::get('/show-missing-words/{result}', 'HistoryRelevanceController@showMissingWords')->name('show.missing.words');
    Route::get('/show-child-words/{result}', 'HistoryRelevanceController@showChildrenRows')->name('show.children.rows');
    Route::get('/get-relevance-projects/', 'HistoryRelevanceController@getProjects')->name('get.relevance.projects');
    Route::get('/get-all-relevance-projects/', 'HistoryRelevanceController@getAllProjects')->name('get.all.relevance.projects');

    Route::post('/create-tag', 'RelevanceTagsController@store')->name('store.relevance.tag');
    Route::post('/destroy-tag', 'RelevanceTagsController@destroy')->name('destroy.relevance.tag');
    Route::post('/edit-tag', 'RelevanceTagsController@edit')->name('edit.relevance.tag');

    Route::get('/remove-user-jobs', 'AdminController@removeUserJobs')->name('remove.user.jobs');
    Route::get('/relevance-config', 'AdminController@showConfig')->name('show.config');
    Route::post('/change-config', 'AdminController@changeConfig')->name('changeConfig');
    Route::post('/change-cleaning-interval', 'AdminController@changeCleaningInterval')->name('change.cleaning.interval');
    Route::get('/edit-policy-files', 'AdminController@editPolicyFilesView')->name('edit.policy.files.view');
    Route::post('/edit-policy-files', 'AdminController@editPolicyFiles')->name('edit.policy.files');
    Route::post('/get-policy-document', 'AdminController@getPolicyDocument')->name('get.policy.document');
    Route::get('/balance/{response?}', 'BalanceController@index')->name('balance.index');
    Route::post('/counting/yandex-metrics/', 'BalanceController@countingMetrics')->name('counting.metrics');
    Route::resource('balance-add', 'BalanceAddController');

    Route::get('/tariff/{confirm?}/unsubscribe', 'TariffPayController@confirmUnsubscribe')->name('tariff.unsubscribe');
    Route::post('/tariff/total', 'TariffPayController@total')->name('tariff.total');
    Route::resource('tariff', 'TariffPayController');
    Route::resource('profile/user-tariff', 'TariffSettingUserValuesController')->only([
        'create', 'show', 'store', 'destroy'
    ]);

    Route::post('/monitoring/stat/delete-queues', 'MonitoringAdminController@deleteQueues')->name('monitoring.stat.deleteQueues');
    Route::get('/monitoring/stat', 'MonitoringAdminController@statPage')->name('monitoring.stat');
    Route::get('/monitoring/admin', 'MonitoringAdminController@adminPage')->name('monitoring.admin');
    Route::post('/monitoring/admin/settings/update', 'MonitoringSettingsController@updateOrCreate')->name('monitoring.admin.settings.update');
    Route::get('/monitoring/admin/settings/delete/{name}', 'MonitoringSettingsController@destroy')->name('monitoring.admin.settings.delete');
    Route::get('/monitoring/charts', 'MonitoringChartsController@getChartData');

    // Monitoring keywords occurrence
    Route::post('/monitoring/occurrence', 'MonitoringOccurrenceController@update');

    // Monitoring export
    Route::get('/monitoring/{id}/export', 'MonitoringExportsController@download');
    Route::get('/monitoring/{id}/export/edit', 'MonitoringExportsController@edit');

    // Monitoring project creator
    Route::post('monitoring/creator/create', 'MonitoringProjectCreatorController@createProject');
    Route::post('monitoring/creator/update', 'MonitoringProjectCreatorController@updateProject');
    Route::post('monitoring/creator/edit', 'MonitoringProjectCreatorController@editProject');
    Route::post('monitoring/creator/queries', 'MonitoringProjectCreatorController@actionQueries');
    Route::get('monitoring/creator/queries', 'MonitoringProjectCreatorController@getQueries');
    Route::get('monitoring/creator/competitors', 'MonitoringProjectCreatorController@getCompetitors');
    Route::post('monitoring/creator/competitors', 'MonitoringProjectCreatorController@createCompetitors');
    Route::post('monitoring/creator/regions', 'MonitoringProjectCreatorController@actionRegion');

    // Monitoring query price
    Route::get('monitoring/{id}/prices', 'MonitoringKeywordPricesController@index')->name('prices.index');
    Route::post('monitoring/{id}/prices', 'MonitoringKeywordPricesController@action')->name('prices.action');

    // Monitoring project approve or detach projects
    Route::post('monitoring/project/approve', 'MonitoringController@approveOrDetachUser')->name('approve.project');
    Route::post('monitoring/project/attach', 'MonitoringController@attachUser')->name('approve.attach');

    // Monitoring query groups
    Route::post('monitoring/groups', 'MonitoringGroupsController@store');
    Route::get('monitoring/{id}/groups', 'MonitoringGroupsController@index')->name('groups.index');
    Route::post('monitoring/{id}/groups', 'MonitoringGroupsController@action')->name('groups.action');

    Route::resource('monitoring', 'MonitoringController');

    Route::get('/monitoring/{id}/count', 'MonitoringController@getCountProject');
    Route::get('/monitoring/get-competitors-array/{project}', 'MonitoringController@getProjectCompetitors');

    Route::post('/monitoring/add-competitor', 'MonitoringController@addCompetitor')->name('monitoring.add.competitor');
    Route::post('/monitoring/add-competitors', 'MonitoringController@addCompetitors')->name('monitoring.add.competitors');
    Route::post('/monitoring/remove-competitor', 'MonitoringController@removeCompetitor')->name('monitoring.remove.competitor');
    Route::get('/monitoring/projects/get', 'MonitoringController@getProjects')->name('monitoring.projects.get');
    Route::post('/monitoring/projects/get', 'MonitoringController@getProjects')->name('monitoring.projects.get');
    Route::get('/monitoring/{project_id}/child-rows/get/{group_id?}', 'MonitoringController@getChildRowsPageByProject')->name('monitoring.child.rows.get');
    Route::post('/monitoring/competitors/history/positions/', 'MonitoringController@competitorsHistoryPositions')->name('monitoring.competitors.history.positions');
    Route::post('/monitoring/competitors/check-analyse-state', 'MonitoringController@checkChangesDatesState')->name('monitoring.changes.dates.check');
    Route::post('/monitoring/competitors/remove-analyse', 'MonitoringController@removeChangesDatesState')->name('monitoring.changes.dates.remove');
    Route::get('/monitoring/competitors/result-analyse/{project}', 'MonitoringController@resultChangesDatesState')->name('monitoring.changes.dates.result');

    Route::get('/monitoring/top-100/{project}', 'MonitoringTopController@index')->name('monitoring.top100');
    Route::post('/monitoring/get-top/sites', 'MonitoringTopController@getTopSites')->name('monitoring.get.top.sites');

    Route::get('/monitoring/{project_id}/table', 'MonitoringKeywordsController@showDataTable')->name('monitoring.get.table.keywords');
    Route::post('/monitoring/{project_id}/table', 'MonitoringKeywordsController@showDataTable')->name('monitoring.get.table.keywords');

    Route::post('/monitoring/projects/get-positions-for-calendars', 'MonitoringController@getPositionsForCalendars')->name('monitoring.projects.get.positions.for.calendars');

    Route::post('/monitoring/get/column/settings', 'MonitoringController@getColumnSettings');
    Route::post('/monitoring/set/column/settings', 'MonitoringController@setColumnSettings');
    Route::post('/monitoring/project/set/column/settings', 'MonitoringController@setColumnSettingsForProject');
    Route::post('/monitoring/project/get/column/settings', 'MonitoringController@getColumnSettingsForProject');

    Route::get('/monitoring/project/update-data-table', 'MonitoringController@updateDataTableProjects');
    Route::post('/monitoring/parse/positions/project', 'MonitoringController@parsePositionsInProject');
    Route::post('/monitoring/parse/positions/project/keys', 'MonitoringController@parsePositionsInProjectKeys');

    Route::resource('monitoring/keywords', 'MonitoringKeywordsController');
    Route::get('/monitoring/keywords/{project_id}/create', 'MonitoringKeywordsController@create');
    Route::get('/monitoring/keywords/empty/modal', 'MonitoringKeywordsController@showEmptyModal');
    Route::get('/monitoring/keywords/show/controls', 'MonitoringKeywordsController@showControlsPanel')->name('keywords.show.controls.panel');
    Route::get('/monitoring/keywords/{id}/edit-plural', 'MonitoringKeywordsController@editPlural')->name('keywords.edit.plural');
    Route::post('/monitoring/keywords/update-plural', 'MonitoringKeywordsController@updatePlural')->name('keywords.update.plural');
    Route::patch('/monitoring/keywords/{project_id}/set-test-positions', 'MonitoringKeywordsController@setTestPositions')->name('keywords.set.test.positions');

    Route::post('monitoring/keywords/queue', 'MonitoringKeywordsController@addingQueue')->name('keywords.queue');

    Route::get('/monitoring/{project}/competitors', 'MonitoringController@monitoringCompetitors')->name('monitoring.competitors');
    Route::post('/monitoring/projects/competitors', 'MonitoringController@getCompetitorsInfo')->name('monitoring.get.competitors');
    Route::post('/monitoring/projects/competitors-domain', 'MonitoringController@getCompetitorsDomain')->name('monitoring.get.competitors.domain');
    Route::get('/monitoring/{project}/competitors/positions', 'MonitoringController@competitorsPositions')->name('monitoring.competitors.positions');
    Route::post('/monitoring/competitors/visibility', 'MonitoringController@getStatistics')->name('monitoring.get.competitors.statistics');
    Route::post('/monitoring/wait-result', 'MonitoringController@getMonitoringCompetitorsResult')->name('monitoring.wait.result');

    Route::get('/share-my-projects', 'SharingController@index')->name('sharing.view');
    Route::get('/share-my-project-config/{project}', 'SharingController@shareProjectConf')->name('share.project.conf');
    Route::post('/get-access-to-my-project', 'SharingController@setAccess')->name('get.access.to.my.project');
    Route::post('/get-multiply-access-to-my-project', 'SharingController@setMultiplyAccess')->name('get.multiply.access.to.my.project');
    Route::post('/remove-multiply-access-to-my-project', 'SharingController@removeMultiplyAccess')->name('remove.multiply.access');
    Route::post('/remove-access-to-my-project', 'SharingController@removeAccess')->name('remove.access.to.my.project');
    Route::post('/remove-guest-access', 'SharingController@removeGuestAccess')->name('remove.guest.access');
    Route::post('/change-access-to-my-project', 'SharingController@changeAccess')->name('change.access.to.my.project');
    Route::get('/access-projects', 'SharingController@accessProject')->name('access.project');
    Route::get('/all-projects', 'AdminController@relevanceHistoryProjects')->name('all.relevance.projects');
    Route::get('/get-queue-count', 'AdminController@getCountQueue')->name('get.queue.count');
    Route::get('/get-user-jobs', 'AdminController@getUserJobs')->name('get.user.jobs');

    Route::get('/show-though/{though}', 'RelevanceThoughController@show')->name('show-though');
    Route::post('/start-through-analyse', 'RelevanceThoughController@startThroughAnalyse')->name('start.through.analyse');
    Route::post('/get-slice-result', 'RelevanceThoughController@getSliceResult')->name('get.slice.result');

    Route::post('/set-cluster-cleaning-interval', 'ClusterController@setCleaningInterval')->name('set.cluster.cleaning.interval');
    Route::get('/cluster', 'ClusterController@index')->name('cluster');
    Route::post('/analysis-cluster', 'ClusterController@analyseCluster')->name('analysis.cluster');
    Route::get('/start-cluster-progress', 'ClusterController@startProgress')->name('start.cluster.progress');
    Route::get('/get-cluster-progress/{id}', 'ClusterController@getProgress')->name('get.cluster.progress');
    Route::get('/get-cluster-progress/{id}/modify', 'ClusterController@getProgressModify')->name('get.cluster.progress.modify');
    Route::get('/destroy-progress/{progress}', 'ClusterController@destroyProgress')->name('destroy.progress');
    Route::get('/cluster-projects', 'ClusterController@clusterProjects')->name('cluster.projects');
    Route::post('/edit-cluster-project', 'ClusterController@edit')->name('cluster.edit');
    Route::post('/get-cluster-request/', 'ClusterController@getClusterRequest')->name('get.cluster.request');
    Route::get('/show-cluster-result/{id}', 'ClusterController@showResult')->name('show.cluster.result');
    Route::get('/wait-cluster-result/id', 'ClusterController@waitClusterResult')->name('wait.cluster.result');
    Route::get('/download-cluster-result/{cluster}/{type}', 'ClusterController@downloadClusterResult')->name('download.cluster.result');
    Route::get('/cluster-configuration', 'ClusterController@clusterConfiguration')->name('cluster.configuration');
    Route::post('/change-cluster-configuration', 'ClusterController@changeClusterConfiguration')->name('change.cluster.configuration');
    Route::post('/fast-scan-clusters', 'ClusterController@fastScanClusters')->name('fast.scan.clusters');
    Route::post('/set-cluster-relevance-url', 'ClusterController@setClusterRelevanceUrl')->name('set.cluster.relevance.url');
    Route::post('/set-cluster-relevance-urls', 'ClusterController@setClusterRelevanceUrls')->name('set.cluster.relevance.urls');
    Route::post('/download-cluster-sites', 'ClusterController@downloadClusterSites')->name('download.cluster.sites');
    Route::post('/download-cluster-competitors', 'ClusterController@downloadClusterCompetitors')->name('download.cluster.competitors');
    Route::post('/download-cluster-phrases', 'ClusterController@downloadClusterPhrases')->name('download.cluster.phrases');
    Route::get('/edit-clusters/{cluster}', 'ClusterController@editClusters')->name('edit.clusters');
    Route::post('/confirmation-new-cluster', 'ClusterController@confirmationNewCluster')->name('confirmation.new.cluster');
    Route::post('/edit-clusters', 'ClusterController@editCluster')->name('edit.cluster');
    Route::post('/check-group-name/', 'ClusterController@checkGroupName')->name('check.group.name');
    Route::post('/change-group-name/', 'ClusterController@changeGroupName')->name('change.group.name');
    Route::post('/reset-all-cluster-changes', 'ClusterController@resetAllChanges')->name('reset.all.cluster.changes');
    Route::post('/download-cluster-group', 'ClusterController@downloadClusterGroup')->name('download.cluster.group');
    Route::post('/save-html', 'ClusterController@saveTree')->name('save.clusters.tree');

    Route::get('/configuration-menu', 'PositionMenuItemsController@index')->name('menu.config');
    Route::post('/configuration-menu', 'PositionMenuItemsController@edit')->name('configuration.menu');
    Route::post('/restore-configuration-menu', 'PositionMenuItemsController@remove')->name('restore.configuration.menu');

    Route::get('/partners', 'PartnersController@partners')->name('partners');
    Route::get('/partners/add-group', 'PartnersController@addGroup')->name('partners.add.group');
    Route::post('/partners/add-group', 'PartnersController@saveGroup')->name('partners.save.group');
    Route::post('/partners/remove-group', 'PartnersController@removeGroup')->name('partners.remove.group');
    Route::get('/partners/edit-group/{group}', 'PartnersController@editGroupView')->name('partners.edit.group');
    Route::post('/partners/edit-group', 'PartnersController@editGroup')->name('partners.edit.save');
    Route::get('/partners/add-item', 'PartnersController@addItem')->name('partners.add.item');
    Route::post('/partners/add-item', 'PartnersController@saveItem')->name('partners.save.item');
    Route::post('/partners/remove-item', 'PartnersController@removeItem')->name('partners.remove.item');
    Route::get('/partners/edit-item/{item}', 'PartnersController@editItemView')->name('partners.edit.item');
    Route::get('/partners/admin', 'PartnersController@admin')->name('partners.admin');
    Route::post('/partners/edit-item/', 'PartnersController@editItem')->name('partners.save.edit.item');
    Route::get('/partners/r/{short_link}', 'PartnersController@redirect')->name('partners.redirect');

    Route::post('/click-tracking', 'HomeController@clickTracking')->name('click.tracking');

    Route::get('/checklist', 'CheckListController@index')->name('checklist');
    Route::get('/checklist-tasks/{checklist}', 'CheckListController@tasks')->name('checklist.tasks');
    Route::post('/store-checklist', 'CheckListController@store')->name('store.checklist');
    Route::post('/add-new-tasks', 'CheckListController@update')->name('update.checklist');
    Route::post('/get-checklist', 'CheckListController@getChecklists')->name('get.checklists');
    Route::get('/move-checklist-to-archive/{project}', 'CheckListController@inArchive')->name('in.archive');
    Route::get('/restore-checklist/{project}', 'CheckListController@restore')->name('restore.checklist');
    Route::get('/get-checklist-archive', 'CheckListController@archive')->name('checklist.archive');
    Route::post('/checklist-tasks/', 'CheckListController@getTasks')->name('checklist.tasks');
    Route::post('/edit-checklist-task/', 'CheckListController@editTask')->name('edit.checklist.task');

    Route::get('/remove-checklist/{project}', 'CheckListController@destroy')->name('destroy');
    Route::post('/create-label', 'CheckListController@createLabel')->name('create.label');
    Route::get('/remove-label/{label}', 'CheckListController@removeLabel')->name('remove.label');
    Route::post('/edit-label/', 'CheckListController@editLabel')->name('edit.label');

    Route::post('/add-checklists-labels-relations', 'CheckListController@createRelation')->name('create.checklist.relation');
    Route::post('/remove-checklist-relation/', 'CheckListController@removeRelation')->name('remove.checklist.relation');

    Route::post('/remove-checklist-task/', 'CheckListController@removeTask')->name('remove.checklist.task');
    Route::post('/add-new-tasks/', 'CheckListController@addNewTasks')->name('add.new.tasks.in.checklist');
});

Route::get('/test', function () {
    $client = new Client();
    $fullUrl = 'https://almamed.su/category/veterinariya/';

    $response = $client->get($fullUrl);
    $html = $response->getBody()->getContents();

    $document = new HtmlDocument();
    $document->load(mb_strtolower($html));

    $icon = $document->find('link[rel="shortcut icon"]');

    if ($icon === []) {
        $icon = $document->find('link[rel="icon"]');
    }

    if ($icon === []) {
        $icon = $document->find('link[rel="apple-touch-icon"]');
    }

    $md5 = md5(microtime(true));

    if (isset($icon[0]->attr['href'])) {
        if(filter_var($icon[0]->attr['href'], FILTER_VALIDATE_URL)){
            $faviconData = file_get_contents($icon[0]->attr['href']);
        } else if(filter_var("https://" . parse_url($fullUrl)['host'] . $icon[0]->attr['href'], FILTER_VALIDATE_URL)){
            $faviconData = file_get_contents("https://" . parse_url($fullUrl)['host'] . $icon[0]->attr['href']);
        } else {

        }
        $path = "/checklist/ $md5.jpg";
        Storage::put($path, $faviconData);
    }
});
