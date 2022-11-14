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

use App\Classes\Xml\RiverFacade;
use App\Classes\Xml\SimplifiedXmlFacade;
use App\LinguaStem;
use App\TextAnalyzer;
use Illuminate\Support\Facades\Auth;

Route::get('info', function () {
    phpinfo();
});

Auth::routes(['verify' => true]);
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
    Route::post('/project-sortable', 'HomeController@projectSort');
    Route::post('/menu-item-sortable', 'HomeController@menuItemSort')->name('menu.item.sort');
    Route::post('/get-description-projects', 'HomeController@getDescriptionProjects')->name('get.description.projects');

    Route::resource('main-projects', 'DescriptionProjectForAdminController');

    Route::get('users/{id}/login', 'UsersController@login')->name('users.login');
    Route::get('/get-verified-users/{type}', 'UsersController@getFile')->name('get.verified.users');
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

    Route::post('/generate-password', 'PasswordGeneratorController@createPassword')->name('generate.password');
    Route::get('/password-generator', 'PasswordGeneratorController@index')->name('pages.password');

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

    Route::get('/start-relevance-progress-percent', 'RelevanceProgressController@startProgress')->name('start.relevance.progress');
    Route::post('/get-relevance-progress-percent', 'RelevanceProgressController@getProgress')->name('get.relevance.progress');
    Route::post('/end-relevance-progress-percent', 'RelevanceProgressController@endProgress')->name('end.relevance.progress');
    Route::post('/create-link-project-with-tag', 'ProjectRelevanceHistoryTagsController@store')->name('create.link.project.with.tag');
    Route::post('/destroy-link-project-with-tag', 'ProjectRelevanceHistoryTagsController@destroy')->name('destroy.link.project.with.tag');

    Route::post('/remove-page-history', 'RelevanceController@removePageHistory')->name('remove.page.history');
    Route::get('/create-queue', 'RelevanceController@createQueue')->name('create.queue.view');
    Route::post('/create-queue', 'RelevanceController@createTaskQueue')->name('create.queue');
    Route::get('/analyze-relevance', 'RelevanceController@index')->name('relevance-analysis');
    Route::post('/analyze-relevance', 'RelevanceController@analysis')->name('analysis.relevance');
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

    Route::post('/create-tag', 'RelevanceTagsController@store')->name('store.relevance.tag');
    Route::post('/destroy-tag', 'RelevanceTagsController@destroy')->name('destroy.relevance.tag');
    Route::post('/edit-tag', 'RelevanceTagsController@edit')->name('edit.relevance.tag');

    Route::get('/relevance-config', 'AdminController@showConfig')->name('show.config');
    Route::post('/change-config', 'AdminController@changeConfig')->name('changeConfig');
    Route::post('/change-cleaning-interval', 'AdminController@changeCleaningInterval')->name('change.cleaning.interval');
    Route::get('/edit-policy-files', 'AdminController@editPolicyFilesView')->name('edit.policy.files.view');
    Route::post('/edit-policy-files', 'AdminController@editPolicyFiles')->name('edit.policy.files');
    Route::post('/get-policy-document', 'AdminController@getPolicyDocument')->name('get.policy.document');

    Route::get('/balance/{response?}', 'BalanceController@index')->name('balance.index');
    Route::resource('balance-add', 'BalanceAddController');

    Route::get('/tariff/{confirm?}/unsubscribe', 'TariffPayController@confirmUnsubscribe')->name('tariff.unsubscribe');
    Route::post('/tariff/total', 'TariffPayController@total')->name('tariff.total');
    Route::resource('tariff', 'TariffPayController');

    Route::post('/monitoring/stat/delete-queues', 'MonitoringAdminController@deleteQueues')->name('monitoring.stat.deleteQueues');
    Route::get('/monitoring/stat', 'MonitoringAdminController@statPage')->name('monitoring.stat');
    Route::get('/monitoring/admin', 'MonitoringAdminController@adminPage')->name('monitoring.admin');
    Route::post('/monitoring/admin/settings/update', 'MonitoringSettingsController@updateOrCreate')->name('monitoring.admin.settings.update');
    Route::get('/monitoring/admin/settings/delete/{name}', 'MonitoringSettingsController@destroy')->name('monitoring.admin.settings.delete');
    Route::get('/monitoring/charts', 'MonitoringChartsController@getChartData');

    Route::resource('monitoring', 'MonitoringController');
    Route::get('/monitoring/projects/get', 'MonitoringController@getProjects')->name('monitoring.projects.get');
    Route::get('/monitoring/{project_id}/child-rows/get', 'MonitoringController@getChildRowsPageByProject')->name('monitoring.child.rows.get');

    Route::get('/monitoring/{project_id}/table', 'MonitoringController@getTableKeywords')->name('monitoring.get.table.keywords');
    Route::post('/monitoring/{project_id}/table', 'MonitoringController@getTableKeywords')->name('monitoring.get.table.keywords');

    Route::post('/monitoring/projects/get-positions-for-calendars', 'MonitoringController@getPositionsForCalendars')->name('monitoring.projects.get.positions.for.calendars');
    Route::post('/monitoring/project/set/column/settings', 'MonitoringController@setColumnSettingsForProject');
    Route::post('/monitoring/project/get/column/settings', 'MonitoringController@getColumnSettingsForProject');
    Route::get('/monitoring/project/remove/cache', 'MonitoringController@removeCache')->name('monitoring.projects.remove.cache');
    Route::post('/monitoring/parse/positions/project', 'MonitoringController@parsePositionsInProject');
    Route::post('/monitoring/parse/positions/all/projects', 'MonitoringController@parsePositionsAllProject');
    Route::post('/monitoring/parse/positions/project/keys', 'MonitoringController@parsePositionsInProjectKeys');

    Route::resource('monitoring/keywords', 'MonitoringKeywordsController');
    Route::get('/monitoring/keywords/{project_id}/create', 'MonitoringKeywordsController@create');
    Route::get('/monitoring/keywords/empty/modal', 'MonitoringKeywordsController@showEmptyModal');
    Route::get('/monitoring/keywords/show/controls', 'MonitoringKeywordsController@showControlsPanel')->name('keywords.show.controls.panel');
    Route::get('/monitoring/keywords/{id}/edit-plural', 'MonitoringKeywordsController@editPlural')->name('keywords.edit.plural');
    Route::post('/monitoring/keywords/update-plural', 'MonitoringKeywordsController@updatePlural')->name('keywords.update.plural');
    Route::patch('/monitoring/keywords/{project_id}/set-test-positions', 'MonitoringKeywordsController@setTestPositions')->name('keywords.set.test.positions');

    Route::resource('monitoring/groups', 'MonitoringGroupsController');
    Route::post('monitoring/keywords/queue', 'MonitoringKeywordsController@addingQueue')->name('keywords.queue');

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

    Route::get('/cluster', 'ClusterController@index')->name('cluster');
    Route::post('/analysis-cluster', 'ClusterController@analysisCluster')->name('analysis.cluster');
    Route::post('/repeat-analysis-cluster', 'ClusterController@repeatAnalysisCluster')->name('repeat.analysis.cluster');
    Route::get('/start-cluster-progress', 'ClusterController@startProgress')->name('start.cluster.progress');
    Route::get('/get-cluster-progress/{progress}', 'ClusterController@getProgress')->name('get.cluster.progress');
    Route::get('/destroy-progress/{progress}', 'ClusterController@destroyProgress')->name('destroy.progress');
    Route::get('/cluster-projects', 'ClusterController@clusterProjects')->name('cluster.projects');
    Route::post('/edit-cluster-project', 'ClusterController@edit')->name('cluster.edit');
    Route::post('/get-cluster-request/', 'ClusterController@getClusterRequest')->name('get.cluster.request');
    Route::post('/repeat-analysis', 'ClusterController@repeatAnalysis')->name('repeat.cluster.analysis');
    Route::get('/show-cluster-result/{cluster}', 'ClusterController@showResult')->name('show.cluster.result');
    Route::get('/download-cluster-result/{cluster}/{type}', 'ClusterController@downloadClusterResult')->name('download.cluster.result');
    Route::get('/cluster-configuration', 'ClusterController@clusterConfiguration')->name('cluster.configuration');
    Route::post('/change-cluster-configuration', 'ClusterController@changeClusterConfiguration')->name('change.cluster.configuration');

    Route::get('/test', function () {
        $request = json_decode('{"_token":"J7tH99xkcmB85ieyZr8vxs3w64JsgADBPFEhdBQJ","save":"1","region":"213","count":"20","phrases":"karoq skoda \u0446\u0435\u043d\u0430\nskoda karoq 2022 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438 \u0438 \u0446\u0435\u043d\u044b\nskoda karoq \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438\nskoda karoq \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438 \u0438 \u0446\u0435\u043d\u044b\nskoda karoq \u043a\u0443\u043f\u0438\u0442\u044c\n\u043a\u0430\u0440\u043e\u043a \u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438 \u0438 \u0446\u0435\u043d\u044b \u0443 \u043e\u0444\u0438\u0446\u0438\u0430\u043b\u044c\u043d\u043e\u0433\u043e\n\u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438 \u0448\u043a\u043e\u0434\u0430 \u043a\u0430\u0440\u043e\u043a\n\u043a\u0443\u043f\u0438\u0442\u044c \u0448\u043a\u043e\u0434\u0430 \u043a\u0430\u0440\u043e\u043a\n\u043a\u0443\u043f\u0438\u0442\u044c \u0448\u043a\u043e\u0434\u0430 \u043a\u0430\u0440\u043e\u043a \u0432 \u0431\u0435\u043b\u0433\u043e\u0440\u043e\u0434\u0435\n\u043e\u0444\u0438\u0446\u0438\u0430\u043b\u044c\u043d\u044b\u0439 \u0434\u0438\u043b\u0435\u0440 \u0448\u043a\u043e\u0434\u0430 \u043a\u0430\u0440\u043e\u043a\n\u0448\u043a\u043e\u0434\u0430 \u043a\u0430\u0440\u043e\u043a 2022\n\u0448\u043a\u043e\u0434\u0430 \u043a\u0430\u0440\u043e\u043a 2022 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438\n\u0448\u043a\u043e\u0434\u0430 \u043a\u0430\u0440\u043e\u043a 2022 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438 \u0438 \u0446\u0435\u043d\u044b\n\u0448\u043a\u043e\u0434\u0430 \u043a\u0430\u0440\u043e\u043a 2022 \u0446\u0435\u043d\u0430 \u0438 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u044f \u043e\u0444\u0438\u0446\u0438\u0430\u043b\u044c\u043d\u044b\u0439\n\u0448\u043a\u043e\u0434\u0430 \u043a\u0430\u0440\u043e\u043a \u0431\u0435\u043b\u0433\u043e\u0440\u043e\u0434\n\u0448\u043a\u043e\u0434\u0430 \u043a\u0430\u0440\u043e\u043a \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438 \u0438 \u0446\u0435\u043d\u044b\n\u0448\u043a\u043e\u0434\u0430 \u043a\u0430\u0440\u043e\u043a \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438 \u0438 \u0446\u0435\u043d\u044b \u0431\u0435\u043b\u0433\u043e\u0440\u043e\u0434\n\u0448\u043a\u043e\u0434\u0430 \u043a\u0430\u0440\u043e\u043a \u043e\u0444\u0438\u0446\u0438\u0430\u043b\u044c\u043d\u044b\u0435 \u0446\u0435\u043d\u044b\n\u0448\u043a\u043e\u0434\u0430 \u043a\u0430\u0440\u043e\u043a \u043e\u0444\u0438\u0446\u0438\u0430\u043b\u044c\u043d\u044b\u0439 \u0434\u0438\u043b\u0435\u0440 \u0446\u0435\u043d\u044b\n\u0448\u043a\u043e\u0434\u0430 \u043a\u0430\u0440\u043e\u043a \u0446\u0435\u043d\u0430\n\u0448\u043a\u043e\u0434\u0430 \u043a\u0430\u0440\u043e\u043a \u0446\u0435\u043d\u0430 2022\n\u0448\u043a\u043e\u0434\u0430 \u043a\u0430\u0440\u043e\u043a \u0446\u0435\u043d\u0430 \u0432 \u0431\u0435\u043b\u0433\u043e\u0440\u043e\u0434\u0435\n\u0448\u043a\u043e\u0434\u0430 \u043a\u0430\u0440\u043e\u043a \u0446\u0435\u043d\u0430 \u0438 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u044f \u043e\u0444\u0438\u0446\u0438\u0430\u043b\u044c\u043d\u044b\u0439 \u0441\u0430\u0439\u0442\n\u0448\u043a\u043e\u0434\u0430 \u043a\u0430\u0440\u043e\u043a \u0446\u0435\u043d\u0430 \u0443 \u0434\u0438\u043b\u0435\u0440\u0430\nskoda rapid\nskoda rapid \u0431\u0435\u043b\u0433\u043e\u0440\u043e\u0434\nskoda rapid \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438 \u0438 \u0446\u0435\u043d\u044b\nskoda rapid \u043a\u0443\u043f\u0438\u0442\u044c\nskoda rapid \u0446\u0435\u043d\u0430\n\u0161koda rapid 2022 \u0446\u0435\u043d\u0430 \u0438 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u044f\n\u043a\u0443\u043f\u0438\u0442\u044c \u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434\n\u043a\u0443\u043f\u0438\u0442\u044c \u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434 \u0432 \u0431\u0435\u043b\u0433\u043e\u0440\u043e\u0434\u0435\n\u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434\n\u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434 2022 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438\n\u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434 2022 \u0446\u0435\u043d\u0430\n\u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434 \u0431\u0435\u043b\u0433\u043e\u0440\u043e\u0434\n\u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434 \u0431\u0435\u043b\u0433\u043e\u0440\u043e\u0434 \u043e\u0444\u0438\u0446\u0438\u0430\u043b\u044c\u043d\u044b\u0439 \u0434\u0438\u043b\u0435\u0440\n\u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434 \u0431\u0435\u043b\u0433\u043e\u0440\u043e\u0434 \u043e\u0444\u0438\u0446\u0438\u0430\u043b\u044c\u043d\u044b\u0439 \u0434\u0438\u043b\u0435\u0440 \u0446\u0435\u043d\u044b\n\u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438\n\u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u044f \u0438 \u0446\u0435\u043d\u044b\n\u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434 \u0444\u043b 2022\n\u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434 \u0446\u0435\u043d\u0430\n\u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434 \u0446\u0435\u043d\u0430 \u0431\u0435\u043b\u0433\u043e\u0440\u043e\u0434\n\u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434 \u0446\u0435\u043d\u0430 \u0438 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u044f 2022\nskoda kodiaq\nskoda kodiaq \u0446\u0435\u043d\u0430\n\u0161koda kodiaq \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438 \u0446\u0435\u043d\u0430\n\u043a\u0443\u043f\u0438\u0442\u044c \u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a\n\u043a\u0443\u043f\u0438\u0442\u044c \u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0431\u0435\u043b\u0433\u043e\u0440\u043e\u0434\n\u043a\u0443\u043f\u0438\u0442\u044c \u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0443 \u043e\u0444\u0438\u0446\u0438\u0430\u043b\u044c\u043d\u043e\u0433\u043e \u0434\u0438\u043b\u0435\u0440\u0430\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a 2022 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a 2022 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438 \u0438 \u0446\u0435\u043d\u044b\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0431\u0435\u043b\u0433\u043e\u0440\u043e\u0434\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0431\u0435\u043b\u0433\u043e\u0440\u043e\u0434 \u0446\u0435\u043d\u044b\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438 \u0438 \u0446\u0435\u043d\u044b\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u043e\u0444\u0438\u0446\u0438\u0430\u043b\u044c\u043d\u044b\u0439 \u0434\u0438\u043b\u0435\u0440\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0446\u0435\u043d\u0430\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0446\u0435\u043d\u0430 \u043e\u0444\u0438\u0446\u0438\u0430\u043b\u044c\u043d\u044b\u0439 \u0434\u0438\u043b\u0435\u0440\nskoda octavia 2022\nskoda octavia 2022 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438 \u0438 \u0446\u0435\u043d\u044b\nskoda octavia \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438 \u0438 \u0446\u0435\u043d\u044b\nskoda octavia \u0446\u0435\u043d\u0430\n\u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438 \u0448\u043a\u043e\u0434\u044b \u043e\u043a\u0442\u0430\u0432\u0438\u0438\n\u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u044f \u043d\u043e\u0432\u043e\u0439 \u0448\u043a\u043e\u0434\u044b \u043e\u043a\u0442\u0430\u0432\u0438\u0438 \u0438 \u0446\u0435\u043d\u0430\n\u043a\u0443\u043f\u0438\u0442\u044c \u043d\u043e\u0432\u0443\u044e skoda octavia\n\u043a\u0443\u043f\u0438\u0442\u044c \u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f\n\u043a\u0443\u043f\u0438\u0442\u044c \u0448\u043a\u043e\u0434\u0443 \u043e\u043a\u0442\u0430\u0432\u0438\u044e\n\u043a\u0443\u043f\u0438\u0442\u044c \u0448\u043a\u043e\u0434\u0443 \u043e\u043a\u0442\u0430\u0432\u0438\u044e \u0431\u0435\u043b\u0433\u043e\u0440\u043e\u0434\n\u043a\u0443\u043f\u0438\u0442\u044c \u0448\u043a\u043e\u0434\u0443 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u0432 \u0431\u0435\u043b\u0433\u043e\u0440\u043e\u0434\u0435\n\u043d\u043e\u0432\u0430\u044f skoda octavia 2022\n\u043d\u043e\u0432\u0430\u044f \u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u0446\u0435\u043d\u0430 \u0438 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u044f\n\u0446\u0435\u043d\u0430 \u0448\u043a\u043e\u0434\u044b \u043e\u043a\u0442\u0430\u0432\u0438\u0438\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438\nskoda \u0437\u0430\u043f\u0447\u0430\u0441\u0442\u0438 \u043a\u0443\u043f\u0438\u0442\u044c\n\u0437\u0430\u043f\u0447\u0430\u0441\u0442\u0438 skoda \u0446\u0435\u043d\u044b\n\u043a\u0430\u0442\u0430\u043b\u043e\u0433 \u0434\u0435\u0442\u0430\u043b\u0435\u0439 \u0448\u043a\u043e\u0434\u0430\n\u043a\u0443\u043f\u0438\u0442\u044c \u0434\u0435\u0442\u0430\u043b\u0438 \u0448\u043a\u043e\u0434\u0430\n\u043a\u0443\u043f\u0438\u0442\u044c \u0437\u0430\u043f\u0447\u0430\u0441\u0442\u0438 \u043d\u0430 \u0448\u043a\u043e\u0434\u0443\n\u043e\u0440\u0438\u0433\u0438\u043d\u0430\u043b\u044c\u043d\u044b\u0435 \u0434\u0435\u0442\u0430\u043b\u0438 \u0448\u043a\u043e\u0434\u0430\n\u043e\u0440\u0438\u0433\u0438\u043d\u0430\u043b\u044c\u043d\u044b\u0435 \u0437\u0430\u043f\u0447\u0430\u0441\u0442\u0438 skoda\n\u043e\u0440\u0438\u0433\u0438\u043d\u0430\u043b\u044c\u043d\u044b\u0435 \u0437\u0430\u043f\u0447\u0430\u0441\u0442\u0438 \u0448\u043a\u043e\u0434\u0430\n\u043e\u0440\u0438\u0433\u0438\u043d\u0430\u043b\u044c\u043d\u044b\u0435 \u0437\u0430\u043f\u0447\u0430\u0441\u0442\u0438 \u0448\u043a\u043e\u0434\u0430 \u043a\u0443\u043f\u0438\u0442\u044c\n\u043e\u0440\u0438\u0433\u0438\u043d\u0430\u043b\u044c\u043d\u044b\u0439 \u043a\u0430\u0442\u0430\u043b\u043e\u0433 \u0437\u0430\u043f\u0447\u0430\u0441\u0442\u0435\u0439 skoda\n\u043e\u0440\u0438\u0433\u0438\u043d\u0430\u043b\u044c\u043d\u044b\u0439 \u043a\u0430\u0442\u0430\u043b\u043e\u0433 \u0437\u0430\u043f\u0447\u0430\u0441\u0442\u0435\u0439 \u0448\u043a\u043e\u0434\u0430\n\u0448\u043a\u043e\u0434\u0430 \u0437\u0430\u043f\u0447\u0430\u0441\u0442\u0438 \u0446\u0435\u043d\u0430\nskoda octavia hockey edition\nskoda octavia hockey edition \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u044f\n\u0161koda octavia hockey edition\n\u0161koda octavia hockey edition 2022\n\u043a\u0443\u043f\u0438\u0442\u044c skoda octavia hockey edition\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u0445\u043e\u043a\u043a\u0435\u0439 \u044d\u0434\u0438\u0448\u043d\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u0445\u043e\u043a\u043a\u0435\u0439 \u044d\u0434\u0438\u0448\u043d 2022\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u0445\u043e\u043a\u043a\u0435\u0439 \u044d\u0434\u0438\u0448\u043d \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u044f\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u0445\u043e\u043a\u043a\u0435\u0439 \u044d\u0434\u0438\u0448\u043d \u043a\u0443\u043f\u0438\u0442\u044c\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u0445\u043e\u043a\u043a\u0435\u0439 \u044d\u0434\u0438\u0448\u043d \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u0445\u043e\u043a\u043a\u0435\u0439 \u044d\u0434\u0438\u0448\u043d \u0446\u0435\u043d\u0430\nskoda kodiaq scout\n\u0161koda kodiaq scout\n\u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u044f \u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0441\u043a\u0430\u0443\u0442\n\u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438 \u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0441\u043a\u0430\u0443\u0442 2022\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0441\u043a\u0430\u0443\u0442\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0441\u043a\u0430\u0443\u0442 2022\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0441\u043a\u0430\u0443\u0442 2022 \u0446\u0435\u043d\u0430\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0441\u043a\u0430\u0443\u0442 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438 \u0438 \u0446\u0435\u043d\u044b\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0441\u043a\u0430\u0443\u0442 \u043a\u0443\u043f\u0438\u0442\u044c\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0441\u043a\u0430\u0443\u0442 \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0441\u043a\u0430\u0443\u0442 \u0446\u0435\u043d\u0430\nskoda \u0430\u0432\u0442\u043e \u0441 \u043f\u0440\u043e\u0431\u0435\u0433\u043e\u043c\nskoda \u0441 \u043f\u0440\u043e\u0431\u0435\u0433\u043e\u043c\nskoda \u0441 \u043f\u0440\u043e\u0431\u0435\u0433\u043e\u043c \u0446\u0435\u043d\u0430\n\u0430\u0432\u0442\u043e \u0441 \u043f\u0440\u043e\u0431\u0435\u0433\u043e\u043c \u0448\u043a\u043e\u0434\u0430\n\u0430\u0432\u0442\u043e\u043c\u043e\u0431\u0438\u043b\u0438 \u0448\u043a\u043e\u0434\u0430 \u0441 \u043f\u0440\u043e\u0431\u0435\u0433\u043e\u043c\n\u043a\u0443\u043f\u0438\u0442\u044c skoda \u0441 \u043f\u0440\u043e\u0431\u0435\u0433\u043e\u043c\n\u043a\u0443\u043f\u0438\u0442\u044c \u0430\u0432\u0442\u043e \u0441 \u043f\u0440\u043e\u0431\u0435\u0433\u043e\u043c \u0448\u043a\u043e\u0434\u0430\n\u043a\u0443\u043f\u0438\u0442\u044c \u0430\u0432\u0442\u043e\u043c\u043e\u0431\u0438\u043b\u044c \u0448\u043a\u043e\u0434\u0430 \u0441 \u043f\u0440\u043e\u0431\u0435\u0433\u043e\u043c\n\u043f\u0440\u043e\u0434\u0430\u0436\u0430 \u0430\u0432\u0442\u043e\u043c\u043e\u0431\u0438\u043b\u0435\u0439 \u0448\u043a\u043e\u0434\u0430 \u0441 \u043f\u0440\u043e\u0431\u0435\u0433\u043e\u043c\n\u0448\u043a\u043e\u0434\u0430 \u0431\u0435\u043b\u0433\u043e\u0440\u043e\u0434 \u0430\u0432\u0442\u043e \u0441 \u043f\u0440\u043e\u0431\u0435\u0433\u043e\u043c\nskoda rapid hockey edition\nskoda rapid \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u044f hockey edition\n\u0161koda rapid hockey edition\n\u0161koda rapid hockey edition 2022\n\u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434 2022 \u0445\u043e\u043a\u043a\u0435\u0439 \u044d\u0434\u0438\u0448\u043d\n\u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u044f \u0445\u043e\u043a\u043a\u0435\u0439 \u044d\u0434\u0438\u0448\u043d\n\u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u044f \u0445\u043e\u043a\u043a\u0435\u0439 \u044d\u0434\u0438\u0448\u043d \u0446\u0435\u043d\u0430\n\u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434 \u0445\u043e\u043a\u043a\u0435\u0439 \u044d\u0434\u0438\u0448\u043d\n\u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434 \u0445\u043e\u043a\u043a\u0435\u0439 \u044d\u0434\u0438\u0448\u043d \u043a\u0443\u043f\u0438\u0442\u044c\n\u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434 \u0445\u043e\u043a\u043a\u0435\u0439 \u044d\u0434\u0438\u0448\u043d \u0446\u0435\u043d\u0430\nskoda octavia combi\nskoda octavia combi \u0446\u0435\u043d\u0430\n\u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438 \u0448\u043a\u043e\u0434\u044b \u043e\u043a\u0442\u0430\u0432\u0438\u0438 \u043a\u043e\u043c\u0431\u0438\n\u043a\u0443\u043f\u0438\u0442\u044c \u0448\u043a\u043e\u0434\u0443 \u043e\u043a\u0442\u0430\u0432\u0438\u044e \u043a\u043e\u043c\u0431\u0438\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u0430 \u043a\u043e\u043c\u0431\u0438\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u043a\u043e\u043c\u0431\u0438\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u043a\u043e\u043c\u0431\u0438 2022\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u043a\u043e\u043c\u0431\u0438 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438 \u0438 \u0446\u0435\u043d\u044b\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u043a\u043e\u043c\u0431\u0438 \u043a\u0443\u043f\u0438\u0442\u044c\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u043a\u043e\u043c\u0431\u0438 \u0446\u0435\u043d\u0430\nskoda superb \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438\nskoda superb \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438 \u0438 \u0446\u0435\u043d\u044b\nskoda superb \u0446\u0435\u043d\u0430\n\u043a\u0443\u043f\u0438\u0442\u044c \u0448\u043a\u043e\u0434\u0430 \u0441\u0443\u043f\u0435\u0440\u0431\n\u043d\u043e\u0432\u0430\u044f \u0448\u043a\u043e\u0434\u0430 \u0441\u0443\u043f\u0435\u0440\u0431 \u0446\u0435\u043d\u0430 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u044f\n\u0448\u043a\u043e\u0434\u0430 \u0441\u0443\u043f\u0435\u0440\u0431 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438\n\u0448\u043a\u043e\u0434\u0430 \u0441\u0443\u043f\u0435\u0440\u0431 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438 \u0438 \u0446\u0435\u043d\u044b\n\u0448\u043a\u043e\u0434\u0430 \u0441\u0443\u043f\u0435\u0440\u0431 \u043d\u043e\u0432\u0430\u044f \u0446\u0435\u043d\u0430\n\u0448\u043a\u043e\u0434\u0430 \u0441\u0443\u043f\u0435\u0440\u0431 \u0446\u0435\u043d\u0430\nskoda superb combi\nskoda superb combi \u043a\u0443\u043f\u0438\u0442\u044c\nskoda superb combi \u0446\u0435\u043d\u0430\n\u0161koda superb combi 2022\n\u043a\u0443\u043f\u0438\u0442\u044c \u0448\u043a\u043e\u0434\u0443 \u0441\u0443\u043f\u0435\u0440\u0431 \u043a\u043e\u043c\u0431\u0438\n\u043d\u043e\u0432\u044b\u0439 \u0448\u043a\u043e\u0434\u0430 \u0441\u0443\u043f\u0435\u0440\u0431 \u043a\u043e\u043c\u0431\u0438\n\u0448\u043a\u043e\u0434\u0430 \u0441\u0443\u043f\u0435\u0440\u0431 \u043a\u043e\u043c\u0431\u0438\n\u0448\u043a\u043e\u0434\u0430 \u0441\u0443\u043f\u0435\u0440\u0431 \u043a\u043e\u043c\u0431\u0438 2022\n\u0448\u043a\u043e\u0434\u0430 \u0441\u0443\u043f\u0435\u0440\u0431 \u043a\u043e\u043c\u0431\u0438 \u0446\u0435\u043d\u0430\nskoda superb sportline\n\u0161koda superb sportline\n\u0161koda superb sportline 2022\n\u0161koda superb sportline \u043a\u0443\u043f\u0438\u0442\u044c\n\u0448\u043a\u043e\u0434\u0430 \u0441\u0443\u043f\u0435\u0440\u0431 2022\n\u0448\u043a\u043e\u0434\u0430 \u0441\u0443\u043f\u0435\u0440\u0431 2022 \u0441\u043f\u043e\u0440\u0442\u043b\u0430\u0439\u043d\n\u0448\u043a\u043e\u0434\u0430 \u0441\u0443\u043f\u0435\u0440\u0431 \u0441\u043f\u043e\u0440\u0442\u043b\u0430\u0439\u043d\n\u0448\u043a\u043e\u0434\u0430 \u0441\u0443\u043f\u0435\u0440\u0431 \u0441\u043f\u043e\u0440\u0442\u043b\u0430\u0439\u043d 2022 \u0446\u0435\u043d\u0430\n\u0448\u043a\u043e\u0434\u0430 \u0441\u0443\u043f\u0435\u0440\u0431 \u0441\u043f\u043e\u0440\u0442\u043b\u0430\u0439\u043d \u043a\u0443\u043f\u0438\u0442\u044c\nskoda \u0430\u0432\u0442\u043e \u0432 \u043d\u0430\u043b\u0438\u0447\u0438\u0438\nskoda \u0432 \u043d\u0430\u043b\u0438\u0447\u0438\u0438\n\u043a\u0443\u043f\u0438\u0442\u044c skoda \u0432 \u043d\u0430\u043b\u0438\u0447\u0438\u0438\n\u043a\u0443\u043f\u0438\u0442\u044c \u0448\u043a\u043e\u0434\u0443 \u0432 \u043d\u0430\u043b\u0438\u0447\u0438\u0438\n\u0448\u043a\u043e\u0434\u0430 \u0430\u0432\u0442\u043e\u043c\u043e\u0431\u0438\u043b\u0438 \u0432 \u043d\u0430\u043b\u0438\u0447\u0438\u0438\n\u0448\u043a\u043e\u0434\u0430 \u0432 \u043d\u0430\u043b\u0438\u0447\u0438\u0438\n\u0448\u043a\u043e\u0434\u0430 \u0432 \u043d\u0430\u043b\u0438\u0447\u0438\u0438 \u0443 \u043e\u0444\u0438\u0446\u0438\u0430\u043b\u044c\u043d\u044b\u0445 \u0434\u0438\u043b\u0435\u0440\u043e\u0432\n\u0448\u043a\u043e\u0434\u0430 \u043e\u0444\u0438\u0446\u0438\u0430\u043b\u044c\u043d\u044b\u0439 \u0432 \u043d\u0430\u043b\u0438\u0447\u0438\u0435\n\u0434\u0438\u043b\u0435\u0440 \u0448\u043a\u043e\u0434\u0430 \u0441\u0442\u0430\u0440\u044b\u0439 \u043e\u0441\u043a\u043e\u043b\n\u0448\u043a\u043e\u0434\u0430 \u043e\u0441\u043a\u043e\u043b\n\u0448\u043a\u043e\u0434\u0430 \u043e\u0441\u043a\u043e\u043b \u043e\u0444\u0438\u0446\u0438\u0430\u043b\u044c\u043d\u044b\u0439 \u0434\u0438\u043b\u0435\u0440\n\u0448\u043a\u043e\u0434\u0430 \u0441\u0442\u0430\u0440\u044b\u0439 \u043e\u0441\u043a\u043e\u043b\n\u0448\u043a\u043e\u0434\u0430 \u0441\u0442\u0430\u0440\u044b\u0439 \u043e\u0441\u043a\u043e\u043b \u043e\u0444\u0438\u0446\u0438\u0430\u043b\u044c\u043d\u044b\u0439 \u0434\u0438\u043b\u0435\u0440\n\u0448\u043a\u043e\u0434\u0430 \u0441\u0442\u0430\u0440\u044b\u0439 \u043e\u0441\u043a\u043e\u043b \u043e\u0444\u0438\u0446\u0438\u0430\u043b\u044c\u043d\u044b\u0439 \u0434\u0438\u043b\u0435\u0440 \u0446\u0435\u043d\u044b\n\u0448\u043a\u043e\u0434\u0430 \u0441\u0442\u0430\u0440\u044b\u0439 \u043e\u0441\u043a\u043e\u043b \u043e\u0444\u0438\u0446\u0438\u0430\u043b\u044c\u043d\u044b\u0439 \u0441\u0430\u0439\u0442\n\u0448\u043a\u043e\u0434\u0430 \u0441\u0442\u0430\u0440\u044b\u0439 \u043e\u0441\u043a\u043e\u043b \u0446\u0435\u043d\u0430\nskoda kodiaq hockey edition\nskoda kodiaq hockey edition \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u044f\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u044f \u0445\u043e\u043a\u043a\u0435\u0439 \u044d\u0434\u0438\u0448\u043d\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0445\u043e\u043a\u043a\u0435\u0439\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0445\u043e\u043a\u043a\u0435\u0439 \u044d\u0434\u0438\u0448\u043d\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0445\u043e\u043a\u043a\u0435\u0439 \u044d\u0434\u0438\u0448\u043d 2022\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0445\u043e\u043a\u043a\u0435\u0439 \u044d\u0434\u0438\u0448\u043d \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u044f \u0438 \u0446\u0435\u043d\u044b\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0445\u043e\u043a\u043a\u0435\u0439 \u044d\u0434\u0438\u0448\u043d \u0446\u0435\u043d\u0430\nskoda karoq 2022\nskoda karoq 2022 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438\n\u0161koda karoq 2022 \u0442\u0435\u0445\u043d\u0438\u0447\u0435\u0441\u043a\u0438\u0435 \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\n\u0161koda karoq \u0442\u0435\u0445\u043d\u0438\u0447\u0435\u0441\u043a\u0438\u0435 \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\n\u0161koda karoq \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\n\u0448\u043a\u043e\u0434\u0430 \u043a\u0430\u0440\u043e\u043a \u0442\u0435\u0445\u043d\u0438\u0447\u0435\u0441\u043a\u0438\u0435 \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\n\u0448\u043a\u043e\u0434\u0430 \u043a\u0430\u0440\u043e\u043a \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\nskoda octavia\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u0430\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u0431\u0435\u043b\u0433\u043e\u0440\u043e\u0434\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u0432 \u043d\u0430\u043b\u0438\u0447\u0438\u0438 \u0443 \u043e\u0444\u0438\u0446\u0438\u0430\u043b\u044c\u043d\u044b\u0445 \u0434\u0438\u043b\u0435\u0440\u043e\u0432\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438 \u0438 \u0446\u0435\u043d\u044b\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u0446\u0435\u043d\u0430\nskoda octavia \u0442\u0435\u0445\u043d\u0438\u0447\u0435\u0441\u043a\u0438\u0435 \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\nskoda octavia \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\n\u0442\u0435\u0445\u043d\u0438\u0447\u0435\u0441\u043a\u0438\u0435 \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438 \u0448\u043a\u043e\u0434\u044b \u043e\u043a\u0442\u0430\u0432\u0438\u0438\n\u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438 \u0448\u043a\u043e\u0434\u044b \u043e\u043a\u0442\u0430\u0432\u0438\u0438\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u0442\u0435\u0445\u043d\u0438\u0447\u0435\u0441\u043a\u0438\u0435 \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\ntrade in skoda\ntrade in skoda \u0443\u0441\u043b\u043e\u0432\u0438\u044f\n\u043a\u0443\u043f\u0438\u0442\u044c \u0448\u043a\u043e\u0434\u0443 \u0442\u0440\u0435\u0439\u0434 \u0438\u043d\n\u0443\u0441\u043b\u043e\u0432\u0438\u044f \u0442\u0440\u0435\u0439\u0434 \u0438\u043d \u0448\u043a\u043e\u0434\u0430\n\u0448\u043a\u043e\u0434\u0430 trade in\n\u0448\u043a\u043e\u0434\u0430 \u0442\u0440\u0435\u0439\u0434 \u0438\u043d\nskoda kodiaq laurin & klement\n\u0161koda kodiaq laurin klement\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u043a\u043b\u0435\u043c\u0435\u043d\u0442\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u043b\u0430\u0443\u0440\u0438\u043d \u043a\u043b\u0435\u043c\u0435\u043d\u0442\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u043b\u0430\u0443\u0440\u0438\u043d \u043a\u043b\u0435\u043c\u0435\u043d\u0442 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u044f \u0446\u0435\u043d\u0430\nskoda kodiaq 2022\nskoda kodiaq 2022 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438 \u0438 \u0446\u0435\u043d\u044b\nskoda kodiaq \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438\nskoda kodiaq \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438 \u0438 \u0446\u0435\u043d\u044b\nskoda kodiaq \u043a\u0443\u043f\u0438\u0442\u044c\nskoda kodiaq sportline\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0441\u043f\u043e\u0440\u0442\u043b\u0430\u0439\u043d\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0441\u043f\u043e\u0440\u0442\u043b\u0430\u0439\u043d 2022\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0441\u043f\u043e\u0440\u0442\u043b\u0430\u0439\u043d 2022 \u0432 \u043d\u043e\u0432\u043e\u043c \u043a\u0443\u0437\u043e\u0432\u0435\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0441\u043f\u043e\u0440\u0442\u043b\u0430\u0439\u043d \u0446\u0435\u043d\u0430\n\u043a\u0443\u043f\u0438\u0442\u044c \u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434 \u0441\u0442\u0430\u0440\u044b\u0439 \u043e\u0441\u043a\u043e\u043b\n\u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434 2022 \u0441\u0442\u0430\u0440\u044b\u0439 \u043e\u0441\u043a\u043e\u043b\n\u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434 \u0441\u0442\u0430\u0440\u044b\u0439 \u043e\u0441\u043a\u043e\u043b\n\u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434 \u0446\u0435\u043d\u044b \u0441\u0442\u0430\u0440\u044b\u0439 \u043e\u0441\u043a\u043e\u043b\nskoda superb \u0442\u0435\u0445\u043d\u0438\u0447\u0435\u0441\u043a\u0438\u0435 \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\nskoda superb \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\n\u0448\u043a\u043e\u0434\u0430 \u0441\u0443\u043f\u0435\u0440\u0431 \u0442\u0435\u0445\u043d\u0438\u0447\u0435\u0441\u043a\u0438\u0435 \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\n\u0448\u043a\u043e\u0434\u0430 \u0441\u0443\u043f\u0435\u0440\u0431 \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\nskoda octavia combi \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\n\u0161koda octavia combi \u0442\u0435\u0445\u043d\u0438\u0447\u0435\u0441\u043a\u0438\u0435 \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u043a\u043e\u043c\u0431\u0438 \u0442\u0435\u0445\u043d\u0438\u0447\u0435\u0441\u043a\u0438\u0435 \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u043a\u043e\u043c\u0431\u0438 \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\n\u0442\u0435\u0445\u043d\u0438\u0447\u0435\u0441\u043a\u043e\u0435 \u043e\u0431\u0441\u043b\u0443\u0436\u0438\u0432\u0430\u043d\u0438\u0435 skoda\n\u0442\u0435\u0445\u043d\u0438\u0447\u0435\u0441\u043a\u043e\u0435 \u043e\u0431\u0441\u043b\u0443\u0436\u0438\u0432\u0430\u043d\u0438\u0435 \u0430\u0432\u0442\u043e\u043c\u043e\u0431\u0438\u043b\u0435\u0439 \u0448\u043a\u043e\u0434\u0430\n\u0442\u0435\u0445\u043d\u0438\u0447\u0435\u0441\u043a\u043e\u0435 \u043e\u0431\u0441\u043b\u0443\u0436\u0438\u0432\u0430\u043d\u0438\u0435 \u0448\u043a\u043e\u0434\u0430\nskoda octavia combi \u0432 \u043d\u0430\u043b\u0438\u0447\u0438\u0438\nskoda octavia combi \u043a\u0443\u043f\u0438\u0442\u044c\nskoda octavia combi \u043e\u0444\u0438\u0446\u0438\u0430\u043b\u044c\u043d\u044b\u0439 \u0434\u0438\u043b\u0435\u0440\nskoda \u0442\u0435\u0441\u0442 \u0434\u0440\u0430\u0439\u0432\n\u043d\u043e\u0432\u0430\u044f \u0448\u043a\u043e\u0434\u0430 \u0442\u0435\u0441\u0442 \u0434\u0440\u0430\u0439\u0432\n\u0442\u0435\u0441\u0442 \u0434\u0440\u0430\u0439\u0432 \u0448\u043a\u043e\u0434\u0430\nskoda superb\n\u0448\u043a\u043e\u0434\u0430 \u0441\u0443\u043f\u0435\u0440\u0431\n\u0161koda kodiaq \u0442\u0435\u0445\u043d\u0438\u0447\u0435\u0441\u043a\u0438\u0435 \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\n\u0161koda kodiaq \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\n\u043a\u0443\u043f\u0438\u0442\u044c \u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u0441\u0442\u0430\u0440\u044b\u0439 \u043e\u0441\u043a\u043e\u043b\n\u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u0441\u0442\u0430\u0440\u044b\u0439 \u043e\u0441\u043a\u043e\u043b\nskoda karoq\n\u0448\u043a\u043e\u0434\u0430 \u043a\u0430\u0440\u043e\u043a\n\u0161koda superb 2022\n\u043e\u0431\u043d\u043e\u0432\u043b\u0435\u043d\u043d\u044b\u0439 \u0448\u043a\u043e\u0434\u0430 \u0441\u0443\u043f\u0435\u0440\u0431 2022\n\u0161koda octavia combi 2022\n\u043d\u043e\u0432\u0430\u044f \u0448\u043a\u043e\u0434\u0430 \u043e\u043a\u0442\u0430\u0432\u0438\u044f \u043a\u043e\u043c\u0431\u0438 2022\nskoda rapid 2022 \u0442\u0435\u0445\u043d\u0438\u0447\u0435\u0441\u043a\u0438\u0435 \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\n\u0161koda rapid 2022 \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\n\u043a\u0443\u043f\u0438\u0442\u044c \u0448\u043a\u043e\u0434\u0443 \u043a\u043e\u0434\u0438\u0430\u043a \u0441\u043f\u043e\u0440\u0442\u043b\u0430\u0439\u043d\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0441\u043f\u043e\u0440\u0442\u043b\u0430\u0439\u043d 2022 \u0446\u0435\u043d\u0430\nskoda rapid fl\n\u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434 \u0444\u043b\nskoda rapid \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438\n\u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u044f \u043d\u043e\u0432\u043e\u0433\u043e \u0448\u043a\u043e\u0434\u0430 \u0440\u0430\u043f\u0438\u0434\n\u0448\u043a\u043e\u0434\u0430 \u0441\u0443\u043f\u0435\u0440\u0431 \u0431\u0435\u043b\u0433\u043e\u0440\u043e\u0434\n\u0448\u043a\u043e\u0434\u0430 \u0441\u0443\u043f\u0435\u0440\u0431 \u043a\u0443\u043f\u0438\u0442\u044c \u0431\u0435\u043b\u0433\u043e\u0440\u043e\u0434\nskoda rapid \u0442\u0435\u0445\u043d\u0438\u0447\u0435\u0441\u043a\u0438\u0435 \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\n\u0161koda rapid \u0445\u0430\u0440\u0430\u043a\u0442\u0435\u0440\u0438\u0441\u0442\u0438\u043a\u0438\n\u043e\u0431\u043d\u043e\u0432\u043b\u0435\u043d\u043d\u0430\u044f skoda superb\n\u043e\u0431\u043d\u043e\u0432\u043b\u0435\u043d\u043d\u0430\u044f \u0448\u043a\u043e\u0434\u0430 \u0441\u0443\u043f\u0435\u0440\u0431\n\u0448\u043a\u043e\u0434\u0430 \u043a\u043e\u0434\u0438\u0430\u043a \u0441\u0442\u0430\u0440\u044b\u0439 \u043e\u0441\u043a\u043e\u043b\n\u0448\u043a\u043e\u0434\u0430 \u043a\u0443\u043f\u0438\u0442\u044c \u0441\u0442\u0430\u0440\u044b\u0439 \u043e\u0441\u043a\u043e\u043b\n\u0448\u043a\u043e\u0434\u0430 \u043a\u0430\u0440\u043e\u043a \u0441\u0442\u0430\u0440\u044b\u0439 \u043e\u0441\u043a\u043e\u043b\n\u0430\u0432\u0442\u043e\u043c\u043e\u0431\u0438\u043b\u044c skoda superb\n\u043d\u043e\u0432\u0430\u044f skoda octavia\nskoda octavia \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438\n\u043a\u0443\u0437\u043e\u0432\u043d\u044b\u0435 \u0434\u0435\u0442\u0430\u043b\u0438 \u0448\u043a\u043e\u0434\u0430\nskoda octavia \u043a\u0443\u043f\u0438\u0442\u044c\n\u0161koda rapid 2022 \u043a\u043e\u043c\u043f\u043b\u0435\u043a\u0442\u0430\u0446\u0438\u0438\nskoda rapid fl 2022\nskoda superb \u043a\u0443\u043f\u0438\u0442\u044c\n\u0448\u043a\u043e\u0434\u0430 \u0442\u0435\u0441\u0442 \u0434\u0440\u0430\u0439\u0432 \u0430\u0432\u0442\u043e\u043c\u0430\u0442","clusteringLevel":"light","engineVersion":"latest","searchBased":"true","searchPhrases":"false","searchTarget":"false","progressId":"181","domain":null,"comment":null,"sendMessage":"1"}', true);

        dd(array_unique(array_diff(explode("\n", str_replace("\r", "", $request['phrases'])), [])));
        $minimum = 8;
        $willClustered = [];
        $clusters = [];

        foreach ($jayParsedAry as $phrase => $item) {
            foreach ($jayParsedAry as $phrase2 => $item2) {
                if (isset($willClustered[$phrase2])) {
                    continue;
                } else if (isset($this->clusters[$phrase])) {
                    foreach ($clusters[$phrase] as $target => $elem) {
                        if (count(array_intersect($item2['sites'], $elem['sites'])) >= $minimum) {
                            $clusters[$phrase][$phrase2] = ['sites' => $item2['sites']];
                            $willClustered[$phrase2] = true;
                            break;
                        }
                    }
                } else if (count(array_intersect($item['sites'], $item2['sites'])) >= $minimum) {
                    $clusters[$phrase][$phrase2] = ['sites' => $item2['sites']];
                    $willClustered[$phrase2] = true;
                }
            }
        }

        foreach ($clusters as $keyPhrase => $cluster) {
            foreach ($clusters as $anotherKeyPhrase => $anotherCluster) {
                if ($keyPhrase === $anotherKeyPhrase) {
                    continue;
                }
                foreach ($cluster as $key1 => $elems) {
                    foreach ($anotherCluster as $key2 => $anotherElems) {
                        if (isset($elems['sites']) && isset($anotherElems['sites'])) {
                            if (count(array_intersect($elems['sites'], $anotherElems['sites'])) >= $minimum) {
                                $clusters[$keyPhrase] = array_merge_recursive($cluster, $anotherCluster);
                                $clusters[$keyPhrase][$anotherKeyPhrase]['merge'] = [$key1 => $key2];
                                unset($clusters[$anotherKeyPhrase]);
                                break 2;
                            }
                        }
                    }
                }
            }
        }

        dd($clusters);
    });
});
