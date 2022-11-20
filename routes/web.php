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
    Route::get('/start-cluster-progress', 'ClusterController@startProgress')->name('start.cluster.progress');
    Route::get('/get-cluster-progress/{id}', 'ClusterController@getProgress')->name('get.cluster.progress');
    Route::get('/get-cluster-progress/{id}/modify', 'ClusterController@getProgressModify')->name('get.cluster.progress.modify');
    Route::get('/destroy-progress/{progress}', 'ClusterController@destroyProgress')->name('destroy.progress');
    Route::get('/cluster-projects', 'ClusterController@clusterProjects')->name('cluster.projects');
    Route::post('/edit-cluster-project', 'ClusterController@edit')->name('cluster.edit');
    Route::post('/get-cluster-request/', 'ClusterController@getClusterRequest')->name('get.cluster.request');
    Route::get('/show-cluster-result/{cluster}', 'ClusterController@showResult')->name('show.cluster.result');
    Route::get('/wait-cluster-result/id', 'ClusterController@waitClusterResult')->name('wait.cluster.result');
    Route::get('/download-cluster-result/{cluster}/{type}', 'ClusterController@downloadClusterResult')->name('download.cluster.result');
    Route::get('/cluster-configuration', 'ClusterController@clusterConfiguration')->name('cluster.configuration');
    Route::post('/change-cluster-configuration', 'ClusterController@changeClusterConfiguration')->name('change.cluster.configuration');
    Route::post('/fast-scan-clusters', 'ClusterController@fastScanClusters')->name('fast.scan.clusters');

    Route::get('/test', function () {
        $jayParsedAry = [
            "karoq skoda цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq/price",
                    "https://auto.ru/moskva/cars/skoda/karoq/all/",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://www.skoda-major.ru/karoq/",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/karoq-asgbagicaktgtg2emsjitg26stu",
                    "https://moscow.drom.ru/skoda/karoq/new/",
                    "https://www.rolf.ru/cars/new/skoda/karoq/",
                    "https://skoda-favorit.ru/models/karoq/price",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://skoda-kuntsevo.ru/models/karoq",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://carsdo.ru/skoda/karoq/",
                    "https://autospot.ru/brands/skoda/karoq/suv/price/",
                    "https://www.autoskd.ru/models/karoq/price",
                    "https://skoda-avtoruss.ru/models/karoq/price",
                    "https://www.atlant-motors.ru/models/karoq",
                    "https://nz-cars.ru/cars/skoda/karoq/",
                    "https://www.bogemia-skd.ru/models/karoq",
                    "https://keyauto.ru/cars/new/skoda/karoq/",
                    "https://avtoruss.ru/skoda/karoq.html"
                ]
            ],
            "skoda karoq 2022 комплектации и цены" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq/price",
                    "https://auto.ru/moskva/cars/skoda/karoq/2022-year/all/",
                    "https://www.drom.ru/catalog/skoda/karoq/2022/",
                    "https://www.major-auto.ru/models/skoda/karoq/",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://carsdo.ru/skoda/karoq/",
                    "https://www.skoda-major.ru/karoq/",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://naavtotrasse.ru/skoda/skoda-karoq-2022.html",
                    "https://skoda-favorit.ru/models/karoq/price",
                    "https://www.autoskd.ru/models/karoq/price",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-karoq-2022",
                    "https://skoda-avtoruss.ru/models/karoq",
                    "https://skoda-kuntsevo.ru/models/karoq",
                    "https://moscow.autovsalone.ru/cars/skoda/karoq/compare",
                    "https://www.bogemia-skd.ru/models/karoq",
                    "https://avtoruss.ru/skoda/karoq.html",
                    "https://autospot.ru/brands/skoda/karoq/suv/price/",
                    "https://www.rolf.ru/cars/new/skoda/karoq/"
                ]
            ],
            "skoda karoq комплектации" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq/price",
                    "https://www.skoda-avto.ru/models/karoq",
                    "https://auto.ru/catalog/cars/skoda/karoq/",
                    "https://www.drom.ru/catalog/skoda/karoq/",
                    "https://www.autoskd.ru/models/karoq/price",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://www.major-auto.ru/models/skoda/karoq/",
                    "https://www.drive.ru/brands/skoda/models/2017/karoq",
                    "https://skoda-avtoruss.ru/models/karoq/price",
                    "https://skoda-favorit.ru/models/karoq/price",
                    "https://www.skoda-major.ru/karoq/",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://moscow.autovsalone.ru/cars/skoda/karoq/compare",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://carsdo.ru/skoda/karoq/",
                    "https://karoq-fan.ru/komplektacii-i-ceny-skoda-karoq/",
                    "https://www.rolf.ru/cars/skoda/karoq/",
                    "https://skoda-karoq.ru/price.html",
                    "https://avtoruss.ru/skoda/karoq.html",
                    "https://www.bogemia-skd.ru/models/karoq"
                ]
            ],
            "skoda karoq комплектации и цены" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq/price",
                    "https://www.skoda-avto.ru/models/karoq",
                    "https://auto.ru/moskva/cars/skoda/karoq/all/",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://skoda-favorit.ru/models/karoq/price",
                    "https://carsdo.ru/skoda/karoq/",
                    "https://www.skoda-major.ru/karoq/",
                    "https://www.major-auto.ru/models/skoda/karoq/",
                    "https://skoda-avtoruss.ru/models/karoq/price",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://skoda-kuntsevo.ru/models/karoq",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://www.autoskd.ru/models/karoq/price",
                    "https://autospot.ru/brands/skoda/karoq/suv/price/",
                    "https://www.drom.ru/catalog/skoda/karoq/",
                    "https://www.rolf.ru/cars/new/skoda/karoq/",
                    "https://nz-cars.ru/cars/skoda/karoq/",
                    "https://m.avito.ru/moskva/avtomobili/skoda/karoq-asgbagicaktgtg2emsjitg26stu",
                    "https://www.drive.ru/brands/skoda/models/2017/karoq",
                    "https://www.bogemia-skd.ru/models/karoq"
                ]
            ],
            "skoda karoq купить" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq",
                    "https://auto.ru/moskva/cars/skoda/karoq/21010081/all/",
                    "https://skoda-kuntsevo.ru/models/karoq",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://www.skoda-major.ru/karoq/",
                    "https://www.major-auto.ru/models/skoda/karoq/",
                    "https://www.avito.ru/moskva/avtomobili/skoda/karoq-asgbagicaktgtg2emsjitg26stu",
                    "https://skoda-avtoruss.ru/models/karoq",
                    "https://www.rolf.ru/cars/skoda/karoq/",
                    "https://moscow.drom.ru/skoda/karoq/new/",
                    "https://avtoruss.ru/skoda/karoq.html",
                    "https://skoda-favorit.ru/models/karoq",
                    "https://www.bogemia-skd.ru/models/karoq",
                    "https://autospot.ru/brands/skoda/karoq/suv/price/",
                    "https://www.atlant-motors.ru/models/karoq",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://nz-cars.ru/cars/skoda/karoq/",
                    "https://carso.ru/skoda/karoq",
                    "https://auto-nrg.com/auto/skoda/karoq"
                ]
            ],
            "карок шкода комплектации и цены у официального" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq/price",
                    "https://www.skoda-avto.ru/models/karoq",
                    "https://www.skoda-major.ru/karoq/",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://www.major-auto.ru/models/skoda/karoq/",
                    "https://skoda-favorit.ru/models/karoq/price",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://skoda-avtoruss.ru/models/karoq/price",
                    "https://skoda-kuntsevo.ru/models/karoq",
                    "https://carsdo.ru/skoda/karoq/",
                    "https://auto.ru/moskva/cars/skoda/karoq/new/",
                    "https://www.autoskd.ru/models/karoq/price",
                    "https://www.rolf.ru/cars/new/skoda/karoq/",
                    "https://www.bogemia-skd.ru/models/karoq",
                    "https://www.atlant-motors.ru/models/karoq",
                    "https://avtoruss.ru/skoda/karoq.html",
                    "https://www.autocity-sk.ru/models/karoq",
                    "https://nz-cars.ru/cars/skoda/karoq/",
                    "https://moscow.autovsalone.ru/cars/skoda/karoq"
                ]
            ],
            "комплектации шкода карок" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq/price",
                    "https://www.drom.ru/catalog/skoda/karoq/",
                    "https://auto.ru/catalog/cars/skoda/karoq/21010081/21010112/equipment/",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://carsdo.ru/skoda/karoq/",
                    "https://www.autoskd.ru/models/karoq/price",
                    "https://moscow.autovsalone.ru/cars/skoda/karoq/compare",
                    "https://www.major-auto.ru/models/skoda/karoq/",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://www.skoda-major.ru/karoq/",
                    "https://skoda-favorit.ru/models/karoq/price",
                    "https://karoq-fan.ru/komplektacii-i-ceny-skoda-karoq/",
                    "https://www.drive.ru/brands/skoda/models/2017/karoq",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://skoda-avtoruss.ru/models/karoq/price",
                    "https://skoda-karoq.ru/price.html",
                    "https://chehia-avto.ru/models/karoq/price",
                    "https://karoqs.ru/forum/threads/262/",
                    "https://nz-cars.ru/cars/skoda/karoq/",
                    "https://avtoruss.ru/skoda/karoq.html"
                ]
            ],
            "купить шкода карок" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq",
                    "https://auto.ru/moskva/cars/skoda/karoq/all/",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/karoq-asgbagicaktgtg2emsjitg26stu",
                    "https://www.skoda-major.ru/karoq/",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://moscow.drom.ru/skoda/karoq/new/",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://autospot.ru/brands/skoda/karoq/suv/price/",
                    "https://www.rolf.ru/cars/new/skoda/karoq/",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://skoda-kuntsevo.ru/models/karoq",
                    "https://www.atlant-motors.ru/models/karoq",
                    "https://skoda-favorit.ru/models/karoq",
                    "https://www.bogemia-skd.ru/models/karoq",
                    "https://skoda-avtoruss.ru/models/karoq",
                    "https://nz-cars.ru/cars/skoda/karoq/",
                    "https://www.ventus.ru/models/karoq",
                    "https://avtoruss.ru/skoda/karoq.html",
                    "https://moscow.autovsalone.ru/cars/skoda/karoq",
                    "https://aksa-auto.ru/catalog/skoda/karog"
                ]
            ],
            "официальный дилер шкода карок" => [
                "sites" => [
                    "https://skoda-kuntsevo.ru/models/karoq",
                    "https://cars.skoda-avto.ru/karoq",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://www.skoda-major.ru/karoq/",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://www.bogemia-skd.ru/models/karoq",
                    "https://www.atlant-motors.ru/models/karoq",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://skoda-avtoruss.ru/models/karoq",
                    "https://skoda-favorit.ru/models/karoq",
                    "https://www.rolf.ru/cars/new/skoda/karoq/",
                    "https://avtoruss.ru/skoda/karoq.html",
                    "https://skoda-autopraga.ru/models/karoq",
                    "https://adom.ru/skoda/karoq",
                    "https://auto.ru/diler-oficialniy/cars/new/bogemiya_dmitrovka_moskva_skoda/skoda/karoq/",
                    "https://rolf-center.ru/brands/skoda/karoq/",
                    "https://nz-cars.ru/cars/skoda/karoq/",
                    "https://autospot.ru/autoservice/to/skoda/karoq/",
                    "https://carsdo.ru/skoda/karoq/moscow/",
                    "https://auto-nrg.com/auto/skoda/karoq"
                ]
            ],
            "шкода карок 2022" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq",
                    "https://auto.ru/moskva/cars/skoda/karoq/2022-year/all/",
                    "https://www.major-auto.ru/models/skoda/karoq/",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://naavtotrasse.ru/skoda/skoda-karoq-2022.html",
                    "https://skoda-karoq.ru/read/112-obnovlennyj-skoda-karoq-2022.html",
                    "https://www.youtube.com/watch?v=wm4oxlu8m-g",
                    "https://m.avito.ru/moskva/avtomobili/skoda/karoq-asgbagicaktgtg2emsjitg26stu",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-karoq-2022",
                    "https://www.drom.ru/catalog/skoda/karoq/2022/",
                    "https://carsdo.ru/skoda/karoq/",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://skoda-favorit.ru/models/karoq",
                    "https://autoreview.ru/news/obnovlennyy-krossover-skoda-karoq-predstavlen-v-evrope",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://skoda-kuntsevo.ru/models/karoq",
                    "https://skoda-avtoruss.ru/models/karoq",
                    "https://www.drive2.ru/b/604700610024984890/",
                    "https://www.rolf.ru/cars/new/skoda/karoq/",
                    "https://fastmb.ru/autonews/autonews_mir/19684-skoda-karoq-2022-obnovlennyj-krossover.html"
                ]
            ],
            "шкода карок 2022 комплектации" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq/price",
                    "https://www.skoda-avto.ru/models/karoq",
                    "https://www.drom.ru/catalog/skoda/karoq/2022/",
                    "https://auto.ru/moskva/cars/skoda/karoq/2022-year/all/",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-karoq-2022",
                    "https://carsdo.ru/skoda/karoq/",
                    "https://naavtotrasse.ru/skoda/skoda-karoq-2022.html",
                    "https://www.major-auto.ru/models/skoda/karoq/",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://www.skoda-major.ru/karoq/",
                    "https://skoda-favorit.ru/models/karoq/price",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://www.autoskd.ru/models/karoq/price",
                    "https://moscow.autovsalone.ru/cars/skoda/karoq/compare",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://skoda-centr.ru/karoq/complect/",
                    "https://fastmb.ru/autonews/autonews_rus/21126-skoda-karoq-2022-v-rossii-start-prodazh-komplektatsii-i-tseny.html",
                    "https://skoda-avtoruss.ru/models/karoq",
                    "https://avtoruss.ru/skoda/karoq.html",
                    "https://skoda-kuntsevo.ru/models/karoq"
                ]
            ],
            "шкода карок 2022 комплектации и цены" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq/price",
                    "https://auto.ru/moskva/cars/skoda/karoq/2022-year/all/",
                    "https://carsdo.ru/skoda/karoq/",
                    "https://www.major-auto.ru/models/skoda/karoq/",
                    "https://www.skoda-major.ru/karoq/",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://www.drom.ru/catalog/skoda/karoq/2022/",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://skoda-favorit.ru/models/karoq/price",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://naavtotrasse.ru/skoda/skoda-karoq-2022.html",
                    "https://www.autoskd.ru/models/karoq/price",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-karoq-2022",
                    "https://skoda-avtoruss.ru/models/karoq",
                    "https://skoda-kuntsevo.ru/models/karoq",
                    "https://www.bogemia-skd.ru/models/karoq",
                    "https://moscow.autovsalone.ru/cars/skoda/karoq/compare",
                    "https://nz-cars.ru/cars/skoda/karoq/",
                    "https://autospot.ru/brands/skoda/karoq/suv/price/",
                    "https://www.rolf.ru/cars/new/skoda/karoq/"
                ]
            ],
            "шкода карок 2022 цена и комплектация официальный" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq/price",
                    "https://www.skoda-avto.ru/models/karoq",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://www.major-auto.ru/models/skoda/karoq/",
                    "https://www.skoda-major.ru/karoq/",
                    "https://auto.ru/moskva/cars/skoda/karoq/2022-year/all/",
                    "https://skoda-favorit.ru/models/karoq/price",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://carsdo.ru/skoda/karoq/",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://skoda-avtoruss.ru/models/karoq",
                    "https://avtoruss.ru/skoda/karoq.html",
                    "https://www.autoskd.ru/models/karoq/price",
                    "https://skoda-kuntsevo.ru/models/karoq",
                    "https://www.bogemia-skd.ru/models/karoq",
                    "https://www.rolf.ru/cars/new/skoda/karoq/",
                    "https://www.atlant-motors.ru/models/karoq",
                    "https://www.autocity-sk.ru/models/karoq",
                    "https://nz-cars.ru/cars/skoda/karoq/",
                    "https://adom.ru/skoda/karoq"
                ]
            ],
            "шкода карок комплектации и цены" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq/price",
                    "https://auto.ru/moskva/cars/skoda/karoq/all/",
                    "https://www.major-auto.ru/models/skoda/karoq/",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://carsdo.ru/skoda/karoq/",
                    "https://www.skoda-major.ru/karoq/",
                    "https://skoda-favorit.ru/models/karoq/price",
                    "https://www.drom.ru/catalog/skoda/karoq/",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://skoda-avtoruss.ru/models/karoq/price",
                    "https://skoda-kuntsevo.ru/models/karoq",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://www.autoskd.ru/models/karoq/price",
                    "https://www.rolf.ru/cars/new/skoda/karoq/",
                    "https://m.avito.ru/moskva/avtomobili/skoda/karoq-asgbagicaktgtg2emsjitg26stu",
                    "https://autospot.ru/brands/skoda/karoq/suv/price/",
                    "https://www.drive.ru/brands/skoda/models/2017/karoq",
                    "https://moscow.autovsalone.ru/cars/skoda/karoq/compare",
                    "https://avtoruss.ru/skoda/karoq.html",
                    "https://nz-cars.ru/cars/skoda/karoq/"
                ]
            ],
            "шкода карок официальные цены" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq/price",
                    "https://www.skoda-avto.ru/models/karoq",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://www.skoda-major.ru/karoq/",
                    "https://www.major-auto.ru/models/skoda/karoq/",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://skoda-favorit.ru/models/karoq/price",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://auto.ru/moskva/cars/skoda/karoq/new/",
                    "https://skoda-kuntsevo.ru/models/karoq",
                    "https://skoda-avtoruss.ru/models/karoq/price",
                    "https://www.autoskd.ru/models/karoq",
                    "https://www.atlant-motors.ru/models/karoq",
                    "https://www.rolf.ru/cars/new/skoda/karoq/",
                    "https://carsdo.ru/skoda/karoq/",
                    "https://www.bogemia-skd.ru/models/karoq",
                    "https://www.autocity-sk.ru/models/karoq",
                    "https://www.ventus.ru/models/karoq/price",
                    "https://avtoruss.ru/skoda/karoq.html",
                    "https://autospot.ru/brands/skoda/karoq/suv/price/"
                ]
            ],
            "шкода карок официальный дилер цены" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://skoda-kuntsevo.ru/models/karoq",
                    "https://www.major-auto.ru/models/skoda/karoq/",
                    "https://www.skoda-major.ru/karoq/",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://skoda-favorit.ru/models/karoq/price",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://www.rolf.ru/cars/new/skoda/karoq/",
                    "https://skoda-avtoruss.ru/models/karoq",
                    "https://www.atlant-motors.ru/models/karoq",
                    "https://www.bogemia-skd.ru/models/karoq",
                    "https://www.autoskd.ru/models/karoq",
                    "https://www.autocity-sk.ru/models/karoq",
                    "https://avtoruss.ru/skoda/karoq.html",
                    "https://auto.ru/moskva/cars/skoda/karoq/new/",
                    "https://adom.ru/skoda/karoq",
                    "https://www.ventus.ru/models/karoq/price",
                    "https://nz-cars.ru/cars/skoda/karoq/",
                    "https://rolf-center.ru/brands/skoda/karoq/"
                ]
            ],
            "шкода карок цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq/price",
                    "https://auto.ru/moskva/cars/skoda/karoq/all/",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://www.skoda-major.ru/karoq/",
                    "https://www.avito.ru/moskva/avtomobili/skoda/karoq-asgbagicaktgtg2emsjitg26stu",
                    "https://www.rolf.ru/cars/new/skoda/karoq/",
                    "https://moscow.drom.ru/skoda/karoq/new/",
                    "https://skoda-kuntsevo.ru/models/karoq",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://carsdo.ru/skoda/karoq/",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://skoda-favorit.ru/models/karoq/price",
                    "https://autospot.ru/brands/skoda/karoq/suv/price/",
                    "https://www.bogemia-skd.ru/models/karoq",
                    "https://www.autoskd.ru/models/karoq/price",
                    "https://skoda-avtoruss.ru/models/karoq/price",
                    "https://www.atlant-motors.ru/models/karoq",
                    "https://avtoruss.ru/skoda/karoq.html",
                    "https://skoda-karoq.ru/price.html",
                    "https://moscow.autovsalone.ru/cars/skoda/karoq"
                ]
            ],
            "шкода карок цена 2022" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq/price",
                    "https://auto.ru/moskva/cars/skoda/karoq/2022-year/all/",
                    "https://m.avito.ru/moskva/avtomobili/skoda/karoq-asgbagicaktgtg2emsjitg26stu",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://www.major-auto.ru/models/skoda/karoq/",
                    "https://www.skoda-major.ru/karoq/",
                    "https://carsdo.ru/skoda/karoq/",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://www.drom.ru/catalog/skoda/karoq/2022/",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-karoq-2022",
                    "https://skoda-favorit.ru/models/karoq/price",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://www.rolf.ru/cars/new/skoda/karoq/",
                    "https://skoda-avtoruss.ru/models/karoq",
                    "https://www.autoskd.ru/models/karoq/price",
                    "https://moscow.autovsalone.ru/cars/skoda/karoq",
                    "https://skoda-kuntsevo.ru/models/karoq",
                    "https://www.bogemia-skd.ru/models/karoq",
                    "https://naavtotrasse.ru/skoda/skoda-karoq-2022.html",
                    "https://autospot.ru/brands/skoda/karoq/suv/price/"
                ]
            ],
            "шкода карок цена и комплектация официальный сайт" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq/price",
                    "https://www.skoda-avto.ru/models/karoq",
                    "https://cars.skoda-avto.ru/karoq",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://www.skoda-major.ru/karoq/",
                    "https://skoda-kuntsevo.ru/models/karoq",
                    "https://carsdo.ru/skoda/karoq/",
                    "https://auto.ru/moskva/cars/skoda/karoq/2021-year/all/",
                    "https://www.major-auto.ru/models/skoda/karoq/",
                    "https://skoda-favorit.ru/models/karoq/price",
                    "https://skoda-avtoruss.ru/models/karoq/price",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://skoda-ap.ru/models/karoq/price",
                    "https://www.bogemia-skd.ru/models/karoq",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://skoda-forward.ru/models/karoq",
                    "https://chehia-avto.ru/models/karoq/price",
                    "https://skoda-karoq.ru/price.html",
                    "https://mlada-auto.ru/models/karoq",
                    "https://www.rolf.ru/cars/new/skoda/karoq/"
                ]
            ],
            "шкода карок цена у дилера" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq/price",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://www.skoda-major.ru/karoq/",
                    "https://www.major-auto.ru/models/skoda/karoq/",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://skoda-kuntsevo.ru/models/karoq",
                    "https://skoda-favorit.ru/models/karoq/price",
                    "https://skoda-avtoruss.ru/models/karoq",
                    "https://www.rolf.ru/cars/new/skoda/karoq/",
                    "https://www.bogemia-skd.ru/models/karoq",
                    "https://www.atlant-motors.ru/models/karoq",
                    "https://www.autoskd.ru/models/karoq",
                    "https://auto.ru/moskva/cars/skoda/karoq/new/",
                    "https://avtoruss.ru/skoda/karoq.html",
                    "https://www.autocity-sk.ru/models/karoq",
                    "https://moscow.autovsalone.ru/cars/skoda/karoq",
                    "https://www.ventus.ru/models/karoq/price",
                    "https://adom.ru/skoda/karoq",
                    "https://carsdo.ru/skoda/karoq/moscow/"
                ]
            ],
            "skoda karoq 2022" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq",
                    "https://auto.ru/moskva/cars/skoda/karoq/2022-year/all/",
                    "https://autoreview.ru/news/obnovlennyy-krossover-skoda-karoq-predstavlen-v-evrope",
                    "https://skoda-karoq.ru/",
                    "https://www.youtube.com/watch?v=wm4oxlu8m-g",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://www.major-auto.ru/models/skoda/karoq/",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-karoq-2022",
                    "https://m.avito.ru/moskva/avtomobili/skoda/karoq-asgbagicaktgtg2emsjitg26stu",
                    "https://www.drom.ru/catalog/skoda/karoq/2022/",
                    "https://naavtotrasse.ru/skoda/skoda-karoq-2022.html",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://www.drive2.ru/b/604700610024984890/",
                    "https://www.ventus.ru/models/karoq",
                    "https://skoda-favorit.ru/models/karoq",
                    "https://skoda-kuntsevo.ru/models/karoq",
                    "https://www.drive.ru/test-drive/skoda/5e5517f3ec05c4324f000166.html",
                    "https://www.rolf.ru/cars/new/skoda/karoq/",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://skoda-avtoruss.ru/models/karoq"
                ]
            ],
            "skoda karoq 2022 комплектации" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq",
                    "https://www.drom.ru/catalog/skoda/karoq/2022/",
                    "https://auto.ru/moskva/cars/skoda/karoq/2022-year/all/",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-karoq-2022",
                    "https://naavtotrasse.ru/skoda/skoda-karoq-2022.html",
                    "https://www.major-auto.ru/models/skoda/karoq/",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://carsdo.ru/skoda/karoq/",
                    "https://www.skoda-major.ru/karoq/",
                    "https://moscow.autovsalone.ru/cars/skoda/karoq/compare",
                    "https://www.autoskd.ru/models/karoq/price",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://skoda-favorit.ru/models/karoq/price",
                    "https://skoda-centr.ru/karoq/complect/",
                    "https://gt-news.ru/skoda/skoda-karoq-2022/",
                    "https://fastmb.ru/autonews/autonews_rus/21126-skoda-karoq-2022-v-rossii-start-prodazh-komplektatsii-i-tseny.html",
                    "https://skoda-avtoruss.ru/models/karoq",
                    "https://avtoruss.ru/skoda/karoq.html",
                    "https://skoda-kuntsevo.ru/models/karoq"
                ]
            ],
            "skoda karoq" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq",
                    "https://auto.ru/moskva/cars/skoda/karoq/all/",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://www.drive.ru/test-drive/skoda/5e5517f3ec05c4324f000166.html",
                    "https://skoda.drom.ru/karoq/",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://www.drive2.ru/cars/skoda/karoq/m3204/",
                    "https://www.major-auto.ru/models/skoda/karoq/",
                    "https://www.skoda-auto.com/models/range/karoq",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_karoq",
                    "https://skoda-karoq.ru/",
                    "https://skoda-kuntsevo.ru/models/karoq",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/karoq-asgbagicaktgtg2emsjitg26stu",
                    "https://skoda-favorit.ru/models/karoq",
                    "https://www.autoskd.ru/models/karoq",
                    "https://www.atlant-motors.ru/models/karoq",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://skoda-avtoruss.ru/models/karoq",
                    "https://www.rolf.ru/cars/new/skoda/karoq/",
                    "https://karoqs.ru/"
                ]
            ],
            "шкода карок" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq",
                    "https://auto.ru/moskva/cars/skoda/karoq/all/",
                    "https://rolf-skoda.ru/models/karoq",
                    "https://skoda-karoq.ru/",
                    "https://skoda-kuntsevo.ru/models/karoq",
                    "https://www.skoda-major.ru/karoq/",
                    "https://skoda.drom.ru/karoq/",
                    "https://www.drive2.ru/cars/skoda/karoq/m3204/",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_karoq",
                    "https://www.drive.ru/test-drive/skoda/5e5517f3ec05c4324f000166.html",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/karoq-asgbagicaktgtg2emsjitg26stu",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/",
                    "https://www.rolf.ru/cars/new/skoda/karoq/",
                    "https://avtomir.ru/new-cars/skoda/karoq/",
                    "https://www.atlant-motors.ru/models/karoq",
                    "https://skoda-avtoruss.ru/models/karoq",
                    "https://autospot.ru/brands/skoda/karoq/suv/price/",
                    "https://www.bogemia-skd.ru/models/karoq",
                    "https://karoqs.ru/",
                    "https://avtoruss.ru/skoda/karoq.html"
                ]
            ],
            "skoda octavia 2022" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia",
                    "https://auto.ru/moskva/cars/skoda/octavia/2022-year/all/",
                    "https://rolf-skoda.ru/models/octavia",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-octavia-2022",
                    "https://www.drom.ru/catalog/skoda/octavia/2022/",
                    "https://naavtotrasse.ru/skoda/skoda-octavia-2022.html",
                    "https://www.major-auto.ru/models/skoda/octavia_iv/",
                    "https://www.skoda-major.ru/octavia/",
                    "https://www.rolf.ru/cars/new/skoda/octavia-new/",
                    "https://skoda-favorit.ru/models/octavia",
                    "https://www.avito.ru/all/avtomobili?q=%c5%a0koda+octavia+2022",
                    "https://dzen.ru/media/id/5d88fbf43d008800ae98ccee/skoda-octavia-2022-goda-9-minusov-i-14-pliusov-resurs-motorov-s-kakim-motorom-kupit-621257d838e7b1267a4fbacf",
                    "https://skoda-kuntsevo.ru/models/octavia",
                    "https://www.youtube.com/watch?v=ktz5vmvt5pu",
                    "https://skoda-s-auto.ru/models/octavia",
                    "https://skoda-avtoruss.ru/models/octavia",
                    "https://gt-news.ru/skoda/skoda-octavia-2022/",
                    "https://www.bogemia-skd.ru/models/octavia",
                    "https://www.allcarz.ru/skoda-octavia-2020/",
                    "https://cenyavto.com/skoda-octavia-2022/"
                ]
            ],
            "skoda octavia 2022 комплектации и цены" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia/price",
                    "https://auto.ru/moskva/cars/skoda/octavia/2022-year/all/",
                    "https://www.drom.ru/catalog/skoda/octavia/2022/",
                    "https://rolf-skoda.ru/models/octavia/price",
                    "https://carsdo.ru/skoda/octavia/",
                    "https://www.major-auto.ru/models/skoda/octavia_iv/",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-octavia-2022",
                    "https://www.skoda-major.ru/octavia/",
                    "https://skoda-favorit.ru/models/octavia",
                    "https://naavtotrasse.ru/skoda/skoda-octavia-2022.html",
                    "https://favorit-motors.ru/catalog/new/skoda/new_octavia/",
                    "https://www.rolf.ru/cars/new/skoda/octavia-new/",
                    "https://skoda-avtoruss.ru/models/octavia",
                    "https://gt-news.ru/skoda/skoda-octavia-2022/",
                    "https://moscow.autovsalone.ru/cars/skoda/octavia",
                    "https://carso.ru/skoda/octavia",
                    "https://skoda-centr.ru/oktavia/complect/",
                    "https://auto-kay.ru/cars/skoda/octavia/",
                    "https://www.autoskd.ru/models/octavia/price",
                    "https://rolf-center.ru/brands/skoda/octavia-new/"
                ]
            ],
            "skoda octavia комплектации и цены" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia/price",
                    "https://rolf-skoda.ru/models/octavia/price",
                    "https://auto.ru/moskva/cars/skoda/octavia/new/",
                    "https://skoda-kuntsevo.ru/models/octavia",
                    "https://www.skoda-major.ru/octavia/",
                    "https://skoda-avtoruss.ru/models/octavia",
                    "https://skoda-favorit.ru/models/octavia/price",
                    "https://www.rolf.ru/cars/new/skoda/octavia-new/",
                    "https://carso.ru/skoda/octavia",
                    "https://moscow.drom.ru/skoda/octavia/new/",
                    "https://www.major-auto.ru/models/skoda/octavia_iv/",
                    "https://www.autoskd.ru/models/octavia/price",
                    "https://carsdo.ru/skoda/octavia/",
                    "https://www.autocity-sk.ru/models/octavia",
                    "https://avtoruss.ru/skoda/octavia2.html",
                    "https://favorit-motors.ru/catalog/new/skoda/new_octavia/",
                    "https://adom.ru/skoda/octavia",
                    "https://autospot.ru/brands/skoda/octavia_iv/liftback/price/",
                    "https://www.bogemia-skd.ru/models/octavia",
                    "https://rolf-center.ru/brands/skoda/octavia-new/"
                ]
            ],
            "skoda octavia цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia/price",
                    "https://auto.ru/moskva/cars/skoda/octavia/all/",
                    "https://rolf-skoda.ru/models/octavia/price",
                    "https://moscow.drom.ru/skoda/octavia/new/",
                    "https://www.avito.ru/moskva/avtomobili/skoda/octavia-asgbagicaktgtg2emsjitg2ercg",
                    "https://www.skoda-major.ru/octavia/",
                    "https://www.rolf.ru/cars/new/skoda/octavia-new/",
                    "https://skoda-kuntsevo.ru/models/octavia",
                    "https://carsdo.ru/skoda/octavia/",
                    "https://favorit-motors.ru/catalog/new/skoda/new_octavia/",
                    "https://skoda-favorit.ru/models/octavia",
                    "https://skoda-avtoruss.ru/models/octavia",
                    "https://carso.ru/skoda/octavia-old",
                    "https://autospot.ru/brands/skoda/octavia_iv/liftback/price/",
                    "https://xn----7sbah6aanflhic0bm6c.xn--80adxhks/cars/skoda/octavia/",
                    "https://www.ventus.ru/models/octavia/price",
                    "https://avtomir.ru/new-cars/skoda/octavia/",
                    "https://www.autoskd.ru/models/octavia",
                    "https://www.bogemia-skd.ru/models/octavia",
                    "https://adom.ru/skoda/octavia-combi"
                ]
            ],
            "комплектации шкоды октавии" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia/price",
                    "https://www.drom.ru/catalog/skoda/octavia/",
                    "https://auto.ru/catalog/cars/skoda/octavia/",
                    "https://rolf-skoda.ru/models/octavia/price",
                    "https://carsdo.ru/skoda/octavia/",
                    "https://skoda-kuntsevo.ru/models/octavia",
                    "https://www.skoda-major.ru/octavia/",
                    "https://www.major-auto.ru/models/skoda/octavia_iv/",
                    "http://www.octavia-avto.ru/prices",
                    "https://skoda-favorit.ru/models/octavia",
                    "https://www.drive.ru/brands/skoda/models/2020/octavia",
                    "https://carso.ru/skoda/octavia-old",
                    "https://favorit-motors.ru/catalog/new/skoda/new_octavia/",
                    "https://www.autoskd.ru/models/octavia/price",
                    "https://quto.ru/skoda/octavia",
                    "https://skoda-avtoruss.ru/models/octavia",
                    "https://moscow.autovsalone.ru/cars/skoda/octavia/compare",
                    "https://www.bogemia-skd.ru/models/octavia/price",
                    "https://www.rolf.ru/cars/new/skoda/octavia-new/",
                    "https://autospot.ru/brands/skoda/octavia_iv/liftback/price/"
                ]
            ],
            "комплектация новой шкоды октавии и цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia/price",
                    "https://carsdo.ru/skoda/octavia/",
                    "https://auto.ru/moskva/cars/skoda/octavia/new/",
                    "https://rolf-skoda.ru/models/octavia/price",
                    "https://www.skoda-major.ru/octavia/",
                    "https://favorit-motors.ru/catalog/new/skoda/new_octavia/",
                    "https://www.major-auto.ru/models/skoda/octavia_iv/",
                    "https://carso.ru/skoda/octavia",
                    "https://skoda-kuntsevo.ru/models/octavia",
                    "https://skoda-favorit.ru/models/octavia",
                    "https://www.rolf.ru/cars/new/skoda/octavia-new/",
                    "https://moscow.drom.ru/skoda/octavia/new/",
                    "https://skoda-avtoruss.ru/models/octavia",
                    "https://www.autoskd.ru/models/octavia/price",
                    "https://moscow.autovsalone.ru/cars/skoda/octavia",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-octavia-2022",
                    "https://avtomir.ru/new-cars/skoda/octavia/",
                    "https://autospot.ru/brands/skoda/octavia_iv/liftback/price/",
                    "https://rolf-center.ru/brands/skoda/octavia-new/",
                    "https://adom.ru/skoda/octavia-combi"
                ]
            ],
            "купить новую skoda octavia" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia/price",
                    "https://auto.ru/moskva/cars/skoda/octavia/new/",
                    "https://www.rolf.ru/cars/new/skoda/octavia-new/",
                    "https://www.major-auto.ru/models/skoda/octavia_iv/",
                    "https://rolf-skoda.ru/models/octavia/price",
                    "https://skoda-kuntsevo.ru/models/octavia",
                    "https://skoda-favorit.ru/models/octavia",
                    "https://moscow.drom.ru/skoda/octavia/new/",
                    "https://favorit-motors.ru/catalog/new/skoda/new_octavia/",
                    "https://skoda-avtoruss.ru/models/octavia",
                    "https://www.atlant-motors.ru/models/octavia",
                    "https://avtomir.ru/new-cars/skoda/octavia/",
                    "https://www.avito.ru/moskva/avtomobili/novyy/skoda/octavia-asgbagica0sgfmbmaec2dz6zkok2dysska",
                    "https://carso.ru/skoda/octavia-old",
                    "https://www.bogemia-skd.ru/models/octavia",
                    "https://autospot.ru/brands/skoda/octavia_iv/liftback/price/",
                    "https://xn----7sbah6aanflhic0bm6c.xn--80adxhks/cars/skoda/octavia/",
                    "https://www.ventus.ru/models/octavia/price",
                    "https://carsdo.ru/skoda/octavia/moscow/",
                    "https://abc-auto.ru/skoda/octavia/"
                ]
            ],
            "купить шкода октавия" => [
                "sites" => [
                    "https://auto.ru/moskva/cars/skoda/octavia/used/",
                    "https://cars.skoda-avto.ru/octavia",
                    "https://www.avito.ru/moskva/avtomobili/skoda/octavia-asgbagicaktgtg2emsjitg2ercg",
                    "https://www.rolf.ru/cars/new/skoda/octavia-new/",
                    "https://moscow.drom.ru/skoda/octavia/used/",
                    "https://skoda-kuntsevo.ru/models/octavia",
                    "https://www.major-auto.ru/models/skoda/octavia_iv/",
                    "https://rolf-skoda.ru/models/octavia",
                    "https://skoda-avtoruss.ru/models/octavia",
                    "https://skoda-favorit.ru/models/octavia",
                    "https://www.incom-auto.ru/auto/skoda/octavia/",
                    "https://www.skoda-major.ru/octavia/",
                    "https://carso.ru/skoda/octavia-old",
                    "https://favorit-motors.ru/catalog/new/skoda/new_octavia/",
                    "https://www.atlant-motors.ru/models/octavia",
                    "https://autospot.ru/brands/skoda/octavia_iv/liftback/price/",
                    "https://rolf-probeg.ru/cars/skoda/octavia/",
                    "https://www.bogemia-skd.ru/models/octavia",
                    "https://www.major-expert.ru/cars/moscow/skoda/octavia/",
                    "https://avtomir.ru/new-cars/skoda/octavia/"
                ]
            ],
            "купить шкоду октавию" => [
                "sites" => [
                    "https://auto.ru/moskva/cars/skoda/octavia/used/",
                    "https://cars.skoda-avto.ru/octavia",
                    "https://www.avito.ru/moskva/avtomobili/skoda/octavia-asgbagicaktgtg2emsjitg2ercg",
                    "https://moscow.drom.ru/skoda/octavia/used/",
                    "https://www.rolf.ru/cars/new/skoda/octavia-new/",
                    "https://www.major-auto.ru/models/skoda/octavia_iv/",
                    "https://skoda-kuntsevo.ru/models/octavia",
                    "https://rolf-skoda.ru/models/octavia",
                    "https://skoda-favorit.ru/models/octavia",
                    "https://favorit-motors.ru/catalog/new/skoda/new_octavia/",
                    "https://carso.ru/skoda/octavia-old",
                    "https://skoda-avtoruss.ru/models/octavia",
                    "https://moscow.110km.ru/prodazha/skoda/octavia/poderzhannie/",
                    "https://autospot.ru/brands/skoda/octavia_iv/liftback/price/",
                    "https://moskva.mbib.ru/skoda/octavia/used",
                    "https://avtomir.ru/new-cars/skoda/octavia/",
                    "https://www.bogemia-skd.ru/models/octavia",
                    "https://www.autoskd.ru/models/octavia",
                    "https://xn----7sbah6aanflhic0bm6c.xn--80adxhks/cars/skoda/octavia/",
                    "https://www.atlant-motors.ru/models/octavia"
                ]
            ],
            "новая skoda octavia 2022" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia",
                    "https://auto.ru/moskva/cars/skoda/octavia/2022-year/new/",
                    "https://naavtotrasse.ru/skoda/skoda-octavia-2022.html",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-octavia-2022",
                    "https://rolf-skoda.ru/models/octavia",
                    "https://www.drom.ru/catalog/skoda/octavia/2022/",
                    "https://www.rolf.ru/cars/new/skoda/octavia-new/",
                    "https://skoda-favorit.ru/models/octavia",
                    "https://www.skoda-major.ru/octavia/",
                    "https://cenyavto.com/skoda-octavia-2022/",
                    "https://skoda-kuntsevo.ru/models/octavia",
                    "https://skoda-avtoruss.ru/models/octavia",
                    "https://gt-news.ru/skoda/skoda-octavia-2022/",
                    "https://www.major-auto.ru/models/skoda/octavia_iv/",
                    "https://www.youtube.com/watch?v=ktz5vmvt5pu",
                    "https://moscow.autovsalone.ru/cars/skoda/octavia",
                    "https://unitedavto.ru/cars/skoda/octavia-new/",
                    "https://www.bogemia-skd.ru/models/octavia",
                    "https://favorit-motors.ru/catalog/new/skoda/new_octavia/",
                    "https://www.avito.ru/all/avtomobili?q=%c5%a0koda+octavia+2022"
                ]
            ],
            "новая шкода октавия цена и комплектация" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia/price",
                    "https://carsdo.ru/skoda/octavia/",
                    "https://auto.ru/moskva/cars/skoda/octavia/new/",
                    "https://rolf-skoda.ru/models/octavia/price",
                    "https://skoda-kuntsevo.ru/models/octavia",
                    "https://www.skoda-major.ru/octavia/",
                    "https://www.rolf.ru/cars/new/skoda/octavia-new/",
                    "https://favorit-motors.ru/catalog/new/skoda/new_octavia/",
                    "https://www.major-auto.ru/models/skoda/octavia_iv/",
                    "https://auto.drom.ru/skoda/octavia/new/",
                    "https://skoda-favorit.ru/models/octavia",
                    "https://carso.ru/skoda/octavia",
                    "https://skoda-avtoruss.ru/models/octavia",
                    "https://www.avito.ru/moskva/avtomobili/novyy/skoda/octavia-asgbagica0sgfmbmaec2dz6zkok2dysska",
                    "https://www.autoskd.ru/models/octavia/price",
                    "https://xn----7sbah6aanflhic0bm6c.xn--80adxhks/cars/skoda/octavia/",
                    "https://avtomir.ru/new-cars/skoda/octavia/",
                    "https://moscow.autovsalone.ru/cars/skoda/octavia",
                    "https://autospot.ru/brands/skoda/octavia_iv/liftback/price/",
                    "https://adom.ru/skoda/octavia-combi"
                ]
            ],
            "цена шкоды октавии" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia/price",
                    "https://auto.ru/moskva/cars/skoda/octavia/all/",
                    "https://moscow.drom.ru/skoda/octavia/",
                    "https://www.avito.ru/moskva/avtomobili/skoda/octavia-asgbagicaktgtg2emsjitg2ercg",
                    "https://www.major-auto.ru/models/skoda/octavia_iv/",
                    "https://rolf-skoda.ru/models/octavia/price",
                    "https://carsdo.ru/skoda/octavia/",
                    "https://skoda-kuntsevo.ru/models/octavia",
                    "https://www.rolf.ru/cars/new/skoda/octavia-new/",
                    "https://carso.ru/skoda/octavia-old",
                    "https://skoda-favorit.ru/models/octavia",
                    "https://favorit-motors.ru/catalog/new/skoda/new_octavia/",
                    "https://skoda-avtoruss.ru/models/octavia",
                    "https://autospot.ru/brands/skoda/octavia_iv/liftback/price/",
                    "https://www.autoskd.ru/models/octavia",
                    "https://www.ventus.ru/models/octavia/price",
                    "https://avtomir.ru/new-cars/skoda/octavia/",
                    "https://moscow.autovsalone.ru/cars/skoda/octavia",
                    "https://www.bogemia-skd.ru/models/octavia",
                    "https://adom.ru/skoda/octavia-combi"
                ]
            ],
            "шкода октавия комплектации" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia/price",
                    "https://www.drom.ru/catalog/skoda/octavia/",
                    "https://rolf-skoda.ru/models/octavia/price",
                    "https://auto.ru/catalog/cars/skoda/octavia/",
                    "https://carsdo.ru/skoda/octavia/",
                    "https://skoda-kuntsevo.ru/models/octavia",
                    "https://www.major-auto.ru/models/skoda/octavia_iv/",
                    "https://skoda-favorit.ru/models/octavia",
                    "https://www.autoskd.ru/models/octavia/price",
                    "https://www.drive.ru/brands/skoda/models/2020/octavia",
                    "https://skoda-avtoruss.ru/models/octavia",
                    "https://favorit-motors.ru/catalog/new/skoda/new_octavia/",
                    "https://www.rolf.ru/cars/new/skoda/octavia-new/",
                    "https://carso.ru/skoda/octavia-old",
                    "https://www.europa-avto.ru/models/octavia/price",
                    "https://quto.ru/skoda/octavia",
                    "https://moscow.autovsalone.ru/cars/skoda/octavia/compare",
                    "https://www.bogemia-skd.ru/models/octavia/price",
                    "https://aksa-auto.ru/catalog/skoda/octavia",
                    "https://autospot.ru/brands/skoda/octavia_iv/liftback/price/"
                ]
            ],
            "skoda octavia" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia",
                    "https://auto.ru/moskva/cars/skoda/octavia/all/",
                    "https://www.skoda-auto.com/models/range/octavia",
                    "https://moscow.drom.ru/skoda/octavia/",
                    "https://rolf-skoda.ru/models/octavia",
                    "https://www.avito.ru/moskva/avtomobili/skoda/octavia-asgbagicaktgtg2emsjitg2ercg",
                    "https://www.rolf.ru/cars/new/skoda/octavia-new/",
                    "https://www.drive2.ru/cars/skoda/octavia/m2473/",
                    "https://www.major-auto.ru/models/skoda/octavia_iv/",
                    "https://skoda-kuntsevo.ru/models/octavia",
                    "https://skoda-avtoruss.ru/models/octavia",
                    "https://skoda-favorit.ru/models/octavia",
                    "https://www.drive.ru/brands/skoda/models/2020/octavia",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_octavia_(1996)",
                    "https://www.autoskd.ru/models/octavia",
                    "https://en.wikipedia.org/wiki/%c5%a0koda_octavia",
                    "https://autospot.ru/brands/skoda/octavia_iv/liftback/price/",
                    "https://favorit-motors.ru/catalog/new/skoda/new_octavia/",
                    "https://www.bogemia-skd.ru/models/octavia",
                    "https://avtomir.ru/new-cars/skoda/octavia/"
                ]
            ],
            "шкода октавиа" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia",
                    "https://auto.ru/moskva/cars/skoda/octavia/all/",
                    "https://moscow.drom.ru/skoda/octavia/",
                    "https://www.avito.ru/moskva/avtomobili/skoda/octavia-asgbagicaktgtg2emsjitg2ercg",
                    "https://rolf-skoda.ru/models/octavia",
                    "https://skoda-kuntsevo.ru/models/octavia",
                    "https://www.skoda-major.ru/octavia/",
                    "https://www.drive2.ru/cars/skoda/octavia/m2473/",
                    "https://www.rolf.ru/cars/new/skoda/octavia-new/",
                    "https://skoda-avtoruss.ru/models/octavia",
                    "https://skoda-favorit.ru/models/octavia",
                    "https://translate.yandex.ru/translate?lang=en-ru&url=https%3a%2f%2fen.wikipedia.org%2fwiki%2f%25c5%25a0koda_octavia&view=c",
                    "https://www.drive.ru/brands/skoda/models/2020/octavia",
                    "https://www.autoskd.ru/models/octavia",
                    "https://carsdo.ru/skoda/octavia/",
                    "https://favorit-motors.ru/catalog/new/skoda/new_octavia/",
                    "https://carso.ru/skoda/octavia",
                    "https://autospot.ru/brands/skoda/octavia_iv/liftback/price/",
                    "https://avtomir.ru/new-cars/skoda/octavia/",
                    "https://www.bogemia-skd.ru/models/octavia"
                ]
            ],
            "шкода октавия" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia",
                    "https://auto.ru/moskva/cars/skoda/octavia/all/",
                    "https://skoda-kuntsevo.ru/models/octavia",
                    "https://moscow.drom.ru/skoda/octavia/",
                    "https://www.avito.ru/moskva/avtomobili/skoda/octavia-asgbagicaktgtg2emsjitg2ercg",
                    "https://rolf-skoda.ru/models/octavia",
                    "https://www.rolf.ru/cars/new/skoda/octavia-new/",
                    "https://www.drive2.ru/cars/skoda/octavia/m2473/",
                    "https://www.major-auto.ru/models/skoda/octavia_iv/",
                    "https://skoda-avtoruss.ru/models/octavia",
                    "https://skoda-favorit.ru/models/octavia",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_octavia_(1996)",
                    "https://www.drive.ru/brands/skoda/models/2020/octavia",
                    "https://www.skoda-auto.com/models/range/octavia",
                    "https://favorit-motors.ru/catalog/new/skoda/new_octavia/",
                    "https://translate.yandex.ru/translate?lang=en-ru&url=https%3a%2f%2fen.wikipedia.org%2fwiki%2f%25c5%25a0koda_octavia&view=c",
                    "https://carso.ru/skoda/octavia-old",
                    "https://carsdo.ru/skoda/octavia/",
                    "https://www.autoskd.ru/models/octavia",
                    "https://autospot.ru/brands/skoda/octavia_iv/liftback/price/"
                ]
            ],
            "шкода октавия в наличии у официальных дилеров" => [
                "sites" => [
                    "https://cars.skoda-avto.ru/octavia",
                    "https://www.rolf.ru/cars/new/skoda/octavia-new/",
                    "https://www.skoda-major.ru/octavia/",
                    "https://rolf-skoda.ru/models/octavia",
                    "https://skoda-favorit.ru/models/octavia",
                    "https://favorit-motors.ru/catalog/new/skoda/new_octavia/",
                    "https://skoda-kuntsevo.ru/models/octavia",
                    "https://skoda-avtoruss.ru/models/octavia",
                    "https://avtomir.ru/new-cars/skoda/octavia/",
                    "https://auto.ru/moskva/cars/skoda/octavia/new/",
                    "https://www.bogemia-skd.ru/models/octavia",
                    "https://www.autoskd.ru/models/octavia",
                    "https://carsdo.ru/skoda/octavia/moscow/",
                    "https://www.bips.ru/skoda/octavia",
                    "https://avtoruss.ru/skoda/octavia2.html",
                    "https://rolf-center.ru/brands/skoda/octavia-new/",
                    "https://carso.ru/skoda/octavia",
                    "https://abc-auto.ru/skoda/octavia/",
                    "https://xn----7sbah6aanflhic0bm6c.xn--80adxhks/cars/skoda/octavia/",
                    "https://adom.ru/skoda/octavia-combi"
                ]
            ],
            "шкода октавия комплектации и цены" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia/price",
                    "https://rolf-skoda.ru/models/octavia/price",
                    "https://auto.ru/moskva/cars/skoda/octavia/new/",
                    "https://carsdo.ru/skoda/octavia/",
                    "https://www.skoda-major.ru/octavia/",
                    "https://skoda-kuntsevo.ru/models/octavia",
                    "https://www.rolf.ru/cars/new/skoda/octavia-new/",
                    "https://skoda-favorit.ru/models/octavia/price",
                    "https://skoda-avtoruss.ru/models/octavia",
                    "https://moscow.drom.ru/skoda/octavia/new/",
                    "https://favorit-motors.ru/catalog/new/skoda/new_octavia/",
                    "https://www.autoskd.ru/models/octavia/price",
                    "https://carso.ru/skoda/octavia-old",
                    "https://www.bogemia-skd.ru/models/octavia/price",
                    "https://autospot.ru/brands/skoda/octavia_iv/liftback/price/",
                    "https://avtomir.ru/new-cars/skoda/octavia/",
                    "https://www.drive.ru/brands/skoda/models/2020/octavia",
                    "https://adom.ru/skoda/octavia",
                    "https://moscow.autovsalone.ru/cars/skoda/octavia",
                    "https://auto-kay.ru/cars/skoda/octavia-new/"
                ]
            ],
            "шкода октавия цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia/price",
                    "https://auto.ru/moskva/cars/skoda/octavia/all/",
                    "https://www.avito.ru/moskva/avtomobili/skoda/octavia-asgbagicaktgtg2emsjitg2ercg",
                    "https://moscow.drom.ru/skoda/octavia/",
                    "https://rolf-skoda.ru/models/octavia/price",
                    "https://www.skoda-major.ru/octavia/",
                    "https://skoda-kuntsevo.ru/models/octavia",
                    "https://www.rolf.ru/cars/new/skoda/octavia-new/",
                    "https://carsdo.ru/skoda/octavia/",
                    "https://favorit-motors.ru/catalog/new/skoda/new_octavia/",
                    "https://skoda-avtoruss.ru/models/octavia",
                    "https://skoda-favorit.ru/models/octavia",
                    "https://autospot.ru/brands/skoda/octavia_iv/liftback/price/",
                    "https://carso.ru/skoda/octavia-old",
                    "https://www.bogemia-skd.ru/models/octavia",
                    "https://avtomir.ru/new-cars/skoda/octavia/",
                    "https://www.autoskd.ru/models/octavia",
                    "https://xn----7sbah6aanflhic0bm6c.xn--80adxhks/cars/skoda/octavia/",
                    "https://www.ventus.ru/models/octavia/price",
                    "https://www.atlant-motors.ru/models/octavia"
                ]
            ],
            "новая skoda octavia" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia",
                    "https://auto.ru/moskva/cars/skoda/octavia/new/",
                    "https://skoda-kuntsevo.ru/models/octavia",
                    "https://www.rolf.ru/cars/new/skoda/octavia-new/",
                    "https://skoda-favorit.ru/models/octavia",
                    "https://www.skoda-major.ru/octavia/",
                    "https://www.drive2.ru/o/b/574858490057589922/",
                    "https://rolf-skoda.ru/models/octavia/price",
                    "https://www.major-auto.ru/models/skoda/octavia_iv/",
                    "https://skoda-avtoruss.ru/models/octavia",
                    "https://moscow.drom.ru/skoda/octavia/new/",
                    "https://favorit-motors.ru/catalog/new/skoda/new_octavia/",
                    "https://www.autoskd.ru/models/octavia",
                    "https://avtomir.ru/new-cars/skoda/octavia/",
                    "https://www.skoda-auto.com/models/range/octavia",
                    "https://carsdo.ru/skoda/octavia/",
                    "https://www.autocity-sk.ru/models/octavia",
                    "https://carso.ru/skoda/octavia",
                    "https://www.bogemia-skd.ru/models/octavia",
                    "https://autospot.ru/brands/skoda/octavia_iv/liftback/price/"
                ]
            ],
            "skoda octavia комплектации" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia/price",
                    "https://rolf-skoda.ru/models/octavia/price",
                    "https://www.drom.ru/catalog/skoda/octavia/",
                    "https://auto.ru/catalog/cars/skoda/octavia/",
                    "https://millenium-avto.ru/models/octavia/price",
                    "https://skoda-kuntsevo.ru/models/octavia",
                    "https://www.major-auto.ru/models/skoda/octavia_iv/",
                    "https://www.skoda-major.ru/octavia/komplektacii-i-tseny/",
                    "https://legion-motors.ru/models/octavia/price",
                    "https://skoda-favorit.ru/models/octavia",
                    "https://www.drive.ru/brands/skoda/models/2020/octavia",
                    "https://skoda-avtoruss.ru/models/octavia",
                    "https://favorit-motors.ru/catalog/new/skoda/new_octavia/",
                    "https://carsdo.ru/skoda/octavia/",
                    "https://carso.ru/skoda/octavia-old",
                    "https://www.rolf.ru/cars/new/skoda/octavia-new/",
                    "https://www.skoda-vitebskiy.ru/models/octavia/price",
                    "https://skoda-autopraga.ru/models/octavia",
                    "https://www.bogemia-skd.ru/models/octavia/price",
                    "https://www.autocity-sk.ru/models/octavia"
                ]
            ],
            "skoda octavia купить" => [
                "sites" => [
                    "https://auto.ru/moskva/cars/skoda/octavia/used/",
                    "https://cars.skoda-avto.ru/octavia",
                    "https://www.avito.ru/moskva/avtomobili/skoda/octavia-asgbagicaktgtg2emsjitg2ercg",
                    "https://www.major-auto.ru/models/skoda/octavia_iv/",
                    "https://moscow.drom.ru/skoda/octavia/",
                    "https://www.rolf.ru/cars/new/skoda/octavia-new/",
                    "https://skoda-kuntsevo.ru/models/octavia",
                    "https://rolf-skoda.ru/models/octavia",
                    "https://skoda-favorit.ru/models/octavia",
                    "https://carso.ru/skoda/octavia-old",
                    "https://favorit-motors.ru/catalog/new/skoda/new_octavia/",
                    "https://skoda-avtoruss.ru/models/octavia",
                    "https://autospot.ru/brands/skoda/octavia_iv/liftback/price/",
                    "https://xn----7sbah6aanflhic0bm6c.xn--80adxhks/cars/skoda/octavia/",
                    "https://www.autoskd.ru/models/octavia",
                    "https://www.bogemia-skd.ru/models/octavia",
                    "https://avtomir.ru/new-cars/skoda/octavia/",
                    "https://moscow.110km.ru/prodazha/skoda/octavia/poderzhannie/",
                    "https://moskva.mbib.ru/skoda/octavia/used",
                    "https://rolf-center.ru/brands/skoda/octavia-new/"
                ]
            ],
            "skoda rapid комплектации и цены" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid/price",
                    "https://auto.ru/moskva/cars/skoda/rapid/new/",
                    "https://rolf-skoda.ru/models/rapid",
                    "https://www.skoda-major.ru/rapid/",
                    "https://carsdo.ru/skoda/rapid/",
                    "https://www.drom.ru/catalog/skoda/rapid/",
                    "https://avtomir.ru/new-cars/skoda/rapid/",
                    "https://favorit-motors.ru/catalog/new/skoda/new_rapid/",
                    "https://skoda-favorit.ru/models/rapid/price",
                    "https://m.avito.ru/moskva/avtomobili/novyy/skoda/rapid-asgbagica0sgfmbmaec2dz6zkok2dzsuka",
                    "https://www.rolf.ru/cars/skoda/novyi_rapid/",
                    "https://skoda-kuntsevo.ru/models/rapid",
                    "https://www.drive.ru/brands/skoda/models/2019/rapid",
                    "https://www.atlant-motors.ru/models/rapid/price",
                    "https://avtoruss.ru/skoda/novyj-rapid.html",
                    "https://www.bogemia-skd.ru/models/rapid/price",
                    "https://skoda-ap.ru/models/rapid/price",
                    "https://autospot.ru/brands/skoda/rapid_ii/liftback/price/",
                    "https://auto-leon.ru/skoda/rapid-new/",
                    "https://www.bips.ru/skoda/rapid"
                ]
            ],
            "skoda rapid цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid/price",
                    "https://auto.ru/moskva/cars/skoda/rapid/all/",
                    "https://rolf-skoda.ru/models/rapid",
                    "https://moscow.drom.ru/skoda/rapid/new/",
                    "https://www.skoda-major.ru/rapid/",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/rapid-asgbagicaktgtg2emsjitg2urig",
                    "https://avtomir.ru/new-cars/skoda/rapid/",
                    "https://www.rolf.ru/cars/new/skoda/novyi_rapid/",
                    "https://autospot.ru/brands/skoda/rapid_ii/liftback/price/",
                    "https://favorit-motors.ru/catalog/new/skoda/new_rapid/",
                    "https://skoda-favorit.ru/models/rapid",
                    "https://skoda-avtoruss.ru/models/rapid",
                    "https://carsdo.ru/skoda/rapid/",
                    "https://skoda-kuntsevo.ru/models/rapid",
                    "https://avtoruss.ru/skoda/novyj-rapid.html",
                    "https://www.atlant-motors.ru/models/rapid",
                    "https://carso.ru/skoda/rapid",
                    "https://nz-cars.ru/cars/skoda/rapid/",
                    "https://autogansa.ru/cars/skoda/rapid/",
                    "https://rolf-center.ru/new/skoda/novyi_rapid/"
                ]
            ],
            "škoda rapid 2022 цена и комплектация" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid/price",
                    "https://auto.ru/moskva/cars/skoda/rapid/2022-year/all/",
                    "https://www.major-auto.ru/models/skoda/rapid_ii/",
                    "https://carsdo.ru/skoda/rapid/",
                    "https://rolf-skoda.ru/models/rapid",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-rapid-2022",
                    "https://skoda-favorit.ru/models/rapid",
                    "https://www.skoda-major.ru/rapid/",
                    "https://naavtotrasse.ru/skoda/skoda-rapid-2022.html",
                    "https://m.avito.ru/moskva/avtomobili/novyy/skoda/rapid-asgbagica0sgfmbmaec2dz6zkok2dzsuka",
                    "https://www.drom.ru/catalog/skoda/rapid/2022/",
                    "https://favorit-motors.ru/catalog/new/skoda/new_rapid/",
                    "https://avtomir.ru/new-cars/skoda/rapid/",
                    "https://moscow.autovsalone.ru/cars/skoda/rapid/compare",
                    "https://skoda-avtoruss.ru/models/rapid",
                    "https://www.bogemia-skd.ru/models/rapid/price",
                    "https://avtoruss.ru/skoda/novyj-rapid.html",
                    "https://nz-cars.ru/cars/skoda/rapid/",
                    "https://auto-kay.ru/cars/skoda/rapid-new/",
                    "https://skoda-kuntsevo.ru/models/rapid"
                ]
            ],
            "купить шкода рапид" => [
                "sites" => [
                    "https://auto.ru/moskva/cars/skoda/rapid/all/",
                    "https://cars.skoda-avto.ru/rapid",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/rapid-asgbagicaktgtg2emsjitg2urig",
                    "https://rolf-skoda.ru/models/rapid",
                    "https://moscow.drom.ru/skoda/rapid/new/",
                    "https://www.major-auto.ru/models/skoda/rapid_ii/",
                    "https://avtomir.ru/new-cars/skoda/rapid/",
                    "https://www.rolf.ru/cars/skoda/novyi_rapid/",
                    "https://skoda-avtoruss.ru/models/rapid",
                    "https://skoda-favorit.ru/models/rapid",
                    "https://skoda-kuntsevo.ru/models/rapid",
                    "https://favorit-motors.ru/catalog/new/skoda/new_rapid/",
                    "https://autospot.ru/brands/skoda/rapid_ii/liftback/price/",
                    "https://www.atlant-motors.ru/models/rapid",
                    "https://www.autoskd.ru/models/rapid",
                    "https://avtoruss.ru/skoda/novyj-rapid.html",
                    "https://carsdo.ru/skoda/rapid/moscow/",
                    "https://www.bogemia-skd.ru/models/rapid",
                    "https://carso.ru/skoda/rapid",
                    "https://moscow.110km.ru/prodazha/skoda/rapid/poderzhannie/"
                ]
            ],
            "шкода рапид" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid",
                    "https://rolf-skoda.ru/models/rapid",
                    "https://auto.ru/moskva/cars/skoda/rapid/all/",
                    "https://skoda.drom.ru/rapid/",
                    "https://www.skoda-major.ru/rapid/",
                    "https://www.avito.ru/moskva/avtomobili/skoda/rapid-asgbagicaktgtg2emsjitg2urig",
                    "https://skoda-kuntsevo.ru/models/rapid",
                    "https://www.drive2.ru/cars/skoda/rapid/m194/",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_rapid_(2012)",
                    "https://skoda-favorit.ru/models/rapid",
                    "https://avtomir.ru/new-cars/skoda/rapid/",
                    "https://www.drive.ru/brands/skoda/models/2019/rapid",
                    "https://www.rolf.ru/cars/new/skoda/novyi_rapid/",
                    "https://skoda-avtoruss.ru/models/rapid",
                    "https://www.autoskd.ru/models/rapid",
                    "https://carsdo.ru/skoda/rapid/",
                    "https://favorit-motors.ru/catalog/new/skoda/new_rapid/",
                    "https://autospot.ru/brands/skoda/rapid_ii/liftback/price/",
                    "https://www.zr.ru/cars/skoda/-/skoda-rapid/",
                    "https://www.bogemia-skd.ru/models/rapid"
                ]
            ],
            "шкода рапид 2022 комплектации" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid/price",
                    "https://www.drom.ru/catalog/skoda/rapid/2022/",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-rapid-2022",
                    "https://naavtotrasse.ru/skoda/skoda-rapid-2022.html",
                    "https://auto.ru/moskva/cars/skoda/rapid/2022-year/new/",
                    "https://carsdo.ru/skoda/rapid/",
                    "https://rolf-skoda.ru/models/rapid",
                    "https://www.skoda-major.ru/rapid/",
                    "https://www.major-auto.ru/models/skoda/rapid_ii/",
                    "https://cenyavto.com/skoda-rapid-2022/",
                    "https://avtomir.ru/new-cars/skoda/rapid/",
                    "https://favorit-motors.ru/catalog/new/skoda/new_rapid/komplektacii-i-ceny/",
                    "https://skoda-centr.ru/rapid/complect/",
                    "https://moscow.autovsalone.ru/cars/skoda/rapid/compare",
                    "https://skoda-favorit.ru/models/rapid",
                    "https://www.bogemia-skd.ru/models/rapid/price",
                    "https://autompv.ru/new-auto/48749-skoda-rapid-2021.html",
                    "https://www.allcarz.ru/skoda-rapid-2021/",
                    "https://skoda-yug-avto.ru/models/rapid/price",
                    "https://avtoruss.ru/skoda/novyj-rapid.html"
                ]
            ],
            "шкода рапид 2022 цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid/price",
                    "https://auto.ru/moskva/cars/skoda/rapid/2022-year/all/",
                    "https://m.avito.ru/moskva/avtomobili/novyy/skoda/rapid-asgbagica0sgfmbmaec2dz6zkok2dzsuka",
                    "https://rolf-skoda.ru/models/rapid",
                    "https://avtomir.ru/new-cars/skoda/rapid/",
                    "https://www.major-auto.ru/models/skoda/rapid_ii/",
                    "https://www.skoda-major.ru/rapid/",
                    "https://carsdo.ru/skoda/rapid/",
                    "https://favorit-motors.ru/catalog/new/skoda/new_rapid/",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-rapid-2022",
                    "https://skoda-favorit.ru/models/rapid",
                    "https://auto.drom.ru/skoda/rapid/year-2022/",
                    "https://naavtotrasse.ru/skoda/skoda-rapid-2022.html",
                    "https://skoda-avtoruss.ru/models/rapid",
                    "https://moscow.autovsalone.ru/cars/skoda/rapid/entry-16_90hp_5mt_2022-modelnyy-god",
                    "https://www.bogemia-skd.ru/models/rapid/price",
                    "https://skoda-kuntsevo.ru/models/rapid",
                    "https://avtoruss.ru/skoda/novyj-rapid.html",
                    "https://auto-kay.ru/cars/skoda/rapid-new/",
                    "https://www.rolf.ru/cars/new/skoda/novyi_rapid/"
                ]
            ],
            "шкода рапид комплектации" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid/price",
                    "https://rolf-skoda.ru/models/rapid",
                    "https://www.skoda-major.ru/rapid/komplektacii-i-tseny/",
                    "https://www.autoskd.ru/models/rapid/price",
                    "https://www.drom.ru/catalog/skoda/rapid/",
                    "https://auto.ru/catalog/cars/skoda/rapid/",
                    "https://avtoruss.ru/skoda/novyj-rapid.html",
                    "https://skoda-kuntsevo.ru/models/rapid",
                    "https://www.atlant-motors.ru/models/rapid/price",
                    "https://www.drive.ru/brands/skoda/models/2019/rapid",
                    "https://www.bogemia-skd.ru/models/rapid/price",
                    "https://skoda-avtoruss.ru/models/rapid",
                    "https://skoda-favorit.ru/models/rapid/price",
                    "https://www.skoda-podolsk.ru/models/rapid/price",
                    "https://www.rolf.ru/cars/skoda/novyi_rapid/",
                    "https://carso.ru/skoda/rapid",
                    "https://www.major-auto.ru/models/skoda/rapid_ii/",
                    "https://auto-leon.ru/skoda/rapid-new/",
                    "https://www.autocity-sk.ru/models/rapid",
                    "https://center-auto.ru/katalog/skoda/rapid/"
                ]
            ],
            "шкода рапид комплектация и цены" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid/price",
                    "https://auto.ru/moskva/cars/skoda/rapid/new/",
                    "https://rolf-skoda.ru/models/rapid",
                    "https://www.skoda-major.ru/rapid/",
                    "https://carsdo.ru/skoda/rapid/",
                    "https://m.avito.ru/moskva/avtomobili/novyy/skoda/rapid-asgbagica0sgfmbmaec2dz6zkok2dzsuka",
                    "https://avtomir.ru/new-cars/skoda/rapid/",
                    "https://moscow.drom.ru/skoda/rapid/new/",
                    "https://favorit-motors.ru/catalog/new/skoda/new_rapid/",
                    "https://skoda-favorit.ru/models/rapid/price",
                    "https://www.rolf.ru/cars/skoda/novyi_rapid/",
                    "https://skoda-avtoruss.ru/models/rapid",
                    "https://skoda-kuntsevo.ru/models/rapid",
                    "https://www.bogemia-skd.ru/models/rapid/price",
                    "https://www.autoskd.ru/models/rapid/price",
                    "https://www.atlant-motors.ru/models/rapid/price",
                    "https://carso.ru/skoda/rapid",
                    "https://avtoruss.ru/skoda/novyj-rapid.html",
                    "https://www.drive.ru/brands/skoda/models/2019/rapid",
                    "https://autospot.ru/brands/skoda/rapid_ii/liftback/price/"
                ]
            ],
            "шкода рапид фл 2022" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid",
                    "https://auto.ru/moskva/cars/skoda/rapid/2022-year/all/",
                    "https://www.drom.ru/catalog/skoda/rapid/2022/",
                    "https://www.youtube.com/watch?v=k9zipo6i6w4",
                    "https://naavtotrasse.ru/skoda/skoda-rapid-2022.html",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-rapid-2022",
                    "https://skoda-favorit.ru/models/rapid",
                    "https://www.sove2u.ru/%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d1%80%d0%b0%d0%bf%d0%b8%d0%b4-%d0%b0%d0%bc%d0%b1%d0%b8%d1%88%d0%bd-2022/",
                    "https://cenyavto.com/skoda-rapid-2022/",
                    "https://avtomir.ru/new-cars/skoda/rapid/",
                    "https://autompv.ru/new-auto/48749-skoda-rapid-2021.html",
                    "https://moscow.autovsalone.ru/cars/skoda/rapid/entry-16_90hp_5mt_2022-modelnyy-god",
                    "https://rolf-skoda.ru/models/rapid",
                    "https://www.major-auto.ru/models/skoda/rapid_ii/",
                    "https://www.allcarz.ru/skoda-rapid-2021/",
                    "https://favorit-motors.ru/catalog/new/skoda/new_rapid/",
                    "https://carsdo.ru/skoda/rapid/",
                    "https://skoda-avtoruss.ru/models/rapid",
                    "https://www.skoda-major.ru/rapid/",
                    "https://auto-leon.ru/skoda/rapid-new/"
                ]
            ],
            "шкода рапид цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid/price",
                    "https://auto.ru/moskva/cars/skoda/rapid/all/",
                    "https://rolf-skoda.ru/models/rapid",
                    "https://moscow.drom.ru/skoda/rapid/new/",
                    "https://www.avito.ru/moskva/avtomobili/skoda/rapid-asgbagicaktgtg2emsjitg2urig",
                    "https://www.skoda-major.ru/rapid/",
                    "https://avtomir.ru/new-cars/skoda/rapid/",
                    "https://carsdo.ru/skoda/rapid/",
                    "https://www.rolf.ru/cars/new/skoda/novyi_rapid/",
                    "https://favorit-motors.ru/catalog/new/skoda/new_rapid/",
                    "https://skoda-kuntsevo.ru/models/rapid",
                    "https://skoda-favorit.ru/models/rapid",
                    "https://skoda-avtoruss.ru/models/rapid",
                    "https://autospot.ru/brands/skoda/rapid_ii/liftback/price/",
                    "https://avtoruss.ru/skoda/novyj-rapid.html",
                    "https://carso.ru/skoda/rapid",
                    "https://www.bogemia-skd.ru/models/rapid/price",
                    "https://www.bips.ru/skoda/rapid",
                    "https://www.atlant-motors.ru/models/rapid",
                    "https://autogansa.ru/cars/skoda/rapid/"
                ]
            ],
            "шкода рапид цена и комплектация 2022" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid/price",
                    "https://auto.ru/moskva/cars/skoda/rapid/2022-year/new/",
                    "https://carsdo.ru/skoda/rapid/",
                    "https://rolf-skoda.ru/models/rapid",
                    "https://www.major-auto.ru/models/skoda/rapid_ii/",
                    "https://www.skoda-major.ru/rapid/",
                    "https://naavtotrasse.ru/skoda/skoda-rapid-2022.html",
                    "https://avtomir.ru/new-cars/skoda/rapid/",
                    "https://m.avito.ru/moskva/avtomobili/novyy/skoda/rapid-asgbagica0sgfmbmaec2dz6zkok2dzsuka",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-rapid-2022",
                    "https://www.drom.ru/catalog/skoda/rapid/2022/",
                    "https://skoda-favorit.ru/models/rapid",
                    "https://favorit-motors.ru/catalog/new/skoda/new_rapid/komplektacii-i-ceny/",
                    "https://moscow.autovsalone.ru/cars/skoda/rapid/compare",
                    "https://skoda-avtoruss.ru/models/rapid",
                    "https://www.bogemia-skd.ru/models/rapid/price",
                    "https://avtoruss.ru/skoda/novyj-rapid.html",
                    "https://auto-kay.ru/cars/skoda/rapid-new/",
                    "https://skoda-kuntsevo.ru/models/rapid",
                    "https://carso.ru/skoda/rapid"
                ]
            ],
            "skoda rapid комплектации" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid/price",
                    "https://www.drom.ru/catalog/skoda/rapid/",
                    "https://auto.ru/catalog/cars/skoda/rapid/",
                    "https://rolf-skoda.ru/models/rapid",
                    "https://www.skoda-major.ru/rapid/komplektacii-i-tseny/",
                    "https://www.drive.ru/brands/skoda/models/2019/rapid",
                    "https://carsdo.ru/skoda/rapid/",
                    "https://favorit-motors.ru/catalog/new/skoda/new_rapid/komplektacii-i-ceny/",
                    "https://www.autoskd.ru/models/rapid/price",
                    "https://skoda-favorit.ru/models/rapid",
                    "https://skoda-kuntsevo.ru/models/rapid",
                    "https://skoda-avtoruss.ru/models/rapid/price",
                    "https://avtomir.ru/new-cars/skoda/rapid/",
                    "https://avtoruss.ru/skoda/novyj-rapid.html",
                    "https://www.skoda-podolsk.ru/models/rapid/price",
                    "https://www.atlant-motors.ru/models/rapid/price",
                    "https://www.bogemia-skd.ru/models/rapid/price",
                    "https://www.rolf.ru/cars/skoda/novyi_rapid/",
                    "https://www.drive2.ru/b/486191123614662896/",
                    "https://nz-cars.ru/cars/skoda/rapid/"
                ]
            ],
            "комплектация нового шкода рапид" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid/price",
                    "https://www.skoda-avto.ru/models/rapid",
                    "https://carsdo.ru/skoda/rapid/",
                    "https://www.drom.ru/catalog/skoda/rapid/2021/",
                    "https://auto.ru/catalog/cars/skoda/rapid/21738448/21738487/equipment/",
                    "https://rolf-skoda.ru/models/rapid",
                    "https://www.skoda-vitebskiy.ru/models/rapid/price",
                    "https://skoda-tts.ru/models/rapid/price",
                    "https://www.drive.ru/brands/skoda/models/2019/rapid",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-rapid-2022",
                    "https://favorit-motors.ru/catalog/new/skoda/new_rapid/komplektacii-i-ceny/",
                    "https://avtomir.ru/new-cars/skoda/rapid/",
                    "https://naavtotrasse.ru/skoda/skoda-rapid-2022.html",
                    "https://www.skoda-major.ru/rapid/komplektacii-i-tseny/",
                    "https://www.major-auto.ru/models/skoda/rapid_ii/",
                    "https://skoda-auto2.ru/komplektacii-i-ceny-novoj-shkoda-rapid/",
                    "https://skoda-wagner.ru/models/rapid/price",
                    "https://skoda-kanavto.ru/models/rapid/price",
                    "https://gt-news.ru/skoda/rapid-2021/",
                    "https://skoda-favorit.ru/models/rapid"
                ]
            ],
            "škoda rapid 2022 комплектации" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid/price",
                    "https://www.drom.ru/catalog/skoda/rapid/2022/",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-rapid-2022",
                    "https://naavtotrasse.ru/skoda/skoda-rapid-2022.html",
                    "https://auto.ru/moskva/cars/skoda/rapid/2022-year/new/",
                    "https://rolf-skoda.ru/models/rapid",
                    "https://carsdo.ru/skoda/rapid/",
                    "https://favorit-motors.ru/catalog/new/skoda/new_rapid/komplektacii-i-ceny/",
                    "https://www.major-auto.ru/models/skoda/rapid_ii/",
                    "https://cenyavto.com/skoda-rapid-2022/",
                    "https://avtomir.ru/new-cars/skoda/rapid/",
                    "https://skoda-favorit.ru/models/rapid",
                    "https://moscow.autovsalone.ru/cars/skoda/rapid/compare",
                    "https://www.skoda-major.ru/rapid/",
                    "https://autompv.ru/new-auto/48749-skoda-rapid-2021.html",
                    "https://skoda-centr.ru/rapid/complect/",
                    "https://www.allcarz.ru/skoda-rapid-2021/",
                    "https://www.bogemia-skd.ru/models/rapid/price",
                    "https://avtoruss.ru/skoda/novyj-rapid.html",
                    "https://www.skoda-vitebskiy.ru/models/rapid/price"
                ]
            ],
            "skoda rapid fl 2022" => [
                "sites" => [
                    "https://auto.ru/moskva/cars/skoda/rapid/2022-year/all/",
                    "https://www.skoda-avto.ru/models/rapid",
                    "https://www.youtube.com/watch?v=k9zipo6i6w4",
                    "https://www.drom.ru/catalog/skoda/rapid/2022/",
                    "https://vestaz.ru/skoda-rapid-fl-8212-chto-eto-i-chem-otlichaetsya-ot-prezhnego-rapid/",
                    "https://skoda-favorit.ru/models/rapid",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-rapid-2022",
                    "https://rolf-skoda.ru/models/rapid",
                    "https://avtomir.ru/new-cars/skoda/rapid/",
                    "https://naavtotrasse.ru/skoda/skoda-rapid-2022.html",
                    "https://dvizhok.su/auto/test-drajv-obnovlennogo-skoda-rapid-fl",
                    "https://moscow.autovsalone.ru/cars/skoda/rapid/active-start-edition-16_90hp_5mt_2022-modelnyy-god",
                    "https://cenyavto.com/skoda-rapid-2022/",
                    "https://www.major-auto.ru/models/skoda/rapid_ii/",
                    "https://favorit-motors.ru/catalog/new/skoda/new_rapid/",
                    "https://autompv.ru/new-auto/48749-skoda-rapid-2021.html",
                    "https://www.allcarz.ru/skoda-rapid-2021/",
                    "https://avtoruss.ru/skoda/novyj-rapid.html",
                    "https://www.bogemia-skd.ru/models/rapid",
                    "https://skoda-avtoruss.ru/models/rapid"
                ]
            ],
            "skoda rapid" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid",
                    "https://auto.ru/moskva/cars/skoda/rapid/all/",
                    "https://rolf-skoda.ru/models/rapid",
                    "https://skoda.drom.ru/rapid/",
                    "https://www.skoda-major.ru/rapid/",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/rapid-asgbagicaktgtg2emsjitg2urig",
                    "https://www.drive2.ru/cars/skoda/rapid/m194/",
                    "https://skoda-avtoruss.ru/models/rapid",
                    "https://skoda-kuntsevo.ru/models/rapid",
                    "https://skoda-favorit.ru/models/rapid",
                    "https://avtomir.ru/new-cars/skoda/rapid/",
                    "https://www.rolf.ru/cars/skoda/novyi_rapid/",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_rapid_(2012)",
                    "https://favorit-motors.ru/catalog/new/skoda/new_rapid/",
                    "https://www.autoskd.ru/models/rapid",
                    "https://www.drive.ru/brands/skoda/models/2019/rapid",
                    "https://autospot.ru/brands/skoda/rapid_ii/liftback/price/",
                    "https://avtoruss.ru/skoda/novyj-rapid.html",
                    "https://www.zr.ru/cars/skoda/-/skoda-rapid/tests/",
                    "https://www.bogemia-skd.ru/models/rapid"
                ]
            ],
            "skoda rapid купить" => [
                "sites" => [
                    "https://cars.skoda-avto.ru/rapid",
                    "https://auto.ru/moskva/cars/skoda/rapid/all/",
                    "https://www.avito.ru/moskva/avtomobili/skoda/rapid-asgbagicaktgtg2emsjitg2urig",
                    "https://moscow.drom.ru/skoda/rapid/new/",
                    "https://rolf-skoda.ru/models/rapid",
                    "https://www.major-auto.ru/models/skoda/rapid_ii/",
                    "https://avtomir.ru/new-cars/skoda/rapid/",
                    "https://www.rolf.ru/cars/skoda/novyi_rapid/",
                    "https://skoda-favorit.ru/models/rapid",
                    "https://skoda-avtoruss.ru/models/rapid",
                    "https://favorit-motors.ru/catalog/new/skoda/new_rapid/",
                    "https://skoda-kuntsevo.ru/models/rapid",
                    "https://autospot.ru/brands/skoda/rapid_ii/liftback/price/",
                    "https://www.atlant-motors.ru/models/rapid",
                    "https://carso.ru/skoda/rapid",
                    "https://www.autoskd.ru/models/rapid",
                    "https://avtoruss.ru/skoda/novyj-rapid.html",
                    "https://nz-cars.ru/cars/skoda/rapid/",
                    "https://carsdo.ru/skoda/rapid/moscow/",
                    "https://keyauto.ru/cars/new/skoda/rapid/"
                ]
            ],
            "skoda rapid fl" => [
                "sites" => [
                    "https://www.drive2.ru/l/467834502111035846/",
                    "https://vestaz.ru/skoda-rapid-fl-8212-chto-eto-i-chem-otlichaetsya-ot-prezhnego-rapid/",
                    "https://www.skoda-avto.ru/models/rapid",
                    "https://skodasite.ru/cars/rapid/skoda-rapid-fl",
                    "https://auto.ru/moskva/cars/skoda/rapid/21005574/all/",
                    "https://otzovik.com/reviews/avtomobil_skoda_rapid_fl_2017_sedan/",
                    "https://dvizhok.su/auto/test-drajv-obnovlennogo-skoda-rapid-fl",
                    "https://www.drom.ru/info/test-drive/skoda-rapid-28152.html?page=2",
                    "https://www.youtube.com/watch?v=fqtv_bztvok",
                    "https://skoda-favorit.ru/models/rapid",
                    "https://www.kolesa.ru/test-drive/legko-li-byt-molodym-test-drajv-obnovlyonnogo-skoda-rapid",
                    "https://rolf-skoda.ru/models/rapid",
                    "https://www.skoda-major.ru/archive/rapid-fl/",
                    "https://www.drive.ru/test-drive/skoda/591dc84eec05c42b030000a0.html",
                    "https://www.zr.ru/content/articles/924479-skoda-rapid-test/",
                    "https://avtomir.ru/new-cars/skoda/rapid/",
                    "https://www.autoskd.ru/models/rapid",
                    "https://skoda-avtoruss.ru/models/rapid",
                    "https://www.major-auto.ru/models/skoda/rapid_ii/",
                    "https://autospot.ru/brands/skoda/rapid_ii/liftback/"
                ]
            ],
            "шкода рапид фл" => [
                "sites" => [
                    "https://www.drive2.ru/b/478668539935326273/",
                    "https://auto.ru/moskva/cars/skoda/rapid/21005574/all/",
                    "https://vestaz.ru/skoda-rapid-fl-8212-chto-eto-i-chem-otlichaetsya-ot-prezhnego-rapid/",
                    "https://www.skoda-avto.ru/models/rapid",
                    "https://skodasite.ru/cars/rapid/skoda-rapid-fl",
                    "https://otzovik.com/reviews/avtomobil_skoda_rapid_fl_2017_sedan/",
                    "https://dvizhok.su/auto/test-drajv-obnovlennogo-skoda-rapid-fl",
                    "https://www.skoda-major.ru/archive/rapid-fl/",
                    "https://www.youtube.com/watch?v=as0s3_gqj-e",
                    "https://moscow.drom.ru/skoda/rapid/",
                    "https://skoda-favorit.ru/models/rapid",
                    "https://rolf-skoda.ru/models/rapid",
                    "https://www.kolesa.ru/test-drive/legko-li-byt-molodym-test-drajv-obnovlyonnogo-skoda-rapid",
                    "https://avtomir.ru/new-cars/skoda/rapid/",
                    "https://www.drive.ru/test-drive/skoda/591dc84eec05c42b030000a0.html",
                    "https://www.zr.ru/content/articles/924479-skoda-rapid-test/",
                    "https://www.major-auto.ru/models/skoda/rapid_ii/",
                    "https://autospot.ru/brands/skoda/rapid_ii/liftback/price/",
                    "https://skoda-avtoruss.ru/models/rapid",
                    "https://www.autoskd.ru/models/rapid"
                ]
            ],
            "шкода кодиак 2022 комплектации" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq/price",
                    "https://www.drom.ru/catalog/skoda/kodiaq/2022/",
                    "https://www.skoda-major.ru/kodiaq/",
                    "https://rolf-skoda.ru/models/kodiaq",
                    "https://auto.ru/moskva/cars/skoda/kodiaq/2022-year/all/",
                    "https://skoda-avtoruss.ru/models/kodiaq",
                    "https://www.major-auto.ru/models/skoda/kodiaq/",
                    "https://carsdo.ru/skoda/kodiaq/",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-kodiaq-2022",
                    "https://naavtotrasse.ru/skoda/skoda-kodiaq-2022.html",
                    "https://skoda-centr.ru/kodiaq/complect/",
                    "https://gt-news.ru/skoda/skoda-kodiaq-2022/",
                    "https://avtomir.ru/new-cars/skoda/kodiaq/",
                    "https://www.auto-dd.ru/skoda-kodiaq-2022/",
                    "https://moscow.autovsalone.ru/cars/skoda/kodiaq/compare",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/komplektacii-i-ceny/",
                    "https://skoda-kuntsevo.ru/models/kodiaq",
                    "https://cenyavto.com/skoda-kodiaq-2022/",
                    "https://www.ventus.ru/models/kodiaq/price",
                    "https://skoda-favorit.ru/models/kodiaq/price"
                ]
            ],
            "skoda kodiaq цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq/price",
                    "https://auto.ru/moskva/cars/skoda/kodiaq/all/",
                    "https://rolf-skoda.ru/models/kodiaq/price",
                    "https://www.avito.ru/moskva/avtomobili/skoda/kodiaq-asgbagicaktgtg2emsjitg3wqcg",
                    "https://www.skoda-major.ru/kodiaq/",
                    "https://moscow.drom.ru/skoda/kodiaq/",
                    "https://skoda-avtoruss.ru/models/kodiaq",
                    "https://autospot.ru/brands/skoda/kodiaq/suv/price/",
                    "https://skoda-favorit.ru/models/kodiaq/price",
                    "https://www.rolf.ru/cars/skoda/kodiaq/",
                    "https://carsdo.ru/skoda/kodiaq/",
                    "https://skoda-kuntsevo.ru/models/kodiaq",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/",
                    "https://www.atlant-motors.ru/models/kodiaq",
                    "https://avtomir.ru/new-cars/skoda/kodiaq/",
                    "https://www.ventus.ru/models/kodiaq/price",
                    "https://www.bogemia-skd.ru/models/kodiaq",
                    "https://xn----7sbah6aanflhic0bm6c.xn--80adxhks/cars/skoda/kodiaq/",
                    "https://keyauto.ru/cars/new/skoda/kodiaq/",
                    "https://avtoruss.ru/skoda/novyj-skoda-kodiaq.html"
                ]
            ],
            "škoda kodiaq характеристики цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq/price",
                    "https://www.drom.ru/catalog/skoda/kodiaq/",
                    "https://auto.ru/catalog/cars/skoda/kodiaq/",
                    "https://rolf-skoda.ru/models/kodiaq/price",
                    "https://www.skoda-major.ru/kodiaq/",
                    "https://carsdo.ru/skoda/kodiaq/",
                    "https://www.drive.ru/brands/skoda/models/2021/kodiaq",
                    "https://skoda-avtoruss.ru/models/kodiaq",
                    "https://autospot.ru/brands/skoda/kodiaq/suv/price/",
                    "https://www.rolf.ru/cars/skoda/kodiaq/",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/komplektacii-i-ceny/",
                    "https://avtomir.ru/new-cars/skoda/kodiaq/",
                    "https://skoda-kuntsevo.ru/models/kodiaq",
                    "https://www.bogemia-skd.ru/models/kodiaq",
                    "https://avtoruss.ru/skoda/novyj-skoda-kodiaq.html",
                    "https://www.ventus.ru/models/kodiaq/price",
                    "https://www.autoskd.ru/models/kodiaq",
                    "https://center-auto.ru/katalog/skoda/kodiaq/",
                    "https://www.atlant-motors.ru/models/kodiaq",
                    "https://skoda-favorit.ru/models/kodiaq/price"
                ]
            ],
            "купить шкода кодиак" => [
                "sites" => [
                    "https://cars.skoda-avto.ru/kodiaq",
                    "https://auto.ru/moskovskaya_oblast/cars/skoda/kodiaq/all/",
                    "https://www.avito.ru/moskva/avtomobili/skoda/kodiaq-asgbagicaktgtg2emsjitg3wqcg",
                    "https://rolf-skoda.ru/models/kodiaq/price",
                    "https://www.skoda-major.ru/kodiaq/komplektacii-i-tseny/",
                    "https://www.rolf.ru/cars/skoda/kodiaq/",
                    "https://moscow.drom.ru/skoda/kodiaq/",
                    "https://www.bogemia-skd.ru/models/kodiaq",
                    "https://skoda-kuntsevo.ru/models/kodiaq",
                    "https://www.major-auto.ru/models/skoda/kodiaq/",
                    "https://autospot.ru/brands/skoda/kodiaq/suv/price/",
                    "https://favorit-motors.ru/catalog/stock/skoda/kodiaq/",
                    "https://www.autocity-sk.ru/models/kodiaq",
                    "https://www.atlant-motors.ru/models/kodiaq",
                    "https://skoda-avtoruss.ru/models/kodiaq",
                    "https://avtoruss.ru/skoda/novyj-skoda-kodiaq.html",
                    "https://center-auto.ru/katalog/skoda/kodiaq/",
                    "https://adom.ru/skoda/kodiaq",
                    "https://www.autoskd.ru/models/kodiaq",
                    "https://avtomir.ru/new-cars/skoda/kodiaq/"
                ]
            ],
            "купить шкода кодиак у официального дилера" => [
                "sites" => [
                    "https://cars.skoda-avto.ru/kodiaq",
                    "https://rolf-skoda.ru/models/kodiaq",
                    "https://www.skoda-major.ru/kodiaq/",
                    "https://www.rolf.ru/cars/skoda/kodiaq/",
                    "https://skoda-kuntsevo.ru/models/kodiaq",
                    "https://skoda-avtoruss.ru/models/kodiaq",
                    "https://avtomir.ru/new-cars/skoda/kodiaq/",
                    "https://www.atlant-motors.ru/models/kodiaq",
                    "https://www.bogemia-skd.ru/models/kodiaq",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/",
                    "https://www.autoskd.ru/models/kodiaq",
                    "https://avtoruss.ru/skoda/novyj-skoda-kodiaq.html",
                    "https://www.ascgroup.ru/buy_car/new_cars/skoda/kodiaq/",
                    "https://auto.ru/moskva/cars/skoda/kodiaq/new/",
                    "https://adom.ru/skoda/kodiaq",
                    "https://center-auto.ru/katalog/skoda/kodiaq/",
                    "https://autogansa.ru/cars/skoda/kodiaq/",
                    "https://rolf-center.ru/new/skoda/kodiaq/",
                    "https://carsdo.ru/skoda/kodiaq/moscow/",
                    "https://autospot.ru/brands/skoda/kodiaq/suv/price/"
                ]
            ],
            "шкода кодиак" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq",
                    "https://auto.ru/moskva/cars/skoda/kodiaq/all/",
                    "https://www.skoda-major.ru/kodiaq/",
                    "https://skoda.drom.ru/kodiaq/",
                    "https://skoda-avtoruss.ru/models/kodiaq",
                    "https://www.avito.ru/moskva/avtomobili/skoda/kodiaq-asgbagicaktgtg2emsjitg3wqcg",
                    "https://skoda-kodiaq.ru/",
                    "https://www.drive2.ru/cars/skoda/kodiaq/m3036/",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_kodiaq",
                    "https://www.rolf.ru/cars/skoda/kodiaq/",
                    "https://autospot.ru/brands/skoda/kodiaq/suv/price/",
                    "https://skoda-kuntsevo.ru/models/kodiaq",
                    "https://www.bogemia-skd.ru/models/kodiaq",
                    "https://avtomir.ru/new-cars/skoda/kodiaq/",
                    "https://www.drive.ru/brands/skoda/models/2021/kodiaq",
                    "https://www.ixbt.com/car/skoda-kodiaq-review.html",
                    "https://www.atlant-motors.ru/models/kodiaq",
                    "https://www.autoskd.ru/models/kodiaq",
                    "https://carsdo.ru/skoda/kodiaq/",
                    "https://www.skoda-auto.com/models/range/kodiaq"
                ]
            ],
            "шкода кодиак 2022 комплектации и цены" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq/price",
                    "https://auto.ru/moskva/cars/skoda/kodiaq/2022-year/all/",
                    "https://carsdo.ru/skoda/kodiaq/",
                    "https://www.skoda-major.ru/kodiaq/",
                    "https://www.drom.ru/catalog/skoda/kodiaq/2022/",
                    "https://rolf-skoda.ru/models/kodiaq/price",
                    "https://www.major-auto.ru/models/skoda/kodiaq/",
                    "https://skoda-avtoruss.ru/models/kodiaq",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-kodiaq-2022",
                    "https://naavtotrasse.ru/skoda/skoda-kodiaq-2022.html",
                    "https://www.atlant-motors.ru/models/kodiaq",
                    "https://avtomir.ru/new-cars/skoda/kodiaq/",
                    "https://skoda-kuntsevo.ru/models/kodiaq",
                    "https://skoda-favorit.ru/models/kodiaq/price",
                    "https://skoda-centr.ru/kodiaq/complect/",
                    "https://www.ventus.ru/models/kodiaq/price",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/komplektacii-i-ceny/",
                    "https://www.rolf.ru/cars/new/skoda/kodiaq-new/",
                    "https://autospot.ru/brands/skoda/kodiaq_i/suv/price/",
                    "https://roadres.com/skoda/kodiaq/price/"
                ]
            ],
            "шкода кодиак комплектации" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq/price",
                    "https://rolf-skoda.ru/models/kodiaq/price",
                    "https://www.drom.ru/catalog/skoda/kodiaq/",
                    "https://auto.ru/catalog/cars/skoda/kodiaq/",
                    "https://carsdo.ru/skoda/kodiaq/",
                    "https://www.drive.ru/brands/skoda/models/2021/kodiaq",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/komplektacii-i-ceny/",
                    "https://www.skoda-major.ru/kodiaq/komplektacii-i-tseny/",
                    "https://skoda-ap.ru/models/kodiaq/price",
                    "https://skoda-avtoruss.ru/models/kodiaq",
                    "https://skoda-kuntsevo.ru/models/kodiaq",
                    "https://skoda-favorit.ru/models/kodiaq/price",
                    "https://www.atlant-motors.ru/models/kodiaq",
                    "https://avtomir.ru/new-cars/skoda/kodiaq/",
                    "https://sigma-skoda.ru/models/kodiaq/price",
                    "https://autospot.ru/brands/skoda/kodiaq/suv/price/",
                    "https://moscow.autovsalone.ru/cars/skoda/kodiaq/compare",
                    "https://www.rolf.ru/cars/skoda/kodiaq/",
                    "https://www.bogemia-skd.ru/models/kodiaq",
                    "https://autoreview.ru/news/obnovlennyy-krossover-skoda-kodiaq-vse-komplektacii-i-ceny"
                ]
            ],
            "шкода кодиак комплектации и цены" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq/price",
                    "https://rolf-skoda.ru/models/kodiaq/price",
                    "https://auto.ru/moskva/cars/skoda/kodiaq/new/",
                    "https://www.skoda-major.ru/kodiaq/",
                    "https://carsdo.ru/skoda/kodiaq/",
                    "https://skoda-favorit.ru/models/kodiaq/price",
                    "https://autospot.ru/brands/skoda/kodiaq/suv/price/",
                    "https://www.drom.ru/catalog/skoda/kodiaq/",
                    "https://skoda-avtoruss.ru/models/kodiaq",
                    "https://www.atlant-motors.ru/models/kodiaq",
                    "https://avtomir.ru/new-cars/skoda/kodiaq/",
                    "https://roadres.com/skoda/kodiaq/price/",
                    "https://www.ventus.ru/models/kodiaq/price",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/komplektacii-i-ceny/",
                    "https://skoda-kuntsevo.ru/models/kodiaq",
                    "https://www.rolf.ru/cars/new/skoda/kodiaq-new/",
                    "https://www.drive.ru/brands/skoda/models/2021/kodiaq",
                    "https://www.bogemia-skd.ru/models/kodiaq",
                    "https://legion-motors.ru/models/kodiaq/price",
                    "https://www.autoskd.ru/models/kodiaq"
                ]
            ],
            "шкода кодиак официальный дилер" => [
                "sites" => [
                    "https://rolf-skoda.ru/models/kodiaq",
                    "https://cars.skoda-avto.ru/kodiaq",
                    "https://skoda-kuntsevo.ru/models/kodiaq",
                    "https://www.skoda-major.ru/kodiaq/",
                    "https://www.rolf.ru/cars/skoda/kodiaq/",
                    "https://www.bogemia-skd.ru/models/kodiaq",
                    "https://www.major-auto.ru/models/skoda/kodiaq/",
                    "https://avtomir.ru/new-cars/skoda/kodiaq/",
                    "https://www.atlant-motors.ru/models/kodiaq",
                    "https://skoda-avtoruss.ru/models/kodiaq",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/",
                    "https://www.autocity-sk.ru/models/kodiaq",
                    "https://www.ascgroup.ru/buy_car/new_cars/skoda/kodiaq/",
                    "https://kuntsevo.com/skoda/kodiaq/price/",
                    "https://avtoruss.ru/skoda/novyj-skoda-kodiaq.html",
                    "https://www.autoskd.ru/models/kodiaq",
                    "https://adom.ru/skoda/kodiaq",
                    "https://rolf-center.ru/new/skoda/kodiaq/",
                    "https://autogansa.ru/cars/skoda/kodiaq/",
                    "https://center-auto.ru/katalog/skoda/kodiaq/"
                ]
            ],
            "шкода кодиак цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq/price",
                    "https://auto.ru/moskva/cars/skoda/kodiaq/all/",
                    "https://rolf-skoda.ru/models/kodiaq/price",
                    "https://www.skoda-major.ru/kodiaq/",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/kodiaq-asgbagicaktgtg2emsjitg3wqcg",
                    "https://moscow.drom.ru/skoda/kodiaq/",
                    "https://carsdo.ru/skoda/kodiaq/",
                    "https://www.rolf.ru/cars/new/skoda/kodiaq-new/",
                    "https://autospot.ru/brands/skoda/kodiaq/suv/price/",
                    "https://skoda-avtoruss.ru/models/kodiaq",
                    "https://avtomir.ru/new-cars/skoda/kodiaq/",
                    "https://skoda-favorit.ru/models/kodiaq/price",
                    "https://www.atlant-motors.ru/models/kodiaq",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/",
                    "https://skoda-kuntsevo.ru/models/kodiaq",
                    "https://www.bogemia-skd.ru/models/kodiaq",
                    "https://www.ventus.ru/models/kodiaq/price",
                    "https://autogansa.ru/cars/skoda/kodiaq/",
                    "https://keyauto.ru/cars/new/skoda/kodiaq/",
                    "https://avtoruss.ru/skoda/novyj-skoda-kodiaq.html"
                ]
            ],
            "шкода кодиак цена официальный дилер" => [
                "sites" => [
                    "https://cars.skoda-avto.ru/kodiaq",
                    "https://rolf-skoda.ru/models/kodiaq",
                    "https://www.skoda-major.ru/kodiaq/",
                    "https://skoda-kuntsevo.ru/models/kodiaq",
                    "https://www.major-auto.ru/models/skoda/kodiaq/",
                    "https://skoda-avtoruss.ru/models/kodiaq",
                    "https://avtomir.ru/new-cars/skoda/kodiaq/",
                    "https://www.rolf.ru/cars/skoda/kodiaq/",
                    "https://www.atlant-motors.ru/models/kodiaq",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/",
                    "https://www.bogemia-skd.ru/models/kodiaq",
                    "https://www.autocity-sk.ru/models/kodiaq",
                    "https://www.ascgroup.ru/buy_car/new_cars/skoda/kodiaq/",
                    "https://kuntsevo.com/skoda/kodiaq/price/",
                    "https://avtoruss.ru/skoda/novyj-skoda-kodiaq.html",
                    "https://auto.ru/moskva/cars/skoda/kodiaq/new/",
                    "https://www.autoskd.ru/models/kodiaq",
                    "https://adom.ru/skoda/kodiaq",
                    "https://carsdo.ru/skoda/kodiaq/moscow/",
                    "https://center-auto.ru/katalog/skoda/kodiaq/"
                ]
            ],
            "skoda kodiaq 2022" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq",
                    "https://auto.ru/moskva/cars/skoda/kodiaq/2022-year/all/",
                    "https://www.zr.ru/content/articles/930969-novyj-skoda-kodiaq-bez-avtoma/",
                    "https://rolf-skoda.ru/models/kodiaq",
                    "https://www.skoda-major.ru/kodiaq/",
                    "https://www.drom.ru/catalog/skoda/kodiaq/2022/",
                    "https://www.youtube.com/watch?v=3i2gvdfxswg",
                    "https://skoda-avtoruss.ru/models/kodiaq",
                    "https://mobile-review.com/all/reviews/auto/test-skoda-kodiaq-2022-minimum-izmenenij/",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-kodiaq-2022",
                    "https://gt-news.ru/skoda/skoda-kodiaq-2022/",
                    "https://www.major-auto.ru/models/skoda/kodiaq/",
                    "https://naavtotrasse.ru/skoda/skoda-kodiaq-2022.html",
                    "https://www.auto-dd.ru/skoda-kodiaq-2022/",
                    "https://www.allcarz.ru/skoda-kodiaq-2022/",
                    "https://avtomir.ru/new-cars/skoda/kodiaq/",
                    "https://www.bogemia-skd.ru/models/kodiaq",
                    "https://skoda-kodiaq.ru/",
                    "https://www.autoskd.ru/models/kodiaq",
                    "https://skoda-kuntsevo.ru/models/kodiaq"
                ]
            ],
            "skoda kodiaq 2022 комплектации и цены" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq/price",
                    "https://auto.ru/moskva/cars/skoda/kodiaq/2022-year/all/",
                    "https://rolf-skoda.ru/models/kodiaq/price",
                    "https://www.drom.ru/catalog/skoda/kodiaq/2022/",
                    "https://www.skoda-major.ru/kodiaq/",
                    "https://www.major-auto.ru/models/skoda/kodiaq/",
                    "https://skoda-avtoruss.ru/models/kodiaq",
                    "https://carsdo.ru/skoda/kodiaq/",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-kodiaq-2022",
                    "https://avtomir.ru/new-cars/skoda/kodiaq/",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/",
                    "https://www.atlant-motors.ru/models/kodiaq",
                    "https://skoda-favorit.ru/models/kodiaq/price",
                    "https://www.ventus.ru/models/kodiaq/price",
                    "https://moscow.autovsalone.ru/cars/skoda/kodiaq/compare",
                    "https://skoda-centr.ru/kodiaq/complect/",
                    "https://www.rolf.ru/cars/skoda/kodiaq/",
                    "https://autospot.ru/brands/skoda/kodiaq/suv/price/",
                    "https://roadres.com/skoda/kodiaq/price/",
                    "https://skoda-kuntsevo.ru/models/kodiaq"
                ]
            ],
            "skoda kodiaq комплектации" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq/price",
                    "https://www.drom.ru/catalog/skoda/kodiaq/",
                    "https://rolf-skoda.ru/models/kodiaq/price",
                    "https://auto.ru/catalog/cars/skoda/kodiaq/",
                    "https://www.drive.ru/brands/skoda/models/2021/kodiaq",
                    "https://www.skoda-major.ru/kodiaq/komplektacii-i-tseny/",
                    "https://skoda-ap.ru/models/kodiaq/price",
                    "https://carsdo.ru/skoda/kodiaq/",
                    "https://skoda-avtoruss.ru/models/kodiaq",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/komplektacii-i-ceny/",
                    "https://www.major-auto.ru/models/skoda/kodiaq/",
                    "https://autospot.ru/brands/skoda/kodiaq/suv/price/",
                    "https://skoda-favorit.ru/models/kodiaq/price",
                    "https://autoreview.ru/news/obnovlennyy-krossover-skoda-kodiaq-vse-komplektacii-i-ceny",
                    "https://skoda-kuntsevo.ru/models/kodiaq",
                    "https://www.rolf.ru/cars/skoda/kodiaq/",
                    "https://skoda-kodiaq.ru/price.html",
                    "https://moscow.autovsalone.ru/cars/skoda/kodiaq/compare",
                    "https://avtomir.ru/new-cars/skoda/kodiaq/",
                    "https://www.bogemia-skd.ru/models/kodiaq"
                ]
            ],
            "skoda kodiaq комплектации и цены" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq/price",
                    "https://rolf-skoda.ru/models/kodiaq/price",
                    "https://auto.ru/moskva/cars/skoda/kodiaq/new/",
                    "https://www.skoda-major.ru/kodiaq/",
                    "https://carsdo.ru/skoda/kodiaq/",
                    "https://skoda-ap.ru/models/kodiaq/price",
                    "https://skoda-favorit.ru/models/kodiaq/price",
                    "https://skoda-avtoruss.ru/models/kodiaq",
                    "https://www.drom.ru/catalog/skoda/kodiaq/",
                    "https://autospot.ru/brands/skoda/kodiaq/suv/price/",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/komplektacii-i-ceny/",
                    "https://www.atlant-motors.ru/models/kodiaq",
                    "https://www.rolf.ru/cars/skoda/kodiaq/",
                    "https://skoda-kuntsevo.ru/models/kodiaq",
                    "https://avtomir.ru/new-cars/skoda/kodiaq/",
                    "https://www.drive.ru/brands/skoda/models/2021/kodiaq",
                    "https://roadres.com/skoda/kodiaq/price/",
                    "https://www.bogemia-skd.ru/models/kodiaq",
                    "https://moscow.autovsalone.ru/cars/skoda/kodiaq",
                    "https://www.avito.ru/moskva/avtomobili/novyy/skoda/kodiaq-asgbagica0sgfmbmaec2dz6zkok2ddaoka"
                ]
            ],
            "skoda kodiaq купить" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq/price",
                    "https://auto.ru/moskva/cars/skoda/kodiaq/all/",
                    "https://rolf-skoda.ru/models/kodiaq/price",
                    "https://www.avito.ru/moskva/avtomobili/skoda/kodiaq-asgbagicaktgtg2emsjitg3wqcg",
                    "https://www.skoda-major.ru/kodiaq/",
                    "https://moscow.drom.ru/skoda/kodiaq/",
                    "https://autospot.ru/brands/skoda/kodiaq/suv/price/",
                    "https://www.rolf.ru/cars/skoda/kodiaq/",
                    "https://www.bogemia-skd.ru/models/kodiaq",
                    "https://skoda-avtoruss.ru/models/kodiaq",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/",
                    "https://www.atlant-motors.ru/models/kodiaq",
                    "https://avtomir.ru/new-cars/skoda/kodiaq/",
                    "https://skoda-kuntsevo.ru/models/kodiaq",
                    "https://skoda-favorit.ru/models/kodiaq/price",
                    "https://www.ventus.ru/models/kodiaq/price",
                    "https://xn----7sbah6aanflhic0bm6c.xn--80adxhks/cars/skoda/kodiaq/",
                    "https://keyauto.ru/cars/new/skoda/kodiaq/",
                    "https://www.autoskd.ru/models/kodiaq",
                    "https://center-auto.ru/katalog/skoda/kodiaq/"
                ]
            ],
            "skoda kodiaq" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq",
                    "https://auto.ru/moskva/cars/skoda/kodiaq/all/",
                    "https://rolf-skoda.ru/models/kodiaq/price",
                    "https://www.skoda-auto.com/models/range/kodiaq",
                    "https://skoda-kodiaq.ru/",
                    "https://www.drive2.ru/cars/skoda/kodiaq/g5261/",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_kodiaq",
                    "https://skoda.drom.ru/kodiaq/",
                    "https://www.skoda-major.ru/kodiaq/",
                    "https://www.major-auto.ru/models/skoda/kodiaq/",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/kodiaq-asgbagicaktgtg2emsjitg3wqcg",
                    "https://www.ixbt.com/car/skoda-kodiaq-review.html",
                    "https://skoda-avtoruss.ru/models/kodiaq",
                    "https://www.bogemia-skd.ru/models/kodiaq",
                    "https://www.drive.ru/brands/skoda/models/2021/kodiaq",
                    "https://autospot.ru/brands/skoda/kodiaq/suv/price/",
                    "https://skoda-kuntsevo.ru/models/kodiaq",
                    "https://www.rolf.ru/cars/skoda/kodiaq/",
                    "https://www.atlant-motors.ru/models/kodiaq",
                    "https://www.autoskd.ru/models/kodiaq"
                ]
            ],
            "skoda superb комплектации" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb/price",
                    "https://www.drom.ru/catalog/skoda/superb/",
                    "https://rolf-skoda.ru/models/superb",
                    "https://auto.ru/catalog/cars/skoda/superb/",
                    "https://interkar.ru/models/superb/price",
                    "https://skoda.medved-vostok.ru/models/superb/price",
                    "https://carsdo.ru/skoda/superb/",
                    "https://www.drive.ru/brands/skoda/models/2019/superb",
                    "https://skoda-kuntsevo.ru/models/superb",
                    "https://skoda-wagner.ru/models/superb/price",
                    "https://skoda-favorit.ru/models/superb/price",
                    "https://www.skoda-major.ru/superb/",
                    "https://favorit-motors.ru/catalog/new/skoda/superb/",
                    "https://www.bogemia-skd.ru/models/superb/price",
                    "https://skoda-avtoruss.ru/models/superb",
                    "https://carso.ru/skoda/superb",
                    "https://moscow.autovsalone.ru/cars/skoda/superb/compare",
                    "https://avtoruss.ru/skoda/obnovlennyj-superb.html",
                    "https://aksa-auto.ru/catalog/skoda/superb",
                    "https://autospot.ru/brands/skoda/superb/liftback/price/"
                ]
            ],
            "skoda superb комплектации и цены" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb/price",
                    "https://rolf-skoda.ru/models/superb",
                    "https://auto.ru/moskva/cars/skoda/superb/new/",
                    "https://www.drom.ru/catalog/skoda/superb/",
                    "https://carsdo.ru/skoda/superb/",
                    "https://skoda-favorit.ru/models/superb/price",
                    "https://skoda-autopraga.ru/models/superb/price",
                    "https://favorit-motors.ru/catalog/new/skoda/superb/",
                    "https://www.skoda-major.ru/superb/",
                    "https://skoda-kuntsevo.ru/models/superb",
                    "https://interkar.ru/models/superb/price",
                    "https://skoda-avtoruss.ru/models/superb/price",
                    "https://skoda-wagner.ru/models/superb/price",
                    "https://skoda.medved-vostok.ru/models/superb/price",
                    "https://www.bogemia-skd.ru/models/superb/price",
                    "https://carso.ru/skoda/superb",
                    "https://autospot.ru/brands/skoda/superb/liftback/price/",
                    "https://www.major-auto.ru/models/skoda/superb_obnovlenniy/",
                    "https://www.rolf.ru/cars/new/skoda/superb-sedan/",
                    "https://adom.ru/skoda/new-superb"
                ]
            ],
            "skoda superb цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb/price",
                    "https://rolf-skoda.ru/models/superb",
                    "https://auto.ru/moskva/cars/skoda/superb/all/",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/superb-asgbagicaktgtg2emsjitg3assg",
                    "https://moscow.drom.ru/skoda/superb/",
                    "https://skoda-kuntsevo.ru/models/superb",
                    "https://skoda-favorit.ru/models/superb",
                    "https://www.skoda-major.ru/superb/",
                    "https://favorit-motors.ru/catalog/new/skoda/superb/",
                    "https://www.rolf.ru/cars/new/skoda/superb-sedan/",
                    "https://autospot.ru/brands/skoda/superb/liftback/price/",
                    "https://skoda-avtoruss.ru/models/superb",
                    "https://carsdo.ru/skoda/superb/",
                    "https://www.bogemia-skd.ru/models/superb/price",
                    "https://carso.ru/skoda/superb",
                    "https://www.atlant-motors.ru/models/superb",
                    "https://www.autoskd.ru/models/superb",
                    "https://avtomir.ru/new-cars/skoda/superb/",
                    "https://aksa-auto.ru/catalog/skoda/superb",
                    "https://nezavisimost.su/cars/skoda/superb/"
                ]
            ],
            "купить шкода суперб" => [
                "sites" => [
                    "https://auto.ru/moskva/cars/skoda/superb/used/",
                    "https://cars.skoda-avto.ru/superb",
                    "https://rolf-skoda.ru/models/superb",
                    "https://www.avito.ru/moskva/avtomobili/skoda/superb-asgbagicaktgtg2emsjitg3assg",
                    "https://moscow.drom.ru/skoda/superb/",
                    "https://skoda-favorit.ru/models/superb",
                    "https://skoda-avtoruss.ru/models/superb",
                    "https://skoda-kuntsevo.ru/models/superb",
                    "https://www.rolf.ru/cars/new/skoda/superb-sedan/",
                    "https://www.skoda-major.ru/superb/",
                    "https://autospot.ru/brands/skoda/superb/liftback/price/",
                    "https://favorit-motors.ru/catalog/new/skoda/superb/",
                    "https://www.atlant-motors.ru/models/superb",
                    "https://aksa-auto.ru/catalog/skoda/superb",
                    "https://carso.ru/skoda/superb",
                    "https://www.bogemia-skd.ru/models/superb/price",
                    "https://moskva.mbib.ru/skoda/superb/used",
                    "https://www.autoskd.ru/models/superb",
                    "https://avtomir.ru/new-cars/skoda/superb/",
                    "https://cars.avtocod.ru/moskva/avto-s-probegom/skoda/superb/"
                ]
            ],
            "новая шкода суперб цена комплектация" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb/price",
                    "https://rolf-skoda.ru/models/superb",
                    "https://auto.ru/moskva/cars/skoda/superb/new/",
                    "https://skoda-favorit.ru/models/superb/price",
                    "https://carsdo.ru/skoda/superb/",
                    "https://skoda-kuntsevo.ru/models/superb",
                    "https://favorit-motors.ru/catalog/new/skoda/superb/",
                    "https://moscow.drom.ru/skoda/superb/new/",
                    "https://adom.ru/skoda/new-superb-combi",
                    "https://www.skoda-major.ru/superb/",
                    "https://www.atlant-motors.ru/models/superb/price",
                    "https://skoda-avtoruss.ru/models/superb",
                    "https://www.bogemia-skd.ru/models/superb/price",
                    "https://www.rolf.ru/cars/new/skoda/superb-sedan/",
                    "https://autospot.ru/brands/skoda/superb/liftback/price/",
                    "https://m.avito.ru/moskva/avtomobili/novyy/skoda/superb-asgbagica0sgfmbmaec2dz6zkok2dccxka",
                    "https://www.major-auto.ru/models/skoda/superb_obnovlenniy/",
                    "https://carso.ru/skoda/superb",
                    "https://skoda-wagner.ru/models/superb/price",
                    "https://skoda.medved-vostok.ru/models/superb/price"
                ]
            ],
            "шкода суперб комплектации" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb/price",
                    "https://www.drom.ru/catalog/skoda/superb/",
                    "https://rolf-skoda.ru/models/superb",
                    "https://interkar.ru/models/superb/price",
                    "https://auto.ru/catalog/cars/skoda/superb/",
                    "https://www.drive.ru/brands/skoda/models/2019/superb",
                    "https://carsdo.ru/skoda/superb/",
                    "https://skoda-wagner.ru/models/superb/price",
                    "https://skoda-kuntsevo.ru/models/superb",
                    "https://skoda-favorit.ru/models/superb/price",
                    "https://www.skoda-major.ru/superb/",
                    "https://favorit-motors.ru/catalog/new/skoda/superb/",
                    "https://www.bogemia-skd.ru/models/superb/price",
                    "https://krona-auto.ru/models/superb/price",
                    "https://skoda-avtoruss.ru/models/superb/price",
                    "https://carso.ru/skoda/superb",
                    "https://aksa-auto.ru/catalog/skoda/superb",
                    "https://www.major-auto.ru/models/skoda/superb_obnovlenniy/",
                    "https://moscow.autovsalone.ru/cars/skoda/superb/compare",
                    "https://autospot.ru/brands/skoda/superb/liftback/price/"
                ]
            ],
            "шкода суперб комплектации и цены" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb/price",
                    "https://rolf-skoda.ru/models/superb",
                    "https://auto.ru/moskva/cars/skoda/superb/new/",
                    "https://skoda-autopraga.ru/models/superb/price",
                    "https://www.drom.ru/catalog/skoda/superb/",
                    "https://carsdo.ru/skoda/superb/",
                    "https://skoda-favorit.ru/models/superb/price",
                    "https://skoda-kuntsevo.ru/models/superb",
                    "https://www.skoda-major.ru/superb/",
                    "https://skoda-avtoruss.ru/models/superb/price",
                    "https://skoda-ap.ru/models/superb/price",
                    "https://www.bogemia-skd.ru/models/superb/price",
                    "https://aksa-auto.ru/catalog/skoda/superb",
                    "https://m.avito.ru/moskva/avtomobili/novyy/skoda/superb-asgbagica0sgfmbmaec2dz6zkok2dccxka",
                    "https://www.drive.ru/brands/skoda/models/2019/superb",
                    "https://autospot.ru/brands/skoda/superb/liftback/price/",
                    "https://carso.ru/skoda/superb",
                    "https://moscow.autovsalone.ru/cars/skoda/superb",
                    "https://skoda-autoug.ru/models/superb/price",
                    "https://adom.ru/skoda/superb"
                ]
            ],
            "шкода суперб новая цена" => [
                "sites" => [
                    "https://rolf-skoda.ru/models/superb",
                    "https://www.skoda-avto.ru/models/superb/price",
                    "https://auto.ru/moskovskaya_oblast/cars/skoda/superb/new/",
                    "https://skoda-kuntsevo.ru/models/superb",
                    "https://adom.ru/skoda/new-superb-combi",
                    "https://skoda-favorit.ru/models/superb",
                    "https://carsdo.ru/skoda/superb/",
                    "https://favorit-motors.ru/catalog/new/skoda/superb/",
                    "https://www.rolf.ru/cars/new/skoda/superb-sedan/",
                    "https://moscow.drom.ru/skoda/superb/new/",
                    "https://m.avito.ru/moskva/avtomobili/novyy/skoda/superb-asgbagica0sgfmbmaec2dz6zkok2dccxka",
                    "https://www.skoda-major.ru/superb/",
                    "https://skoda-avtoruss.ru/models/superb",
                    "https://autospot.ru/brands/skoda/superb/liftback/price/",
                    "https://interkar.ru/models/superb/price",
                    "https://skoda-autopraga.ru/models/superb",
                    "https://www.bogemia-skd.ru/models/superb/price",
                    "https://www.major-auto.ru/models/skoda/superb_obnovlenniy/",
                    "https://carso.ru/skoda/superb",
                    "https://aksa-auto.ru/catalog/skoda/superb"
                ]
            ],
            "шкода суперб цена" => [
                "sites" => [
                    "https://rolf-skoda.ru/models/superb",
                    "https://www.skoda-avto.ru/models/superb/price",
                    "https://auto.ru/moskva/cars/skoda/superb/used/",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/superb-asgbagicaktgtg2emsjitg3assg",
                    "https://moscow.drom.ru/skoda/superb/",
                    "https://skoda-kuntsevo.ru/models/superb",
                    "https://www.rolf.ru/cars/new/skoda/superb-sedan/",
                    "https://www.skoda-major.ru/superb/",
                    "https://carsdo.ru/skoda/superb/",
                    "https://skoda-avtoruss.ru/models/superb",
                    "https://favorit-motors.ru/catalog/new/skoda/superb/",
                    "https://autospot.ru/brands/skoda/superb/liftback/price/",
                    "https://interkar.ru/models/superb/price",
                    "https://www.bogemia-skd.ru/models/superb/price",
                    "https://www.autoskd.ru/models/superb",
                    "https://carso.ru/skoda/superb",
                    "https://aksa-auto.ru/catalog/skoda/superb",
                    "https://www.atlant-motors.ru/models/superb",
                    "https://avtomir.ru/new-cars/skoda/superb/",
                    "https://nz-cars.ru/cars/skoda/superb/"
                ]
            ],
            "skoda superb" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb",
                    "https://rolf-skoda.ru/models/superb",
                    "https://auto.ru/moskva/cars/skoda/superb/all/",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_superb",
                    "https://moscow.drom.ru/skoda/superb/",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/superb-asgbagicaktgtg2emsjitg3assg",
                    "https://skoda-kuntsevo.ru/models/superb",
                    "https://www.skoda-auto.com/models/range/superb",
                    "https://www.skoda-major.ru/superb/",
                    "https://www.rolf.ru/cars/new/skoda/superb-sedan/",
                    "https://skoda-favorit.ru/models/superb",
                    "https://www.drive2.ru/cars/skoda/superb/m215/",
                    "https://www.drive.ru/brands/skoda/models/2019/superb",
                    "https://autospot.ru/brands/skoda/superb/liftback/price/",
                    "https://skoda-avtoruss.ru/models/superb",
                    "https://www.ventus.ru/models/superb",
                    "https://www.autoskd.ru/models/superb",
                    "https://favorit-motors.ru/catalog/new/skoda/superb/",
                    "https://en.wikipedia.org/wiki/%c5%a0koda_superb",
                    "https://www.zr.ru/cars/skoda/-/skoda-superb/tests/"
                ]
            ],
            "шкода суперб" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb",
                    "https://rolf-skoda.ru/models/superb",
                    "https://auto.ru/moskva/cars/skoda/superb/all/",
                    "https://moscow.drom.ru/skoda/superb/",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_superb",
                    "https://skoda-kuntsevo.ru/models/superb",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/superb-asgbagicaktgtg2emsjitg3assg",
                    "https://skoda-favorit.ru/models/superb",
                    "https://www.drive2.ru/cars/skoda/superb/m215/",
                    "https://www.rolf.ru/cars/new/skoda/superb-sedan/",
                    "https://www.skoda-major.ru/superb/",
                    "https://skoda-avtoruss.ru/models/superb",
                    "https://www.drive.ru/brands/skoda/models/2019/superb",
                    "https://autospot.ru/brands/skoda/superb/liftback/price/",
                    "https://www.autoskd.ru/models/superb",
                    "https://www.skoda-auto.com/models/range/superb",
                    "https://favorit-motors.ru/catalog/new/skoda/superb/",
                    "https://translate.yandex.ru/translate?lang=en-ru&url=https%3a%2f%2fen.wikipedia.org%2fwiki%2f%25c5%25a0koda_superb&view=c",
                    "https://skoda-wagner.ru/models/superb",
                    "https://carsdo.ru/skoda/superb/"
                ]
            ],
            "škoda superb 2022" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb",
                    "https://auto.ru/moskva/cars/skoda/superb/2022-year/all/",
                    "https://rolf-skoda.ru/models/superb",
                    "https://naavtotrasse.ru/skoda/skoda-superb-2022.html",
                    "https://www.youtube.com/watch?v=m8gzed5ufgi",
                    "https://gt-news.ru/skoda/superb-2022/",
                    "https://www.drom.ru/catalog/skoda/superb/2022/",
                    "https://skoda-favorit.ru/models/superb",
                    "https://cenyavto.com/skoda-superb-2022/",
                    "https://www.drive.ru/test-drive/skoda/5d2f1970ec05c4ff4a000138.html",
                    "https://skoda-avtoruss.ru/models/superb",
                    "https://skoda-kuntsevo.ru/models/superb",
                    "https://www.auto-dd.ru/skoda-superb-2020/",
                    "https://www.autoskd.ru/models/superb",
                    "https://www.skoda-major.ru/superb/",
                    "https://favorit-motors.ru/catalog/new/skoda/superb/",
                    "https://www.skoda-auto.com/models/range/superb",
                    "https://www.allcarz.ru/skoda-superb-2020/",
                    "https://skoda-wagner.ru/models/superb",
                    "https://carsdo.ru/skoda/superb/"
                ]
            ],
            "обновленный шкода суперб 2022" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb",
                    "https://gt-news.ru/skoda/superb-2022/",
                    "https://naavtotrasse.ru/skoda/skoda-superb-2022.html",
                    "https://www.youtube.com/watch?v=_uk8nsygo6y",
                    "https://cenyavto.com/skoda-superb-2022/",
                    "https://www.auto-dd.ru/skoda-superb-2020/",
                    "https://www.ixbt.com/news/2022/09/22/jeto-novaja-skoda-superb-opublikovany-kachestvennye-izobrazhenija-avtomobilja.html",
                    "https://auto.ru/cars/skoda/superb/2022-year/all/",
                    "https://www.allcarz.ru/skoda-superb-2020/",
                    "https://vk.com/superb.club",
                    "https://www.drom.ru/catalog/skoda/superb/2022/",
                    "https://dzen.ru/media/autoblogcar/novaia-shkoda-superb-2022-s-nadejnym-dvigatelem-czpb-20-tsi-kotorogo-net-u-kia-k5-615bc857797d4c00c8420cef",
                    "https://autoreview.ru/news/obnovlennaya-skoda-superb-v-rossii-moschnye-motory-i-polnyy-privod",
                    "https://skoda-wagner.ru/models/superb",
                    "https://autbar.ru/skodasuperb.html",
                    "https://autompv.ru/new-auto/41452-skoda-superb-iv-2020.html",
                    "https://www.drive2.ru/e/b3heqeaaa3a",
                    "https://www.zr.ru/content/news/917804-skoda-predstavila-obnovlennyj/",
                    "https://radar-holding.ru/models/superb",
                    "https://carsdo.ru/skoda/superb/"
                ]
            ],
            "обновленная skoda superb" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb",
                    "https://rolf-skoda.ru/models/superb",
                    "https://www.drom.ru/info/test-drive/skoda-superb-70054.html",
                    "https://www.drive.ru/test-drive/skoda/5d2f1970ec05c4ff4a000138.html",
                    "https://www.drive2.ru/b/546990818095793200/",
                    "https://autoreview.ru/news/obnovlennaya-skoda-superb-v-rossii-moschnye-motory-i-polnyy-privod",
                    "https://auto.ru/moskva/cars/skoda/superb/2022-year/new/",
                    "https://gt-news.ru/skoda/superb-2022/",
                    "https://skoda-favorit.ru/models/superb",
                    "https://www.zr.ru/content/articles/918717-skoda-super-2019/",
                    "https://skoda-kuntsevo.ru/models/superb",
                    "https://www.autoskd.ru/models/superb",
                    "https://www.major-auto.ru/models/skoda/superb_obnovlenniy/",
                    "https://skoda-avtoruss.ru/models/superb",
                    "https://ru.motor1.com/reviews/434271/skoda-superb-style/",
                    "https://naavtotrasse.ru/skoda/skoda-superb-2021.html",
                    "https://avtomir.ru/new-cars/skoda/superb/",
                    "https://www.ventus.ru/models/superb",
                    "https://www.auto-dd.ru/skoda-superb-2020/",
                    "https://www.allcarz.ru/skoda-superb-2020/"
                ]
            ],
            "обновленная шкода суперб" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb",
                    "https://rolf-skoda.ru/models/superb",
                    "https://gt-news.ru/skoda/superb-2022/",
                    "https://auto.ru/moskva/cars/skoda/superb/2022-year/new/",
                    "https://www.zr.ru/content/news/917804-skoda-predstavila-obnovlennyj/",
                    "https://www.drive2.ru/b/546990818095793200/",
                    "https://www.drive.ru/test-drive/skoda/5d2f1970ec05c4ff4a000138.html",
                    "https://autoreview.ru/news/obnovlennaya-skoda-superb-v-rossii-moschnye-motory-i-polnyy-privod",
                    "https://skoda-favorit.ru/models/superb",
                    "https://www.autoskd.ru/models/superb",
                    "https://skoda-kuntsevo.ru/models/superb",
                    "https://www.drom.ru/info/test-drive/skoda-superb-70054.html",
                    "https://www.auto-dd.ru/skoda-superb-2020/",
                    "https://www.ventus.ru/models/superb",
                    "https://naavtotrasse.ru/skoda/skoda-superb-2021.html",
                    "https://skoda-avtoruss.ru/models/superb",
                    "https://radar-holding.ru/models/superb",
                    "https://www.major-auto.ru/models/skoda/superb_obnovlenniy/",
                    "https://skoda-wagner.ru/models/superb",
                    "https://www.atlant-motors.ru/models/superb"
                ]
            ],
            "автомобиль skoda superb" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb",
                    "https://rolf-skoda.ru/models/superb",
                    "https://auto.ru/moskva/cars/skoda/superb/all/",
                    "https://skoda.drom.ru/superb/",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/superb-asgbagicaktgtg2emsjitg3assg",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_superb",
                    "https://skoda-kuntsevo.ru/models/superb",
                    "https://skoda-favorit.ru/models/superb",
                    "https://www.skoda-major.ru/superb/",
                    "https://skoda-s-auto.ru/models/superb",
                    "https://www.rolf.ru/cars/new/skoda/superb-sedan/",
                    "https://favorit-motors.ru/catalog/new/skoda/superb/",
                    "https://skoda-avtoruss.ru/models/superb",
                    "https://www.autoskd.ru/models/superb",
                    "https://autospot.ru/brands/skoda/superb/liftback/price/",
                    "https://www.drive2.ru/cars/skoda/superb/m215/",
                    "https://www.atlant-motors.ru/models/superb",
                    "https://www.drive.ru/test-drive/skoda/5d2f1970ec05c4ff4a000138.html",
                    "https://www.ventus.ru/models/superb",
                    "https://avtomir.ru/new-cars/skoda/superb/"
                ]
            ],
            "skoda superb купить" => [
                "sites" => [
                    "https://cars.skoda-avto.ru/superb",
                    "https://auto.ru/moskva/cars/skoda/superb/all/",
                    "https://rolf-skoda.ru/models/superb",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/superb-asgbagicaktgtg2emsjitg3assg",
                    "https://moscow.drom.ru/skoda/superb/",
                    "https://skoda-kuntsevo.ru/models/superb",
                    "https://skoda-favorit.ru/models/superb",
                    "https://www.skoda-major.ru/superb/",
                    "https://www.rolf.ru/cars/new/skoda/superb-sedan/",
                    "https://autospot.ru/brands/skoda/superb/liftback/price/",
                    "https://skoda-avtoruss.ru/models/superb",
                    "https://favorit-motors.ru/catalog/new/skoda/superb/",
                    "https://www.atlant-motors.ru/models/superb",
                    "https://carso.ru/skoda/superb",
                    "https://avtomir.ru/new-cars/skoda/superb/",
                    "https://www.bogemia-skd.ru/models/superb/price",
                    "https://www.autoskd.ru/models/superb",
                    "https://moscow.110km.ru/prodazha/skoda/superb/poderzhannie/",
                    "https://nz-cars.ru/cars/skoda/superb/",
                    "https://moskva.mbib.ru/skoda/superb/used"
                ]
            ],
            "шкода суперб 2022" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb",
                    "https://auto.ru/moskva/cars/skoda/superb/2022-year/all/",
                    "https://rolf-skoda.ru/models/superb",
                    "https://naavtotrasse.ru/skoda/skoda-superb-2022.html",
                    "https://www.youtube.com/watch?v=m8gzed5ufgi",
                    "https://www.drom.ru/catalog/skoda/superb/2022/",
                    "https://skoda-favorit.ru/models/superb",
                    "https://gt-news.ru/skoda/superb-2022/",
                    "https://www.skoda-major.ru/superb/",
                    "https://skoda-avtoruss.ru/models/superb",
                    "https://cenyavto.com/skoda-superb-2022/",
                    "https://www.auto-dd.ru/skoda-superb-2020/",
                    "https://carsdo.ru/skoda/superb/",
                    "https://skoda-kuntsevo.ru/models/superb",
                    "https://favorit-motors.ru/catalog/new/skoda/superb/",
                    "https://www.autoskd.ru/models/superb",
                    "https://radar-holding.ru/models/superb",
                    "https://avtomir.ru/new-cars/skoda/superb/",
                    "https://www.allcarz.ru/skoda-superb-2020/",
                    "https://avtoruss.ru/skoda/obnovlennyj-superb.html"
                ]
            ],
            "skoda octavia combi" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia-combi-2017",
                    "https://auto.ru/moskva/cars/skoda/octavia/all/body-wagon/",
                    "https://www.drive2.ru/cars/skoda/octavia_combi/m2474/",
                    "https://www.skoda-major.ru/octavia-combi/",
                    "https://skoda-kuntsevo.ru/archival-models/octavia-combi-old",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/octavia/universal-asgbaqicaktgtg2emsjitg2ercgbqoa2drtmtyg",
                    "https://adom.ru/skoda/octavia-combi",
                    "https://moscow.drom.ru/skoda/octavia/wagon/",
                    "https://www.drive.ru/brands/skoda/models/2017/octavia_combi",
                    "https://carso.ru/skoda/octavia-combi",
                    "https://www.auto-dd.ru/skoda-octavia-combi/",
                    "https://www.zr.ru/content/articles/627115-skoda-octavia-combi-krupnoe-chiterstvo/",
                    "https://www.kolesa.ru/test-drive/skoda-octavia-combi-4h4-laurinklement-shlifovalnaja-mashina-2015-09-17",
                    "https://carsdo.ru/skoda/octavia-universal/",
                    "https://www.skoda-portal.ru/shkoda-oktaviya-kombi-universal-2021-praktichnyj-i-kachestvennyj-semejnyj-avtomobil/",
                    "https://carsclick.ru/skoda/obzor-avtomobilej/oktavija-kombi-2020/",
                    "https://auto-leon.ru/skoda/octavia-combi/",
                    "https://ru.motor1.com/reviews/408819/skoda-octavia-combi-2020-test/",
                    "https://www.bips.ru/skoda/octavia-combi",
                    "https://legend-auto.ru/skoda/octavia-combi/"
                ]
            ],
            "skoda octavia combi цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/showroom/octavia-combi-price",
                    "https://auto.ru/moskva/cars/skoda/octavia/all/body-wagon/",
                    "https://www.skoda-major.ru/octavia-combi/",
                    "https://adom.ru/skoda/octavia-combi",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/octavia/universal-asgbaqicaktgtg2emsjitg2ercgbqoa2drtmtyg",
                    "https://moscow.drom.ru/skoda/octavia/wagon/",
                    "https://carso.ru/skoda/octavia-combi",
                    "https://carsdo.ru/skoda/octavia-universal/",
                    "https://www.bips.ru/skoda/octavia-combi",
                    "https://skoda-kuntsevo.ru/archival-models/octavia-combi-old",
                    "https://auto-leon.ru/skoda/octavia-combi/",
                    "https://riaauto.ru/skoda/octavia-combi",
                    "https://www.incom-auto.ru/auto/skoda/octavia-combi/",
                    "https://xn---102-43dbmbri9azaxlng3ae3adf3f.xn--p1ai/cars/skoda/octavia-combi/",
                    "https://legend-auto.ru/skoda/octavia-combi/",
                    "https://abc-auto.ru/skoda/octavia-combi/",
                    "https://carsdb.ru/skoda/octavia-universal/",
                    "https://avanta-avto-credit.ru/cars/skoda/octavia-combi/",
                    "https://rosgosavto.ru/cars/skoda/octavia-combi/",
                    "https://www.auto-mgn.ru/catalog/skoda/octavia/wagon/price/"
                ]
            ],
            "комплектации шкоды октавии комби" => [
                "sites" => [
                    "https://www.skoda-avto.ru/showroom/octavia-combi-price",
                    "https://www.skoda-major.ru/octavia-combi/komplektacii-i-tseny/",
                    "https://www.skoda-portal.ru/shkoda-oktaviya-kombi-universal-2021-praktichnyj-i-kachestvennyj-semejnyj-avtomobil/",
                    "https://carso.ru/skoda/octavia-combi",
                    "https://www.bips.ru/skoda/octavia-combi",
                    "https://carsdo.ru/skoda/octavia-universal/",
                    "https://adom.ru/skoda/octavia-combi",
                    "https://www.drom.ru/catalog/skoda/octavia/g_2012_2125/",
                    "https://www.drive.ru/brands/skoda/models/2017/octavia_combi",
                    "https://legend-auto.ru/skoda/octavia-combi/",
                    "https://carsdb.ru/skoda/octavia-universal/",
                    "https://auto.ru/catalog/cars/skoda/octavia/4560887/4560896/equipment/",
                    "http://www.octavia-avto.ru/a7-combi-price",
                    "https://www.auto-dd.ru/skoda-octavia-combi/",
                    "https://carsclick.ru/skoda/obzor-avtomobilej/oktavija-kombi-2020/",
                    "https://auto-leon.ru/skoda/octavia-combi/",
                    "https://riaavto.ru/skoda/octavia-combi/komplektacii",
                    "https://skoda-kuntsevo.ru/archival-models/octavia-combi-old",
                    "https://fastmb.ru/testdrive/1782-skoda-octavia-combi-2017-dolgozhdannoe-obnovlenie-universalnogo-cheha.html",
                    "https://auto-kay.ru/cars/skoda/octavia-combi/"
                ]
            ],
            "купить шкоду октавию комби" => [
                "sites" => [
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/octavia/universal-asgbaqicaktgtg2emsjitg2ercgbqoa2drtmtyg",
                    "https://auto.ru/moskva/cars/skoda/octavia/all/body-wagon/",
                    "https://adom.ru/skoda/octavia-combi",
                    "https://www.skoda-avto.ru/showroom/octavia-combi-price",
                    "https://moscow.drom.ru/skoda/octavia/wagon/",
                    "https://www.skoda-major.ru/octavia-combi/",
                    "https://carso.ru/skoda/octavia-combi",
                    "https://skoda-kuntsevo.ru/archival-models/octavia-combi-old",
                    "https://www.bips.ru/skoda/octavia-combi",
                    "https://www.incom-auto.ru/auto/skoda/octavia-combi/",
                    "https://moskva.mbib.ru/skoda/octavia/universal/used",
                    "https://auto-leon.ru/skoda/octavia-combi/",
                    "https://riaauto.ru/skoda/octavia-combi",
                    "https://xn---102-43dbmbri9azaxlng3ae3adf3f.xn--p1ai/cars/skoda/octavia-combi/",
                    "https://center-auto.ru/katalog/skoda/octavia-combi",
                    "https://skoda-avtoruss.ru/archival-models/octavia-combi-2017",
                    "https://avanta-avto-credit.ru/cars/skoda/octavia-combi/",
                    "https://legend-auto.ru/skoda/octavia-combi/",
                    "https://rosgosavto.ru/cars/skoda/octavia-combi/",
                    "https://autodmir.ru/msk/offers/skoda/octavia/universal/"
                ]
            ],
            "шкода октавиа комби" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia-combi-2017",
                    "https://www.skoda-avto.ru/models/octavia-combi-old",
                    "https://auto.ru/moskva/cars/skoda/octavia/all/body-wagon/",
                    "https://www.drive2.ru/cars/skoda/octavia_combi/m2474/",
                    "https://www.skoda-major.ru/octavia-combi/",
                    "https://adom.ru/skoda/octavia-combi",
                    "https://skoda.drom.ru/octavia_wagon/",
                    "https://moscow.drom.ru/skoda/octavia/wagon/",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/octavia/universal-asgbaqicaktgtg2emsjitg2ercgbqoa2drtmtyg",
                    "https://carso.ru/skoda/octavia-combi",
                    "https://skoda-kuntsevo.ru/archival-models/octavia-combi-old",
                    "https://carsclick.ru/skoda/obzor-avtomobilej/oktavija-kombi-2020/",
                    "https://www.drive.ru/test-drive/skoda/51b09f2894a656c0490000c9.html",
                    "https://www.auto-dd.ru/skoda-octavia-combi/",
                    "https://carsdo.ru/skoda/octavia-universal/",
                    "https://www.bips.ru/skoda/octavia-combi",
                    "https://legend-auto.ru/skoda/octavia-combi/",
                    "https://www.skoda-portal.ru/shkoda-oktaviya-kombi-universal-2021-praktichnyj-i-kachestvennyj-semejnyj-avtomobil/",
                    "https://auto-leon.ru/skoda/octavia-combi/",
                    "https://www.zr.ru/content/articles/627115-skoda-octavia-combi-krupnoe-chiterstvo/"
                ]
            ],
            "шкода октавия комби" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia-combi-2017",
                    "https://auto.ru/moskva/cars/skoda/octavia/all/body-wagon/",
                    "https://www.drive2.ru/cars/skoda/octavia_combi/m2474/",
                    "https://www.skoda-major.ru/octavia-combi/",
                    "https://moscow.drom.ru/skoda/octavia/wagon/",
                    "https://adom.ru/skoda/octavia-combi",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/octavia/universal-asgbaqicaktgtg2emsjitg2ercgbqoa2drtmtyg",
                    "https://skoda-kuntsevo.ru/archival-models/octavia-combi-old",
                    "https://www.auto-dd.ru/skoda-octavia-combi/",
                    "https://carso.ru/skoda/octavia-combi",
                    "https://www.drive.ru/brands/skoda/models/2017/octavia_combi",
                    "https://www.skoda-portal.ru/shkoda-oktaviya-kombi-universal-2021-praktichnyj-i-kachestvennyj-semejnyj-avtomobil/",
                    "https://carsdo.ru/skoda/octavia-universal/",
                    "https://carsclick.ru/skoda/obzor-avtomobilej/oktavija-kombi-2020/",
                    "https://www.bips.ru/skoda/octavia-combi",
                    "https://www.kolesa.ru/test-drive/skoda-octavia-combi-4h4-laurinklement-shlifovalnaja-mashina-2015-09-17",
                    "https://legend-auto.ru/skoda/octavia-combi/",
                    "https://center-auto.ru/katalog/skoda/octavia-combi",
                    "https://www.zr.ru/content/articles/627115-skoda-octavia-combi-krupnoe-chiterstvo/",
                    "https://motor.ru/testdrives/skodaoctcombi.htm"
                ]
            ],
            "шкода октавия комби 2022" => [
                "sites" => [
                    "https://www.skoda-avto.ru/showroom/octavia-combi-price",
                    "https://www.skoda-major.ru/octavia-combi/",
                    "https://adom.ru/skoda/octavia-combi",
                    "https://auto.ru/moskva/cars/skoda/octavia/2022-year/all/",
                    "https://carsdb.ru/skoda/octavia-universal/",
                    "https://carsdo.ru/skoda/octavia-universal/photo/",
                    "https://carso.ru/skoda/octavia-combi",
                    "https://unitedavto.ru/cars/skoda/octavia-combi/",
                    "https://auto.ironhorse.ru/skoda-octavia-combi-3_3469.html",
                    "https://www.bips.ru/skoda/octavia-combi",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-octavia-2022",
                    "https://www.allcarz.ru/skoda-octavia-4-scout/",
                    "https://skoda-yeti.ru/skoda-octavia-combi-rs-2022-tsena-novaya-komplektatsii-i-tehnicheskie-harakteristiki/",
                    "https://auto-kay.ru/cars/skoda/octavia-combi/",
                    "https://www.drom.ru/catalog/skoda/octavia/2022/",
                    "https://naavtotrasse.ru/skoda/skoda-octavia-2022.html",
                    "https://legautotrans.ru/new-auto/modelnyj-ryad-skoda/skoda-octavia-combi/",
                    "https://avanta-avto-credit.ru/cars/skoda/octavia-combi/",
                    "https://auto-leon.ru/skoda/octavia-combi/",
                    "https://modus-auto.ru/skoda/octavia-combi/"
                ]
            ],
            "шкода октавия комби комплектации и цены" => [
                "sites" => [
                    "https://www.skoda-avto.ru/showroom/octavia-combi-price",
                    "https://www.skoda-major.ru/octavia-combi/",
                    "https://adom.ru/skoda/octavia-combi",
                    "https://carsdo.ru/skoda/octavia-universal/",
                    "https://carso.ru/skoda/octavia-combi",
                    "https://www.bips.ru/skoda/octavia-combi",
                    "https://auto.ru/cars/skoda/octavia/all/body-wagon/",
                    "https://auto-leon.ru/skoda/octavia-combi/",
                    "https://carsdb.ru/skoda/octavia-universal/",
                    "https://center-auto.ru/katalog/skoda/octavia-combi",
                    "https://skoda-kuntsevo.ru/archival-models/octavia-combi-old",
                    "https://unitedavto.ru/cars/skoda/octavia-combi/",
                    "https://riaauto.ru/skoda/octavia-combi",
                    "https://auto-kay.ru/cars/skoda/octavia-combi/",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/octavia/universal-asgbaqicaktgtg2emsjitg2ercgbqoa2drtmtyg",
                    "https://avanta-avto-credit.ru/cars/skoda/octavia-combi/",
                    "https://abc-auto.ru/skoda/octavia-combi/",
                    "https://rosgosavto.ru/cars/skoda/octavia-combi/",
                    "https://www.skoda-portal.ru/shkoda-oktaviya-kombi-universal-2021-praktichnyj-i-kachestvennyj-semejnyj-avtomobil/",
                    "https://www.moonauto.ru/skoda/skoda-octavia-combi/"
                ]
            ],
            "шкода октавия комби купить" => [
                "sites" => [
                    "https://auto.ru/moskva/cars/skoda/octavia/all/body-wagon/",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/octavia/universal-asgbaqicaktgtg2emsjitg2ercgbqoa2drtmtyg",
                    "https://adom.ru/skoda/octavia-combi",
                    "https://moscow.drom.ru/skoda/octavia/wagon/",
                    "https://www.skoda-avto.ru/showroom/octavia-combi-price",
                    "https://www.skoda-major.ru/octavia-combi/",
                    "https://carso.ru/skoda/octavia-combi",
                    "https://www.bips.ru/skoda/octavia-combi",
                    "https://skoda-kuntsevo.ru/archival-models/octavia-combi-old",
                    "https://moskva.mbib.ru/skoda/octavia/universal/used",
                    "https://riaauto.ru/skoda/octavia-combi",
                    "https://auto-leon.ru/skoda/octavia-combi/",
                    "https://skoda-avtoruss.ru/archival-models/octavia-combi-2017",
                    "https://www.incom-auto.ru/auto/skoda/octavia-combi/",
                    "https://xn---102-43dbmbri9azaxlng3ae3adf3f.xn--p1ai/cars/skoda/octavia-combi/",
                    "https://avanta-avto-credit.ru/cars/skoda/octavia-combi/",
                    "https://center-auto.ru/katalog/skoda/octavia-combi",
                    "https://rosgosavto.ru/cars/skoda/octavia-combi/",
                    "https://carsdo.ru/skoda/octavia-universal/",
                    "https://legend-auto.ru/skoda/octavia-combi/"
                ]
            ],
            "шкода октавия комби цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/showroom/octavia-combi-price",
                    "https://adom.ru/skoda/octavia-combi",
                    "https://auto.ru/moskva/cars/skoda/octavia/all/body-wagon/",
                    "https://www.skoda-major.ru/octavia-combi/",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/octavia/universal-asgbaqicaktgtg2emsjitg2ercgbqoa2drtmtyg",
                    "https://carso.ru/skoda/octavia-combi",
                    "https://moscow.drom.ru/skoda/octavia/wagon/",
                    "https://carsdo.ru/skoda/octavia-universal/",
                    "https://www.bips.ru/skoda/octavia-combi",
                    "https://skoda-kuntsevo.ru/archival-models/octavia-combi-old",
                    "https://riaauto.ru/skoda/octavia-combi",
                    "https://www.incom-auto.ru/auto/skoda/octavia-combi/",
                    "https://auto-leon.ru/skoda/octavia-combi/",
                    "https://center-auto.ru/katalog/skoda/octavia-combi",
                    "https://auto-kay.ru/cars/skoda/octavia-combi/",
                    "https://avanta-avto-credit.ru/cars/skoda/octavia-combi/",
                    "https://carsdb.ru/skoda/octavia-universal/",
                    "https://xn---102-43dbmbri9azaxlng3ae3adf3f.xn--p1ai/cars/skoda/octavia-combi/",
                    "https://www.auto-mgn.ru/catalog/skoda/octavia/wagon/price/",
                    "https://abc-auto.ru/skoda/octavia-combi/"
                ]
            ],
            "skoda octavia combi в наличии" => [
                "sites" => [
                    "https://www.skoda-major.ru/octavia-combi/",
                    "https://adom.ru/skoda/octavia-combi",
                    "https://auto.ru/moskva/cars/skoda/octavia/all/body-wagon/",
                    "https://carso.ru/skoda/octavia-combi",
                    "https://www.skoda-avto.ru/models/octavia-combi-2017",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/octavia/universal-asgbaqicaktgtg2emsjitg2ercgbqoa2drtmtyg",
                    "https://skoda-kuntsevo.ru/archival-models/octavia-combi-old",
                    "https://riaauto.ru/skoda/octavia-combi",
                    "https://www.bips.ru/skoda/octavia-combi",
                    "https://www.incom-auto.ru/auto/skoda/octavia-combi/",
                    "https://xn---102-43dbmbri9azaxlng3ae3adf3f.xn--p1ai/cars/skoda/octavia-combi/",
                    "https://unitedavto.ru/cars/skoda/octavia-combi/",
                    "https://auto-nrg.com/auto/skoda/octavia-combi",
                    "https://center-auto.ru/katalog/skoda/octavia-combi",
                    "https://rosgosavto.ru/cars/skoda/octavia-combi/",
                    "https://abc-auto.ru/skoda/octavia-combi/",
                    "https://moscow.drom.ru/skoda/octavia/wagon/",
                    "https://avtoruss.ru/skoda/octavia/octavia-combi.html",
                    "https://auto-kay.ru/cars/skoda/octavia-combi/",
                    "https://auto-racurs.ru/skoda/octavia-combi/"
                ]
            ],
            "skoda octavia combi купить" => [
                "sites" => [
                    "https://auto.ru/moskva/cars/skoda/octavia/all/body-wagon/",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/octavia/universal-asgbaqicaktgtg2emsjitg2ercgbqoa2drtmtyg",
                    "https://www.skoda-avto.ru/showroom/octavia-combi-price",
                    "https://adom.ru/skoda/octavia-combi",
                    "https://www.skoda-major.ru/octavia-combi/",
                    "https://moscow.drom.ru/skoda/octavia/wagon/",
                    "https://carso.ru/skoda/octavia-combi",
                    "https://skoda-kuntsevo.ru/archival-models/octavia-combi-old",
                    "https://www.bips.ru/skoda/octavia-combi",
                    "https://mos-dealer.ru/skoda/octavia-combi/",
                    "https://moskva.mbib.ru/skoda/octavia/universal/used",
                    "https://center-auto.ru/katalog/skoda/octavia-combi",
                    "https://xn---102-43dbmbri9azaxlng3ae3adf3f.xn--p1ai/cars/skoda/octavia-combi/",
                    "https://auto-leon.ru/skoda/octavia-combi/",
                    "https://riaauto.ru/skoda/octavia-combi",
                    "https://www.incom-auto.ru/auto/skoda/octavia-combi/",
                    "https://unitedavto.ru/cars/skoda/octavia-combi/",
                    "https://avanta-avto-credit.ru/cars/skoda/octavia-combi/",
                    "https://abc-auto.ru/skoda/octavia-combi/",
                    "https://legend-auto.ru/skoda/octavia-combi/"
                ]
            ],
            "skoda octavia combi официальный дилер" => [
                "sites" => [
                    "https://www.skoda-major.ru/octavia-combi/",
                    "https://adom.ru/skoda/octavia-combi",
                    "https://skoda-kuntsevo.ru/archival-models/octavia-combi-old",
                    "https://www.skoda-avto.ru/showroom/octavia-combi-price",
                    "https://rolf-skoda.ru/models/octavia",
                    "https://mos-dealer.ru/skoda/octavia-combi/",
                    "https://skoda-avtoruss.ru/archival-models/octavia-combi-2017",
                    "https://www.bips.ru/skoda/octavia-combi",
                    "https://center-auto.ru/katalog/skoda/octavia-combi",
                    "https://carso.ru/skoda/octavia-combi",
                    "https://rosgosavto.ru/cars/skoda/octavia-combi/",
                    "https://www.allcarz.ru/moscow/skoda-octavia-combi/",
                    "https://auto-kay.ru/cars/skoda/octavia-combi/",
                    "https://unitedavto.ru/cars/skoda/octavia-combi/",
                    "https://skoda-favorit.ru/models/octavia",
                    "https://www.rolf.ru/cars/new/skoda/octavia-new/",
                    "https://xn---102-43dbmbri9azaxlng3ae3adf3f.xn--p1ai/cars/skoda/octavia-combi/",
                    "https://auto-leon.ru/skoda/octavia-combi/",
                    "https://akvilon-avto.ru/skoda/octavia-combi/",
                    "https://auto.ru/moskva/dilery/cars/skoda/new/"
                ]
            ],
            "škoda octavia combi 2022" => [
                "sites" => [
                    "https://www.skoda-avto.ru/showroom/octavia-combi-price",
                    "https://www.skoda-major.ru/octavia-combi/",
                    "https://adom.ru/skoda/octavia-combi",
                    "https://auto.ru/moskva/cars/skoda/octavia/2022-year/all/",
                    "https://www.youtube.com/watch?v=ep1ezeqqx6y",
                    "https://carsdb.ru/skoda/octavia-universal/",
                    "https://carsdo.ru/skoda/octavia-universal/photo/",
                    "https://carso.ru/skoda/octavia-combi",
                    "https://avanta-avto-credit.ru/cars/skoda/octavia-combi/",
                    "https://www.drom.ru/catalog/skoda/octavia/2022/",
                    "https://www.bips.ru/skoda/octavia-combi",
                    "https://skoda-yeti.ru/skoda-octavia-combi-rs-2022-tsena-novaya-komplektatsii-i-tehnicheskie-harakteristiki/",
                    "https://auto.ironhorse.ru/skoda-octavia-combi-3_3469.html",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-octavia-2022",
                    "https://auto-kay.ru/cars/skoda/octavia-combi/",
                    "https://naavtotrasse.ru/skoda/skoda-octavia-2022.html",
                    "https://legautotrans.ru/new-auto/modelnyj-ryad-skoda/skoda-octavia-combi/",
                    "https://modus-auto.ru/skoda/octavia-combi/",
                    "https://www.moonauto.ru/skoda/skoda-octavia-combi/moscow/",
                    "https://auto-leon.ru/skoda/octavia-combi/"
                ]
            ],
            "новая шкода октавия комби 2022" => [
                "sites" => [
                    "https://www.skoda-major.ru/octavia-combi/",
                    "https://www.skoda-avto.ru/showroom/octavia-combi-price",
                    "https://adom.ru/skoda/octavia-combi",
                    "https://carsdb.ru/skoda/octavia-universal/",
                    "https://auto.ru/moskva/cars/skoda/octavia/2022-year/all/",
                    "https://carsdo.ru/skoda/octavia-universal/photo/",
                    "https://carso.ru/skoda/octavia-combi",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-octavia-2022",
                    "https://naavtotrasse.ru/skoda/skoda-octavia-2022.html",
                    "https://www.bips.ru/skoda/octavia-combi",
                    "https://avto-kay.ru/new-auto/skoda/octavia-combi/",
                    "https://skoda-yeti.ru/skoda-octavia-combi-rs-2022-tsena-novaya-komplektatsii-i-tehnicheskie-harakteristiki/",
                    "https://legautotrans.ru/new-auto/modelnyj-ryad-skoda/skoda-octavia-combi/",
                    "https://cenyavto.com/skoda-octavia-2022/",
                    "https://auto-kay.ru/cars/skoda/octavia-combi/",
                    "https://auto.ironhorse.ru/skoda-octavia-combi-3_3469.html",
                    "https://www.allcarz.ru/skoda-octavia-4-scout/",
                    "https://avanta-avto-credit.ru/cars/skoda/octavia-combi/",
                    "https://www.drom.ru/catalog/skoda/octavia/2022/",
                    "https://skoda-avtoruss.ru/models/superb-combi"
                ]
            ],
            "skoda запчасти купить" => [
                "sites" => [
                    "https://parts.skoda-avto.ru/",
                    "https://am-parts.ru/katalog/skoda",
                    "https://www.partarium.ru/search/zapchasti-skoda/",
                    "https://baza.drom.ru/moskva/sell_spare_parts/model/skoda/",
                    "https://www.zzap.ru/public/manufact.aspx?class_man=skoda",
                    "https://m.avito.ru/moskva/zapchasti_i_aksessuary?query=%d0%b7%d0%b0%d0%bf%d1%87%d0%b0%d1%81%d1%82%d0%b8%20%d1%88%d0%ba%d0%be%d0%b4%d0%b0",
                    "https://www.autodoc.ru/catalogs/original/skoda",
                    "https://www.autocompas.ru/zapchasti/skoda/",
                    "https://exist.ru/catalog/global/cars/skoda",
                    "https://www.major-auto.ru/zapchasti/skoda/",
                    "https://euroauto.ru/auto/cars/skoda/",
                    "https://edg-parts.ru/zapchasti-skoda",
                    "https://rem.ru/catalog/parts/skoda/",
                    "http://skodada.ru/",
                    "https://autonomia.ru/skoda",
                    "https://auto3n.ru/parts-catalog/skoda",
                    "https://market.yandex.ru/search?text=%d0%b7%d0%b0%d0%bf%d1%87%d0%b0%d1%81%d1%82%d0%b8%20%d0%bd%d0%b0%20%d1%88%d0%ba%d0%be%d0%b4%d1%83",
                    "https://avtograde.ru/zapchasti-skoda-cat119/",
                    "https://varaosa.ru/catalog/skoda.html",
                    "https://www.auto2.ru/tcd/skoda/"
                ]
            ],
            "запчасти skoda цены" => [
                "sites" => [
                    "https://parts.skoda-avto.ru/",
                    "https://am-parts.ru/katalog/skoda",
                    "https://www.partarium.ru/search/zapchasti-skoda/",
                    "https://www.zzap.ru/public/manufact.aspx?class_man=skoda",
                    "https://baza.drom.ru/moskva/sell_spare_parts/model/skoda/",
                    "https://www.autocompas.ru/zapchasti/skoda/",
                    "https://exist.ru/catalog/global/cars/skoda",
                    "https://m.avito.ru/moskva/zapchasti_i_aksessuary?query=%d0%b7%d0%b0%d0%bf%d1%87%d0%b0%d1%81%d1%82%d0%b8%20%d1%88%d0%ba%d0%be%d0%b4%d0%b0",
                    "https://euroauto.ru/auto/cars/skoda/",
                    "https://www.autodoc.ru/catalogs/original/skoda",
                    "https://www.major-auto.ru/zapchasti/skoda/",
                    "https://varaosa.ru/catalog/skoda.html",
                    "https://avtograde.ru/zapchasti-skoda-cat119/",
                    "https://auto3n.ru/parts-catalog/skoda",
                    "https://market.yandex.ru/search?text=%d0%b7%d0%b0%d0%bf%d1%87%d0%b0%d1%81%d1%82%d0%b8%20%d0%bd%d0%b0%20skoda%20%d0%be%d0%ba%d1%82%d0%b0%d0%b2%d0%b8%d1%8f%20%d1%86%d0%b5%d0%bd%d0%b0",
                    "https://edg-parts.ru/zapchasti-skoda",
                    "https://rem.ru/catalog/parts/skoda/",
                    "https://autonomia.ru/skoda",
                    "https://www.auto2.ru/tcd/skoda/",
                    "http://skodada.ru/"
                ]
            ],
            "каталог деталей шкода" => [
                "sites" => [
                    "https://skoda.catalogs-parts.com/",
                    "https://www.ilcats.ru/skoda/?function=getmodels&market=cz",
                    "https://parts.skoda-avto.ru/",
                    "https://exist.ru/catalog/global/cars/skoda",
                    "https://www.zzap.ru/public/manufact.aspx?class_man=skoda",
                    "https://am-parts.ru/katalog/skoda",
                    "https://www.partarium.ru/search/zapchasti-skoda/",
                    "https://www.autodoc.ru/catalogs/original/skoda",
                    "https://www.reformauto.ru/etka/skoda/",
                    "https://emex.ru/catalogs/original/?screen=model&brandid=sk926&brandname=skoda&catalog=sk926",
                    "https://www.avtoall.ru/catalog_to/skoda/",
                    "https://www.port3.ru/catalog/skoda/",
                    "https://www.autocompas.ru/zapchasti/skoda/",
                    "https://webautocats.com/ru/etka/skoda/",
                    "https://edg-parts.ru/zapchasti-skoda",
                    "https://rem.ru/catalog/parts/skoda/",
                    "https://auto3n.ru/parts-catalog/skoda",
                    "https://ixora-auto.ru/catalog-parts/skoda",
                    "https://www.auto2.ru/tcd/skoda/",
                    "https://apex.ru/catalog/zapchasti/skoda"
                ]
            ],
            "купить детали шкода" => [
                "sites" => [
                    "https://parts.skoda-avto.ru/",
                    "https://am-parts.ru/katalog/skoda",
                    "https://baza.drom.ru/moskva/sell_spare_parts/model/skoda/",
                    "https://www.partarium.ru/search/zapchasti-skoda/",
                    "https://www.zzap.ru/public/manufact.aspx?class_man=skoda",
                    "https://m.avito.ru/moskva/zapchasti_i_aksessuary?query=%d0%b7%d0%b0%d0%bf%d1%87%d0%b0%d1%81%d1%82%d0%b8%20%d1%88%d0%ba%d0%be%d0%b4%d0%b0",
                    "https://www.autodoc.ru/catalogs/original/skoda",
                    "https://exist.ru/catalog/global/cars/skoda",
                    "https://www.autocompas.ru/zapchasti/skoda/",
                    "https://euroauto.ru/auto/cars/skoda/",
                    "https://edg-parts.ru/zapchasti-skoda",
                    "https://www.major-auto.ru/zapchasti/skoda/",
                    "https://autonomia.ru/skoda",
                    "https://rem.ru/catalog/parts/skoda/",
                    "https://market.yandex.ru/search?text=%d0%b7%d0%b0%d0%bf%d1%87%d0%b0%d1%81%d1%82%d0%b8%20%d0%bd%d0%b0%20%d1%88%d0%ba%d0%be%d0%b4%d1%83",
                    "https://skoda-favorit.ru/service/original-accessories",
                    "https://auto3n.ru/parts-catalog/skoda",
                    "http://skodada.ru/",
                    "https://avtograde.ru/zapchasti-skoda-cat119/",
                    "https://www.auto2.ru/tcd/skoda/"
                ]
            ],
            "купить запчасти на шкоду" => [
                "sites" => [
                    "https://parts.skoda-avto.ru/",
                    "https://am-parts.ru/katalog/skoda",
                    "https://m.avito.ru/moskva/zapchasti_i_aksessuary?query=%d0%b7%d0%b0%d0%bf%d1%87%d0%b0%d1%81%d1%82%d0%b8%20%d1%88%d0%ba%d0%be%d0%b4%d0%b0",
                    "https://baza.drom.ru/moskva/sell_spare_parts/model/skoda/",
                    "https://www.partarium.ru/search/zapchasti-skoda/",
                    "https://www.zzap.ru/public/manufact.aspx?class_man=skoda",
                    "https://exist.ru/catalog/global/cars/skoda",
                    "https://www.autocompas.ru/zapchasti/skoda/",
                    "https://www.autodoc.ru/catalogs/original/skoda",
                    "https://edg-parts.ru/zapchasti-skoda",
                    "https://www.major-auto.ru/zapchasti/skoda/",
                    "https://autonomia.ru/skoda",
                    "https://rem.ru/catalog/parts/skoda/",
                    "https://www.z-skd.ru/",
                    "https://market.yandex.ru/search?text=%d0%b7%d0%b0%d0%bf%d1%87%d0%b0%d1%81%d1%82%d0%b8%20%d0%bd%d0%b0%20%d1%88%d0%ba%d0%be%d0%b4%d1%83",
                    "https://auto3n.ru/parts-catalog/skoda",
                    "https://skoda-favorit.ru/service/original-accessories",
                    "https://2gis.ru/moscow/search/%d0%9c%d0%b0%d0%b3%d0%b0%d0%b7%d0%b8%d0%bd%20%d0%b0%d0%b2%d1%82%d0%be%d0%b7%d0%b0%d0%bf%d1%87%d0%b0%d1%81%d1%82%d0%b5%d0%b9%20%d1%88%d0%ba%d0%be%d0%b4%d0%b0",
                    "https://www.xn--80aaasbafk1acftx0c6n.xn--p1ai/zapchasti-skoda",
                    "https://www.auto2.ru/tcd/skoda/"
                ]
            ],
            "оригинальные детали шкода" => [
                "sites" => [
                    "https://parts.skoda-avto.ru/",
                    "https://www.skoda-avto.ru/service/original-parts",
                    "https://am-parts.ru/katalog/skoda",
                    "https://skoda-favorit.ru/service/original-accessories",
                    "https://www.bogemia-skd.ru/service/original-parts",
                    "https://www.partarium.ru/search/zapchasti-skoda/",
                    "https://www.autodoc.ru/catalogs/original/skoda",
                    "https://www.exist.ru/catalog/global/cars/skoda",
                    "https://www.zzap.ru/public/manufact.aspx?class_man=skoda",
                    "https://parts.autoskd.ru/",
                    "https://edg-parts.ru/zapchasti-skoda",
                    "https://skoda.catalogs-parts.com/",
                    "https://skoda-accessories.ru/",
                    "https://www.ilcats.ru/skoda/?function=getmodels&market=cz",
                    "https://www.autocompas.ru/zapchasti/skoda/",
                    "https://emex.ru/catalogs/original/?screen=model&brandid=sk926&brandname=skoda&catalog=sk926",
                    "https://www.major-auto.ru/zapchasti/skoda/",
                    "https://rem.ru/catalog/parts/skoda/",
                    "https://autonomia.ru/skoda",
                    "https://auto3n.ru/parts-catalog/skoda"
                ]
            ],
            "оригинальные запчасти skoda" => [
                "sites" => [
                    "https://parts.skoda-avto.ru/",
                    "https://am-parts.ru/katalog/skoda",
                    "https://exist.ru/catalog/global/cars/skoda",
                    "https://skoda-favorit.ru/service/original-accessories",
                    "https://www.partarium.ru/search/zapchasti-skoda/",
                    "https://www.autodoc.ru/catalogs/original/skoda",
                    "https://remkom-auto.ru/",
                    "https://www.zzap.ru/public/manufact.aspx?class_man=skoda",
                    "https://parts.autoskd.ru/",
                    "https://www.major-auto.ru/zapchasti/skoda/",
                    "https://www.autocompas.ru/zapchasti/skoda/",
                    "https://www.bogemia-skd.ru/service/original-parts",
                    "https://edg-parts.ru/zapchasti-skoda",
                    "https://rem.ru/catalog/parts/skoda/",
                    "https://baza.drom.ru/moskva/sell_spare_parts/model/skoda/",
                    "https://emex.ru/catalogs/original/?screen=model&brandid=sk926&brandname=skoda&catalog=sk926",
                    "https://auto3n.ru/parts-catalog/skoda",
                    "https://www.ilcats.ru/skoda/?function=getmodels&market=cz",
                    "https://autonomia.ru/skoda",
                    "https://www.svautoz.ru/catalog/cars/skoda/"
                ]
            ],
            "оригинальные запчасти шкода" => [
                "sites" => [
                    "https://parts.skoda-avto.ru/",
                    "https://am-parts.ru/katalog/skoda",
                    "https://remkom-auto.ru/",
                    "https://skoda-favorit.ru/service/original-accessories",
                    "https://exist.ru/catalog/global/cars/skoda",
                    "https://www.bogemia-skd.ru/service/original-parts",
                    "https://www.autodoc.ru/catalogs/original/skoda",
                    "https://www.partarium.ru/search/zapchasti-skoda/",
                    "https://parts.autoskd.ru/",
                    "https://www.autocompas.ru/zapchasti/skoda/",
                    "https://www.zzap.ru/public/manufact.aspx?class_man=skoda",
                    "https://edg-parts.ru/zapchasti-skoda",
                    "https://www.major-auto.ru/zapchasti/skoda/",
                    "https://autonomia.ru/skoda",
                    "https://skoda-accessories.ru/",
                    "https://auto3n.ru/parts-catalog/skoda",
                    "https://rem.ru/catalog/parts/skoda/",
                    "https://emex.ru/catalogs/original/?screen=model&brandid=sk926&brandname=skoda&catalog=sk926",
                    "https://baza.drom.ru/moskva/sell_spare_parts/model/skoda/",
                    "https://www.svautoz.ru/catalog/cars/skoda/"
                ]
            ],
            "оригинальные запчасти шкода купить" => [
                "sites" => [
                    "https://parts.skoda-avto.ru/",
                    "https://am-parts.ru/katalog/skoda",
                    "https://exist.ru/catalog/global/cars/skoda",
                    "https://www.partarium.ru/search/zapchasti-skoda/",
                    "https://www.autodoc.ru/catalogs/original/skoda",
                    "https://skoda-favorit.ru/service/original-accessories",
                    "https://www.zzap.ru/public/manufact.aspx?class_man=skoda",
                    "https://www.major-auto.ru/zapchasti/skoda/",
                    "https://www.autocompas.ru/zapchasti/skoda/",
                    "https://parts.autoskd.ru/",
                    "https://edg-parts.ru/zapchasti-skoda",
                    "https://rem.ru/catalog/parts/skoda/",
                    "https://emex.ru/catalogs/original/?screen=model&brandid=sk926&brandname=skoda&catalog=sk926",
                    "https://autonomia.ru/skoda",
                    "https://www.bogemia-skd.ru/service/original-parts",
                    "https://auto3n.ru/parts-catalog/skoda",
                    "https://baza.drom.ru/moskva/sell_spare_parts/model/skoda/",
                    "https://market.yandex.ru/search?text=%d0%b7%d0%b0%d0%bf%d1%87%d0%b0%d1%81%d1%82%d0%b8%20%d0%bd%d0%b0%20%d1%88%d0%ba%d0%be%d0%b4%d1%83",
                    "https://www.svautoz.ru/catalog/cars/skoda/",
                    "https://www.xn--80aaasbafk1acftx0c6n.xn--p1ai/zapchasti-skoda"
                ]
            ],
            "оригинальный каталог запчастей skoda" => [
                "sites" => [
                    "https://skoda.catalogs-parts.com/",
                    "https://www.ilcats.ru/skoda/?function=getmodels&market=cz",
                    "https://parts.skoda-avto.ru/",
                    "https://exist.ru/catalog/global/cars/skoda",
                    "https://am-parts.ru/katalog/skoda",
                    "https://emex.ru/catalogs/original/?screen=model&brandid=sk926&brandname=skoda&catalog=sk926",
                    "https://www.zzap.ru/public/manufact.aspx?class_man=skoda",
                    "https://skoda.7zap.com/ru/cz/",
                    "https://www.partarium.ru/search/zapchasti-skoda/",
                    "https://www.autodoc.ru/catalogs/original/skoda",
                    "https://www.port3.ru/catalog/skoda/",
                    "https://www.reformauto.ru/etka/skoda/",
                    "https://webautocats.com/ru/etka/skoda/",
                    "https://rem.ru/catalog/parts/skoda/",
                    "https://vagtec.ru/catalog/skoda/",
                    "https://www.auto2.ru/tcd/skoda/",
                    "https://www.avtoall.ru/catalog_to/skoda/",
                    "https://edg-parts.ru/zapchasti-skoda",
                    "https://auto3n.ru/parts-catalog/skoda",
                    "https://www.autocompas.ru/zapchasti/skoda/"
                ]
            ],
            "оригинальный каталог запчастей шкода" => [
                "sites" => [
                    "https://skoda.catalogs-parts.com/",
                    "https://www.ilcats.ru/skoda/?function=getmodels&market=cz",
                    "https://parts.skoda-avto.ru/",
                    "https://exist.ru/catalog/global/cars/skoda",
                    "https://am-parts.ru/katalog/skoda",
                    "https://www.autodoc.ru/catalogs/original/skoda",
                    "https://skoda.7zap.com/ru/cz/",
                    "https://emex.ru/catalogs/original/?screen=model&brandid=sk926&brandname=skoda&catalog=sk926",
                    "https://www.zzap.ru/public/manufact.aspx?class_man=skoda",
                    "https://www.partarium.ru/search/zapchasti-skoda/",
                    "https://www.port3.ru/catalog/skoda/",
                    "https://www.reformauto.ru/etka/skoda/",
                    "https://rem.ru/catalog/parts/skoda/",
                    "https://vagtec.ru/catalog/skoda/",
                    "https://www.avtoall.ru/catalog_to/skoda/",
                    "https://webautocats.com/ru/etka/skoda/",
                    "https://edg-parts.ru/zapchasti-skoda",
                    "https://www.autocompas.ru/zapchasti/skoda/",
                    "https://auto3n.ru/parts-catalog/skoda",
                    "https://www.auto2.ru/tcd/skoda/"
                ]
            ],
            "шкода запчасти цена" => [
                "sites" => [
                    "https://parts.skoda-avto.ru/",
                    "https://am-parts.ru/katalog/skoda",
                    "https://baza.drom.ru/moskva/sell_spare_parts/model/skoda/",
                    "https://www.partarium.ru/search/zapchasti-skoda/",
                    "https://www.zzap.ru/public/manufact.aspx?class_man=skoda",
                    "https://m.avito.ru/moskva/zapchasti_i_aksessuary?query=%d0%b7%d0%b0%d0%bf%d1%87%d0%b0%d1%81%d1%82%d0%b8%20%d1%88%d0%ba%d0%be%d0%b4%d0%b0",
                    "https://exist.ru/catalog/global/cars/skoda?all=1",
                    "https://euroauto.ru/auto/cars/skoda/",
                    "https://www.autocompas.ru/zapchasti/skoda/",
                    "https://www.autodoc.ru/catalogs/original/skoda",
                    "https://market.yandex.ru/search?text=%d0%b7%d0%b0%d0%bf%d1%87%d0%b0%d1%81%d1%82%d0%b8%20%d0%bd%d0%b0%20%d1%88%d0%ba%d0%be%d0%b4%d1%83",
                    "https://www.major-auto.ru/zapchasti/skoda/",
                    "https://auto3n.ru/parts-catalog/skoda",
                    "https://varaosa.ru/catalog/skoda.html",
                    "https://edg-parts.ru/zapchasti-skoda",
                    "https://autonomia.ru/skoda",
                    "https://avtograde.ru/zapchasti-skoda-cat119/",
                    "https://www.xn--80aaasbafk1acftx0c6n.xn--p1ai/zapchasti-skoda",
                    "https://rem.ru/catalog/parts/skoda/",
                    "https://www.auto2.ru/tcd/skoda/"
                ]
            ],
            "skoda kodiaq scout" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-scout-2017",
                    "https://www.skoda-major.ru/kodiaq-scout/",
                    "https://www.drive2.ru/e/bnlkweaaafk",
                    "https://www.drive.ru/news/skoda/59e77a2bec05c4444c00003b.html",
                    "https://carso.ru/skoda/kodiaq-scout",
                    "https://www.drom.ru/catalog/skoda/kodiaq/189690/",
                    "https://www.youtube.com/watch?v=xr2d2mgv19i",
                    "https://skodakodiaq.club/skoda-kodiaq-scout-my-proverili-stoit-li-pokupat-cheshskij-7-mestnyj-vnedorozhnik/",
                    "https://www.autonews.ru/news/5d9ecced9a79470260357ea8",
                    "https://dc-sever.ru/model/skoda/kodiaq-scout/",
                    "https://adom.ru/skoda/kodiaq-scout",
                    "https://aksa-auto.ru/catalog/skoda/kodiaq-scout",
                    "https://center-auto.ru/katalog/skoda/kodiaq-scout/",
                    "https://www.bips.ru/skoda/kodiaq-scout",
                    "https://auto.fm/reviews/test-skoda-kodiaq-scout-protiv-kodiaq",
                    "https://dzen.ru/media/autosouz/testdraiv-skoda-kodiaq-scout-5e206ef4f73d9d00af63aaae",
                    "https://ac-moscow.ru/auto/skoda/kodiaq/kodiaqscout",
                    "https://riaauto.ru/skoda/kodiaq-scout",
                    "https://110km.ru/art/testdrive-skoda-kodiaq-scout-razvedka-dizelem-126747.html",
                    "https://www.incom-auto.ru/auto/skoda/kodiaq-scout/"
                ]
            ],
            "škoda kodiaq scout" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-scout-2017",
                    "https://www.skoda-major.ru/kodiaq-scout/",
                    "https://www.youtube.com/watch?v=xr2d2mgv19i",
                    "https://www.drom.ru/catalog/skoda/kodiaq/189690/",
                    "https://www.drive.ru/news/skoda/59e77a2bec05c4444c00003b.html",
                    "https://carso.ru/skoda/kodiaq-scout",
                    "https://www.drive2.ru/e/bnlkweaaafk",
                    "https://skodakodiaq.club/skoda-kodiaq-scout-my-proverili-stoit-li-pokupat-cheshskij-7-mestnyj-vnedorozhnik/",
                    "https://aksa-auto.ru/catalog/skoda/kodiaq-scout",
                    "https://adom.ru/skoda/kodiaq-scout",
                    "https://skoda-kodiaq.ru/novosti/104-skoda-rassekretila-kodiaq-scout.html",
                    "https://www.autonews.ru/news/5d9ecced9a79470260357ea8",
                    "https://skoda-auto2.ru/skoda-kodiaq-scout/",
                    "https://autoreview.ru/news/krossover-skoda-kodiaq-scout-v-rossii-ob-yavleny-ceny",
                    "https://dinaplus.ru/archival-models/kodiaq-scout-2017",
                    "https://www.bips.ru/skoda/kodiaq-scout",
                    "https://skoda-ap.ru/archival-models/kodiaq-scout-2017",
                    "https://center-auto.ru/katalog/skoda/kodiaq-scout/",
                    "https://riaauto.ru/skoda/kodiaq-scout",
                    "https://dc-sever.ru/model/skoda/kodiaq-scout/"
                ]
            ],
            "комплектация шкода кодиак скаут" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-scout-2017",
                    "https://www.drom.ru/catalog/skoda/kodiaq/189690/",
                    "https://www.skoda-major.ru/kodiaq-scout/",
                    "https://carso.ru/skoda/kodiaq-scout",
                    "https://www.drive.ru/news/skoda/59e77a2bec05c4444c00003b.html",
                    "https://www.drive2.ru/e/bnlkweaaafk",
                    "https://skoda-auto2.ru/skoda-kodiaq-scout/",
                    "https://adom.ru/skoda/kodiaq-scout",
                    "https://aksa-auto.ru/catalog/skoda/kodiaq-scout",
                    "https://center-auto.ru/katalog/skoda/kodiaq-scout/",
                    "https://www.bips.ru/skoda/kodiaq-scout",
                    "https://auto.ironhorse.ru/kodiaq-scout_17708.html",
                    "https://dc-sever.ru/model/skoda/kodiaq-scout/",
                    "https://auto.fm/reviews/test-skoda-kodiaq-scout-protiv-kodiaq",
                    "https://www.incom-auto.ru/auto/skoda/kodiaq-scout/komplektacii/scout/",
                    "https://riaauto.ru/skoda/kodiaq-scout",
                    "https://skodakodiaq.club/skoda-kodiaq-scout-my-proverili-stoit-li-pokupat-cheshskij-7-mestnyj-vnedorozhnik/",
                    "https://autoreview.ru/news/krossover-skoda-kodiaq-scout-v-rossii-ob-yavleny-ceny",
                    "https://skoda-kodiaq.ru/novosti/104-skoda-rassekretila-kodiaq-scout.html",
                    "https://skoda-ap.ru/archival-models/kodiaq-scout-2017"
                ]
            ],
            "характеристики шкода кодиак скаут 2022" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-scout-2017",
                    "https://www.drom.ru/catalog/skoda/kodiaq/189690/",
                    "https://auto.ironhorse.ru/kodiaq-scout_17708.html",
                    "https://www.skoda-major.ru/kodiaq-scout/",
                    "https://www.drive.ru/news/skoda/59e77a2bec05c4444c00003b.html",
                    "https://www.drive2.ru/e/bnlkweaaafk",
                    "https://b-kredit.com/catalog/skoda/kodiaq_scout/harakteristiki/",
                    "https://skoda-centr.ru/kodiaq-scout/complect/",
                    "https://adom.ru/skoda/kodiaq-scout/tth",
                    "https://naavtotrasse.ru/skoda/skoda-kodiaq-2022.html",
                    "https://carso.ru/skoda/kodiaq-scout",
                    "https://www.allcarz.ru/skoda-kodiaq-scout/",
                    "https://aksa-auto.ru/catalog/skoda/kodiaq-scout",
                    "https://gt-news.ru/skoda/skoda-kodiaq-2022/",
                    "https://www.auto-dd.ru/skoda-kodiaq-2022/",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-kodiaq-2022",
                    "https://skodakodiaq.club/skoda-kodiaq-scout-my-proverili-stoit-li-pokupat-cheshskij-7-mestnyj-vnedorozhnik/",
                    "https://www.zr.ru/content/articles/930969-novyj-skoda-kodiaq-bez-avtoma/",
                    "https://centr-autocredit.ru/new_cars/skoda/kodiaq-scout/",
                    "https://skoda-kodiaq.ru/novosti/104-skoda-rassekretila-kodiaq-scout.html"
                ]
            ],
            "шкода кодиак скаут" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-scout-2017",
                    "https://www.skoda-major.ru/kodiaq-scout/",
                    "https://www.drive2.ru/e/bnlkweaaafk",
                    "https://www.youtube.com/watch?v=xr2d2mgv19i",
                    "https://www.drive.ru/news/skoda/59e77a2bec05c4444c00003b.html",
                    "https://carso.ru/skoda/kodiaq-scout",
                    "https://www.drom.ru/catalog/skoda/kodiaq/189690/",
                    "https://aksa-auto.ru/catalog/skoda/kodiaq-scout",
                    "https://skoda-kuntsevo.ru/archival-models/kodiaq-scout-2017",
                    "https://www.autonews.ru/news/5d9ecced9a79470260357ea8",
                    "https://adom.ru/skoda/kodiaq-scout",
                    "https://skoda-auto2.ru/skoda-kodiaq-scout/",
                    "https://skoda-kodiaq.ru/novosti/104-skoda-rassekretila-kodiaq-scout.html",
                    "https://center-auto.ru/katalog/skoda/kodiaq-scout/",
                    "https://skodakodiaq.club/skoda-kodiaq-scout-my-proverili-stoit-li-pokupat-cheshskij-7-mestnyj-vnedorozhnik/",
                    "https://riaauto.ru/skoda/kodiaq-scout",
                    "https://auto.fm/reviews/test-skoda-kodiaq-scout-protiv-kodiaq",
                    "https://www.bips.ru/skoda/kodiaq-scout",
                    "https://110km.ru/art/testdrive-skoda-kodiaq-scout-razvedka-dizelem-126747.html",
                    "https://dc-sever.ru/model/skoda/kodiaq-scout/"
                ]
            ],
            "шкода кодиак скаут 2022" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-scout-2017",
                    "https://www.skoda-major.ru/kodiaq-scout/",
                    "https://www.drom.ru/catalog/skoda/kodiaq/189690/",
                    "https://aksa-auto.ru/catalog/skoda/kodiaq-scout",
                    "https://carso.ru/skoda/kodiaq-scout",
                    "https://www.youtube.com/watch?v=xr2d2mgv19i",
                    "https://auto.ironhorse.ru/kodiaq-scout_17708.html",
                    "https://www.drive.ru/news/skoda/59e77a2bec05c4444c00003b.html",
                    "https://adom.ru/skoda/kodiaq-scout",
                    "https://www.autonews.ru/news/5d9ecced9a79470260357ea8",
                    "https://www.drive2.ru/e/bnlkweaaafk",
                    "https://www.bips.ru/skoda/kodiaq-scout",
                    "https://ac-moscow.ru/auto/skoda/kodiaq/kodiaqscout",
                    "https://skoda-kodiaq.ru/novosti/104-skoda-rassekretila-kodiaq-scout.html",
                    "https://autocentr.su/auto/skoda/kodiaq-scout",
                    "https://auto.ru/moskva/cars/skoda/kodiaq/2022-year/all/",
                    "https://www.incom-auto.ru/auto/skoda/kodiaq-scout/",
                    "https://www.allcarz.ru/skoda-kodiaq-scout/",
                    "https://autoreview.ru/news/krossover-skoda-kodiaq-scout-v-rossii-ob-yavleny-ceny",
                    "https://avanta-avto-credit.ru/cars/skoda/kodiaq-scout/"
                ]
            ],
            "шкода кодиак скаут 2022 цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-scout-2017",
                    "https://www.skoda-major.ru/kodiaq-scout/",
                    "https://aksa-auto.ru/catalog/skoda/kodiaq-scout",
                    "https://carso.ru/skoda/kodiaq-scout",
                    "https://auto.ru/moskva/cars/skoda/kodiaq/2022-year/all/",
                    "https://www.bips.ru/skoda/kodiaq-scout",
                    "https://www.drive.ru/news/skoda/59e77a2bec05c4444c00003b.html",
                    "https://www.incom-auto.ru/auto/skoda/kodiaq-scout/",
                    "https://center-auto.ru/katalog/skoda/kodiaq-scout/",
                    "https://autocentr.su/auto/skoda/kodiaq-scout",
                    "https://ac-moscow.ru/auto/skoda/kodiaq/kodiaqscout",
                    "https://riaauto.ru/skoda/kodiaq-scout",
                    "https://avanta-avto-credit.ru/cars/skoda/kodiaq-scout/",
                    "https://autoreview.ru/news/krossover-skoda-kodiaq-scout-v-rossii-ob-yavleny-ceny",
                    "https://auto.ironhorse.ru/kodiaq-scout_17708.html",
                    "https://www.allcarz.ru/skoda-kodiaq-scout/",
                    "https://m.avito.ru/all/avtomobili/novyy/skoda/kodiaq-asgbagica0sgfmbmaec2dz6zkok2ddaoka",
                    "https://centr-autocredit.ru/new_cars/skoda/kodiaq-scout/",
                    "https://dm-motors.ru/model/skoda/kodiaq-scout/",
                    "https://trex.ru/skoda/kodiaq-scout"
                ]
            ],
            "шкода кодиак скаут комплектации и цены" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-scout-2017",
                    "https://www.skoda-major.ru/kodiaq-scout/",
                    "https://carso.ru/skoda/kodiaq-scout",
                    "https://aksa-auto.ru/catalog/skoda/kodiaq-scout",
                    "https://center-auto.ru/katalog/skoda/kodiaq-scout/",
                    "https://www.bips.ru/skoda/kodiaq-scout",
                    "https://adom.ru/skoda/kodiaq-scout",
                    "https://riaauto.ru/skoda/kodiaq-scout",
                    "https://www.drive.ru/brands/skoda/models/2016/kodiaq_scout",
                    "https://dc-sever.ru/model/skoda/kodiaq-scout/",
                    "https://auto.ru/catalog/cars/skoda/kodiaq/20839003/20839055/equipment/",
                    "https://www.incom-auto.ru/auto/skoda/kodiaq-scout/",
                    "https://skoda-auto2.ru/skoda-kodiaq-scout/",
                    "https://www.drom.ru/catalog/skoda/kodiaq/189690/",
                    "https://ac-moscow.ru/auto/skoda/kodiaq/kodiaqscout",
                    "https://autocentr.su/auto/skoda/kodiaq-scout",
                    "https://skoda-motoravto.ru/models/kodiaq-scout",
                    "https://autocommunity.ru/skoda/kodiaq-scout",
                    "https://avanta-avto-credit.ru/cars/skoda/kodiaq-scout/",
                    "https://rolf-skoda.ru/models/kodiaq/price"
                ]
            ],
            "шкода кодиак скаут купить" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-scout-2017",
                    "https://www.skoda-major.ru/kodiaq-scout/",
                    "https://carso.ru/skoda/kodiaq-scout",
                    "https://aksa-auto.ru/catalog/skoda/kodiaq-scout",
                    "https://riaauto.ru/skoda/kodiaq-scout",
                    "https://center-auto.ru/katalog/skoda/kodiaq-scout/",
                    "https://www.incom-auto.ru/auto/skoda/kodiaq-scout/",
                    "https://adom.ru/skoda/kodiaq-scout",
                    "https://ac-moscow.ru/auto/skoda/kodiaq/kodiaqscout",
                    "https://dc-sever.ru/model/skoda/kodiaq-scout/",
                    "https://www.bips.ru/skoda/kodiaq-scout",
                    "https://auto.ru/moskva/cars/skoda/kodiaq/used/",
                    "https://autocentr.su/auto/skoda/kodiaq-scout",
                    "https://avanta-avto-credit.ru/cars/skoda/kodiaq-scout/",
                    "https://autocommunity.ru/skoda/kodiaq-scout",
                    "https://www.bogemia-skd.ru/models/kodiaq",
                    "https://autockidka.ru/cars/skoda/kodiaq/komplektacii-i-ceny/scout/",
                    "https://autospot.ru/brands/skoda/kodiaq/suv/price/",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/",
                    "https://skoda-centr.ru/kodiaq-scout/"
                ]
            ],
            "шкода кодиак скаут характеристики" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-scout-2017",
                    "https://www.drom.ru/catalog/skoda/kodiaq/189690/",
                    "https://auto.ironhorse.ru/kodiaq-scout_17708.html",
                    "https://www.drive2.ru/e/bnlkweaaafk",
                    "https://www.skoda-major.ru/kodiaq-scout/tth/",
                    "https://www.drive.ru/brands/skoda/models/2016/kodiaq_scout/20_tsi_4x4",
                    "https://skodakodiaq.club/skoda-kodiaq-scout-my-proverili-stoit-li-pokupat-cheshskij-7-mestnyj-vnedorozhnik/",
                    "https://skoda-auto2.ru/skoda-kodiaq-scout/",
                    "https://riaauto.ru/skoda/kodiaq-scout/tth",
                    "https://adom.ru/skoda/kodiaq-scout/tth",
                    "https://auto.fm/reviews/test-skoda-kodiaq-scout-protiv-kodiaq",
                    "https://mlada-auto.ru/archival-models/kodiaq-scout-2017",
                    "https://carso.ru/skoda/kodiaq-scout",
                    "https://www.incom-auto.ru/auto/skoda/kodiaq-scout/tech/",
                    "https://gold-avto.com/auto/skoda/kodiaq_scout/",
                    "https://www.autonews.ru/news/5d9ecced9a79470260357ea8",
                    "https://aksa-auto.ru/catalog/skoda/kodiaq-scout",
                    "https://110km.ru/art/testdrive-skoda-kodiaq-scout-razvedka-dizelem-126747.html",
                    "https://center-auto.ru/katalog/skoda/kodiaq-scout/",
                    "https://skoda-centr.ru/kodiaq-scout/"
                ]
            ],
            "шкода кодиак скаут цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-scout-2017",
                    "https://www.skoda-major.ru/kodiaq-scout/",
                    "https://carso.ru/skoda/kodiaq-scout",
                    "https://aksa-auto.ru/catalog/skoda/kodiaq-scout",
                    "https://riaauto.ru/skoda/kodiaq-scout",
                    "https://www.drive.ru/news/skoda/59e77a2bec05c4444c00003b.html",
                    "https://www.bips.ru/skoda/kodiaq-scout",
                    "https://center-auto.ru/katalog/skoda/kodiaq-scout/",
                    "https://www.incom-auto.ru/auto/skoda/kodiaq-scout/",
                    "https://ac-moscow.ru/auto/skoda/kodiaq/kodiaqscout",
                    "https://autocentr.su/auto/skoda/kodiaq-scout",
                    "https://autocommunity.ru/skoda/kodiaq-scout",
                    "https://avanta-avto-credit.ru/cars/skoda/kodiaq-scout/",
                    "https://autoreview.ru/news/krossover-skoda-kodiaq-scout-v-rossii-ob-yavleny-ceny",
                    "https://rolf-skoda.ru/models/kodiaq/price",
                    "https://auto.ru/moskva/cars/skoda/kodiaq/new/",
                    "https://skoda-kodiaq.ru/novosti/104-skoda-rassekretila-kodiaq-scout.html",
                    "https://skoda-auto2.ru/skoda-kodiaq-scout/",
                    "https://www.allcarz.ru/skoda-kodiaq-scout/",
                    "https://www.bogemia-skd.ru/models/kodiaq"
                ]
            ],
            "skoda octavia hockey edition" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia-hockey-edition",
                    "https://www.skoda-avto.ru/models/hockey-edition-overview",
                    "https://www.skoda-major.ru/octavia-hockey-edition/",
                    "https://www.drom.ru/catalog/skoda/octavia/229014/",
                    "https://auto.ru/catalog/cars/skoda/octavia/20898195/20898233/equipment/20898233_21403777_20913312/",
                    "https://skoda-avtoruss.ru/models/octavia-hockey-edition",
                    "https://rolf-skoda.ru/models/octavia-hockey-edition",
                    "https://www.drive2.ru/o/b/543980905014756128/",
                    "https://www.bogemia-skd.ru/models/octavia-hockey-edition",
                    "https://carsclick.ru/skoda/obzor-avtomobilej/octavia-hockey-edition/",
                    "https://www.youtube.com/watch?v=n5fsyquazgg",
                    "https://www.sove2u.ru/%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d0%be%d0%ba%d1%82%d0%b0%d0%b2%d0%b8%d1%8f-%d1%85%d0%be%d0%ba%d0%ba%d0%b5%d0%b9-%d1%8d%d0%b4%d0%b8%d1%88%d0%bd-2020/",
                    "https://www.provolochki.ru/auto/%d0%ba%d0%be%d0%bc%d0%bf%d0%bb%d0%b5%d0%ba%d1%82%d0%b0%d1%86%d0%b8%d1%8f-skoda-octavia-hockey-edition-2020/",
                    "https://skoda-kors.ru/models/hockey-edition-overview",
                    "https://www.avito.ru/all?q=skoda+octavia+hockey+edition",
                    "https://skoda-ideal.ru/models/hockey-edition-overview",
                    "https://dzen.ru/media/id/5ad0845d61049348b6c05bb0/10-fishek-koda-octavia-hockey-edition-5be18f29a4b57700acc25692",
                    "https://skoda-forward.ru/models/octavia-hockey-edition",
                    "https://v1.ru/text/auto/2018/11/07/65589421/",
                    "https://carso.ru/skoda/octavia/kit/hockey-edition"
                ]
            ],
            "skoda octavia hockey edition комплектация" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia-hockey-edition",
                    "https://www.skoda-avto.ru/models/hockey-edition-overview",
                    "https://rolf-skoda.ru/models/octavia-hockey-edition",
                    "https://auto.ru/catalog/cars/skoda/octavia/20898195/20898233/equipment/20898233_21403777_20913312/",
                    "https://www.drom.ru/catalog/skoda/octavia/229014/",
                    "https://www.skoda-major.ru/octavia-hockey-edition/",
                    "https://skoda-avtoruss.ru/models/octavia-hockey-edition",
                    "https://www.drive2.ru/o/b/543980905014756128/",
                    "https://carsclick.ru/skoda/obzor-avtomobilej/octavia-hockey-edition/",
                    "https://www.sove2u.ru/%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d0%be%d0%ba%d1%82%d0%b0%d0%b2%d0%b8%d1%8f-%d1%85%d0%be%d0%ba%d0%ba%d0%b5%d0%b9-%d1%8d%d0%b4%d0%b8%d1%88%d0%bd-2020/",
                    "https://skoda-kuntsevo.ru/models/octavia-hockey-edition",
                    "https://www.bogemia-skd.ru/models/octavia-hockey-edition",
                    "https://www.rosso-sk.ru/press/obzor-osobennostey-serii-khokkey-edishn-ot-skoda",
                    "https://www.provolochki.ru/auto/%d0%ba%d0%be%d0%bc%d0%bf%d0%bb%d0%b5%d0%ba%d1%82%d0%b0%d1%86%d0%b8%d1%8f-skoda-octavia-hockey-edition-2020/",
                    "https://www.youtube.com/watch?v=adzpcu-duwi",
                    "https://avisavto.ru/cars/skoda/octavia-new/komplektacii/hockey-edition",
                    "https://autovn.ru/models/octavia-hockey-edition",
                    "https://kveta-auto.ru/models/hockey-edition-overview",
                    "https://skoda-koleso.ru/models/octavia-hockey-edition",
                    "https://carso.ru/skoda/octavia/kit/hockey-edition"
                ]
            ],
            "škoda octavia hockey edition" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/hockey-edition-overview",
                    "https://skoda-avtoruss.ru/models/octavia-hockey-edition",
                    "https://www.skoda-major.ru/octavia-hockey-edition/",
                    "https://rolf-skoda.ru/models/octavia-hockey-edition",
                    "https://www.bogemia-skd.ru/models/octavia-hockey-edition",
                    "https://auto.ru/catalog/cars/skoda/octavia/20898195/20898233/equipment/20898233_21403777_20913312/",
                    "https://www.drom.ru/catalog/skoda/octavia/229014/",
                    "https://www.drive2.ru/o/b/543980905014756128/",
                    "https://www.autocity-sk.ru/models/octavia-hockey-edition",
                    "https://www.rosso-sk.ru/models/octavia-hockey-edition",
                    "https://carsclick.ru/skoda/obzor-avtomobilej/octavia-hockey-edition/",
                    "https://www.sove2u.ru/%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d0%be%d0%ba%d1%82%d0%b0%d0%b2%d0%b8%d1%8f-%d1%85%d0%be%d0%ba%d0%ba%d0%b5%d0%b9-%d1%8d%d0%b4%d0%b8%d1%88%d0%bd-2020/",
                    "https://cze-auto.ru/archival-models/octavia-hockey-edition-2017",
                    "https://skoda-forward.ru/models/octavia-hockey-edition",
                    "https://autovn.ru/models/octavia-hockey-edition",
                    "https://www.provolochki.ru/auto/%d0%ba%d0%be%d0%bc%d0%bf%d0%bb%d0%b5%d0%ba%d1%82%d0%b0%d1%86%d0%b8%d1%8f-skoda-octavia-hockey-edition-2020/",
                    "https://carso.ru/skoda/octavia/kit/hockey-edition",
                    "https://dzen.ru/media/id/5ad0845d61049348b6c05bb0/10-fishek-koda-octavia-hockey-edition-5be18f29a4b57700acc25692",
                    "https://moravia-motors.ru/models/octavia-hockey-edition",
                    "https://krona-auto.ru/models/octavia-hockey-edition"
                ]
            ],
            "škoda octavia hockey edition 2022" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia-hockey-edition",
                    "https://www.skoda-avto.ru/models/hockey-edition-overview",
                    "https://skoda-avtoruss.ru/models/octavia-hockey-edition",
                    "https://www.skoda-major.ru/octavia-hockey-edition/",
                    "https://www.bogemia-skd.ru/models/octavia-hockey-edition",
                    "https://rolf-skoda.ru/models/octavia-hockey-edition",
                    "https://www.drive2.ru/o/b/543980905014756128/",
                    "https://skoda-kanavto.ru/models/octavia-hockey-edition",
                    "https://auto.ru/catalog/cars/skoda/octavia/20898195/20898233/equipment/20898233_21403777_20913312/",
                    "https://www.youtube.com/watch?v=n5fsyquazgg",
                    "https://www.drom.ru/catalog/skoda/octavia/334592/",
                    "https://carsclick.ru/skoda/obzor-avtomobilej/octavia-hockey-edition/",
                    "https://www.rosso-sk.ru/press/obzor-osobennostey-serii-khokkey-edishn-ot-skoda",
                    "https://skoda-vozrojdenie.ru/models/octavia-hockey-edition",
                    "https://www.sove2u.ru/%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d0%be%d0%ba%d1%82%d0%b0%d0%b2%d0%b8%d1%8f-%d1%85%d0%be%d0%ba%d0%ba%d0%b5%d0%b9-%d1%8d%d0%b4%d0%b8%d1%88%d0%bd-2020/",
                    "https://skoda-forward.ru/models/octavia-hockey-edition",
                    "https://www.provolochki.ru/auto/%d0%ba%d0%be%d0%bc%d0%bf%d0%bb%d0%b5%d0%ba%d1%82%d0%b0%d1%86%d0%b8%d1%8f-skoda-octavia-hockey-edition-2020/",
                    "https://carso.ru/skoda/octavia/kit/hockey-edition",
                    "https://bogemia-nn.ru/models/octavia-hockey-edition",
                    "https://moravia-motors.ru/models/octavia-hockey-edition"
                ]
            ],
            "купить skoda octavia hockey edition" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia-hockey-edition",
                    "https://www.skoda-major.ru/octavia-hockey-edition/",
                    "https://rolf-skoda.ru/models/octavia-hockey-edition",
                    "https://www.bogemia-skd.ru/models/octavia-hockey-edition",
                    "https://auto.ru/catalog/cars/skoda/octavia/20898195/20898233/equipment/20898233_21403777_20913312/",
                    "https://www.avito.ru/all?q=skoda+octavia+hockey+edition",
                    "https://www.autocity-sk.ru/models/octavia-hockey-edition",
                    "https://ac-sokolniki.ru/auto/skoda/octavia-hockey-edition/octavia-hockey-edition",
                    "https://carso.ru/skoda/octavia/kit/hockey-edition",
                    "https://auto-legend.ru/skoda/octavia-132500259/8118",
                    "https://ac-trust.ru/auto/skoda/octavia_hockey_edition/",
                    "https://www.drive2.ru/o/b/543980905014756128/",
                    "https://www.drom.ru/catalog/skoda/octavia/309524/",
                    "https://moravia-motors.ru/models/octavia-hockey-edition",
                    "https://krona-auto.ru/models/octavia-hockey-edition",
                    "https://sevavto.ru/models/octavia-hockey-edition",
                    "https://www.sove2u.ru/%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d0%be%d0%ba%d1%82%d0%b0%d0%b2%d0%b8%d1%8f-%d1%85%d0%be%d0%ba%d0%ba%d0%b5%d0%b9-%d1%8d%d0%b4%d0%b8%d1%88%d0%bd-2020/",
                    "https://skoda-wagner.ru/models/octavia-hockey-edition",
                    "https://ringsever.ru/models/octavia-hockey-edition",
                    "https://autospot.ru/brands/skoda/octavia_iv/liftback/price/"
                ]
            ],
            "шкода октавия хоккей эдишн" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/hockey-edition-overview",
                    "https://www.skoda-avto.ru/models/octavia-hockey-edition",
                    "https://auto.ru/catalog/cars/skoda/octavia/20898195/20898233/equipment/20898233_21403777_20913312/",
                    "https://www.drive2.ru/o/b/543980905014756128/",
                    "https://www.drom.ru/catalog/skoda/octavia/229014/",
                    "https://www.skoda-major.ru/octavia-hockey-edition/",
                    "https://www.rosso-sk.ru/press/obzor-osobennostey-serii-khokkey-edishn-ot-skoda",
                    "https://www.sove2u.ru/%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d0%be%d0%ba%d1%82%d0%b0%d0%b2%d0%b8%d1%8f-%d1%85%d0%be%d0%ba%d0%ba%d0%b5%d0%b9-%d1%8d%d0%b4%d0%b8%d1%88%d0%bd-2020/",
                    "https://rolf-skoda.ru/models/octavia-hockey-edition",
                    "https://skoda-avtoruss.ru/models/octavia-hockey-edition",
                    "https://www.youtube.com/watch?v=n5fsyquazgg",
                    "https://www.bogemia-skd.ru/models/octavia-hockey-edition",
                    "https://skoda-forward.ru/models/octavia-hockey-edition",
                    "https://v1.ru/text/auto/2018/11/07/65589421/",
                    "https://www.autocity-sk.ru/models/octavia-hockey-edition",
                    "https://skoda-gradavto.ru/models/hockey-edition-overview",
                    "https://dzen.ru/media/id/5ad0845d61049348b6c05bb0/10-fishek-koda-octavia-hockey-edition-5be18f29a4b57700acc25692",
                    "https://cze-auto.ru/archival-models/octavia-hockey-edition-2017",
                    "https://skoda-vostokmotors.ru/models/octavia-hockey-edition",
                    "https://novostivolgograda.ru/news/tech/07-11-2018/10-fishek-koda-octavia-hockey-edition"
                ]
            ],
            "шкода октавия хоккей эдишн 2022" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia-hockey-edition",
                    "https://www.skoda-avto.ru/models/hockey-edition-overview",
                    "https://www.skoda-major.ru/octavia-hockey-edition/",
                    "https://www.drive2.ru/o/b/543980905014756128/",
                    "https://auto.ru/catalog/cars/skoda/octavia/21713968/21714014/equipment/21714014_22997377_22568936/",
                    "https://skoda-avtoruss.ru/models/octavia-hockey-edition",
                    "https://rolf-skoda.ru/models/octavia-hockey-edition",
                    "https://www.bogemia-skd.ru/models/octavia-hockey-edition",
                    "https://www.rosso-sk.ru/press/obzor-osobennostey-serii-khokkey-edishn-ot-skoda",
                    "https://www.drom.ru/catalog/skoda/octavia/334593/",
                    "https://www.autocity-sk.ru/models/octavia-hockey-edition",
                    "https://www.youtube.com/watch?v=n5fsyquazgg",
                    "https://skoda.fenix-auto.ru/models/octavia-hockey-edition",
                    "https://skoda-forward.ru/models/octavia-hockey-edition",
                    "https://www.atlant-motors.ru/models/octavia-hockey-edition",
                    "https://carso.ru/skoda/octavia/kit/hockey-edition",
                    "https://www.sove2u.ru/%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d0%be%d0%ba%d1%82%d0%b0%d0%b2%d0%b8%d1%8f-%d1%85%d0%be%d0%ba%d0%ba%d0%b5%d0%b9-%d1%8d%d0%b4%d0%b8%d1%88%d0%bd-2020/",
                    "https://www.provolochki.ru/auto/%d0%ba%d0%be%d0%bc%d0%bf%d0%bb%d0%b5%d0%ba%d1%82%d0%b0%d1%86%d0%b8%d1%8f-skoda-octavia-hockey-edition-2020/",
                    "https://dzen.ru/media/id/5ad0845d61049348b6c05bb0/10-fishek-koda-octavia-hockey-edition-5be18f29a4b57700acc25692",
                    "https://skoda-gradavto.ru/models/hockey-edition-overview"
                ]
            ],
            "шкода октавия хоккей эдишн комплектация" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/hockey-edition-overview",
                    "https://www.skoda-avto.ru/models/octavia-hockey-edition",
                    "https://auto.ru/catalog/cars/skoda/octavia/20898195/20898233/equipment/20898233_21403777_20913312/",
                    "https://www.sove2u.ru/%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d0%be%d0%ba%d1%82%d0%b0%d0%b2%d0%b8%d1%8f-%d1%85%d0%be%d0%ba%d0%ba%d0%b5%d0%b9-%d1%8d%d0%b4%d0%b8%d1%88%d0%bd-2020/",
                    "https://www.drom.ru/catalog/skoda/octavia/229014/",
                    "https://carsclick.ru/skoda/obzor-avtomobilej/octavia-hockey-edition/",
                    "https://www.drive2.ru/o/b/543980905014756128/",
                    "https://www.rosso-sk.ru/press/obzor-osobennostey-serii-khokkey-edishn-ot-skoda",
                    "https://rolf-skoda.ru/models/octavia-hockey-edition",
                    "https://www.skoda-major.ru/octavia-hockey-edition/",
                    "https://skoda-kuntsevo.ru/models/octavia-hockey-edition",
                    "https://skoda-avtoruss.ru/models/octavia-hockey-edition",
                    "https://www.bogemia-skd.ru/models/octavia-hockey-edition",
                    "https://www.youtube.com/watch?v=n5fsyquazgg",
                    "https://skoda-forward.ru/models/octavia-hockey-edition",
                    "https://cze-auto.ru/models/octavia-hockey-edition",
                    "https://v1.ru/text/auto/2018/11/07/65589421/",
                    "https://www.autocity-sk.ru/models/octavia-hockey-edition",
                    "https://skoda.fenix-auto.ru/press/octavia-hockey-edition",
                    "https://naavtotrasse.ru/cat/skoda/octavia/2019_8862/20-tsi-dsg-ambition-plus-hockey-edition-132862/"
                ]
            ],
            "шкода октавия хоккей эдишн купить" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia-hockey-edition",
                    "https://www.skoda-major.ru/octavia-hockey-edition/",
                    "https://www.avito.ru/all?q=skoda+octavia+hockey+edition",
                    "https://rolf-skoda.ru/models/octavia-hockey-edition",
                    "https://auto.ru/catalog/cars/skoda/octavia/20898195/20898233/equipment/20898233_21403777_20913312/",
                    "https://www.bogemia-skd.ru/models/octavia-hockey-edition",
                    "https://www.autocity-sk.ru/models/octavia-hockey-edition",
                    "https://ac-sokolniki.ru/auto/skoda/octavia-hockey-edition/octavia-hockey-edition",
                    "https://carso.ru/skoda/octavia/kit/hockey-edition",
                    "https://www.drive2.ru/o/b/543980905014756128/",
                    "https://auto-legend.ru/skoda/octavia-132500259/8118",
                    "https://www.drom.ru/catalog/skoda/octavia/309524/",
                    "https://cze-auto.ru/models/octavia-hockey-edition",
                    "https://ac-trust.ru/auto/skoda/octavia_hockey_edition/",
                    "https://moravia-motors.ru/models/octavia-hockey-edition",
                    "https://skoda-vostokmotors.ru/models/octavia-hockey-edition",
                    "https://skoda.fenix-auto.ru/models/octavia-hockey-edition",
                    "https://sevavto.ru/models/octavia-hockey-edition",
                    "https://www.sove2u.ru/%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d0%be%d0%ba%d1%82%d0%b0%d0%b2%d0%b8%d1%8f-%d1%85%d0%be%d0%ba%d0%ba%d0%b5%d0%b9-%d1%8d%d0%b4%d0%b8%d1%88%d0%bd-2020/",
                    "https://krona-auto.ru/models/octavia-hockey-edition"
                ]
            ],
            "шкода октавия хоккей эдишн характеристики" => [
                "sites" => [
                    "https://www.drom.ru/catalog/skoda/octavia/229014/",
                    "https://www.skoda-avto.ru/models/octavia-hockey-edition",
                    "https://auto.ru/catalog/cars/skoda/octavia/20898195/20898233/specifications/20898233_21403777_20898376/",
                    "https://carsclick.ru/skoda/obzor-avtomobilej/octavia-hockey-edition/",
                    "https://www.skoda-major.ru/octavia-hockey-edition/tehnicheskie-harakteristiki/",
                    "https://www.sove2u.ru/%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d0%be%d0%ba%d1%82%d0%b0%d0%b2%d0%b8%d1%8f-%d1%85%d0%be%d0%ba%d0%ba%d0%b5%d0%b9-%d1%8d%d0%b4%d0%b8%d1%88%d0%bd-2020/",
                    "https://skoda-kanavto.ru/models/octavia-hockey-edition",
                    "https://skoda-kuntsevo.ru/models/octavia-hockey-edition",
                    "https://skoda-avtoruss.ru/models/octavia-hockey-edition",
                    "https://www.rosso-sk.ru/press/obzor-osobennostey-serii-khokkey-edishn-ot-skoda",
                    "https://www.drive2.ru/o/b/543980905014756128/",
                    "https://skoda.medved-vostok.ru/models/octavia-hockey-edition",
                    "https://ac-trust.ru/auto/skoda/octavia_hockey_edition/",
                    "https://www.provolochki.ru/auto/%d0%ba%d0%be%d0%bc%d0%bf%d0%bb%d0%b5%d0%ba%d1%82%d0%b0%d1%86%d0%b8%d1%8f-skoda-octavia-hockey-edition-2020/",
                    "https://www.bogemia-skd.ru/models/octavia-hockey-edition",
                    "https://www.autocity-sk.ru/models/octavia-hockey-edition",
                    "https://www.youtube.com/watch?v=n5fsyquazgg",
                    "https://naavtotrasse.ru/cat/skoda/octavia/2019_8862/14-tsi-mt-ambition-plus-hockey-edition-132848/",
                    "https://skoda.fenix-auto.ru/press/octavia-hockey-edition",
                    "https://rolf-skoda.ru/models/octavia-hockey-edition"
                ]
            ],
            "шкода октавия хоккей эдишн цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia-hockey-edition",
                    "https://www.skoda-major.ru/octavia-hockey-edition/",
                    "https://auto.ru/catalog/cars/skoda/octavia/20898195/20898233/equipment/20898233_21403777_20913312/",
                    "https://rolf-skoda.ru/models/octavia-hockey-edition",
                    "https://skoda-kuntsevo.ru/models/octavia-hockey-edition",
                    "https://www.bogemia-skd.ru/models/octavia-hockey-edition",
                    "https://m.avito.ru/rossiya/avtomobili/s_probegom/skoda/octavia-asgbagica0sgfmjmaec2dz6zkok2dysska?q=hockey%2bedition",
                    "https://www.autocity-sk.ru/models/octavia-hockey-edition",
                    "https://www.drom.ru/catalog/skoda/octavia/229014/",
                    "https://www.sove2u.ru/%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d0%be%d0%ba%d1%82%d0%b0%d0%b2%d0%b8%d1%8f-%d1%85%d0%be%d0%ba%d0%ba%d0%b5%d0%b9-%d1%8d%d0%b4%d0%b8%d1%88%d0%bd-2020/",
                    "https://www.rosso-sk.ru/models/octavia-hockey-edition",
                    "https://ac-sokolniki.ru/auto/skoda/octavia-hockey-edition/octavia-hockey-edition",
                    "https://carso.ru/skoda/octavia/kit/hockey-edition",
                    "https://skoda-forward.ru/models/octavia-hockey-edition",
                    "https://www.drive2.ru/o/b/543980905014756128/",
                    "https://ac-trust.ru/auto/skoda/octavia_hockey_edition/",
                    "https://moravia-motors.ru/models/octavia-hockey-edition",
                    "https://sevavto.ru/models/octavia-hockey-edition",
                    "https://skoda-vostokmotors.ru/models/octavia-hockey-edition",
                    "https://carsclick.ru/skoda/obzor-avtomobilej/octavia-hockey-edition/"
                ]
            ],
            "skoda rapid hockey edition" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid-hockey-edition",
                    "https://rolf-skoda.ru/models/rapid-hockey-edition",
                    "https://auto.ru/catalog/cars/skoda/rapid/21005574/21005618/equipment/21005618_21515404_21031674/",
                    "https://www.skoda-major.ru/rapid-hockey-edition/",
                    "https://skoda-avtoruss.ru/models/rapid-hockey-edition",
                    "https://www.drom.ru/catalog/skoda/rapid/227028/",
                    "https://www.atlant-motors.ru/models/rapid-hockey-edition",
                    "https://www.drive2.ru/o/b/543980905014756128/",
                    "https://skoda-kuntsevo.ru/models/rapid-hockey-edition",
                    "https://skoda-favorit.ru/models/rapid-hockey-edition",
                    "https://www.bogemia-skd.ru/models/rapid-hockey-edition",
                    "https://www.zr.ru/content/articles/901954-dlitelnyj-test-skoda-rapid-k/",
                    "https://ringsever.ru/models/rapid-hockey-edition",
                    "https://www.autocity-sk.ru/models/rapid-hockey-edition",
                    "https://www.avito.ru/rossiya/avtomobili/skoda/rapid-asgbagicaktgtg2emsjitg2urig?q=hockey+edition",
                    "https://www.ventus.ru/models/hockey-edition-overview",
                    "https://www.skoda-vitebskiy.ru/models/rapid-hockey-edition",
                    "https://skoda-wagner.ru/models/rapid-hockey-edition",
                    "https://autoreview.ru/news/obnovlennaya-skoda-rapid-obzavelas-versiey-hockey-edition",
                    "https://www.skoda-podolsk.ru/models/rapid-hockey-edition"
                ]
            ],
            "skoda rapid комплектация hockey edition" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid-hockey-edition",
                    "https://rolf-skoda.ru/models/rapid-hockey-edition",
                    "https://auto.ru/catalog/cars/skoda/rapid/21005574/21005618/equipment/21005618_21515404_21031674/",
                    "https://www.drom.ru/catalog/skoda/rapid/227028/",
                    "https://www.skoda-major.ru/rapid-hockey-edition/",
                    "https://skoda-avtoruss.ru/models/rapid-hockey-edition",
                    "https://locman-skoda.ru/models/rapid-hockey-edition",
                    "https://www.drive2.ru/o/b/543980905014756128/",
                    "https://www.bogemia-skd.ru/models/rapid-hockey-edition",
                    "https://autoreview.ru/news/obnovlennaya-skoda-rapid-obzavelas-versiey-hockey-edition",
                    "https://skoda-kuntsevo.ru/models/rapid-hockey-edition",
                    "https://www.rosso-sk.ru/press/obzor-osobennostey-serii-khokkey-edishn-ot-skoda",
                    "https://www.zr.ru/content/articles/901954-dlitelnyj-test-skoda-rapid-k/",
                    "https://carso.ru/skoda/rapid/kit/active-hockey-edition",
                    "https://www.autocity-sk.ru/models/rapid-hockey-edition",
                    "https://ringsever.ru/models/rapid-hockey-edition",
                    "https://skoda-favorit.ru/models/rapid-hockey-edition",
                    "https://skoda-orehovo.ru/models/rapid-hockey-edition",
                    "https://www.ventus.ru/models/rapid-hockey-edition",
                    "https://skoda-wagner.ru/models/rapid-hockey-edition"
                ]
            ],
            "škoda rapid hockey edition" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid-hockey-edition",
                    "https://rolf-skoda.ru/models/rapid-hockey-edition",
                    "https://skoda-avtoruss.ru/models/rapid-hockey-edition",
                    "https://www.skoda-major.ru/rapid-hockey-edition/",
                    "https://www.drom.ru/catalog/skoda/rapid/227028/",
                    "https://locman-skoda.ru/models/rapid-hockey-edition",
                    "https://auto.ru/catalog/cars/skoda/rapid/21005574/21005618/equipment/21005618_21515404_21031674/",
                    "https://skoda-kuntsevo.ru/models/rapid-hockey-edition",
                    "https://skoda-favorit.ru/models/rapid-hockey-edition",
                    "https://www.bogemia-skd.ru/models/rapid-hockey-edition",
                    "https://www.drive2.ru/o/b/543980905014756128/",
                    "https://www.zr.ru/content/articles/901954-dlitelnyj-test-skoda-rapid-k/",
                    "https://skoda-yug-avto.ru/models/rapid-hockey-edition",
                    "https://www.ventus.ru/models/hockey-edition-overview",
                    "https://autoreview.ru/news/obnovlennaya-skoda-rapid-obzavelas-versiey-hockey-edition",
                    "https://www.autocity-sk.ru/models/rapid-hockey-edition",
                    "https://skoda-ap.ru/models/rapid-hockey-edition",
                    "https://www.skoda-vitebskiy.ru/models/rapid-hockey-edition",
                    "https://skoda-wagner.ru/models/rapid-hockey-edition",
                    "https://www.rosso-sk.ru/press/obzor-osobennostey-serii-khokkey-edishn-ot-skoda"
                ]
            ],
            "škoda rapid hockey edition 2022" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid-hockey-edition",
                    "https://rolf-skoda.ru/models/rapid-hockey-edition",
                    "https://www.sove2u.ru/%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d1%80%d0%b0%d0%bf%d0%b8%d0%b4-%d1%85%d0%be%d0%ba%d0%ba%d0%b5%d0%b9-%d1%8d%d0%b4%d0%b8%d1%88%d0%bd-2022/",
                    "https://www.skoda-major.ru/rapid-hockey-edition/",
                    "https://skoda-avtoruss.ru/models/rapid-hockey-edition",
                    "https://www.bogemia-skd.ru/models/rapid-hockey-edition",
                    "https://skoda-favorit.ru/models/rapid-hockey-edition",
                    "https://prime-motors.ru/models/rapid-hockey-edition",
                    "https://locman-skoda.ru/models/rapid-hockey-edition",
                    "https://skoda-kuntsevo.ru/models/rapid-hockey-edition",
                    "https://autoreview.ru/news/obnovlennaya-skoda-rapid-obzavelas-versiey-hockey-edition",
                    "https://www.autocity-sk.ru/models/rapid-hockey-edition",
                    "https://carsar.su/models/rapid-hockey-edition",
                    "https://kveta-auto.ru/models/rapid-hockey-edition",
                    "https://auto.ru/catalog/cars/skoda/rapid/21005574/21005618/equipment/21005618_21515404_21031674/",
                    "https://aspec-lider.ru/models/rapid-hockey-edition",
                    "https://ringsever.ru/models/rapid-hockey-edition",
                    "https://www.skoda-podolsk.ru/models/rapid-hockey-edition",
                    "https://skoda-wagner.ru/models/rapid-hockey-edition",
                    "https://avto-bravo.ru/models/rapid-hockey-edition"
                ]
            ],
            "шкода рапид 2022 хоккей эдишн" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid-hockey-edition",
                    "https://rolf-skoda.ru/models/rapid-hockey-edition",
                    "https://www.sove2u.ru/%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d1%80%d0%b0%d0%bf%d0%b8%d0%b4-%d1%85%d0%be%d0%ba%d0%ba%d0%b5%d0%b9-%d1%8d%d0%b4%d0%b8%d1%88%d0%bd-2022/",
                    "https://www.youtube.com/watch?v=k-znkujc3y8",
                    "https://auto.ru/catalog/cars/skoda/rapid/21005574/21005618/equipment/21005618_21515404_21031674/",
                    "https://www.skoda-major.ru/rapid-hockey-edition/",
                    "https://locman-skoda.ru/models/rapid-hockey-edition",
                    "https://autoreview.ru/news/obnovlennaya-skoda-rapid-obzavelas-versiey-hockey-edition",
                    "https://www.zr.ru/content/articles/901954-dlitelnyj-test-skoda-rapid-k/",
                    "https://skoda-avtoruss.ru/models/rapid-hockey-edition",
                    "https://www.bogemia-skd.ru/models/rapid-hockey-edition",
                    "https://www.rosso-sk.ru/press/obzor-osobennostey-serii-khokkey-edishn-ot-skoda",
                    "https://carsar.su/models/rapid-hockey-edition",
                    "https://www.drive2.ru/o/b/543980905014756128/",
                    "https://www.drom.ru/catalog/skoda/rapid/294238/",
                    "https://aspec-lider.ru/models/rapid-hockey-edition",
                    "https://skoda.planeta-avto.ru/models/rapid-hockey-edition",
                    "https://skoda-wagner.ru/models/rapid-hockey-edition",
                    "https://ringsever.ru/models/rapid-hockey-edition",
                    "https://www.autoskd.ru/models/rapid-hockey-edition"
                ]
            ],
            "шкода рапид комплектация хоккей эдишн" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid-hockey-edition",
                    "https://www.drom.ru/catalog/skoda/rapid/227028/",
                    "https://auto.ru/catalog/cars/skoda/rapid/21005574/21005618/equipment/21005618_21515404_21031674/",
                    "https://rolf-skoda.ru/models/rapid-hockey-edition",
                    "https://www.drive2.ru/l/9188352/",
                    "https://locman-skoda.ru/models/rapid-hockey-edition",
                    "https://www.zr.ru/content/articles/901954-dlitelnyj-test-skoda-rapid-k/",
                    "https://www.rosso-sk.ru/press/obzor-osobennostey-serii-khokkey-edishn-ot-skoda",
                    "https://autoreview.ru/news/obnovlennaya-skoda-rapid-obzavelas-versiey-hockey-edition",
                    "https://www.skoda-major.ru/rapid-hockey-edition/",
                    "https://skoda-avtoruss.ru/models/rapid-hockey-edition",
                    "https://ringsever.ru/models/rapid-hockey-edition",
                    "https://www.skoda-vitebskiy.ru/models/rapid-hockey-edition",
                    "https://lmotors-skoda.ru/models/rapid-hockey-edition",
                    "https://avto-bravo.ru/models/rapid-hockey-edition",
                    "https://kveta-auto.ru/models/rapid-hockey-edition",
                    "https://skoda.planeta-avto.ru/models/rapid-hockey-edition",
                    "https://www.autoskd.ru/models/rapid-hockey-edition",
                    "https://skoda-kuntsevo.ru/models/rapid-hockey-edition",
                    "https://skoda.medved-vostok.ru/about/articles/1838"
                ]
            ],
            "шкода рапид комплектация хоккей эдишн цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid-hockey-edition",
                    "https://rolf-skoda.ru/models/rapid-hockey-edition",
                    "https://www.skoda-major.ru/rapid-hockey-edition/",
                    "https://auto.ru/catalog/cars/skoda/rapid/21005574/21005618/equipment/21005618_21515404_21031674/",
                    "https://skoda-avtoruss.ru/models/rapid-hockey-edition",
                    "https://www.avito.ru/rossiya/avtomobili/skoda/rapid-asgbagicaktgtg2emsjitg2urig?q=hockey+edition",
                    "https://www.bogemia-skd.ru/models/rapid-hockey-edition",
                    "https://skoda-favorit.ru/models/rapid-hockey-edition",
                    "https://www.atlant-motors.ru/models/rapid-hockey-edition",
                    "https://skoda-kuntsevo.ru/models/rapid-hockey-edition",
                    "https://www.drom.ru/catalog/skoda/rapid/227028/",
                    "https://www.autocity-sk.ru/models/rapid-hockey-edition",
                    "https://carso.ru/skoda/rapid/kit/ambition-hockey-edition",
                    "https://avisavto.ru/cars/skoda/rapid-new/komplektacii/hockey-edition",
                    "https://ringsever.ru/models/rapid-hockey-edition",
                    "https://skoda-tts.ru/models/rapid-hockey-edition",
                    "https://skoda-yug-avto.ru/models/rapid-hockey-edition",
                    "https://avto-bravo.ru/models/rapid-hockey-edition",
                    "https://skoda-orehovo.ru/models/rapid-hockey-edition",
                    "https://aspec-lider.ru/models/rapid-hockey-edition"
                ]
            ],
            "шкода рапид хоккей эдишн" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid-hockey-edition",
                    "https://rolf-skoda.ru/models/rapid-hockey-edition",
                    "https://auto.ru/catalog/cars/skoda/rapid/21005574/21005618/equipment/21005618_21515404_21031674/",
                    "https://www.drom.ru/catalog/skoda/rapid/227028/",
                    "https://www.drive2.ru/l/9188352/",
                    "https://www.zr.ru/content/articles/901954-dlitelnyj-test-skoda-rapid-k/",
                    "https://locman-skoda.ru/models/rapid-hockey-edition",
                    "https://www.skoda-major.ru/rapid-hockey-edition/",
                    "https://autoreview.ru/news/obnovlennaya-skoda-rapid-obzavelas-versiey-hockey-edition",
                    "https://skoda-avtoruss.ru/models/rapid-hockey-edition",
                    "https://www.rosso-sk.ru/press/obzor-osobennostey-serii-khokkey-edishn-ot-skoda",
                    "https://www.youtube.com/watch?v=31d05daxcss",
                    "https://www.skoda-vitebskiy.ru/models/rapid-hockey-edition",
                    "https://aspec-lider.ru/models/rapid-hockey-edition",
                    "https://ringsever.ru/models/rapid-hockey-edition",
                    "https://skoda.planeta-avto.ru/models/rapid-hockey-edition",
                    "https://www.bogemia-skd.ru/models/rapid-hockey-edition",
                    "https://skoda-kuntsevo.ru/models/rapid-hockey-edition",
                    "https://www.autoskd.ru/models/rapid-hockey-edition",
                    "https://www.avito.ru/rossiya/avtomobili?q=skoda+rapid+2016+hockey+edition"
                ]
            ],
            "шкода рапид хоккей эдишн купить" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid-hockey-edition",
                    "https://rolf-skoda.ru/models/rapid-hockey-edition",
                    "https://www.avito.ru/rossiya/avtomobili/skoda/rapid-asgbagicaktgtg2emsjitg2urig?q=hockey+edition",
                    "https://www.skoda-major.ru/rapid-hockey-edition/",
                    "https://skoda-avtoruss.ru/models/rapid-hockey-edition",
                    "https://www.bogemia-skd.ru/models/rapid-hockey-edition",
                    "https://skoda-kuntsevo.ru/models/rapid-hockey-edition",
                    "https://skoda-favorit.ru/models/rapid-hockey-edition",
                    "https://auto.ru/catalog/cars/skoda/rapid/21005574/21005618/equipment/21005618_21515404_21031674/",
                    "https://www.atlant-motors.ru/models/rapid-hockey-edition",
                    "https://www.autocity-sk.ru/models/rapid-hockey-edition",
                    "https://carso.ru/skoda/rapid/kit/ambition-hockey-edition",
                    "https://b-kredit.com/catalog/skoda/rapid_hockey_edition/",
                    "https://ac-sokolniki.ru/auto/skoda/rapid-hockey-edition/rapid-hockey-edition",
                    "https://skoda-tts.ru/models/rapid-hockey-edition",
                    "https://t-motors-skoda.ru/models/rapid-hockey-edition",
                    "https://www.bips.ru/skoda/rapid/kit/active-hockey-edition",
                    "https://skoda-orehovo.ru/models/rapid-hockey-edition",
                    "https://avto-bravo.ru/models/rapid-hockey-edition",
                    "https://www.ventus.ru/models/rapid-hockey-edition"
                ]
            ],
            "шкода рапид хоккей эдишн цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid-hockey-edition",
                    "https://rolf-skoda.ru/models/rapid-hockey-edition",
                    "https://www.skoda-major.ru/rapid-hockey-edition/",
                    "https://skoda-avtoruss.ru/models/rapid-hockey-edition",
                    "https://www.avito.ru/rossiya/avtomobili/skoda/rapid-asgbagicaktgtg2emsjitg2urig?q=hockey+edition",
                    "https://auto.ru/catalog/cars/skoda/rapid/21005574/21005618/equipment/21005618_21515404_21031674/",
                    "https://skoda-kuntsevo.ru/models/rapid-hockey-edition",
                    "https://www.bogemia-skd.ru/models/rapid-hockey-edition",
                    "https://skoda-favorit.ru/models/rapid-hockey-edition",
                    "https://www.atlant-motors.ru/models/rapid-hockey-edition",
                    "https://www.autocity-sk.ru/models/rapid-hockey-edition",
                    "https://carso.ru/skoda/rapid/kit/ambition-hockey-edition",
                    "https://www.bips.ru/skoda/rapid/kit/active-hockey-edition",
                    "https://skoda-yug-avto.ru/models/rapid-hockey-edition",
                    "https://skoda-orehovo.ru/models/rapid-hockey-edition",
                    "https://www.drom.ru/catalog/skoda/rapid/227028/",
                    "https://www.skoda-vitebskiy.ru/models/rapid-hockey-edition",
                    "https://skoda-wagner.ru/models/rapid-hockey-edition",
                    "https://aspec-lider.ru/models/rapid-hockey-edition",
                    "https://avto-bravo.ru/models/rapid-hockey-edition"
                ]
            ],
            "skoda superb combi" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb-combi",
                    "https://rolf-skoda.ru/models/superb-combi",
                    "https://auto.ru/moskva/cars/skoda/superb/all/body-wagon/",
                    "https://skoda-avtoruss.ru/models/superb-combi",
                    "https://www.drive.ru/test-drive/skoda/55acd85695a6568df0000187.html",
                    "https://www.drive2.ru/cars/skoda/superb_combi/m2329/",
                    "https://skoda-kuntsevo.ru/models/superb-combi",
                    "https://adom.ru/skoda/new-superb-combi",
                    "https://www.skoda-major.ru/superb-combi/",
                    "https://skoda-favorit.ru/models/superb-combi",
                    "https://www.kolesa.ru/test-drive/hozyajstvennik-v-smokinge-test-drajv-skoda-superb-combi",
                    "https://carso.ru/skoda/superb-combi",
                    "https://www.avito.ru/moskva/avtomobili/skoda/superb/universal-asgbaqicaktgtg2emsjitg3assgbqoa2drtmtyg",
                    "https://www.drom.ru/info/test-drive/skoda-superb-combi-45481.html",
                    "https://carsdo.ru/skoda/superb-universal/",
                    "https://www.youtube.com/watch?v=9-1redmgsho",
                    "https://www.bogemia-skd.ru/models/superb-combi",
                    "https://www.auto-dd.ru/skoda-superb-combi/",
                    "https://autospot.ru/brands/skoda/superb_combi/wagon/",
                    "https://www.autocity-sk.ru/models/superb-combi"
                ]
            ],
            "skoda superb combi купить" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb-combi/price",
                    "https://auto.ru/moskva/cars/skoda/superb/all/body-wagon/",
                    "https://rolf-skoda.ru/models/superb-combi",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/skoda/superb/universal-asgbaqicaktgtg2emsjitg3assgbqoa2drtmtyg",
                    "https://skoda-avtoruss.ru/models/superb-combi",
                    "https://skoda-kuntsevo.ru/models/superb-combi",
                    "https://skoda-favorit.ru/models/superb-combi",
                    "https://www.skoda-major.ru/superb-combi/",
                    "https://adom.ru/skoda/new-superb-combi",
                    "https://carso.ru/skoda/superb-combi",
                    "https://moscow.drom.ru/skoda/superb/wagon/",
                    "https://www.autoskd.ru/models/superb-combi/price",
                    "https://moscow.autovsalone.ru/cars/skoda/superb-combi",
                    "https://auto-kay.ru/cars/skoda/superb-combi-new/",
                    "https://www.bogemia-skd.ru/models/superb-combi",
                    "https://carsdo.ru/skoda/superb-universal/moscow/",
                    "https://www.bips.ru/skoda/newsuperb-combi",
                    "https://abc-auto.ru/skoda/superb-combi-new/",
                    "https://www.autocity-sk.ru/models/superb-combi",
                    "https://riaauto.ru/skoda/superb-combi"
                ]
            ],
            "skoda superb combi цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb-combi/price",
                    "https://rolf-skoda.ru/models/superb-combi",
                    "https://auto.ru/moskva/cars/skoda/superb/all/body-wagon/",
                    "https://skoda-avtoruss.ru/models/superb-combi",
                    "https://carso.ru/skoda/superb-combi",
                    "https://www.skoda-major.ru/superb-combi/",
                    "https://skoda-kuntsevo.ru/models/superb-combi",
                    "https://adom.ru/skoda/new-superb-combi",
                    "https://skoda-favorit.ru/models/superb-combi",
                    "https://www.atlant-motors.ru/models/superb-combi/price",
                    "https://carsdo.ru/skoda/superb-universal/",
                    "https://moscow.autovsalone.ru/cars/skoda/superb-combi",
                    "https://www.bogemia-skd.ru/models/superb-combi",
                    "https://auto-kay.ru/cars/skoda/superb-combi-new/",
                    "https://www.drive.ru/brands/skoda/models/2019/superb_combi",
                    "https://auto-leon.ru/skoda/superb-combi-new/",
                    "https://www.autocity-sk.ru/models/superb-combi",
                    "https://abc-auto.ru/skoda/superb-combi-new/",
                    "https://riaauto.ru/skoda/superb-combi",
                    "https://www.bips.ru/skoda/superb-combi"
                ]
            ],
            "škoda superb combi 2022" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb-combi",
                    "https://skoda-avtoruss.ru/models/superb-combi",
                    "https://rolf-skoda.ru/models/superb-combi",
                    "https://auto.ru/moskva/cars/skoda/superb/2022-year/all/",
                    "https://www.skoda-major.ru/superb-combi/",
                    "https://skoda-kuntsevo.ru/models/superb-combi",
                    "https://sigma-skoda.ru/models/superb-combi",
                    "https://www.bogemia-skd.ru/models/superb-combi",
                    "https://carsdo.ru/skoda/superb-universal/",
                    "https://adom.ru/skoda/new-superb-combi",
                    "https://carso.ru/skoda/superb-combi",
                    "https://auto-kay.ru/cars/skoda/superb-combi-new/",
                    "https://www.allcarz.ru/skoda-superb-3-combi/",
                    "https://moscow.autovsalone.ru/cars/skoda/superb-combi",
                    "https://www.autoskd.ru/models/superb-combi/price",
                    "https://strela-avto.ru/models/superb-combi",
                    "https://autoevrazia.ru/models/superb-combi",
                    "https://skoda-forward.ru/models/superb-combi",
                    "https://msmotors.ru/models/superb-combi",
                    "https://otto-car.ru/models/superb-combi"
                ]
            ],
            "купить шкоду суперб комби" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb-combi/price",
                    "https://auto.ru/moskva/cars/skoda/superb/all/body-wagon/",
                    "https://m.avito.ru/moskva/avtomobili/skoda/superb/universal-asgbaqicaktgtg2emsjitg3assgbqoa2drtmtyg",
                    "https://rolf-skoda.ru/models/superb-combi",
                    "https://skoda-avtoruss.ru/models/superb-combi",
                    "https://adom.ru/skoda/new-superb-combi",
                    "https://skoda-favorit.ru/models/superb-combi",
                    "https://skoda-kuntsevo.ru/models/superb-combi",
                    "https://carso.ru/skoda/superb-combi",
                    "https://moscow.drom.ru/skoda/superb/wagon/",
                    "https://www.skoda-major.ru/superb-combi/",
                    "https://auto-kay.ru/cars/skoda/superb-combi-new/",
                    "https://www.bogemia-skd.ru/models/superb-combi",
                    "https://moskva.mbib.ru/skoda/superb/universal",
                    "https://carsdo.ru/skoda/superb-universal/moscow/",
                    "https://www.bips.ru/skoda/superb-combi",
                    "https://moscow.autovsalone.ru/cars/skoda/superb-combi",
                    "https://riaauto.ru/skoda/superb-combi",
                    "https://abc-auto.ru/skoda/superb-combi-new/",
                    "https://www.autoskd.ru/models/superb-combi"
                ]
            ],
            "новый шкода суперб комби" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb-combi",
                    "https://rolf-skoda.ru/models/superb-combi",
                    "https://skoda-avtoruss.ru/models/superb-combi",
                    "https://adom.ru/skoda/new-superb-combi",
                    "https://carso.ru/skoda/superb-combi",
                    "https://skoda-kuntsevo.ru/models/superb-combi",
                    "https://www.skoda-major.ru/superb-combi/",
                    "https://carsdo.ru/skoda/superb-universal/",
                    "https://www.drive.ru/brands/skoda/models/2019/superb_combi",
                    "https://skoda-favorit.ru/models/superb-combi",
                    "https://www.auto-dd.ru/skoda-superb-combi/",
                    "https://moscow.autovsalone.ru/cars/skoda/superb-combi",
                    "https://skoda-ap.ru/models/superb-combi/price",
                    "https://www.bogemia-skd.ru/models/superb-combi",
                    "https://www.autocity-sk.ru/models/superb-combi",
                    "https://www.kolesa.ru/test-drive/hozyajstvennik-v-smokinge-test-drajv-skoda-superb-combi",
                    "https://auto-kay.ru/cars/skoda/superb-combi-new/",
                    "https://www.youtube.com/watch?v=9-1redmgsho",
                    "https://interkar.ru/models/superb-combi/price",
                    "https://www.drive2.ru/b/459655235112021011/"
                ]
            ],
            "шкода суперб комби" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb-combi",
                    "https://rolf-skoda.ru/models/superb-combi",
                    "https://auto.ru/moskva/cars/skoda/superb/all/body-wagon/",
                    "https://skoda-avtoruss.ru/models/superb-combi",
                    "https://www.drive.ru/test-drive/skoda/55acd85695a6568df0000187.html",
                    "https://adom.ru/skoda/new-superb-combi",
                    "https://skoda-kuntsevo.ru/models/superb-combi",
                    "https://www.kolesa.ru/test-drive/hozyajstvennik-v-smokinge-test-drajv-skoda-superb-combi",
                    "https://skoda-favorit.ru/models/superb-combi",
                    "https://www.drive2.ru/cars/skoda/superb_combi/m2329/",
                    "https://www.skoda-major.ru/superb-combi/",
                    "https://carso.ru/skoda/superb-combi",
                    "https://www.avito.ru/moskva/avtomobili/skoda/superb/universal-asgbaqicaktgtg2emsjitg3assgbqoa2drtmtyg",
                    "https://carsdo.ru/skoda/superb-universal/",
                    "https://www.drom.ru/info/test-drive/skoda-superb-combi-45481.html",
                    "https://skoda-s-auto.ru/models/superb-combi/price",
                    "https://www.bogemia-skd.ru/models/superb-combi",
                    "https://www.youtube.com/watch?v=9-1redmgsho",
                    "https://www.auto-dd.ru/skoda-superb-combi/",
                    "https://skoda-wagner.ru/models/superb-combi"
                ]
            ],
            "шкода суперб комби 2022" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb-combi",
                    "https://skoda-avtoruss.ru/models/superb-combi",
                    "https://rolf-skoda.ru/models/superb-combi",
                    "https://auto.ru/moskva/cars/skoda/superb/2022-year/all/",
                    "https://adom.ru/skoda/new-superb-combi",
                    "https://carsdo.ru/skoda/superb-universal/",
                    "https://www.skoda-major.ru/superb-combi/",
                    "https://carso.ru/skoda/superb-combi",
                    "https://www.allcarz.ru/skoda-superb-3-combi/",
                    "https://sigma-skoda.ru/models/superb-combi",
                    "https://skoda-s-auto.ru/models/superb-combi",
                    "https://www.bogemia-skd.ru/models/superb-combi",
                    "https://auto-kay.ru/cars/skoda/superb-combi-new/",
                    "https://moscow.autovsalone.ru/cars/skoda/superb-combi",
                    "https://skoda-kuntsevo.ru/models/superb-combi",
                    "https://naavtotrasse.ru/skoda/skoda-superb-2022.html",
                    "https://auto.ironhorse.ru/skoda-superb-3-combi_11494.html",
                    "https://autoevrazia.ru/models/superb-combi",
                    "https://moravia-motors.ru/models/superb-combi/price",
                    "https://auto-leon.ru/skoda/superb-combi-new/"
                ]
            ],
            "шкода суперб комби цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb-combi/price",
                    "https://rolf-skoda.ru/models/superb-combi",
                    "https://auto.ru/moskva/cars/skoda/superb/all/body-wagon/",
                    "https://adom.ru/skoda/new-superb-combi",
                    "https://skoda-avtoruss.ru/models/superb-combi",
                    "https://m.avito.ru/moskva/avtomobili/skoda/superb/universal-asgbaqicaktgtg2emsjitg3assgbqoa2drtmtyg",
                    "https://skoda-kuntsevo.ru/models/superb-combi",
                    "https://carso.ru/skoda/superb-combi",
                    "https://www.skoda-major.ru/superb-combi/",
                    "https://skoda-favorit.ru/models/superb-combi",
                    "https://carsdo.ru/skoda/superb-universal/",
                    "https://moscow.drom.ru/skoda/superb/wagon/",
                    "https://www.atlant-motors.ru/models/superb-combi/price",
                    "https://moscow.autovsalone.ru/cars/skoda/superb-combi",
                    "https://www.drive.ru/brands/skoda/models/2019/superb_combi",
                    "https://www.bogemia-skd.ru/models/superb-combi",
                    "https://auto-kay.ru/cars/skoda/superb-combi-new/",
                    "https://www.bips.ru/skoda/superb-combi",
                    "https://auto-leon.ru/skoda/superb-combi-new/",
                    "https://interkar.ru/models/superb-combi/price"
                ]
            ],
            "skoda авто с пробегом" => [
                "sites" => [
                    "https://auto.ru/moskva/cars/skoda/used/",
                    "https://www.avito.ru/moskva/avtomobili/s_probegom/skoda-asgbagicaksgfmjmaec2dz6zka",
                    "https://www.skoda-major.ru/used/",
                    "https://moscow.drom.ru/skoda/used/all/",
                    "https://rolf-probeg.ru/cars/skoda/",
                    "https://favorit-motors.ru/catalog/used/skoda/",
                    "https://www.avtogermes.ru/sale/second_hand/skoda/",
                    "https://avtomir.ru/used-cars/skoda/",
                    "https://www.ascgroup.ru/cars/used/skoda/",
                    "https://autospot.ru/used-car/skoda/",
                    "https://used-cars.ru/auto/skoda",
                    "https://cars.rolf-skoda.ru/used/",
                    "https://youauto.ru/used/skoda",
                    "https://business-car.ru/cars/used/skoda/",
                    "https://avilon.ru/catalog/cars/used/filter/mark-is-skoda/apply/",
                    "https://www.bips.ru/used/skoda",
                    "https://moscow.110km.ru/vybor/skoda/kupit-s-probegom-poderzhannie-moscow/",
                    "https://cars.avtocod.ru/moskva/avto-s-probegom/skoda/",
                    "https://incom-auto-expert.ru/cars/skoda/",
                    "https://moskva.mbib.ru/skoda/used"
                ]
            ],
            "skoda с пробегом" => [
                "sites" => [
                    "https://auto.ru/moskva/cars/skoda/used/",
                    "https://www.skoda-major.ru/used/",
                    "https://www.avito.ru/moskva/avtomobili/s_probegom/skoda-asgbagicaksgfmjmaec2dz6zka",
                    "https://rolf-probeg.ru/cars/skoda/",
                    "https://moscow.drom.ru/skoda/used/",
                    "https://favorit-motors.ru/catalog/used/skoda/",
                    "https://used-cars.ru/auto/skoda",
                    "https://avtomir.ru/used-cars/skoda/",
                    "https://www.avtogermes.ru/sale/second_hand/skoda/",
                    "https://autospot.ru/used-car/skoda/",
                    "https://www.ascgroup.ru/cars/used/skoda/",
                    "https://urus-expert.com/catalog/skoda",
                    "https://moscow.110km.ru/vybor/skoda/kupit-s-probegom-poderzhannie-moscow/",
                    "https://avilon.ru/catalog/cars/used/filter/mark-is-skoda/apply/",
                    "https://incom-auto-expert.ru/cars/skoda/",
                    "https://msk.keyauto-probeg.ru/used/skoda/",
                    "https://business-car.ru/cars/used/skoda/",
                    "https://moskva.mbib.ru/skoda/used",
                    "https://cars.avtocod.ru/moskva/avto-s-probegom/skoda/",
                    "https://cars.rolf-skoda.ru/used/"
                ]
            ],
            "skoda с пробегом цена" => [
                "sites" => [
                    "https://auto.ru/moskva/cars/skoda/used/",
                    "https://www.avito.ru/moskva/avtomobili/s_probegom/skoda-asgbagicaksgfmjmaec2dz6zka",
                    "https://moscow.drom.ru/skoda/used/all/",
                    "https://www.major-expert.ru/cars/moscow/skoda/",
                    "https://rolf-probeg.ru/cars/skoda/",
                    "https://favorit-motors.ru/catalog/used/skoda/",
                    "https://www.avtogermes.ru/sale/second_hand/skoda/",
                    "https://moscow.110km.ru/vybor/skoda/kupit-s-probegom-poderzhannie-moscow/",
                    "https://avtomir.ru/used-cars/skoda/",
                    "https://moskva.mbib.ru/skoda/used",
                    "https://autospot.ru/used-car/skoda/",
                    "https://used-cars.ru/auto/skoda",
                    "https://urus-expert.com/catalog/skoda",
                    "https://www.ascgroup.ru/cars/used/skoda/",
                    "https://cars.rolf-skoda.ru/used/",
                    "https://incom-auto-expert.ru/cars/skoda/",
                    "https://avilon.ru/catalog/cars/used/filter/mark-is-skoda/apply/",
                    "https://business-car.ru/cars/used/skoda/",
                    "https://msk.keyauto-probeg.ru/used/skoda/",
                    "https://youauto.ru/used/skoda"
                ]
            ],
            "авто с пробегом шкода" => [
                "sites" => [
                    "https://auto.ru/moskva/cars/skoda/used/",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/s_probegom/skoda-asgbagicaksgfmjmaec2dz6zka",
                    "https://moscow.drom.ru/skoda/used/all/",
                    "https://www.skoda-major.ru/used/",
                    "https://rolf-probeg.ru/cars/skoda/",
                    "https://favorit-motors.ru/catalog/used/skoda/",
                    "https://www.ascgroup.ru/cars/used/skoda/",
                    "https://www.avtogermes.ru/sale/second_hand/skoda/",
                    "https://used-cars.ru/auto/skoda",
                    "https://avtomir.ru/used-cars/skoda/",
                    "https://autospot.ru/used-car/skoda/",
                    "https://youauto.ru/used/skoda",
                    "https://moscow.110km.ru/vybor/skoda/kupit-s-probegom-poderzhannie-moscow/",
                    "https://www.bips.ru/used/skoda",
                    "https://avilon.ru/catalog/cars/used/filter/mark-is-skoda/apply/",
                    "https://incom-auto-expert.ru/cars/skoda/",
                    "https://moskva.mbib.ru/skoda/used",
                    "https://cars.rolf-skoda.ru/used/",
                    "https://msk.keyauto-probeg.ru/used/skoda/",
                    "https://business-car.ru/cars/used/skoda/"
                ]
            ],
            "автомобили шкода с пробегом" => [
                "sites" => [
                    "https://auto.ru/moskva/cars/skoda/used/",
                    "https://www.avito.ru/moskva/avtomobili/s_probegom/skoda-asgbagicaksgfmjmaec2dz6zka",
                    "https://moscow.drom.ru/skoda/used/all/",
                    "https://www.skoda-major.ru/used/",
                    "https://rolf-probeg.ru/cars/skoda/",
                    "https://favorit-motors.ru/catalog/used/skoda/",
                    "https://www.ascgroup.ru/cars/used/skoda/",
                    "https://used-cars.ru/auto/skoda",
                    "https://www.avtogermes.ru/sale/second_hand/skoda/",
                    "https://avtomir.ru/used-cars/skoda/",
                    "https://moscow.110km.ru/vybor/skoda/kupit-s-probegom-poderzhannie-moscow/",
                    "https://cars.rolf-skoda.ru/used/",
                    "https://autospot.ru/used-car/skoda/",
                    "https://urus-expert.com/catalog/skoda",
                    "https://business-car.ru/cars/used/skoda/",
                    "https://avilon.ru/catalog/cars/used/filter/mark-is-skoda/apply/",
                    "https://incom-auto-expert.ru/cars/skoda/",
                    "https://youauto.ru/used/skoda",
                    "https://cars.avtocod.ru/moskva/avto-s-probegom/skoda/",
                    "https://moskva.mbib.ru/skoda/used"
                ]
            ],
            "купить skoda с пробегом" => [
                "sites" => [
                    "https://auto.ru/moskva/cars/skoda/used/",
                    "https://www.avito.ru/moskva/avtomobili/s_probegom/skoda-asgbagicaksgfmjmaec2dz6zka",
                    "https://moscow.drom.ru/skoda/used/",
                    "https://www.major-expert.ru/cars/moscow/skoda/",
                    "https://rolf-probeg.ru/cars/skoda/",
                    "https://favorit-motors.ru/catalog/used/skoda/",
                    "https://used-cars.ru/auto/skoda",
                    "https://moscow.110km.ru/vybor/skoda/kupit-s-probegom-poderzhannie-moscow/",
                    "https://www.avtogermes.ru/sale/second_hand/skoda/",
                    "https://urus-expert.com/catalog/skoda",
                    "https://avtomir.ru/used-cars/skoda/",
                    "https://autospot.ru/used-car/skoda/",
                    "https://www.ascgroup.ru/cars/used/skoda/",
                    "https://incom-auto-expert.ru/cars/skoda/",
                    "https://youauto.ru/used/skoda",
                    "https://avilon.ru/catalog/cars/used/filter/mark-is-skoda/apply/",
                    "https://moskva.mbib.ru/skoda/used",
                    "https://business-car.ru/cars/used/skoda/",
                    "https://msk.keyauto-probeg.ru/used/skoda/",
                    "https://cars.rolf-skoda.ru/used/"
                ]
            ],
            "купить авто с пробегом шкода" => [
                "sites" => [
                    "https://auto.ru/moskva/cars/skoda/used/",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/s_probegom/skoda-asgbagicaksgfmjmaec2dz6zka",
                    "https://moscow.drom.ru/skoda/used/all/",
                    "https://www.major-expert.ru/cars/moscow/skoda/",
                    "https://youauto.ru/used/skoda",
                    "https://rolf-probeg.ru/cars/skoda/",
                    "https://favorit-motors.ru/catalog/used/skoda/",
                    "https://used-cars.ru/auto/skoda",
                    "https://www.avtogermes.ru/sale/second_hand/skoda/",
                    "https://moscow.110km.ru/vybor/skoda/kupit-s-probegom-poderzhannie-moscow/",
                    "https://www.ascgroup.ru/cars/used/skoda/",
                    "https://autospot.ru/used-car/skoda/",
                    "https://cars.rolf-skoda.ru/used/",
                    "https://www.bips.ru/used/skoda",
                    "https://incom-auto-expert.ru/cars/skoda/",
                    "https://avilon.ru/catalog/cars/used/filter/mark-is-skoda/apply/",
                    "https://msk.keyauto-probeg.ru/used/skoda/",
                    "https://cars.avtocod.ru/moskva/avto-s-probegom/skoda/",
                    "https://urus-expert.com/catalog/skoda",
                    "https://avtomir.ru/used-cars/skoda/"
                ]
            ],
            "купить автомобиль шкода с пробегом" => [
                "sites" => [
                    "https://auto.ru/moskva/cars/skoda/used/",
                    "https://www.avito.ru/moskva_i_mo/avtomobili/s_probegom/skoda-asgbagicaksgfmjmaec2dz6zka",
                    "https://moscow.drom.ru/skoda/used/all/",
                    "https://www.major-expert.ru/cars/moscow/skoda/",
                    "https://favorit-motors.ru/catalog/used/skoda/",
                    "https://rolf-probeg.ru/cars/skoda/",
                    "https://used-cars.ru/auto/skoda",
                    "https://youauto.ru/used/skoda",
                    "https://moscow.110km.ru/vybor/skoda/kupit-s-probegom-poderzhannie-moscow/",
                    "https://www.avtogermes.ru/sale/second_hand/skoda/",
                    "https://www.ascgroup.ru/cars/used/skoda/",
                    "https://autospot.ru/used-car/skoda/",
                    "https://business-car.ru/cars/used/skoda/",
                    "https://incom-auto-expert.ru/cars/skoda/",
                    "https://moskva.mbib.ru/skoda/used",
                    "https://avtomir.ru/used-cars/skoda/",
                    "https://avilon-trade.ru/catalog/skoda/",
                    "https://msk.keyauto-probeg.ru/used/skoda/",
                    "https://avilon.ru/catalog/cars/used/filter/mark-is-skoda/apply/",
                    "https://cars.rolf-skoda.ru/used/"
                ]
            ],
            "продажа автомобилей шкода с пробегом" => [
                "sites" => [
                    "https://auto.ru/moskva/cars/skoda/used/",
                    "https://www.avito.ru/moskva/avtomobili/s_probegom/skoda-asgbagicaksgfmjmaec2dz6zka",
                    "https://moscow.drom.ru/skoda/used/all/",
                    "https://www.major-expert.ru/cars/moscow/skoda/",
                    "https://rolf-probeg.ru/cars/skoda/",
                    "https://favorit-motors.ru/catalog/used/skoda/",
                    "https://www.avtogermes.ru/sale/second_hand/skoda/",
                    "https://used-cars.ru/auto/skoda",
                    "https://moskva.mbib.ru/skoda/used",
                    "https://moscow.110km.ru/vybor/skoda/kupit-s-probegom-poderzhannie-moscow/",
                    "https://www.ascgroup.ru/cars/used/skoda/",
                    "https://autospot.ru/used-car/skoda/",
                    "https://avtomir.ru/used-cars/skoda/",
                    "https://urus-expert.com/catalog/skoda",
                    "https://avilon.ru/catalog/cars/used/filter/mark-is-skoda/apply/",
                    "https://incom-auto-expert.ru/cars/skoda/",
                    "https://business-car.ru/cars/used/skoda/",
                    "https://cars.avtocod.ru/moskva/avto-s-probegom/skoda/",
                    "https://cars.rolf-skoda.ru/used/",
                    "https://youauto.ru/used/skoda"
                ]
            ],
            "дилер шкода старый оскол" => [
                "sites" => [
                    "https://moravia-motors.ru/",
                    "https://yandex.ru/maps/org/koda_moraviya_motors/1301511506/",
                    "https://vk.com/skodabelgorod31",
                    "https://cars.skoda-avto.ru/?dealerid=rusc01749",
                    "https://skoda-oskol.tmgauto.ru/",
                    "https://2gis.ru/staroskol/firm/8444777582327660",
                    "https://auto.drom.ru/moravia_center/",
                    "https://stariy-oskol.autovsalone.ru/cars/sellers/moraviya-motors-staryy-oskol/lineup",
                    "https://auto.ru/diler-oficialniy/cars/all/moraviya_centr_stariy_oskol_skoda/",
                    "http://www.skodamir.ru/diler/2123-skoda-staryj-oskol.html",
                    "https://auto.catalogd.ru/staryj-oskol/moraviya_centr_stariy_oskol_skoda/",
                    "https://oskol.zoon.ru/autoservice/ofitsialnyj_diler_skoda_moraviya_motors/",
                    "https://belspravka.ru/screen/2/directory?fid=500000015102",
                    "https://bezrulya.ru/dealers/list/skoda/1731/",
                    "https://staroskol.flamp.ru/firm/moraviya_motors_ooo_oficialnyjj_diler_skoda-8444777582327660",
                    "https://mestam.info/ru/starii-oskol/mesto/3399560-moraviya-centr-oficialnii-diler-skoda-prospekt-alekseya-ugarova-9",
                    "https://autosalon-s.ru/avtosalony/staryj-oskol/avantazh-evro-motors-skoda-staryj-oskol",
                    "https://staryy-oskol.110km.ru/dilery-salony/moraviacenter.html",
                    "https://totadres.ru/staryy_oskol/org/moraviya_motors/2669860",
                    "http://carscan24.ru/dilers/skoda/stoskol/moraviya-centr/"
                ]
            ],
            "шкода оскол" => [
                "sites" => [
                    "https://moravia-motors.ru/",
                    "https://cars.skoda-avto.ru/?dealerid=rusc01749",
                    "https://auto.ru/staryy_oskol/cars/skoda/all/",
                    "https://yandex.ru/maps/org/koda_moraviya_motors/1301511506/",
                    "https://vk.com/skodabelgorod31",
                    "https://www.avito.ru/staryy_oskol/avtomobili/skoda-asgbagicautgtg2emsg",
                    "https://stariy-oskol.drom.ru/skoda/",
                    "https://stariy-oskol.autovsalone.ru/cars/skoda",
                    "https://skoda-oskol.tmgauto.ru/",
                    "https://2gis.ru/staroskol/firm/8444777582327660",
                    "https://staryy-oskol.mbib.ru/skoda/octavia/used",
                    "https://staryy-oskol.110km.ru/prodazha/skoda/",
                    "https://auto.catalogd.ru/staryj-oskol/moraviya_centr_stariy_oskol_skoda/",
                    "https://staryy-oskol.b-kredit.com/catalog/skoda/",
                    "http://www.skodamir.ru/diler/2123-skoda-staryj-oskol.html",
                    "http://stariy-oskol.lst-group.ru/new/skoda/",
                    "https://staryj-oskol.ab-club.ru/catalog/skoda/",
                    "https://trinity-motors.ru/autocenter_skoda",
                    "https://oskol.zoon.ru/autoservice/type/skoda/",
                    "https://stariy-oskol.cardana.ru/auto/models/skoda.html"
                ]
            ],
            "шкода оскол официальный дилер" => [
                "sites" => [
                    "https://moravia-motors.ru/",
                    "https://vk.com/skodabelgorod31",
                    "https://cars.skoda-avto.ru/?dealerid=rusc01749",
                    "https://yandex.ru/maps/org/koda_moraviya_motors/1301511506/",
                    "https://skoda-oskol.tmgauto.ru/",
                    "https://2gis.ru/staroskol/firm/8444777582327660",
                    "https://auto.drom.ru/moravia_center/",
                    "https://stariy-oskol.autovsalone.ru/cars/sellers/moraviya-motors-staryy-oskol/lineup",
                    "https://auto.ru/diler-oficialniy/cars/all/moraviya_centr_stariy_oskol_skoda/",
                    "https://auto.catalogd.ru/staryj-oskol/moraviya_centr_stariy_oskol_skoda/",
                    "http://www.skodamir.ru/diler/2123-skoda-staryj-oskol.html",
                    "https://oskol.zoon.ru/autoservice/ofitsialnyj_diler_skoda_moraviya_motors/",
                    "https://bezrulya.ru/dealers/list/skoda/1731/",
                    "https://belspravka.ru/screen/2/directory?fid=500000015102",
                    "http://carscan24.ru/dilers/skoda/stoskol/moraviya-centr/",
                    "https://bigspravka.ru/staryj_oskol/avtoservisy/moraviya_motors/",
                    "https://mestam.info/ru/starii-oskol/mesto/3399560-moraviya-centr-oficialnii-diler-skoda-prospekt-alekseya-ugarova-9",
                    "https://totadres.ru/staryy_oskol/org/moraviya_motors/2669860",
                    "https://stary-oskol.cataloxy.ru/firms/www.moravia-center.ru.htm",
                    "https://staryy-oskol.110km.ru/dilery-salony/moraviacenter.html"
                ]
            ],
            "шкода старый оскол" => [
                "sites" => [
                    "https://moravia-motors.ru/",
                    "https://auto.ru/staryy_oskol/cars/skoda/all/",
                    "https://cars.skoda-avto.ru/?dealerid=rusc01749",
                    "https://vk.com/skodabelgorod31",
                    "https://yandex.ru/maps/org/koda_moraviya_motors/1301511506/",
                    "https://www.avito.ru/staryy_oskol/avtomobili/skoda-asgbagicautgtg2emsg",
                    "https://stariy-oskol.drom.ru/skoda/",
                    "https://stariy-oskol.autovsalone.ru/cars/skoda",
                    "https://skoda-oskol.tmgauto.ru/",
                    "https://2gis.ru/staroskol/firm/8444777582327660",
                    "https://staryy-oskol.mbib.ru/skoda",
                    "https://staryy-oskol.110km.ru/prodazha/skoda/",
                    "https://oskol.zoon.ru/autoservice/type/avtosalon-skoda/",
                    "https://staryj-oskol.ab-club.ru/catalog/skoda/",
                    "https://auto.catalogd.ru/staryj-oskol/moraviya_centr_stariy_oskol_skoda/",
                    "https://staryy-oskol.b-kredit.com/catalog/skoda/",
                    "https://trinity-motors.ru/autocenter_skoda",
                    "http://www.skodamir.ru/diler/2123-skoda-staryj-oskol.html",
                    "http://stariy-oskol.lst-group.ru/new/skoda/",
                    "https://www.drive2.ru/cars/skoda/?city=35861"
                ]
            ],
            "шкода старый оскол официальный дилер" => [
                "sites" => [
                    "https://moravia-motors.ru/",
                    "https://auto.moravia-motors.ru/index.php",
                    "https://vk.com/skodabelgorod31",
                    "https://yandex.ru/maps/org/koda_moraviya_motors/1301511506/",
                    "https://cars.skoda-avto.ru/?dealerid=rusc01749",
                    "https://skoda-oskol.tmgauto.ru/",
                    "https://2gis.ru/staroskol/firm/8444777582327660",
                    "https://stariy-oskol.autovsalone.ru/cars/sellers/moraviya-motors-staryy-oskol/lineup",
                    "https://auto.drom.ru/moravia_center/",
                    "https://auto.ru/diler-oficialniy/cars/all/moraviya_centr_stariy_oskol_skoda/",
                    "https://auto.catalogd.ru/staryj-oskol/moraviya_centr_stariy_oskol_skoda/",
                    "http://www.skodamir.ru/diler/2123-skoda-staryj-oskol.html",
                    "https://bezrulya.ru/dealers/list/skoda/1731/",
                    "https://dilert.ru/skoda/skoda-staryj-oskol/moraviya_centr_stariy_oskol_skoda/",
                    "https://totadres.ru/staryy_oskol/org/moraviya_motors/2669860",
                    "https://belspravka.ru/screen/2/directory?fid=500000015102",
                    "https://bigspravka.ru/staryj_oskol/avtoservisy/moraviya_motors/",
                    "https://mestam.info/ru/starii-oskol/mesto/3399560-moraviya-centr-oficialnii-diler-skoda-prospekt-alekseya-ugarova-9",
                    "http://carscan24.ru/dilers/skoda/stoskol/moraviya-centr/",
                    "https://autosalon-s.ru/avtosalony/staryj-oskol/avantazh-evro-motors-skoda-staryj-oskol"
                ]
            ],
            "шкода старый оскол официальный дилер цены" => [
                "sites" => [
                    "https://moravia-motors.ru/",
                    "https://cars.skoda-avto.ru/?dealerid=rusc01749",
                    "https://vk.com/skodabelgorod31",
                    "https://stariy-oskol.autovsalone.ru/cars/sellers/moraviya-motors-staryy-oskol/lineup",
                    "https://skoda-oskol.tmgauto.ru/",
                    "https://auto.drom.ru/moravia_center/",
                    "https://auto.ru/diler-oficialniy/cars/all/moraviya_centr_stariy_oskol_skoda/",
                    "https://dilert.ru/skoda/skoda-staryj-oskol/moraviya_centr_stariy_oskol_skoda/",
                    "https://yandex.ru/maps/org/koda_moraviya_motors/1301511506/",
                    "https://auto.catalogd.ru/staryj-oskol/moraviya_centr_stariy_oskol_skoda/",
                    "https://www.avito.ru/staryy_oskol/avtomobili/skoda-asgbagicautgtg2emsg",
                    "https://bezrulya.ru/dealers/list/skoda/1731/",
                    "https://avto-dilery.ru/shkoda-staryj-oskol/",
                    "https://autosalon-s.ru/avtosalony/staryj-oskol/avantazh-evro-motors-skoda-staryj-oskol",
                    "http://www.skodamir.ru/diler/2123-skoda-staryj-oskol.html",
                    "http://carscan24.ru/dilers/skoda/stoskol/moraviya-centr/",
                    "https://autoleak.ru/dealers/skoda/staryy-oskol/avantazh-evro-motors-metallurgov-d-5/",
                    "https://staryy-oskol.b-kredit.com/catalog/skoda/",
                    "http://stariy-oskol.lst-group.ru/auto/skoda/",
                    "https://staryy-oskol.110km.ru/vybor/skoda/kupit-v-salone-u-dilera-staryy-oskol/"
                ]
            ],
            "шкода старый оскол официальный сайт" => [
                "sites" => [
                    "https://moravia-motors.ru/",
                    "https://auto.moravia-motors.ru/",
                    "https://vk.com/skodabelgorod31",
                    "https://cars.skoda-avto.ru/?dealerid=rusc01749",
                    "https://skoda-oskol.tmgauto.ru/",
                    "https://yandex.ru/maps/org/koda_moraviya_motors/1301511506/",
                    "https://auto.drom.ru/moravia_center/",
                    "https://auto.ru/staryy_oskol/cars/skoda/all/",
                    "https://stariy-oskol.autovsalone.ru/cars/skoda",
                    "https://auto.catalogd.ru/staryj-oskol/moraviya_centr_stariy_oskol_skoda/",
                    "https://2gis.ru/staroskol/firm/8444777582327660",
                    "http://www.skodamir.ru/diler/2123-skoda-staryj-oskol.html",
                    "https://www.avito.ru/staryy_oskol/avtomobili/skoda-asgbagicautgtg2emsg",
                    "http://carscan24.ru/dilers/skoda/stoskol/moraviya-centr/",
                    "https://autosalon-s.ru/avtosalony/staryj-oskol/avantazh-evro-motors-skoda-staryj-oskol",
                    "https://bezrulya.ru/dealers/list/skoda/1731/",
                    "https://stary-oskol.cataloxy.ru/firms/www.moravia-center.ru.htm",
                    "https://belspravka.ru/screen/2/directory?fid=500000015102",
                    "https://dilert.ru/skoda/skoda-staryj-oskol/moraviya_centr_stariy_oskol_skoda/",
                    "https://bigspravka.ru/staryj_oskol/avtoservisy/moraviya_motors/"
                ]
            ],
            "шкода старый оскол цена" => [
                "sites" => [
                    "https://auto.ru/staryy_oskol/cars/skoda/all/",
                    "https://moravia-motors.ru/",
                    "https://www.avito.ru/staryy_oskol/avtomobili/skoda-asgbagicautgtg2emsg",
                    "https://stariy-oskol.drom.ru/skoda/",
                    "https://stariy-oskol.autovsalone.ru/cars/skoda",
                    "https://cars.skoda-avto.ru/?dealerid=rusc01749",
                    "https://skoda-oskol.tmgauto.ru/",
                    "https://staryy-oskol.110km.ru/vybor/skoda/kupit-novie-staryy-oskol/",
                    "https://staryy-oskol.mbib.ru/skoda",
                    "https://staryj-oskol.ab-club.ru/catalog/skoda/",
                    "https://staryy-oskol.b-kredit.com/catalog/skoda/",
                    "https://vk.com/skodabelgorod31",
                    "http://stariy-oskol.lst-group.ru/new/skoda/",
                    "https://stariy-oskol.cardana.ru/auto/models/skoda.html",
                    "https://v-starom-oskole.kupit-auto.com/new/skoda",
                    "https://avto-dilery.ru/shkoda-staryj-oskol/",
                    "https://auto.catalogd.ru/staryj-oskol/moraviya_centr_stariy_oskol_skoda/",
                    "https://dilert.ru/skoda/skoda-staryj-oskol/moraviya_centr_stariy_oskol_skoda/",
                    "https://stariy-oskol.newautosalon.ru/kodiaq/",
                    "https://www.indexus.ru/staryy_oskol/transport/avtomobili/skoda"
                ]
            ],
            "шкода купить старый оскол" => [
                "sites" => [
                    "https://www.avito.ru/staryy_oskol/avtomobili/skoda-asgbagicautgtg2emsg",
                    "https://auto.ru/staryy_oskol/cars/skoda/all/",
                    "https://moravia-motors.ru/",
                    "https://stariy-oskol.drom.ru/skoda/",
                    "https://cars.skoda-avto.ru/?dealerid=rusc01749",
                    "https://stariy-oskol.autovsalone.ru/cars/skoda",
                    "https://staryy-oskol.110km.ru/prodazha/skoda/",
                    "https://skoda-oskol.tmgauto.ru/",
                    "https://staryy-oskol.mbib.ru/skoda",
                    "https://staryj-oskol.ab-club.ru/catalog/skoda/",
                    "https://vk.com/skodabelgorod31",
                    "https://staryy-oskol.b-kredit.com/catalog/skoda/",
                    "https://v-starom-oskole.kupit-auto.com/new/skoda",
                    "http://stariy-oskol.lst-group.ru/new/skoda/",
                    "https://www.indexus.ru/staryy_oskol/transport/avtomobili/skoda",
                    "https://auto.catalogd.ru/staryj-oskol/moraviya_centr_stariy_oskol_skoda/",
                    "https://stariy-oskol.cardana.ru/auto/models/skoda.html",
                    "https://yandex.ru/maps/org/koda_moraviya_motors/1301511506/",
                    "https://avto-dilery.ru/shkoda-staryj-oskol/",
                    "https://stariy-oskol.newautosalon.ru/superb-fl/"
                ]
            ],
            "skoda superb sportline" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb-sportline",
                    "https://rolf-skoda.ru/models/superb-sportline",
                    "https://www.skoda-major.ru/sportline/",
                    "https://skoda-avtoruss.ru/models/superb-sportline",
                    "https://auto.ru/catalog/cars/skoda/superb/20483375/20483435/equipment/20483435_20898974_20483785/",
                    "https://www.kolesa.ru/test-drive/ty-leti-s-dorogi-ptitsa-zver-s-dorogi-uhodi-test-drajv-skoda-superb-sportline",
                    "https://motor.ru/testdrives/superbsportlinelong2.htm",
                    "https://www.bogemia-skd.ru/models/superb-sportline",
                    "https://www.drive2.ru/b/592491358032257373/",
                    "https://www.drom.ru/catalog/skoda/superb/302587/",
                    "https://www.drive.ru/brands/skoda/models/2019/superb/sportline_20_amt_280hp",
                    "https://www.sove2u.ru/%d0%bd%d0%be%d0%b2%d0%b0%d1%8f-%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d1%81%d1%83%d0%bf%d0%b5%d1%80%d0%b1-%d1%81%d0%bf%d0%be%d1%80%d1%82%d0%bb%d0%b0%d0%b9%d0%bd-2021/",
                    "https://skoda-centr.ru/superb-sportline/",
                    "https://carsdo.ru/skoda/superb/equipment-17/",
                    "https://favorit-motors.ru/catalog/new/skoda/superb/sportline/",
                    "https://autoreview.ru/news/obnovlennaya-skoda-superb-v-rossii-teper-1-4-tsi-i-sportline#!comment=1629681",
                    "https://www.rolf.ru/cars/new/skoda/superb-sedan/stock_car609672/",
                    "https://riaauto.ru/skoda/superb-sportline",
                    "https://www.masmotors.ru/car/skoda/superb_sportline",
                    "https://vseautomobilimira.ru/auto/skoda/superb_sportline"
                ]
            ],
            "škoda superb sportline" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb-sportline",
                    "https://rolf-skoda.ru/models/superb-sportline",
                    "https://www.skoda-major.ru/sportline/",
                    "https://skoda-avtoruss.ru/models/superb-sportline",
                    "https://motor.ru/testdrives/superbsportlinelong2.htm",
                    "https://www.bogemia-skd.ru/models/superb-sportline",
                    "https://www.kolesa.ru/test-drive/ty-leti-s-dorogi-ptitsa-zver-s-dorogi-uhodi-test-drajv-skoda-superb-sportline",
                    "https://www.drive2.ru/b/592491358032257373/",
                    "https://auto.ru/catalog/cars/skoda/superb/20483375/20483435/equipment/20483435_20898974_20483785/",
                    "https://www.sove2u.ru/%d0%bd%d0%be%d0%b2%d0%b0%d1%8f-%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d1%81%d1%83%d0%bf%d0%b5%d1%80%d0%b1-%d1%81%d0%bf%d0%be%d1%80%d1%82%d0%bb%d0%b0%d0%b9%d0%bd-2021/",
                    "https://www.drom.ru/catalog/skoda/superb/302591/",
                    "https://www.drive.ru/brands/skoda/models/2019/superb/sportline_20_amt_280hp",
                    "https://www.rolf.ru/cars/new/skoda/superb-sedan/stock_car609672/",
                    "https://carsdo.ru/skoda/superb/equipment-17/",
                    "https://riaauto.ru/skoda/superb-sportline",
                    "https://skoda-centr.ru/superb-sportline/",
                    "https://skodakodiaq.club/shkoda-superb-sportlajn-avtomobil-s-harakterom/",
                    "https://sevavto.ru/models/superb-sportline",
                    "https://skoda-kanavto.ru/models/superb-sportline",
                    "https://www.incom-auto.ru/auto/skoda/superb-sportline/"
                ]
            ],
            "škoda superb sportline 2022" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb-sportline",
                    "https://auto.ru/moskva/cars/skoda/superb/2022-year/all/",
                    "https://rolf-skoda.ru/models/superb-sportline",
                    "https://skoda-avtoruss.ru/models/superb-sportline",
                    "https://www.skoda-major.ru/sportline/",
                    "https://www.youtube.com/watch?v=m8gzed5ufgi",
                    "https://www.skoda-podolsk.ru/models/superb-sportline",
                    "https://www.bogemia-skd.ru/models/superb-sportline",
                    "https://www.drom.ru/catalog/skoda/superb/2022/",
                    "https://cenyavto.com/skoda-superb-2022/",
                    "https://sigma-skoda.ru/models/superb-sportline",
                    "https://naavtotrasse.ru/skoda/skoda-superb-2022.html",
                    "https://gt-news.ru/skoda/superb-2022/",
                    "https://carsdo.ru/skoda/superb/equipment-17/",
                    "https://www.autocity-sk.ru/models/superb-sportline",
                    "https://optomobuv.ru/testy/superb-2021.html",
                    "https://favorit-motors.ru/catalog/new/skoda/superb/sportline/",
                    "https://sevavto.ru/models/superb-sportline",
                    "https://www.auto-dd.ru/skoda-superb-2020/",
                    "https://motor.ru/testdrives/superbsportlinelong2.htm"
                ]
            ],
            "škoda superb sportline купить" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb-sportline",
                    "https://rolf-skoda.ru/models/superb-sportline",
                    "https://www.skoda-major.ru/sportline/",
                    "https://skoda-avtoruss.ru/models/superb-sportline",
                    "https://auto.ru/moskva/cars/skoda/superb/2022-year/all/",
                    "https://www.bogemia-skd.ru/models/superb-sportline",
                    "https://www.autocity-sk.ru/models/superb-sportline",
                    "https://favorit-motors.ru/catalog/new/skoda/superb/sportline/",
                    "https://www.rolf.ru/cars/new/skoda/superb-sedan/stock_car609672/",
                    "https://riaauto.ru/skoda/superb-sportline",
                    "https://www.incom-auto.ru/auto/skoda/superb-sportline/",
                    "https://carso.ru/skoda/superb/kit/sportline",
                    "https://auto-nrg.com/auto/skoda/superb/sportline",
                    "https://carsdo.ru/skoda/superb/equipment-17/",
                    "https://autospot.ru/brands/skoda/superb/liftback/price/",
                    "https://moscow.autovsalone.ru/cars/skoda/superb",
                    "https://skoda-centr.ru/superb-sportline/",
                    "https://adom.ru/skoda/superb",
                    "https://www.masmotors.ru/car/skoda/superb_sportline",
                    "https://vseautomobilimira.ru/auto/skoda/superb_sportline"
                ]
            ],
            "шкода суперб 2022 спортлайн" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb-sportline",
                    "https://rolf-skoda.ru/models/superb-sportline",
                    "https://skoda-avtoruss.ru/models/superb-sportline",
                    "https://www.skoda-major.ru/sportline/",
                    "https://auto.ru/moskovskaya_oblast/cars/skoda/superb/2022-year/all/",
                    "https://www.autoskd.ru/models/superb-sportline",
                    "https://carsdo.ru/skoda/superb/equipment-17/",
                    "https://www.bogemia-skd.ru/models/superb-sportline",
                    "https://www.drom.ru/catalog/skoda/superb/2022/",
                    "https://favorit-motors.ru/catalog/new/skoda/superb/sportline/",
                    "https://cenyavto.com/skoda-superb-2022/",
                    "https://motor.ru/testdrives/superbsportlinelong2.htm",
                    "https://www.drive2.ru/b/592491358032257373/",
                    "https://optomobuv.ru/testy/superb-2021.html",
                    "https://autoreview.ru/news/obnovlennaya-skoda-superb-v-rossii-teper-1-4-tsi-i-sportline#!comment=1629681",
                    "https://www.auto-dd.ru/skoda-superb-2020/",
                    "https://naavtotrasse.ru/skoda/skoda-superb-2022.html",
                    "https://www.kolesa.ru/test-drive/ty-leti-s-dorogi-ptitsa-zver-s-dorogi-uhodi-test-drajv-skoda-superb-sportline",
                    "https://carso.ru/skoda/superb/kit/sportline",
                    "https://skoda-kanavto.ru/models/superb-sportline"
                ]
            ],
            "шкода суперб спортлайн" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb-sportline",
                    "https://rolf-skoda.ru/models/superb-sportline",
                    "https://www.skoda-major.ru/sportline/",
                    "https://skoda-avtoruss.ru/models/superb-sportline",
                    "https://www.kolesa.ru/test-drive/ty-leti-s-dorogi-ptitsa-zver-s-dorogi-uhodi-test-drajv-skoda-superb-sportline",
                    "https://www.bogemia-skd.ru/models/superb-sportline",
                    "https://www.drive2.ru/b/592491358032257373/",
                    "https://auto.ru/catalog/cars/skoda/superb/20483375/20483435/equipment/20483435_20898974_20483785/",
                    "https://www.drom.ru/catalog/skoda/superb/302591/",
                    "https://motor.ru/testdrives/superbsportlinelong2.htm",
                    "https://www.autocity-sk.ru/models/superb-sportline",
                    "https://www.drive.ru/brands/skoda/models/2019/superb/sportline_20_amt_280hp",
                    "https://carsdo.ru/skoda/superb/equipment-17/",
                    "https://www.rolf.ru/cars/new/skoda/superb-sedan/stock_car609672/",
                    "https://autoreview.ru/news/obnovlennaya-skoda-superb-v-rossii-teper-1-4-tsi-i-sportline#!comment=1629681",
                    "https://optomobuv.ru/testy/superb-2021.html",
                    "https://favorit-motors.ru/catalog/new/skoda/superb/sportline/",
                    "https://skoda-centr.ru/superb-sportline/",
                    "https://www.sove2u.ru/%d0%bd%d0%be%d0%b2%d0%b0%d1%8f-%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d1%81%d1%83%d0%bf%d0%b5%d1%80%d0%b1-%d1%81%d0%bf%d0%be%d1%80%d1%82%d0%bb%d0%b0%d0%b9%d0%bd-2021/",
                    "https://www.masmotors.ru/car/skoda/superb_sportline"
                ]
            ],
            "шкода суперб спортлайн 2022 цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb-sportline",
                    "https://auto.ru/moskva/cars/skoda/superb/2022-year/all/",
                    "https://rolf-skoda.ru/models/superb-sportline",
                    "https://www.skoda-major.ru/sportline/",
                    "https://skoda-avtoruss.ru/models/superb-sportline",
                    "https://carsdo.ru/skoda/superb/",
                    "https://www.bogemia-skd.ru/models/superb-sportline",
                    "https://www.drom.ru/catalog/skoda/superb/2022/",
                    "https://favorit-motors.ru/catalog/new/skoda/superb/sportline/",
                    "https://www.autocity-sk.ru/models/superb-sportline",
                    "https://cenyavto.com/skoda-superb-2022/",
                    "https://moscow.autovsalone.ru/cars/skoda/superb",
                    "https://carso.ru/skoda/superb/kit/sportline",
                    "https://naavtotrasse.ru/skoda/skoda-superb-2022.html",
                    "https://skoda-centr.ru/superb-sportline/",
                    "https://riaauto.ru/skoda/superb-sportline",
                    "https://roadres.com/skoda/superb-3/price/",
                    "https://carsdb.ru/skoda/superb/sportline-17/",
                    "https://www.major-auto.ru/models/skoda/superb_obnovlenniy/",
                    "https://autospot.ru/brands/skoda/superb/liftback/price/"
                ]
            ],
            "шкода суперб спортлайн купить" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/superb-sportline",
                    "https://rolf-skoda.ru/models/superb-sportline",
                    "https://www.skoda-major.ru/sportline/",
                    "https://skoda-avtoruss.ru/models/superb-sportline",
                    "https://auto.ru/moskva/cars/skoda/superb/2022-year/all/",
                    "https://www.bogemia-skd.ru/models/superb-sportline",
                    "https://favorit-motors.ru/catalog/new/skoda/superb/sportline/",
                    "https://www.autocity-sk.ru/models/superb-sportline",
                    "https://riaauto.ru/skoda/superb-sportline",
                    "https://www.rolf.ru/cars/new/skoda/superb-sedan/stock_car609672/",
                    "https://carso.ru/skoda/superb/kit/sportline",
                    "https://www.incom-auto.ru/auto/skoda/superb-sportline/",
                    "https://carsdo.ru/skoda/superb/equipment-17/",
                    "https://auto-nrg.com/auto/skoda/superb/sportline",
                    "https://moscow.autovsalone.ru/cars/skoda/superb",
                    "https://autospot.ru/brands/skoda/superb/liftback/price/",
                    "https://skoda-centr.ru/superb-sportline/",
                    "https://www.major-auto.ru/models/skoda/superb_obnovlenniy/",
                    "https://www.masmotors.ru/car/skoda/superb_sportline",
                    "https://vseautomobilimira.ru/auto/skoda/superb_sportline"
                ]
            ],
            "skoda авто в наличии" => [
                "sites" => [
                    "https://cars.skoda-avto.ru/",
                    "https://auto.ru/moskva/cars/skoda/new/",
                    "https://cars.rolf-skoda.ru/new/",
                    "https://www.rolf.ru/cars/new/skoda/",
                    "https://www.autocity-sk.ru/avalible-cars",
                    "https://avtomir.ru/new-cars/skoda/",
                    "https://favorit-motors.ru/catalog/stock/skoda/",
                    "https://skoda-kuntsevo.ru/models/cars-in-stock",
                    "https://skoda-avtoruss.ru/models/available-cars",
                    "https://rolf-center.ru/new/skoda/",
                    "https://autospot.ru/brands/skoda/",
                    "https://www.ventus.ru/stock/available",
                    "https://m.avito.ru/moskva/avtomobili/novyy/skoda-asgbagicaksgfmbmaec2dz6zka",
                    "https://rolf-veshki.ru/brands/skoda/",
                    "https://moscow.drom.ru/skoda/new/",
                    "https://cars.skoda-favorit.ru/",
                    "https://carso.ru/skoda",
                    "https://autogansa.ru/cars/skoda/",
                    "https://cars.atlant-motors.ru/",
                    "https://www.incom-auto.ru/auto/skoda/"
                ]
            ],
            "skoda в наличии" => [
                "sites" => [
                    "https://cars.skoda-avto.ru/",
                    "https://cars.rolf-skoda.ru/new/",
                    "https://auto.ru/moskva/cars/skoda/new/",
                    "https://www.skoda-major.ru/cars/",
                    "https://www.rolf.ru/cars/new/skoda/",
                    "https://favorit-motors.ru/catalog/stock/skoda/",
                    "https://avtomir.ru/new-cars/skoda/",
                    "https://skoda-avtoruss.ru/models/available-cars",
                    "https://rolf-center.ru/new/skoda/",
                    "https://skoda-kuntsevo.ru/models/cars-in-stock",
                    "https://rolf-veshki.ru/brands/skoda/",
                    "https://www.ventus.ru/stock/available",
                    "https://autospot.ru/brands/skoda/",
                    "https://moscow.drom.ru/skoda/new/",
                    "https://m.avito.ru/moskva/avtomobili/novyy/skoda-asgbagicaksgfmbmaec2dz6zka",
                    "https://skoda-favorit.ru/",
                    "https://carso.ru/skoda",
                    "https://www.incom-auto.ru/auto/skoda/",
                    "https://autogansa.ru/cars/skoda/",
                    "https://www.atlant-motors.ru/"
                ]
            ],
            "купить skoda в наличии" => [
                "sites" => [
                    "https://cars.skoda-avto.ru/",
                    "https://auto.ru/moskva/cars/skoda/new/",
                    "https://cars.rolf-skoda.ru/new/",
                    "https://www.rolf.ru/cars/new/skoda/",
                    "https://www.major-auto.ru/models/skoda/",
                    "https://favorit-motors.ru/catalog/stock/skoda/",
                    "https://skoda-avtoruss.ru/models/available-cars",
                    "https://skoda-favorit.ru/",
                    "https://avtomir.ru/new-cars/skoda/",
                    "https://moscow.drom.ru/skoda/new/",
                    "https://m.avito.ru/moskva/avtomobili/novyy/skoda-asgbagicaksgfmbmaec2dz6zka",
                    "https://autospot.ru/brands/skoda/",
                    "https://skoda-kuntsevo.ru/models/cars-in-stock",
                    "https://www.ventus.ru/stock/available",
                    "https://carso.ru/skoda",
                    "https://rolf-veshki.ru/cars/new/skoda/",
                    "https://www.incom-auto.ru/auto/skoda/",
                    "https://autogansa.ru/cars/skoda/",
                    "https://www.atlant-motors.ru/",
                    "https://nz-cars.ru/cars/skoda/"
                ]
            ],
            "купить шкоду в наличии" => [
                "sites" => [
                    "https://cars.skoda-avto.ru/",
                    "https://auto.ru/moskva/cars/skoda/new/",
                    "https://cars.rolf-skoda.ru/new/",
                    "https://favorit-motors.ru/catalog/stock/skoda/",
                    "https://www.rolf.ru/cars/new/skoda/",
                    "https://www.major-auto.ru/models/skoda/",
                    "https://skoda-avtoruss.ru/models/available-cars",
                    "https://avtomir.ru/new-cars/skoda/",
                    "https://m.avito.ru/moskva/avtomobili/novyy/skoda-asgbagicaksgfmbmaec2dz6zka",
                    "https://skoda-favorit.ru/",
                    "https://skoda-kuntsevo.ru/models/cars-in-stock",
                    "https://moscow.drom.ru/skoda/new/",
                    "https://autospot.ru/brands/skoda/",
                    "https://rolf-center.ru/new/skoda/",
                    "https://www.ventus.ru/stock/available",
                    "https://www.incom-auto.ru/auto/skoda/",
                    "https://carso.ru/skoda",
                    "https://www.atlant-motors.ru/",
                    "https://avtoruss.ru/auto/skoda.html",
                    "https://autogansa.ru/cars/skoda/"
                ]
            ],
            "шкода автомобили в наличии" => [
                "sites" => [
                    "https://cars.skoda-avto.ru/",
                    "https://cars.rolf-skoda.ru/new/",
                    "https://www.rolf.ru/cars/new/skoda/",
                    "https://auto.ru/moskva/cars/skoda/new/",
                    "https://www.skoda-major.ru/cars/",
                    "https://favorit-motors.ru/catalog/stock/skoda/",
                    "https://skoda-kuntsevo.ru/models/cars-in-stock",
                    "https://skoda-avtoruss.ru/models/available-cars",
                    "https://avtomir.ru/new-cars/skoda/",
                    "https://cars.skoda-favorit.ru/",
                    "https://rolf-center.ru/new/skoda/",
                    "https://www.ventus.ru/stock/available",
                    "https://m.avito.ru/moskva/avtomobili/novyy/skoda-asgbagicaksgfmbmaec2dz6zka",
                    "https://autospot.ru/brands/skoda/",
                    "https://moscow.drom.ru/skoda/new/",
                    "https://autogansa.ru/cars/skoda/",
                    "https://carso.ru/skoda",
                    "https://cars.atlant-motors.ru/",
                    "https://avtoruss.ru/auto/skoda.html",
                    "https://www.incom-auto.ru/auto/skoda/"
                ]
            ],
            "шкода в наличии" => [
                "sites" => [
                    "https://cars.skoda-avto.ru/",
                    "https://auto.ru/moskva/cars/skoda/new/",
                    "https://www.rolf.ru/cars/new/skoda/",
                    "https://cars.rolf-skoda.ru/new/",
                    "https://favorit-motors.ru/catalog/stock/skoda/",
                    "https://avtomir.ru/new-cars/skoda/",
                    "https://www.major-auto.ru/models/skoda/",
                    "https://skoda-avtoruss.ru/models/available-cars",
                    "https://skoda-kuntsevo.ru/models/cars-in-stock",
                    "https://rolf-center.ru/new/skoda/",
                    "https://autospot.ru/brands/skoda/",
                    "https://cars.skoda-favorit.ru/",
                    "https://m.avito.ru/moskva/avtomobili/novyy/skoda-asgbagicaksgfmbmaec2dz6zka",
                    "https://moscow.drom.ru/skoda/new/",
                    "https://rolf-veshki.ru/brands/skoda/",
                    "https://www.ventus.ru/stock/available",
                    "https://carso.ru/skoda",
                    "https://autogansa.ru/cars/skoda/",
                    "https://www.incom-auto.ru/auto/skoda/",
                    "https://avtoruss.ru/auto/skoda.html"
                ]
            ],
            "шкода в наличии у официальных дилеров" => [
                "sites" => [
                    "https://cars.skoda-avto.ru/",
                    "https://www.major-auto.ru/models/skoda/",
                    "https://cars.rolf-skoda.ru/new/",
                    "https://skoda-favorit.ru/",
                    "https://favorit-motors.ru/catalog/stock/skoda/",
                    "https://www.rolf.ru/cars/new/skoda/",
                    "https://skoda-avtoruss.ru/models/available-cars",
                    "https://avtomir.ru/new-cars/skoda/",
                    "https://auto.ru/moskva/dilery/cars/skoda/new/",
                    "https://skoda-kuntsevo.ru/models/cars-in-stock",
                    "https://rolf-center.ru/new/skoda/",
                    "https://www.ventus.ru/stock/available",
                    "https://www.atlant-motors.ru/",
                    "https://www.autoskd.ru/",
                    "https://autogansa.ru/cars/skoda/",
                    "https://avilon.ru/brands/skoda/",
                    "https://avtoruss.ru/auto/skoda.html",
                    "https://carso.ru/skoda",
                    "https://www.incom-auto.ru/auto/skoda/",
                    "https://moscow.autovsalone.ru/cars/sellers/search-skoda"
                ]
            ],
            "шкода официальный в наличие" => [
                "sites" => [
                    "https://cars.skoda-avto.ru/",
                    "https://cars.rolf-skoda.ru/new/",
                    "https://favorit-motors.ru/catalog/stock/skoda/",
                    "https://www.skoda-major.ru/cars/",
                    "https://www.rolf.ru/cars/new/skoda/",
                    "https://skoda-avtoruss.ru/models/available-cars",
                    "https://avtomir.ru/new-cars/skoda/",
                    "https://skoda-kuntsevo.ru/models/cars-in-stock",
                    "https://www.ventus.ru/stock/available",
                    "https://skoda-favorit.ru/",
                    "https://cars.skoda-favorit.ru/",
                    "https://rolf-center.ru/new/skoda/",
                    "https://auto.ru/diler-oficialniy/cars/all/rolf_skoda_centr_moskva/",
                    "https://www.atlant-motors.ru/",
                    "https://msk-skoda-auto.ru/",
                    "https://avtoruss.ru/auto/skoda.html",
                    "https://www.autoskd.ru/",
                    "https://autogansa.ru/cars/skoda/",
                    "https://www.bogemia-skd.ru/",
                    "https://autospot.ru/brands/skoda/"
                ]
            ],
            "skoda kodiaq hockey edition" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-hockey-edition-2017",
                    "https://www.skoda-major.ru/kodiaq-he/",
                    "https://www.drom.ru/catalog/skoda/kodiaq/229020/",
                    "https://auto.ru/catalog/cars/skoda/kodiaq/20839003/20839055/equipment/20839055_21404914_20839377/",
                    "https://www.drive2.ru/o/b/543980905014756128/",
                    "https://www.sove2u.ru/%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d0%ba%d0%be%d0%b4%d0%b8%d0%b0%d0%ba-%d1%85%d0%be%d0%ba%d0%ba%d0%b5%d0%b9-%d1%8d%d0%b4%d0%b8%d1%88%d0%bd-2020/",
                    "https://www.youtube.com/watch?v=leetksh-k04",
                    "https://www.bogemia-skd.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://v1.ru/text/auto/2021/03/19/69820388/",
                    "https://ac-sokolniki.ru/auto/skoda/kodiaq-hockey-edition/kodiaq-hockey-edition",
                    "https://www.ixbt.com/car/skoda-kodiaq-review.html",
                    "https://www.incom-auto.ru/auto/skoda/kodiaq/komplektacii/hockey-edition/",
                    "https://avisavto.ru/cars/skoda/kodiaq/komplektacii/hockey-edition",
                    "https://www.rosso-sk.ru/press/obzor-osobennostey-serii-khokkey-edishn-ot-skoda",
                    "https://skoda-kodiaq.ru/forum/viewtopic.php?t=675",
                    "https://skoda-elvis.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://gold-avto.com/auto/skoda/kodiaq_hockey_edition/",
                    "https://skoda-favorit.ru/hockey-edition",
                    "https://naavtotrasse.ru/cat/skoda/kodiaq/2021_8932/20-tsi-dsg-4x4-ambition-hockey-edition-136173/",
                    "https://www.ventus.ru/models/hockey-edition-overview"
                ]
            ],
            "skoda kodiaq hockey edition комплектация" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-hockey-edition-2017",
                    "https://www.drom.ru/catalog/skoda/kodiaq/229020/",
                    "https://www.sove2u.ru/%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d0%ba%d0%be%d0%b4%d0%b8%d0%b0%d0%ba-%d1%85%d0%be%d0%ba%d0%ba%d0%b5%d0%b9-%d1%8d%d0%b4%d0%b8%d1%88%d0%bd-2020/",
                    "https://www.skoda-major.ru/kodiaq-he/",
                    "https://auto.ru/catalog/cars/skoda/kodiaq/20839003/20839055/equipment/20839055_21404914_20839377/",
                    "https://www.drive2.ru/o/b/543980905014756128/",
                    "https://v1.ru/text/auto/2021/03/19/69820388/",
                    "https://www.rosso-sk.ru/press/obzor-osobennostey-serii-khokkey-edishn-ot-skoda",
                    "https://www.youtube.com/watch?v=ifflzbp95lw",
                    "https://www.bogemia-skd.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://www.ixbt.com/car/skoda-kodiaq-review.html",
                    "https://skoda.planeta-avto.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://avisavto.ru/cars/skoda/kodiaq/komplektacii/hockey-edition",
                    "https://msmotors.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://skoda-freshauto.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://ac-sokolniki.ru/auto/skoda/kodiaq-hockey-edition/kodiaq-hockey-edition",
                    "https://www.incom-auto.ru/auto/skoda/kodiaq/komplektacii/hockey-edition/",
                    "https://agat-skoda.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://www.provolochki.ru/auto/%d0%ba%d0%be%d0%bc%d0%bf%d0%bb%d0%b5%d0%ba%d1%82%d0%b0%d1%86%d0%b8%d1%8f-skoda-kodiaq-hockey-edition-2020/",
                    "https://ai-sk.ru/archival-models/kodiaq-hockey-edition-2017"
                ]
            ],
            "шкода кодиак комплектация хоккей эдишн" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-hockey-edition-2017",
                    "https://www.drom.ru/catalog/skoda/kodiaq/229020/",
                    "https://auto.ru/catalog/cars/skoda/kodiaq/20839003/20839055/equipment/20839055_21404914_20839377/",
                    "https://www.sove2u.ru/%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d0%ba%d0%be%d0%b4%d0%b8%d0%b0%d0%ba-%d1%85%d0%be%d0%ba%d0%ba%d0%b5%d0%b9-%d1%8d%d0%b4%d0%b8%d1%88%d0%bd-2020/",
                    "https://v1.ru/text/auto/2021/03/19/69820388/",
                    "https://www.skoda-major.ru/kodiaq-he/",
                    "https://www.drive2.ru/o/b/543980905014756128/",
                    "https://skoda-kuntsevo.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://www.rosso-sk.ru/press/obzor-osobennostey-serii-khokkey-edishn-ot-skoda",
                    "https://www.bogemia-skd.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://skoda-autoug.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://www.youtube.com/watch?v=ifflzbp95lw",
                    "https://skoda-kodiaq.ru/forum/viewtopic.php?t=675",
                    "https://skoda.medved-abakan.ru/articles/582",
                    "https://msmotors.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://naavtotrasse.ru/cat/skoda/kodiaq/2021_8932/20-tsi-dsg-4x4-ambition-hockey-edition-136173/",
                    "https://interkar.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://skoda-orehovo.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://skoda-freshauto.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://gold-avto.com/auto/skoda/kodiaq_hockey_edition/"
                ]
            ],
            "шкода кодиак хоккей" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-hockey-edition-2017",
                    "https://www.drom.ru/catalog/skoda/kodiaq/229020/",
                    "https://www.sove2u.ru/%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d0%ba%d0%be%d0%b4%d0%b8%d0%b0%d0%ba-%d1%85%d0%be%d0%ba%d0%ba%d0%b5%d0%b9-%d1%8d%d0%b4%d0%b8%d1%88%d0%bd-2020/",
                    "https://auto.ru/catalog/cars/skoda/kodiaq/20839003/20839055/equipment/20839055_21404914_20839377/",
                    "https://www.skoda-major.ru/kodiaq-he/",
                    "https://v1.ru/text/auto/2021/03/19/69820388/",
                    "https://www.youtube.com/watch?v=ifflzbp95lw",
                    "https://www.drive2.ru/o/b/543980905014756128/",
                    "https://skoda-kodiaq.ru/forum/viewtopic.php?t=675",
                    "https://www.bogemia-skd.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://skoda.medved-abakan.ru/articles/582",
                    "https://www.ixbt.com/car/skoda-kodiaq-review.html",
                    "https://www.rosso-sk.ru/press/obzor-osobennostey-serii-khokkey-edishn-ot-skoda",
                    "https://autovn.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://gold-avto.com/auto/skoda/kodiaq_hockey_edition/",
                    "https://msmotors.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://agat-skoda.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://skoda-freshauto.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://km-auto.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://skoda-vozrojdenie.ru/archival-models/kodiaq-hockey-edition-2017"
                ]
            ],
            "шкода кодиак хоккей эдишн" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-hockey-edition-2017",
                    "https://cars.skoda-avto.ru/kodiaq-hockey-edition",
                    "https://www.drom.ru/catalog/skoda/kodiaq/229020/",
                    "https://auto.ru/catalog/cars/skoda/kodiaq/20839003/20839055/equipment/20839055_21404914_20839377/",
                    "https://v1.ru/text/auto/2021/03/19/69820388/",
                    "https://www.skoda-major.ru/kodiaq-he/",
                    "https://www.drive2.ru/o/b/543980905014756128/",
                    "https://www.rosso-sk.ru/press/obzor-osobennostey-serii-khokkey-edishn-ot-skoda",
                    "https://skoda-kodiaq.ru/forum/viewtopic.php?t=675",
                    "https://www.youtube.com/watch?v=dbuxbnbizj8",
                    "https://www.bogemia-skd.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://www.sove2u.ru/%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d0%ba%d0%be%d0%b4%d0%b8%d0%b0%d0%ba-%d1%85%d0%be%d0%ba%d0%ba%d0%b5%d0%b9-%d1%8d%d0%b4%d0%b8%d1%88%d0%bd-2020/",
                    "https://msmotors.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://km-auto.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://naavtotrasse.ru/cat/skoda/kodiaq/2021_8932/20-tsi-dsg-4x4-ambition-hockey-edition-136173/",
                    "https://skoda.medved-abakan.ru/articles/582",
                    "https://www.ixbt.com/car/skoda-kodiaq-review.html",
                    "https://skoda-freshauto.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://ac-sokolniki.ru/auto/skoda/kodiaq-hockey-edition/kodiaq-hockey-edition",
                    "https://gold-avto.com/auto/skoda/kodiaq_hockey_edition/"
                ]
            ],
            "шкода кодиак хоккей эдишн 2022" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-hockey-edition-2017",
                    "https://www.drom.ru/catalog/skoda/kodiaq/229020/",
                    "https://www.skoda-major.ru/kodiaq-he/",
                    "https://auto.ru/catalog/cars/skoda/kodiaq/22990108/22990175/equipment/22990175_23052498_23041030/",
                    "https://www.youtube.com/watch?v=ifflzbp95lw",
                    "https://v1.ru/text/auto/2021/03/19/69820388/",
                    "https://www.sove2u.ru/%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d0%ba%d0%be%d0%b4%d0%b8%d0%b0%d0%ba-%d1%85%d0%be%d0%ba%d0%ba%d0%b5%d0%b9-%d1%8d%d0%b4%d0%b8%d1%88%d0%bd-2020/",
                    "https://www.drive2.ru/o/b/543980905014756128/",
                    "https://www.rosso-sk.ru/press/obzor-osobennostey-serii-khokkey-edishn-ot-skoda",
                    "https://skoda.medved-abakan.ru/articles/582",
                    "https://gold-avto.com/auto/skoda/kodiaq_hockey_edition/",
                    "https://ac-sokolniki.ru/auto/skoda/kodiaq-hockey-edition/kodiaq-hockey-edition",
                    "https://skoda-autoug.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://msmotors.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://skoda-yeti.ru/shkoda-kodiak-2022-foto-tsena-i-komplektatsii-novogo-skoda-kodiaq-harakteristiki/",
                    "https://skoda-freshauto.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://agat-skoda.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://clubskodakodiak.ru/sovety/skoda-kodiaq-hockey-edition-uzhe-v-prodazhe.php",
                    "https://km-auto.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://ruautoshop.com/skoda/kodiaq_hockey_edition/"
                ]
            ],
            "шкода кодиак хоккей эдишн комплектация и цены" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-hockey-edition-2017",
                    "https://www.skoda-major.ru/kodiaq-he/",
                    "https://www.sove2u.ru/%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d0%ba%d0%be%d0%b4%d0%b8%d0%b0%d0%ba-%d1%85%d0%be%d0%ba%d0%ba%d0%b5%d0%b9-%d1%8d%d0%b4%d0%b8%d1%88%d0%bd-2020/",
                    "https://www.drom.ru/catalog/skoda/kodiaq/229020/",
                    "https://auto.ru/catalog/cars/skoda/kodiaq/20839003/20839055/equipment/20839055_21404914_20839377/",
                    "https://v1.ru/text/auto/2021/03/19/69820388/",
                    "https://www.incom-auto.ru/auto/skoda/kodiaq/komplektacii/hockey-edition/",
                    "https://ac-sokolniki.ru/auto/skoda/kodiaq-hockey-edition/kodiaq-hockey-edition",
                    "https://www.bogemia-skd.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://autospot.ru/brands/skoda/kodiaq/suv/offer/294183/",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/komplektacii-i-ceny/",
                    "https://skoda-favorit.ru/hockey-edition",
                    "https://skoda-freshauto.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://avanta-avto-credit.ru/cars/skoda/kodiaq/komplektacii/hockey-edition-2.0-amt/",
                    "https://agat-skoda.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://rolf-skoda.ru/models/kodiaq/price",
                    "https://skoda-elvis.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://ruautoshop.com/skoda/kodiaq_hockey_edition/",
                    "https://ai-sk.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://www.provolochki.ru/auto/%d0%ba%d0%be%d0%bc%d0%bf%d0%bb%d0%b5%d0%ba%d1%82%d0%b0%d1%86%d0%b8%d1%8f-skoda-kodiaq-hockey-edition-2020/"
                ]
            ],
            "шкода кодиак хоккей эдишн цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-hockey-edition-2017",
                    "https://cars.skoda-avto.ru/kodiaq-hockey-edition",
                    "https://www.skoda-major.ru/kodiaq-he/",
                    "https://auto.ru/catalog/cars/skoda/kodiaq/20839003/20839055/equipment/20839055_21404914_20839377/",
                    "https://www.sove2u.ru/%d1%88%d0%ba%d0%be%d0%b4%d0%b0-%d0%ba%d0%be%d0%b4%d0%b8%d0%b0%d0%ba-%d1%85%d0%be%d0%ba%d0%ba%d0%b5%d0%b9-%d1%8d%d0%b4%d0%b8%d1%88%d0%bd-2020/",
                    "https://www.drom.ru/catalog/skoda/kodiaq/229020/",
                    "https://skoda-kuntsevo.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://ac-sokolniki.ru/auto/skoda/kodiaq-hockey-edition/kodiaq-hockey-edition",
                    "https://www.incom-auto.ru/auto/skoda/kodiaq/komplektacii/hockey-edition/",
                    "https://b-kredit.com/catalog/skoda/kodiaq_hockey_edition/",
                    "https://v1.ru/text/auto/2021/03/19/69820388/",
                    "https://autospot.ru/brands/skoda/kodiaq/suv/offer/294183/",
                    "https://www.bogemia-skd.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://avanta-avto-credit.ru/cars/skoda/kodiaq/komplektacii/hockey-edition-2.0-amt/",
                    "https://skoda-favorit.ru/hockey-edition",
                    "https://gold-avto.com/auto/skoda/kodiaq_hockey_edition/",
                    "https://skoda-vostokmotors.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://skoda-freshauto.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://skoda.planeta-avto.ru/archival-models/kodiaq-hockey-edition-2017",
                    "https://rolf-skoda.ru/models/kodiaq/price"
                ]
            ],
            "skoda kodiaq sportline" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-sportline",
                    "https://www.skoda-major.ru/kodiaq-sportline/",
                    "https://www.drive2.ru/b/533793105149690520/",
                    "https://skoda-kuntsevo.ru/models/kodiaq-sportline",
                    "https://skoda-avtoruss.ru/models/kodiaq-sportline",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/sportline/",
                    "https://www.youtube.com/watch?v=hsi0xpy5w-m",
                    "https://auto.ru/catalog/cars/skoda/kodiaq/20839003/20839055/equipment/20839055_21199273_20839377/",
                    "https://adom.ru/skoda/kodiaq-sportline",
                    "https://www.drom.ru/catalog/skoda/kodiaq/200631/",
                    "https://www.drive.ru/news/skoda/5a799309ec05c42b200000ba.html",
                    "https://dinaplus.ru/models/kodiaq-sportline",
                    "https://skoda-ap.ru/archival-models/kodiaq-sportline-2017",
                    "https://carsar.su/models/kodiaq-sportline",
                    "https://skoda-favorit.ru/models/kodiaq-sportline",
                    "https://autovn.ru/models/kodiaq-sportline",
                    "https://skoda-freshauto.ru/models/kodiaq-sportline",
                    "https://skoda-orehovo.ru/models/kodiaq-sportline",
                    "https://sigma-skoda.ru/models/kodiaq-sportline",
                    "https://www.autocity-sk.ru/models/kodiaq-sportline"
                ]
            ],
            "шкода кодиак спортлайн" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-sportline",
                    "https://www.skoda-major.ru/kodiaq-sportline/",
                    "https://skoda-avtoruss.ru/models/kodiaq-sportline",
                    "https://www.drive2.ru/b/533793105149690520/",
                    "https://mlada-auto.ru/models/kodiaq-sportline",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/sportline/",
                    "https://www.youtube.com/watch?v=hsi0xpy5w-m",
                    "https://praga-motors.ru/models/kodiaq-sportline",
                    "https://adom.ru/skoda/kodiaq-sportline",
                    "https://www.drive.ru/news/skoda/5a799309ec05c42b200000ba.html",
                    "https://auto.ru/catalog/cars/skoda/kodiaq/20839003/20839055/equipment/20839055_21199273_20839377/",
                    "https://www.autocity-sk.ru/models/kodiaq-sportline",
                    "https://chehia-avto.ru/models/kodiaq-sportline",
                    "https://msmotors.ru/models/kodiaq-sportline",
                    "https://blik-auto.ru/models/kodiaq-sportline",
                    "https://skoda-tts.ru/models/kodiaq-sportline",
                    "https://skoda-yug-avto.ru/models/kodiaq-sportline",
                    "https://skoda-ap.ru/archival-models/kodiaq-sportline-2017",
                    "https://skoda-orehovo.ru/models/kodiaq-sportline",
                    "https://skoda-vozrojdenie.ru/models/kodiaq-sportline"
                ]
            ],
            "шкода кодиак спортлайн 2022" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-sportline",
                    "https://www.skoda-major.ru/kodiaq-sportline/",
                    "https://auto.ru/moskva/cars/skoda/kodiaq/2022-year/all/",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/sportline/",
                    "https://www.youtube.com/watch?v=9wamn-dkpao",
                    "https://www.autocity-sk.ru/models/kodiaq-sportline",
                    "https://adom.ru/skoda/kodiaq-sportline",
                    "https://www.bogemia-yar.ru/models/kodiaq-sportline",
                    "https://ringsever.ru/models/kodiaq-sportline",
                    "https://www.drom.ru/catalog/skoda/kodiaq/200631/",
                    "https://l-ring.ru/models/kodiaq-sportline",
                    "https://www.bogemia-skd.ru/models/kodiaq-sportline",
                    "https://aspec-lider.ru/models/kodiaq-sportline",
                    "https://skoda-ap.ru/models/kodiaq-sportline",
                    "https://skoda-tts.ru/models/kodiaq-sportline",
                    "https://eskadra-auto.ru/models/kodiaq-sportline",
                    "https://lmotors-skoda.ru/models/kodiaq-sportline",
                    "https://avto-bravo.ru/models/kodiaq-sportline",
                    "https://skoda-vozrojdenie.ru/models/kodiaq-sportline",
                    "https://sevavto.ru/models/kodiaq-sportline"
                ]
            ],
            "шкода кодиак спортлайн цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-sportline",
                    "https://www.skoda-major.ru/kodiaq-sportline/",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/sportline/",
                    "https://auto.ru/moskva/cars/skoda/kodiaq/2022-year/all/",
                    "https://adom.ru/skoda/kodiaq-sportline",
                    "https://riaauto.ru/skoda/kodiaq-sportline",
                    "https://ac-moscow.ru/auto/skoda/kodiaq/sportline",
                    "https://dc-sever.ru/model/skoda/kodiaq-sportline/",
                    "https://carso.ru/skoda/kodiaq/kit/sportline",
                    "https://www.autocity-sk.ru/archival-models/kodiaq-sportline-2017",
                    "https://autocentr.su/auto/skoda/kodiaq-sportline",
                    "https://www.bips.ru/skoda/kodiaq/kit/sportline",
                    "https://skoda.medved-vostok.ru/models/kodiaq-sportline",
                    "https://l-ring.ru/models/kodiaq-sportline",
                    "https://chehia-avto.ru/models/kodiaq-sportline",
                    "https://aspec-lider.ru/models/kodiaq-sportline",
                    "https://skoda-wagner.ru/models/kodiaq-sportline",
                    "https://skoda-tts.ru/models/kodiaq-sportline",
                    "https://sigma-skoda.ru/models/kodiaq-sportline",
                    "https://skoda-orehovo.ru/archival-models/kodiaq-sportline-2017"
                ]
            ],
            "купить шкоду кодиак спортлайн" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-sportline",
                    "https://www.skoda-major.ru/kodiaq-sportline/",
                    "https://auto.ru/moskva/cars/skoda/kodiaq/2022-year/all/",
                    "https://adom.ru/skoda/kodiaq-sportline",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/sportline/",
                    "https://riaauto.ru/skoda/kodiaq-sportline",
                    "https://dc-sever.ru/model/skoda/kodiaq-sportline/",
                    "https://www.autocity-sk.ru/archival-models/kodiaq-sportline-2017",
                    "https://carso.ru/skoda/kodiaq/kit/sportline",
                    "https://autocentr.su/auto/skoda/kodiaq-sportline",
                    "https://carsar.su/models/kodiaq-sportline",
                    "https://skoda-wagner.ru/models/kodiaq-sportline",
                    "https://skoda-orehovo.ru/models/kodiaq-sportline",
                    "https://skoda.medved-vostok.ru/models/kodiaq-sportline",
                    "https://skoda-ap.ru/archival-models/kodiaq-sportline-2017",
                    "https://autospot.ru/brands/skoda/kodiaq/suv/price/",
                    "https://www.major-auto.ru/models/skoda/kodiaq/",
                    "https://sevavto.ru/models/kodiaq-sportline",
                    "https://novocar-skoda.ru/models/kodiaq-sportline",
                    "https://aspec-lider.ru/models/kodiaq-sportline"
                ]
            ],
            "шкода кодиак спортлайн 2022 цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-sportline",
                    "https://www.skoda-major.ru/kodiaq-sportline/",
                    "https://auto.ru/moskva/cars/skoda/kodiaq/2022-year/all/",
                    "https://adom.ru/skoda/kodiaq-sportline",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/sportline/",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-kodiaq-2022",
                    "https://ac-moscow.ru/auto/skoda/kodiaq/sportline",
                    "https://carso.ru/skoda/kodiaq/kit/sportline",
                    "https://riaauto.ru/skoda/kodiaq-sportline",
                    "https://dc-sever.ru/model/skoda/kodiaq-sportline/",
                    "https://www.bips.ru/skoda/kodiaq/kit/sportline",
                    "https://sigma-skoda.ru/models/kodiaq-sportline",
                    "https://skoda-orehovo.ru/models/kodiaq-sportline",
                    "https://skoda-wagner.ru/models/kodiaq-sportline",
                    "https://carsdo.ru/skoda/kodiaq/equipment-12/",
                    "https://skoda-yug-avto.ru/models/kodiaq-sportline",
                    "https://skoda-tts.ru/models/kodiaq-sportline",
                    "https://otto-car.ru/models/kodiaq-sportline",
                    "https://skoda-avtoritet.ru/models/kodiaq-sportline",
                    "https://moscow.autovsalone.ru/cars/skoda/kodiaq"
                ]
            ],
            "шкода кодиак спортлайн 2022 в новом кузове" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-sportline",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-kodiaq-2022",
                    "https://www.youtube.com/watch?v=9wamn-dkpao",
                    "https://auto.ru/cars/skoda/kodiaq/2022-year/new/",
                    "https://gt-news.ru/skoda/skoda-kodiaq-2022/",
                    "https://naavtotrasse.ru/skoda/skoda-kodiaq-2022.html",
                    "https://skoda-kuntsevo.ru/models/kodiaq-sportline",
                    "https://www.drom.ru/catalog/skoda/kodiaq/200631/",
                    "https://www.allcarz.ru/skoda-kodiaq-sportline/",
                    "https://www.zr.ru/content/articles/930969-novyj-skoda-kodiaq-bez-avtoma/",
                    "https://skoda-centr.ru/kodiaq-sportline/complect/",
                    "https://www.autocity-sk.ru/models/kodiaq-sportline",
                    "https://sigma-skoda.ru/models/kodiaq-sportline",
                    "https://skoda-forward59.ru/models/kodiaq-sportline",
                    "https://ringsever.ru/models/kodiaq-sportline",
                    "https://alt-park.ru/models/kodiaq-sportline",
                    "https://www.skoda-major.ru/kodiaq-sportline/",
                    "https://eskadra-auto.ru/models/kodiaq-sportline",
                    "https://sevavto.ru/models/kodiaq-sportline",
                    "https://skoda-ap.ru/models/kodiaq-sportline"
                ]
            ],
            "skoda octavia технические характеристики" => [
                "sites" => [
                    "https://www.drom.ru/catalog/skoda/octavia/",
                    "https://www.skoda-avto.ru/models/octavia/technology",
                    "https://auto.ru/catalog/cars/skoda/octavia/",
                    "https://avtomarket.ru/catalog/skoda/octavia/",
                    "https://rolf-skoda.ru/models/octavia/technology",
                    "http://www.autonet.ru/auto/ttx/skoda/octavia",
                    "https://skoda-wagner.ru/models/octavia/technology",
                    "https://skoda-kanavto.ru/models/octavia/technology",
                    "https://skoda-favorit.ru/models/octavia/technology",
                    "https://110km.ru/tth/skoda/octavia/",
                    "https://www.skoda-major.ru/octavia/tehnicheskie-harakteristiki/",
                    "https://carexpert.ru/models/skoda/octavia/tech/",
                    "https://www.rosso-sk.ru/models/octavia/technology",
                    "https://drive-skoda.ru/octavia/a7-tehnicheskie-harakteristiki",
                    "https://skoda-motorauto.ru/octavia-specifications/",
                    "https://sevavto.ru/models/octavia/technology",
                    "http://www.motorpage.ru/skoda/octavia/last/",
                    "https://autospot.ru/brands/skoda/666/technical-parameters/",
                    "http://www.octavia-avto.ru/tech",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_octavia_(1996)"
                ]
            ],
            "skoda octavia характеристики" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia/technology",
                    "https://www.drom.ru/catalog/skoda/octavia/",
                    "https://auto.ru/catalog/cars/skoda/octavia/",
                    "https://rolf-skoda.ru/models/octavia/technology",
                    "https://skoda-wagner.ru/models/octavia/technology",
                    "https://avtomarket.ru/catalog/skoda/octavia/",
                    "https://www.skoda-major.ru/octavia/tehnicheskie-harakteristiki/",
                    "https://skoda-kanavto.ru/models/octavia/technology",
                    "https://skoda-favorit.ru/models/octavia/technology",
                    "https://autospot.ru/brands/skoda/666/technical-parameters/",
                    "https://www.rosso-sk.ru/models/octavia/technology",
                    "https://sevavto.ru/models/octavia/technology",
                    "https://carexpert.ru/models/skoda/octavia/tech/",
                    "http://www.motorpage.ru/skoda/octavia/last/",
                    "http://www.autonet.ru/auto/ttx/skoda/octavia",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_octavia_(1996)",
                    "https://skoda-motorauto.ru/octavia-specifications/",
                    "https://110km.ru/tth/skoda/octavia/",
                    "https://krona-auto.ru/models/octavia/technology",
                    "https://drive-skoda.ru/octavia/a7-tehnicheskie-harakteristiki"
                ]
            ],
            "технические характеристики шкоды октавии" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia/technology",
                    "https://www.drom.ru/catalog/skoda/octavia/",
                    "https://auto.ru/catalog/cars/skoda/octavia/",
                    "https://avtomarket.ru/catalog/skoda/octavia/",
                    "https://skoda-wagner.ru/models/octavia/technology",
                    "https://rolf-skoda.ru/models/octavia/technology",
                    "http://www.autonet.ru/auto/ttx/skoda/octavia",
                    "https://drive-skoda.ru/octavia/a7-tehnicheskie-harakteristiki",
                    "https://110km.ru/tth/skoda/octavia/",
                    "https://skoda-kanavto.ru/models/octavia/technology",
                    "https://carexpert.ru/models/skoda/octavia/tech/",
                    "https://www.skoda-major.ru/octavia/tehnicheskie-harakteristiki/",
                    "http://www.octavia-avto.ru/tech",
                    "http://www.motorpage.ru/skoda/octavia/last/",
                    "https://skoda-favorit.ru/models/octavia/technology",
                    "https://www.rosso-sk.ru/models/octavia/technology",
                    "https://skoda-motorauto.ru/octavia-specifications/",
                    "https://sevavto.ru/models/octavia/technology",
                    "https://www.gazeta-a.ru/autocatalog/skoda/octavia/",
                    "https://autospot.ru/brands/skoda/666/technical-parameters/"
                ]
            ],
            "характеристики шкоды октавии" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia/technology",
                    "https://www.drom.ru/catalog/skoda/octavia/",
                    "https://auto.ru/catalog/cars/skoda/octavia/",
                    "https://avtomarket.ru/catalog/skoda/octavia/",
                    "https://drive-skoda.ru/octavia/a7-tehnicheskie-harakteristiki",
                    "https://rolf-skoda.ru/models/octavia/technology",
                    "https://skoda-favorit.ru/models/octavia/technology",
                    "https://110km.ru/tth/skoda/octavia/",
                    "https://skoda-kanavto.ru/models/octavia/technology",
                    "https://carexpert.ru/models/skoda/octavia/tech/",
                    "https://www.skoda-major.ru/octavia/tehnicheskie-harakteristiki/",
                    "http://www.autonet.ru/auto/ttx/skoda/octavia",
                    "https://skoda-motorauto.ru/octavia-specifications/",
                    "https://sevavto.ru/models/octavia/technology",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_octavia_(1996)",
                    "http://www.motorpage.ru/skoda/octavia/last/",
                    "https://autospot.ru/brands/skoda/666/technical-parameters/",
                    "https://www.rosso-sk.ru/models/octavia/technology",
                    "https://carsdo.ru/skoda/octavia/",
                    "https://translate.yandex.ru/translate?lang=en-ru&url=https%3a%2f%2fen.wikipedia.org%2fwiki%2f%25c5%25a0koda_octavia&view=c"
                ]
            ],
            "шкода октавия технические характеристики" => [
                "sites" => [
                    "https://www.drom.ru/catalog/skoda/octavia/",
                    "https://www.skoda-avto.ru/models/octavia/technology",
                    "https://auto.ru/catalog/cars/skoda/octavia/",
                    "https://avtomarket.ru/catalog/skoda/octavia/",
                    "https://rolf-skoda.ru/models/octavia/technology",
                    "http://www.autonet.ru/auto/ttx/skoda/octavia",
                    "https://www.skoda-major.ru/octavia/tehnicheskie-harakteristiki/",
                    "https://carexpert.ru/models/skoda/octavia/tech/",
                    "https://110km.ru/tth/skoda/octavia/",
                    "https://skoda-favorit.ru/models/octavia/technology",
                    "https://drive-skoda.ru/octavia/a7-tehnicheskie-harakteristiki",
                    "https://skoda-motorauto.ru/octavia-specifications/",
                    "https://bibipedia.info/tech_harakteristiki/skoda/octavia",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_octavia_(1996)",
                    "https://autospot.ru/brands/skoda/666/technical-parameters/",
                    "http://www.octavia-avto.ru/tech",
                    "http://www.motorpage.ru/skoda/octavia/last/",
                    "https://www.gazeta-a.ru/autocatalog/skoda/octavia/",
                    "https://avto-russia.ru/autos/skoda/skoda_octavia.html",
                    "https://sevavto.ru/models/octavia/technology"
                ]
            ],
            "шкода октавия характеристики" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia/technology",
                    "https://www.drom.ru/catalog/skoda/octavia/",
                    "https://auto.ru/catalog/cars/skoda/octavia/",
                    "https://avtomarket.ru/catalog/skoda/octavia/",
                    "https://rolf-skoda.ru/models/octavia/technology",
                    "https://skoda-kanavto.ru/models/octavia/technology",
                    "https://carexpert.ru/models/skoda/octavia/tech/",
                    "http://www.autonet.ru/auto/ttx/skoda/octavia",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_octavia_(1996)",
                    "https://www.skoda-major.ru/octavia/tehnicheskie-harakteristiki/",
                    "https://skoda-favorit.ru/models/octavia/technology",
                    "https://110km.ru/tth/skoda/octavia/",
                    "http://www.motorpage.ru/skoda/octavia/last/",
                    "https://drive-skoda.ru/octavia/a7-tehnicheskie-harakteristiki",
                    "https://skoda-motorauto.ru/octavia-specifications/",
                    "https://www.rosso-sk.ru/models/octavia/technology",
                    "https://sevavto.ru/models/octavia/technology",
                    "https://autospot.ru/brands/skoda/666/technical-parameters/",
                    "http://www.octavia-avto.ru/tech",
                    "https://carsclick.ru/skoda/obzor-avtomobilej/novaja-oktavija/"
                ]
            ],
            "skoda rapid белгород" => [
                "sites" => [
                    "https://moravia-motors.ru/models/rapid",
                    "https://auto.ru/belgorod/cars/skoda/rapid/all/",
                    "https://belgorod.drom.ru/skoda/rapid/",
                    "https://www.avito.ru/belgorod/avtomobili/skoda/rapid-asgbagicaktgtg2emsjitg2urig",
                    "https://belgorod.autovsalone.ru/cars/skoda/rapid",
                    "https://belgorod.carso.ru/skoda/rapid",
                    "https://belgorod.autospot.ru/brands/skoda/rapid_ii/liftback/price/",
                    "https://belgorod.110km.ru/prodazha/skoda/rapid/",
                    "https://carsdo.ru/skoda/rapid/belgorod/",
                    "https://belgorod.riaauto.ru/skoda/rapid",
                    "https://mbib.ru/obl-belgorodskaya/skoda/rapid/used",
                    "https://www.drive2.ru/cars/skoda/rapid/m194/?city=34581",
                    "https://belgorod.ab-club.ru/catalog/skoda/rapid/",
                    "https://belgorod.b-kredit.com/catalog/skoda/rapid_new/",
                    "https://www.skoda-avto.ru/models/rapid",
                    "https://belgorod.cardana.ru/auto/skoda/rapid.html",
                    "https://blik-auto.ru/models/rapid/price",
                    "https://v-belgorode.kupit-auto.com/new/skoda/rapid_old",
                    "https://belgorod.incom-auto.ru/auto/skoda/rapid/",
                    "https://www.m.njcar.ru/prices-partners/belgorod/skoda/rapid/all/"
                ]
            ],
            "купить шкода рапид в белгороде" => [
                "sites" => [
                    "https://www.avito.ru/belgorod/avtomobili/skoda/rapid-asgbagicaktgtg2emsjitg2urig",
                    "https://auto.ru/belgorod/cars/skoda/rapid/all/",
                    "https://moravia-motors.ru/models/rapid",
                    "https://belgorod.drom.ru/skoda/rapid/",
                    "https://belgorod.autovsalone.ru/cars/skoda/rapid",
                    "https://belgorod.autospot.ru/brands/skoda/rapid_ii/liftback/price/",
                    "https://belgorod.carso.ru/skoda/rapid",
                    "https://belgorod.110km.ru/prodazha/skoda/rapid/",
                    "https://mbib.ru/obl-belgorodskaya/skoda/rapid/used",
                    "https://carsdo.ru/skoda/rapid/belgorod/",
                    "https://belgorod.abc-auto.ru/skoda/rapid/",
                    "https://belgorod.riaauto.ru/skoda/rapid-2019",
                    "https://belgorod.b-kredit.com/catalog/skoda/rapid_new/",
                    "https://belgorod.ab-club.ru/catalog/skoda/rapid/",
                    "https://autoevrazia.ru/models/rapid",
                    "https://belgorod.cardana.ru/auto/skoda/rapid.html",
                    "https://locman-skoda.ru/models/rapid",
                    "https://v-belgorode.kupit-auto.com/new/skoda/rapid_old",
                    "https://blik-auto.ru/models/rapid/price",
                    "https://cars.skoda-avto.ru/?dealerid=rusc00842,rusc01749"
                ]
            ],
            "шкода рапид белгород" => [
                "sites" => [
                    "https://moravia-motors.ru/models/rapid",
                    "https://www.avito.ru/belgorod/avtomobili/skoda/rapid-asgbagicaktgtg2emsjitg2urig",
                    "https://auto.ru/belgorod/cars/skoda/rapid/all/",
                    "https://belgorod.drom.ru/skoda/rapid/",
                    "https://belgorod.autovsalone.ru/cars/skoda/rapid",
                    "https://belgorod.autospot.ru/brands/skoda/rapid_ii/liftback/price/",
                    "https://belgorod.carso.ru/skoda/rapid",
                    "https://belgorod.110km.ru/prodazha/skoda/rapid/",
                    "https://carsdo.ru/skoda/rapid/belgorod/",
                    "https://mbib.ru/obl-belgorodskaya/skoda/rapid/used",
                    "https://belgorod.riaauto.ru/skoda/rapid",
                    "https://belgorod.abc-auto.ru/skoda/rapid/",
                    "https://www.drive2.ru/cars/skoda/rapid/m194/?city=34581",
                    "https://skoda-belgorod.tmgauto.ru/",
                    "https://belgorod.newautosalon.ru/skoda-rapid/",
                    "https://belgorod.b-kredit.com/catalog/skoda/rapid_new/",
                    "https://belgorod.ab-club.ru/catalog/skoda/rapid/",
                    "https://belgorod.cardana.ru/auto/skoda/rapid.html",
                    "https://v-belgorode.kupit-auto.com/new/skoda/rapid_old",
                    "https://belgorod.incom-auto.ru/auto/skoda/rapid/"
                ]
            ],
            "шкода рапид белгород официальный дилер" => [
                "sites" => [
                    "https://moravia-motors.ru/models/rapid",
                    "https://moravia-motors.ru/",
                    "https://cars.skoda-avto.ru/?dealerid=rusc00842",
                    "https://vk.com/skodabelgorod31",
                    "https://skoda-belgorod.tmgauto.ru/",
                    "https://belgorod.autovsalone.ru/cars/skoda/rapid",
                    "https://yandex.ru/maps/org/moraviya_motors/1052441622/",
                    "https://auto.drom.ru/moravia_motors/",
                    "https://belgorod.carso.ru/skoda/rapid",
                    "https://auto.ru/diler-oficialniy/cars/all/moraviya_centr_skoda_belgorod/",
                    "https://www.avito.ru/belgorod/avtomobili/skoda/rapid-asgbagicaktgtg2emsjitg2urig",
                    "https://carsdo.ru/skoda/rapid/belgorod/",
                    "https://2gis.ru/belgorod/search/%d0%90%d0%b2%d1%82%d0%be%d1%81%d0%b0%d0%bb%d0%be%d0%bd%20skoda%20(%d1%88%d0%ba%d0%be%d0%b4%d0%b0)",
                    "http://www.skodamir.ru/diler/623-shkoda-v-belgorode.html",
                    "https://belgorod.autospot.ru/brands/skoda/rapid_ii/liftback/price/",
                    "https://belgorod.riaauto.ru/skoda/rapid",
                    "https://www.go31.ru/news/2794211/novyj-skoda-rapid-2020-u-oficialnogo-dilera-moravia-motors",
                    "https://belgorod.b-kredit.com/catalog/skoda/rapid_new/",
                    "https://rolf-skoda.ru/models/rapid",
                    "https://cars.krona-auto.ru/new/skoda/rapid"
                ]
            ],
            "шкода рапид белгород официальный дилер цены" => [
                "sites" => [
                    "https://moravia-motors.ru/models/rapid",
                    "https://moravia-motors.ru/",
                    "https://cars.skoda-avto.ru/?dealerid=rusc00842,rusc01749",
                    "https://belgorod.autovsalone.ru/cars/skoda/rapid",
                    "https://skoda-belgorod.tmgauto.ru/",
                    "https://belgorod.carso.ru/skoda/rapid",
                    "https://www.avito.ru/belgorod/avtomobili/skoda/rapid-asgbagicaktgtg2emsjitg2urig",
                    "https://carsdo.ru/skoda/rapid/belgorod/",
                    "https://belgorod.autospot.ru/brands/skoda/rapid_ii/liftback/price/",
                    "https://auto.drom.ru/moravia_motors/",
                    "https://blik-auto.ru/models/rapid/price",
                    "https://auto.ru/belgorod/cars/skoda/rapid/all/",
                    "https://vk.com/skodabelgorod31",
                    "https://radar-holding.ru/models/rapid/price",
                    "https://krona-auto.ru/models/rapid/price",
                    "https://belgorod.riaauto.ru/skoda/rapid",
                    "https://rolf-skoda.ru/models/rapid",
                    "https://belgorod.cardana.ru/auto/skoda/rapid.html",
                    "https://avto-dilery.ru/shkoda-belgorod/",
                    "https://belgorod.masmotors.ru/car/skoda/rapid"
                ]
            ],
            "шкода рапид цена белгород" => [
                "sites" => [
                    "https://auto.ru/belgorod/cars/skoda/rapid/all/",
                    "https://www.avito.ru/belgorod/avtomobili/skoda/rapid-asgbagicaktgtg2emsjitg2urig",
                    "https://moravia-motors.ru/models/rapid",
                    "https://belgorod.drom.ru/skoda/rapid/",
                    "https://belgorod.autovsalone.ru/cars/skoda/rapid",
                    "https://belgorod.autospot.ru/brands/skoda/rapid_ii/liftback/price/",
                    "https://belgorod.carso.ru/skoda/rapid",
                    "https://www.skoda-avto.ru/models/rapid/price",
                    "https://belgorod.110km.ru/prodazha/skoda/rapid/",
                    "https://belgorod.riaauto.ru/skoda/rapid",
                    "https://carsdo.ru/skoda/rapid/belgorod/",
                    "https://mbib.ru/obl-belgorodskaya/skoda/rapid/used",
                    "https://belgorod.abc-auto.ru/skoda/rapid/",
                    "https://belgorod.b-kredit.com/catalog/skoda/rapid_new/",
                    "https://belgorod.newautosalon.ru/skoda-rapid/",
                    "https://www.europa-avto.ru/models/rapid/price",
                    "https://belgorod.cardana.ru/auto/skoda/rapid.html",
                    "https://belgorod.ab-club.ru/catalog/skoda/rapid/",
                    "https://blik-auto.ru/models/rapid/price",
                    "https://v-belgorode.kupit-auto.com/new/skoda/rapid_old"
                ]
            ],
            "trade in skoda" => [
                "sites" => [
                    "https://www.skoda-avto.ru/specials/utilization",
                    "https://skoda-favorit.ru/services/buycar",
                    "https://www.drive2.ru/l/585309622957536175/",
                    "https://skoda-ca.ru/trade-in",
                    "https://auto-skoda-msk.ru/tradein",
                    "https://www.autocity-sk.ru/purchase/trade-in",
                    "https://aksa-auto.ru/tradein/skoda",
                    "https://skoda-auto-moscow.ru/tradein",
                    "https://carso.ru/tradein/skoda",
                    "https://kuntsevo.com/skoda/superb/trade-in/",
                    "https://www.incom-auto.ru/trade-in/skoda/",
                    "https://moscow.kingautosales.ru/skoda/trade-in-skoda/",
                    "https://autosalon-skoda.com/tradein",
                    "https://www.uservice.ru/trade-in/skoda/",
                    "https://www.autodrive.ru/moskva/autosalon/skoda/trade-in/",
                    "https://avisavto.ru/trade-in/skoda",
                    "https://albion-tradein.ru/auto/skoda",
                    "http://avtotochki.ru/catalog/trade-in-skoda/pt7c1657912419996s1104vm159/",
                    "https://alb-tradein.ru/auto/skoda",
                    "http://bordoauto.ru/treyd-in/skoda/"
                ]
            ],
            "trade in skoda условия" => [
                "sites" => [
                    "https://www.skoda-avto.ru/specials/utilization",
                    "https://www.drive2.ru/l/585309622957536175/",
                    "https://skoda-favorit.ru/services/buycar",
                    "https://skoda-ca.ru/trade-in",
                    "https://1avtoyurist.ru/kuplya-prodazha/trejd-in/shkoda.html",
                    "https://auto-skoda-msk.ru/tradein",
                    "https://www.autocity-sk.ru/purchase/trade-in",
                    "https://skoda-auto-moscow.ru/tradein",
                    "https://skoda-karoq.ru/forum/viewtopic.php?t=231",
                    "https://carso.ru/tradein/skoda/octavia-old",
                    "https://xn----7sbabl1agaca2aiayoqc5bs0e.xn--p1ai/auto/scoda/",
                    "https://moscow.kingautosales.ru/skoda/trade-in-skoda/",
                    "https://kuntsevo.com/skoda/octavia/trade-in/",
                    "https://www.incom-auto.ru/trade-in/skoda/",
                    "https://aksa-auto.ru/tradein/skoda/octavia",
                    "https://agat-group.com/specials/programma-utilizaci-v-skoda/",
                    "http://bordoauto.ru/treyd-in/skoda/",
                    "https://avisavto.ru/trade-in/skoda",
                    "https://www.youtube.com/watch?v=bvzgqfqt_9w",
                    "https://center-skoda.com/trade-in"
                ]
            ],
            "купить шкоду трейд ин" => [
                "sites" => [
                    "https://www.skoda-avto.ru/specials/utilization",
                    "https://skoda-favorit.ru/services/buycar",
                    "https://carso.ru/tradein/skoda",
                    "https://aksa-auto.ru/tradein/skoda",
                    "https://skoda-ca.ru/trade-in",
                    "https://skoda-auto-moscow.ru/tradein",
                    "https://www.autocity-sk.ru/purchase/trade-in",
                    "https://auto-skoda-msk.ru/tradein",
                    "https://praga-motors.ru/purchase/specials/utilization",
                    "https://kuntsevo.com/skoda/superb/trade-in/",
                    "https://avtoruss-tradein.ru/skoda.html",
                    "https://tradein.uservice.ru/catalog/skoda/",
                    "https://www.incom-auto.ru/trade-in/skoda/kodiaq/",
                    "https://www.drive2.ru/l/585309622957536175/",
                    "https://moscow.kingautosales.ru/skoda/trade-in-skoda/",
                    "https://1avtoyurist.ru/kuplya-prodazha/trejd-in/shkoda.html",
                    "https://tradein-kuntsevo.ru/catalog/skoda/",
                    "http://avtotochki.ru/catalog/trade-in-skoda/pt7c1657912419996s1104vm159/",
                    "https://riaauto.ru/skoda/rapid-v-tradein",
                    "https://center-skoda.com/trade-in"
                ]
            ],
            "условия трейд ин шкода" => [
                "sites" => [
                    "https://www.skoda-avto.ru/specials/utilization",
                    "https://skoda-favorit.ru/purchase/specials/5418931",
                    "https://www.drive2.ru/l/585309622957536175/",
                    "https://1avtoyurist.ru/kuplya-prodazha/trejd-in/shkoda.html",
                    "https://skoda-ca.ru/trade-in",
                    "https://www.autocity-sk.ru/purchase/trade-in",
                    "https://auto-skoda-msk.ru/tradein",
                    "https://carso.ru/tradein/skoda/octavia-old",
                    "https://aksa-auto.ru/tradein/skoda",
                    "https://moscow.kingautosales.ru/skoda/trade-in-skoda/",
                    "https://kuntsevo.com/skoda/octavia/trade-in/",
                    "https://skoda-karoq.ru/forum/viewtopic.php?t=231",
                    "https://xn----7sbabl1agaca2aiayoqc5bs0e.xn--p1ai/auto/scoda/",
                    "https://skoda-rapid.ru/avtomobili/obmen-avto-po-sisteme-trade-in/",
                    "https://skoda-kodiaq.ru/forum/viewtopic.php?t=3948",
                    "https://www.youtube.com/watch?v=bvzgqfqt_9w",
                    "https://center-skoda.com/trade-in",
                    "https://avisavto.ru/trade-in/skoda/karoq",
                    "http://bordoauto.ru/treyd-in/skoda/",
                    "https://albion-tradein.ru/auto/skoda"
                ]
            ],
            "шкода trade in" => [
                "sites" => [
                    "https://www.skoda-avto.ru/specials/utilization",
                    "https://skoda-favorit.ru/services/buycar",
                    "https://skoda-ca.ru/trade-in",
                    "https://skoda-auto-moscow.ru/tradein",
                    "https://auto-skoda-msk.ru/tradein",
                    "https://www.drive2.ru/l/585309622957536175/",
                    "https://carso.ru/tradein/skoda",
                    "https://aksa-auto.ru/tradein/skoda",
                    "https://www.autocity-sk.ru/purchase/trade-in",
                    "https://1avtoyurist.ru/kuplya-prodazha/trejd-in/shkoda.html",
                    "https://kuntsevo.com/skoda/octavia/trade-in/",
                    "https://www.incom-auto.ru/trade-in/skoda/",
                    "https://autosalon-skoda.com/tradein",
                    "https://albion-tradein.ru/auto/skoda",
                    "https://skoda-karoq.ru/forum/viewtopic.php?t=231",
                    "https://alb-tradein.ru/auto/skoda",
                    "https://www.uservice.ru/trade-in/skoda/",
                    "https://avisavto.ru/trade-in/skoda",
                    "https://moscow.kingautosales.ru/skoda/trade-in-skoda/",
                    "https://www.autodrive.ru/moskva/autosalon/skoda/trade-in/"
                ]
            ],
            "шкода трейд ин" => [
                "sites" => [
                    "https://www.skoda-avto.ru/specials/utilization",
                    "https://skoda-favorit.ru/services/buycar",
                    "https://skoda-ca.ru/trade-in",
                    "https://carso.ru/tradein/skoda",
                    "https://www.autocity-sk.ru/purchase/trade-in",
                    "https://skoda-auto-moscow.ru/tradein",
                    "https://aksa-auto.ru/tradein/skoda",
                    "https://www.drive2.ru/l/585309622957536175/",
                    "https://auto-skoda-msk.ru/tradein",
                    "https://praga-motors.ru/purchase/specials/utilization",
                    "https://kuntsevo.com/skoda/superb/trade-in/",
                    "https://1avtoyurist.ru/kuplya-prodazha/trejd-in/shkoda.html",
                    "https://www.incom-auto.ru/trade-in/skoda/",
                    "https://moscow.kingautosales.ru/skoda/trade-in-skoda/",
                    "https://autosalon-skoda.com/tradein",
                    "https://skoda-karoq.ru/forum/viewtopic.php?t=231",
                    "https://avtoruss-tradein.ru/skoda.html",
                    "https://albion-tradein.ru/auto/skoda",
                    "http://avtotochki.ru/catalog/trade-in-skoda/pt7c1657912419996s1104vm159/",
                    "https://tradein.uservice.ru/catalog/skoda/"
                ]
            ],
            "škoda karoq технические характеристики" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq/technology",
                    "https://www.drom.ru/catalog/skoda/karoq/",
                    "https://auto.ru/catalog/cars/skoda/karoq/21010081/21010112/specifications/",
                    "https://msmotors.ru/models/karoq/technology",
                    "https://www.drive.ru/test-drive/skoda/5e5517f3ec05c4324f000166.html",
                    "https://skoda-karoq.ru/tehnicheskie-harakteristiki.html",
                    "https://agat-skoda.ru/models/karoq/technology",
                    "https://karoq-fan.ru/tehnicheskie-harakteristiki-skoda-karoq/",
                    "https://rolf-skoda.ru/models/karoq/technology",
                    "https://sevavto.ru/models/karoq/technology",
                    "https://www.drive2.ru/e/b689geaaa2s",
                    "https://skoda-favorit.ru/models/karoq/technology",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_karoq",
                    "https://skoda-kd.ru/models/karoq/technology",
                    "https://www.auto-dd.ru/skoda-karoq-2020/",
                    "https://skoda-granadacenter.ru/models/karoq/technology",
                    "https://www.skoda-major.ru/karoq/tehnicheskie-harakteristiki/",
                    "https://skodakaroq.ru/tekhnicheskie-kharakteristiki-shkoda-karok/",
                    "https://www.autocity-sk.ru/models/karoq/technology",
                    "https://alva-skoda.ru/models/karoq/technology"
                ]
            ],
            "škoda karoq 2022 технические характеристики" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq/technology",
                    "https://www.drom.ru/catalog/skoda/karoq/2022/",
                    "https://naavtotrasse.ru/skoda/skoda-karoq-2022.html",
                    "https://agat-skoda.ru/models/karoq/technology",
                    "https://auto.ru/catalog/cars/skoda/karoq/21010081/21010112/specifications/",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-karoq-2022",
                    "https://cenyavto.com/skoda-karoq-2022/",
                    "https://autovn.ru/models/karoq/technology",
                    "https://www.auto-dd.ru/skoda-karoq-2020/",
                    "https://skoda-favorit.ru/models/karoq/technology",
                    "https://sevavto.ru/models/karoq/technology",
                    "https://gt-news.ru/skoda/skoda-karoq-2022/",
                    "https://fastmb.ru/autonews/autonews_rus/21126-skoda-karoq-2022-v-rossii-start-prodazh-komplektatsii-i-tseny.html",
                    "https://autoiwc.ru/skoda/skoda-karoq.html",
                    "https://autoblogcar.ru/testdrives/407-skodakaroqcwva.html",
                    "https://www.autoskd.ru/models/karoq/technology",
                    "https://skoda-karoq.ru/tehnicheskie-harakteristiki.html",
                    "https://autompv.ru/new-auto/50270-skoda-karoq-2023.html",
                    "https://roadres.com/skoda/karoq/tech/",
                    "https://autocentr.su/news/obzor-skoda-karoq-2022-cena-v-russia"
                ]
            ],
            "škoda karoq характеристики" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq/technology",
                    "https://www.drom.ru/catalog/skoda/karoq/",
                    "https://auto.ru/catalog/cars/skoda/karoq/21010081/21010112/specifications/",
                    "https://www.drive.ru/test-drive/skoda/5e5517f3ec05c4324f000166.html",
                    "https://skoda-karoq.ru/tehnicheskie-harakteristiki.html",
                    "https://karoq-fan.ru/tehnicheskie-harakteristiki-skoda-karoq/",
                    "https://agat-skoda.ru/models/karoq/technology",
                    "https://www.drive2.ru/e/b689geaaa2s",
                    "https://rolf-skoda.ru/models/karoq/technology",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_karoq",
                    "https://skoda-favorit.ru/models/karoq/technology",
                    "https://autoblogcar.ru/testdrives/407-skodakaroqcwva.html",
                    "https://sevavto.ru/models/karoq/technology",
                    "https://skodakaroq.ru/tekhnicheskie-kharakteristiki-shkoda-karok/",
                    "https://www.autocity-sk.ru/models/karoq/technology",
                    "https://www.skoda-major.ru/karoq/tehnicheskie-harakteristiki/",
                    "https://skoda-kd.ru/models/karoq/technology",
                    "https://skoda-granadacenter.ru/models/karoq/technology",
                    "https://www.auto-dd.ru/skoda-karoq-2020/",
                    "https://adom.ru/skoda/karoq/tth"
                ]
            ],
            "шкода карок технические характеристики" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq/technology",
                    "https://www.drom.ru/catalog/skoda/karoq/",
                    "https://auto.ru/catalog/cars/skoda/karoq/21010081/21010112/specifications/",
                    "https://www.drive.ru/test-drive/skoda/5e5517f3ec05c4324f000166.html",
                    "https://skoda-karoq.ru/tehnicheskie-harakteristiki.html",
                    "https://msmotors.ru/models/karoq/technology",
                    "https://karoq-fan.ru/tehnicheskie-harakteristiki-skoda-karoq/",
                    "https://www.drive2.ru/e/b689geaaa2s",
                    "https://agat-skoda.ru/models/karoq/technology",
                    "https://www.auto-dd.ru/skoda-karoq-2020/",
                    "https://skoda-favorit.ru/models/karoq/technology",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_karoq",
                    "https://sevavto.ru/models/karoq/technology",
                    "https://rolf-skoda.ru/models/karoq/technology",
                    "https://adom.ru/skoda/karoq/tth",
                    "https://naavtotrasse.ru/skoda/skoda-karoq-2022.html",
                    "https://carsdo.ru/skoda/karoq/",
                    "https://www.skoda-major.ru/karoq/tehnicheskie-harakteristiki/",
                    "https://skoda-elvis.ru/models/karoq/technology",
                    "https://skodakaroq.ru/tekhnicheskie-kharakteristiki-shkoda-karok/"
                ]
            ],
            "шкода карок характеристики" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq/technology",
                    "https://www.drom.ru/catalog/skoda/karoq/",
                    "https://auto.ru/catalog/cars/skoda/karoq/21010081/21010112/specifications/",
                    "https://www.drive.ru/test-drive/skoda/5e5517f3ec05c4324f000166.html",
                    "https://skoda-karoq.ru/tehnicheskie-harakteristiki.html",
                    "https://karoq-fan.ru/tehnicheskie-harakteristiki-skoda-karoq/",
                    "https://agat-skoda.ru/models/karoq/technology",
                    "https://www.drive2.ru/e/b689geaaa2s",
                    "https://rolf-skoda.ru/models/karoq/technology",
                    "https://www.skoda-major.ru/karoq/tehnicheskie-harakteristiki/",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_karoq",
                    "https://skoda-favorit.ru/models/karoq/technology",
                    "https://dzen.ru/media/autogoda/obzor-skoda-karoq-5df396c94e057700ae441037",
                    "https://www.auto-dd.ru/skoda-karoq-2020/",
                    "https://skodakaroq.ru/tekhnicheskie-kharakteristiki-shkoda-karok/",
                    "https://skoda-granadacenter.ru/models/karoq/technology",
                    "https://favorit-motors.ru/catalog/new/skoda/karoq/tehnicheskie-harakteristiki/",
                    "https://adom.ru/skoda/karoq/tth",
                    "https://autoblogcar.ru/testdrives/407-skodakaroqcwva.html",
                    "https://autoiwc.ru/skoda/skoda-karoq.html"
                ]
            ],
            "skoda kodiaq laurin & klement" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-laurin-klement",
                    "https://www.skoda-major.ru/kodiaq-lk/",
                    "https://www.drom.ru/catalog/skoda/kodiaq/216523/",
                    "https://auto.ru/catalog/cars/skoda/kodiaq/20839003/20839055/equipment/20839055_21364497_20839271/",
                    "https://autoreview.ru/news/skoda-kodiaq-versiya-laurin-and-klement-i-drugie-obnovki",
                    "https://skoda-kuntsevo.ru/models/kodiaq-laurin-klement",
                    "https://skoda-vozrojdenie.ru/archival-models/kodiaq-laurin-klement-2017",
                    "https://aif.ru/auto/about/fantom_iz_proshlogo_test-drayv_skoda_kodiaq_laurin_klement",
                    "https://skoda-kodiaq.ru/novosti/185-skoda-kodiaq-laurin-klement.html",
                    "https://skoda-avtoritet.ru/models/kodiaq-laurin-klement",
                    "https://skodakodiaq.club/vse-vozmozhnosti-novogo-skoda-kodiaq-laurin-klement/",
                    "https://www.drive.ru/news/skoda/5b69aa2fec05c4180300002a.html",
                    "https://www.griffin-auto.ru/models/kodiaq-laurin-klement",
                    "https://skoda-elvis.ru/models/kodiaq-laurin-klement",
                    "https://skoda-orehovo.ru/models/kodiaq-laurin-klement",
                    "https://www.autocity-sk.ru/models/kodiaq-laurin-klement",
                    "https://autovn.ru/models/kodiaq-laurin-klement",
                    "https://carso.ru/skoda/kodiaq/kit/laurin-klement",
                    "https://www.skoda-vitebskiy.ru/models/kodiaq-laurin-klement",
                    "https://avto-bravo.ru/models/kodiaq-laurin-klement"
                ]
            ],
            "škoda kodiaq laurin klement" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-laurin-klement",
                    "https://unicum-skoda.ru/models/kodiaq-laurin-klement",
                    "https://www.skoda-major.ru/kodiaq-lk/",
                    "https://www.drom.ru/catalog/skoda/kodiaq/216523/",
                    "https://autoreview.ru/news/skoda-kodiaq-versiya-laurin-and-klement-i-drugie-obnovki",
                    "https://auto.ru/catalog/cars/skoda/kodiaq/20839003/20839055/equipment/20839055_21364497_20839271/",
                    "https://aif.ru/auto/about/fantom_iz_proshlogo_test-drayv_skoda_kodiaq_laurin_klement",
                    "https://skodakodiaq.club/vse-vozmozhnosti-novogo-skoda-kodiaq-laurin-klement/",
                    "https://skoda-kuntsevo.ru/models/kodiaq-laurin-klement",
                    "https://alva-skoda.ru/archival-models/kodiaq-laurin-klement-2017",
                    "https://skoda-freshauto.ru/models/kodiaq-laurin-klement",
                    "https://skoda-favorit.ru/models/kodiaq-laurin-klement",
                    "https://www.skoda-vitebskiy.ru/models/kodiaq-laurin-klement",
                    "https://skoda-elvis.ru/models/kodiaq-laurin-klement",
                    "https://skoda-vozrojdenie.ru/models/kodiaq-laurin-klement",
                    "https://skoda-keyauto-krd.ru/models/kodiaq-laurin-klement",
                    "https://strela-avto.ru/models/kodiaq-laurin-klement",
                    "https://skoda-kodiaq.ru/novosti/185-skoda-kodiaq-laurin-klement.html",
                    "https://skoda-avtoritet.ru/models/kodiaq-laurin-klement",
                    "https://autovn.ru/models/kodiaq-laurin-klement"
                ]
            ],
            "шкода кодиак клемент" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-laurin-klement",
                    "https://www.skoda-major.ru/kodiaq-lk/",
                    "https://auto.ru/catalog/cars/skoda/kodiaq/20839003/20839055/equipment/20839055_21364497_20839271/",
                    "https://www.drom.ru/catalog/skoda/kodiaq/216523/",
                    "https://aif.ru/auto/about/fantom_iz_proshlogo_test-drayv_skoda_kodiaq_laurin_klement",
                    "https://skoda-kodiaq.ru/novosti/185-skoda-kodiaq-laurin-klement.html",
                    "https://autoreview.ru/news/skoda-kodiaq-versiya-laurin-and-klement-i-drugie-obnovki",
                    "https://chehia-avto.ru/models/kodiaq-laurin-klement",
                    "https://skodakodiaq.club/vse-vozmozhnosti-novogo-skoda-kodiaq-laurin-klement/",
                    "https://skoda-freshauto.ru/models/kodiaq-laurin-klement",
                    "https://skoda-vozrojdenie.ru/models/kodiaq-laurin-klement",
                    "https://www.autocity-sk.ru/models/kodiaq-laurin-klement",
                    "https://www.griffin-auto.ru/models/kodiaq-laurin-klement",
                    "https://che-dom.ru/models/kodiaq-laurin-klement",
                    "https://moravia-motors.ru/archival-models/kodiaq-laurin-klement-2017",
                    "https://skoda-elvis.ru/models/kodiaq-laurin-klement",
                    "https://skoda-orehovo.ru/models/kodiaq-laurin-klement",
                    "https://skoda-keyauto-krd.ru/models/kodiaq-laurin-klement",
                    "https://skoda-avtoritet.ru/models/kodiaq-laurin-klement",
                    "https://www.skoda-vitebskiy.ru/models/kodiaq-laurin-klement"
                ]
            ],
            "шкода кодиак лаурин клемент" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-laurin-klement",
                    "https://www.skoda-major.ru/kodiaq-lk/",
                    "https://skoda-kuntsevo.ru/models/kodiaq-laurin-klement",
                    "https://aif.ru/auto/about/fantom_iz_proshlogo_test-drayv_skoda_kodiaq_laurin_klement",
                    "https://autoreview.ru/news/skoda-kodiaq-versiya-laurin-and-klement-i-drugie-obnovki",
                    "https://auto.ru/catalog/cars/skoda/kodiaq/20839003/20839055/equipment/20839055_21364497_20839271/",
                    "https://www.youtube.com/watch?v=mvc8qtobdu0",
                    "https://www.drom.ru/catalog/skoda/kodiaq/216523/",
                    "https://alt-park.ru/models/kodiaq-laurin-klement",
                    "https://skodakodiaq.club/vse-vozmozhnosti-novogo-skoda-kodiaq-laurin-klement/",
                    "https://skoda-orehovo.ru/models/kodiaq-laurin-klement",
                    "https://www.skoda-vitebskiy.ru/models/kodiaq-laurin-klement",
                    "https://skoda-vozrojdenie.ru/models/kodiaq-laurin-klement",
                    "https://www.griffin-auto.ru/models/kodiaq-laurin-klement",
                    "https://www.autocity-sk.ru/models/kodiaq-laurin-klement",
                    "https://skoda-avtoritet.ru/models/kodiaq-laurin-klement",
                    "https://skoda-elvis.ru/models/kodiaq-laurin-klement",
                    "https://che-dom.ru/models/kodiaq-laurin-klement",
                    "https://carso.ru/skoda/kodiaq/kit/laurin-klement",
                    "https://autovn.ru/models/kodiaq-laurin-klement"
                ]
            ],
            "шкода кодиак лаурин клемент комплектация цена" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq-laurin-klement",
                    "https://www.skoda-major.ru/kodiaq-lk/",
                    "https://auto.ru/catalog/cars/skoda/kodiaq/20839003/20839055/equipment/20839055_21364497_20839271/",
                    "https://riaauto.ru/skoda/kodiaq-2020/komplektacii/laurin--klement",
                    "https://skoda-keyauto-krd.ru/models/kodiaq-laurin-klement",
                    "https://carso.ru/skoda/kodiaq/kit/laurin-klement",
                    "https://www.bips.ru/skoda/kodiaq/kit/laurin-klement",
                    "https://aksa-auto.ru/catalog/skoda/kodiaq/laurin-klement",
                    "https://carsdb.ru/skoda/kodiaq/laurin__klement-13/",
                    "https://www.autocity-sk.ru/models/kodiaq-laurin-klement",
                    "https://skoda-ap.ru/models/kodiaq-laurin-klement",
                    "https://www.incom-auto.ru/auto/skoda/kodiaq/komplektacii/laurin--and--klement/",
                    "https://www.griffin-auto.ru/models/kodiaq-laurin-klement",
                    "https://strela-avto.ru/models/kodiaq-laurin-klement",
                    "https://kveta-auto.ru/models/kodiaq-laurin-klement",
                    "https://skoda-vostokmotors.ru/models/kodiaq-laurin-klement",
                    "https://carsdo.ru/skoda/kodiaq/equipment-13/",
                    "https://skoda-avtoritet.ru/models/kodiaq-laurin-klement",
                    "https://www.drom.ru/catalog/skoda/kodiaq/216523/",
                    "https://skoda-orehovo.ru/models/kodiaq-laurin-klement"
                ]
            ],
            "купить шкода карок в белгороде" => [
                "sites" => [
                    "https://moravia-motors.ru/models/karoq",
                    "https://www.avito.ru/belgorod/avtomobili/skoda/karoq-asgbagicaktgtg2emsjitg26stu",
                    "https://auto.ru/belgorod/cars/skoda/karoq/all/",
                    "https://belgorod.autovsalone.ru/cars/skoda/karoq",
                    "https://belgorod.autospot.ru/brands/skoda/karoq/suv/price/",
                    "https://www.skoda-avto.ru/models/karoq/price",
                    "https://carsdo.ru/skoda/karoq/belgorod/",
                    "https://belgorod.110km.ru/prodazha/skoda/karoq/",
                    "https://belgorod.drom.ru/skoda/karoq/new/",
                    "https://belgorod.carso.ru/skoda/karoq",
                    "https://belgorod.riaauto.ru/skoda/karoq",
                    "https://belgorod.newautosalon.ru/skoda-karoq/",
                    "https://belgorod.b-kredit.com/catalog/skoda/karoq/",
                    "https://belgorod.mbib.ru/skoda/karoq",
                    "https://belgorod.cardana.ru/auto/skoda/karoq.html",
                    "https://v-belgorode.kupit-auto.com/new/skoda/karoq",
                    "https://belgorod.incom-auto.ru/auto/skoda/karoq/",
                    "https://quto.ru/inventory/belgorodskayaoblast/skoda/karoq",
                    "https://belgorod.masmotors.ru/car/skoda/karoq",
                    "https://belgorod.altera-auto.ru/skoda/karoq/"
                ]
            ],
            "шкода карок белгород" => [
                "sites" => [
                    "https://moravia-motors.ru/models/karoq",
                    "https://auto.ru/belgorod/cars/skoda/karoq/all/",
                    "https://www.avito.ru/belgorod/avtomobili/skoda/karoq-asgbagicaktgtg2emsjitg26stu",
                    "https://belgorod.autovsalone.ru/cars/skoda/karoq",
                    "https://belgorod.autospot.ru/brands/skoda/karoq/suv/price/",
                    "https://www.skoda-avto.ru/models/karoq",
                    "https://belgorod.drom.ru/new/skoda/karoq/",
                    "https://carsdo.ru/skoda/karoq/belgorod/",
                    "https://belgorod.carso.ru/skoda/karoq",
                    "https://belgorod.110km.ru/prodazha/skoda/karoq/",
                    "https://belgorod.riaauto.ru/skoda/karoq",
                    "https://belgorod.newautosalon.ru/skoda-karoq/",
                    "https://belgorod.b-kredit.com/catalog/skoda/karoq/",
                    "https://v-belgorode.kupit-auto.com/new/skoda/karoq",
                    "https://belgorod.mbib.ru/skoda/karoq",
                    "https://belgorod.cardana.ru/auto/skoda/karoq.html",
                    "https://belgorod.incom-auto.ru/auto/skoda/karoq/",
                    "https://belgorod.ab-club.ru/catalog/skoda/karoq/",
                    "https://vk.com/skodabelgorod31",
                    "https://belgorod.masmotors.ru/car/skoda/karoq"
                ]
            ],
            "шкода карок комплектации и цены белгород" => [
                "sites" => [
                    "https://moravia-motors.ru/models/karoq",
                    "https://www.skoda-avto.ru/models/karoq/price",
                    "https://auto.ru/belgorod/cars/skoda/karoq/all/",
                    "https://belgorod.autovsalone.ru/cars/skoda/karoq",
                    "https://www.avito.ru/belgorod/avtomobili/skoda/karoq-asgbagicaktgtg2emsjitg26stu",
                    "https://belgorod.autospot.ru/brands/skoda/karoq/suv/price/",
                    "https://carsdo.ru/skoda/karoq/belgorod/",
                    "https://belgorod.carso.ru/skoda/karoq",
                    "https://belgorod.drom.ru/new/skoda/karoq/",
                    "https://belgorod.riaauto.ru/skoda/karoq",
                    "https://belgorod.newautosalon.ru/skoda-karoq/",
                    "https://belgorod.b-kredit.com/catalog/skoda/karoq/",
                    "https://belgorod.110km.ru/prodazha/skoda/karoq/",
                    "https://v-belgorode.kupit-auto.com/new/skoda/karoq",
                    "https://belgorod.cardana.ru/auto/skoda/karoq.html",
                    "https://belgorod.incom-auto.ru/auto/skoda/karoq/",
                    "https://interkar.ru/models/karoq",
                    "https://skoda-elvis.ru/models/karoq",
                    "https://belgorod.masmotors.ru/car/skoda/karoq",
                    "https://belgorod.altera-auto.ru/skoda/karoq/"
                ]
            ],
            "шкода карок цена в белгороде" => [
                "sites" => [
                    "https://moravia-motors.ru/models/karoq",
                    "https://auto.ru/belgorod/cars/skoda/karoq/all/",
                    "https://www.avito.ru/belgorod/avtomobili/skoda/karoq-asgbagicaktgtg2emsjitg26stu",
                    "https://belgorod.autovsalone.ru/cars/skoda/karoq",
                    "https://www.skoda-avto.ru/models/karoq/price",
                    "https://belgorod.autospot.ru/brands/skoda/karoq/suv/price/",
                    "https://carsdo.ru/skoda/karoq/belgorod/",
                    "https://belgorod.carso.ru/skoda/karoq",
                    "https://belgorod.110km.ru/prodazha/skoda/karoq/",
                    "https://belgorod.drom.ru/new/skoda/karoq/",
                    "https://belgorod.riaauto.ru/skoda/karoq",
                    "https://belgorod.newautosalon.ru/skoda-karoq/",
                    "https://v-belgorode.kupit-auto.com/new/skoda/karoq",
                    "https://belgorod.cardana.ru/auto/skoda/karoq.html",
                    "https://belgorod.mbib.ru/skoda/karoq",
                    "https://belgorod.b-kredit.com/catalog/skoda/karoq/",
                    "https://belgorod.incom-auto.ru/auto/skoda/karoq/",
                    "https://quto.ru/inventory/belgorodskayaoblast/skoda/karoq",
                    "https://avto-bravo.ru/models/karoq/price",
                    "https://belgorod.altera-auto.ru/skoda/karoq/"
                ]
            ],
            "skoda octavia combi характеристики" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia-combi-2017/technology",
                    "https://auto.ru/catalog/cars/skoda/octavia/20898195/20898245/specifications/",
                    "https://www.drom.ru/catalog/skoda/octavia/g_2016_7304/",
                    "http://www.octavia-avto.ru/a7-combi-tech",
                    "https://vmsurgut-skoda.ru/archival-models/octavia-combi-2017/technology",
                    "https://www.skoda-major.ru/octavia-combi/tehnicheskie-harakteristiki/",
                    "https://avtomarket.ru/catalog/skoda/octavia/5dr-wagon-2009-2013/",
                    "https://www.auto-dd.ru/skoda-octavia-combi/",
                    "https://carsclick.ru/skoda/obzor-avtomobilej/oktavija-kombi-2020/",
                    "https://carexpert.ru/models/skoda/octavia_combi/tech/",
                    "https://www.gazeta-a.ru/autocatalog/skoda/octavia_(2017-2019)/20_tdi_combi/",
                    "https://www.skoda-portal.ru/shkoda-oktaviya-kombi-universal-2021-praktichnyj-i-kachestvennyj-semejnyj-avtomobil/",
                    "https://www.drive.ru/test-drive/skoda/4efb32bb00f11713001e1f49.html",
                    "https://www.auto-mgn.ru/catalog/skoda/octavia/wagon/tech/",
                    "https://fastmb.ru/testdrive/1782-skoda-octavia-combi-2017-dolgozhdannoe-obnovlenie-universalnogo-cheha.html",
                    "https://skoda-kuntsevo.ru/archival-models/octavia-combi-old",
                    "https://bibipedia.info/tech_harakteristiki/skoda/octavia_combi",
                    "https://adom.ru/skoda/octavia-combi",
                    "https://motor.ru/testdrives/skodaoctcombi.htm",
                    "https://prime-auto.ru/cars/skoda/octavia-combi/tech/"
                ]
            ],
            "škoda octavia combi технические характеристики" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia-combi-2017/technology",
                    "https://auto.ru/catalog/cars/skoda/octavia/20898195/20898245/specifications/",
                    "https://skoda-kuntsevo.ru/archival-models/octavia-combi-2017/technology",
                    "https://www.drom.ru/catalog/skoda/octavia/g_2016_7304/",
                    "https://www.skoda-major.ru/octavia-combi/tehnicheskie-harakteristiki/",
                    "https://km-auto.ru/archival-models/octavia-combi-2017/technology",
                    "http://www.octavia-avto.ru/a7-combi-tech",
                    "https://carsclick.ru/skoda/obzor-avtomobilej/oktavija-kombi-2020/",
                    "https://carexpert.ru/models/skoda/octavia_combi/tech/",
                    "https://www.auto-dd.ru/skoda-octavia-combi/",
                    "https://otto-car.ru/archival-models/octavia-combi-2017/technology",
                    "https://avtomarket.ru/catalog/skoda/octavia/5dr-wagon-2009-2013/",
                    "https://www.gazeta-a.ru/autocatalog/skoda/octavia_(2017-2019)/20_tdi_combi/",
                    "https://adom.ru/skoda/octavia-combi",
                    "https://www.auto-mgn.ru/catalog/skoda/octavia/wagon/tech/",
                    "https://www.drive.ru/brands/skoda/models/2017/octavia_combi/18_dsg_ambition",
                    "https://auto.ironhorse.ru/skoda-octavia-combi-3_3469.html",
                    "https://roadres.com/skoda/octavia-3-combi/tech/",
                    "https://bibipedia.info/tech_harakteristiki/skoda/octavia_combi",
                    "https://riaauto.ru/skoda/octavia-combi/tth"
                ]
            ],
            "шкода октавия комби технические характеристики" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia-combi-2017/technology",
                    "https://auto.ru/catalog/cars/skoda/octavia/20898195/20898245/specifications/",
                    "https://carsclick.ru/skoda/obzor-avtomobilej/oktavija-kombi-2020/",
                    "https://skoda-kuntsevo.ru/archival-models/octavia-combi-2017/technology",
                    "https://www.drom.ru/catalog/skoda/octavia/g_2016_7304/",
                    "http://www.octavia-avto.ru/a7-combi-tech",
                    "https://avtomarket.ru/catalog/skoda/octavia/5dr-wagon-2009-2013/",
                    "https://carexpert.ru/models/skoda/octavia_combi/tech/",
                    "https://www.skoda-major.ru/octavia-combi/tehnicheskie-harakteristiki/",
                    "https://www.auto-dd.ru/skoda-octavia-combi/",
                    "https://www.gazeta-a.ru/autocatalog/skoda/octavia_(2017-2019)/20_tdi_combi/",
                    "https://www.auto-mgn.ru/catalog/skoda/octavia/wagon/tech/",
                    "https://auto.ironhorse.ru/skoda-octavia-combi-3_3469.html",
                    "https://bibipedia.info/tech_harakteristiki/skoda/octavia_combi/octavia_i_combi_1997_-_2010",
                    "https://www.skoda-portal.ru/shkoda-oktaviya-kombi-universal-2021-praktichnyj-i-kachestvennyj-semejnyj-avtomobil/",
                    "https://fastmb.ru/testdrive/1782-skoda-octavia-combi-2017-dolgozhdannoe-obnovlenie-universalnogo-cheha.html",
                    "https://riaauto.ru/skoda/octavia-combi/tth",
                    "https://roadres.com/skoda/octavia-3-combi/tech/",
                    "https://adom.ru/skoda/octavia-combi",
                    "https://www.drive.ru/brands/skoda/models/2017/octavia_combi/18_dsg_ambition"
                ]
            ],
            "шкода октавия комби характеристики" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/octavia-combi-2017/technology",
                    "https://auto.ru/catalog/cars/skoda/octavia/4560887/4560896/specifications/",
                    "https://carsclick.ru/skoda/obzor-avtomobilej/oktavija-kombi-2020/",
                    "http://www.octavia-avto.ru/a7-combi-tech",
                    "https://www.drom.ru/catalog/skoda/octavia/g_2016_7304/",
                    "https://www.auto-dd.ru/skoda-octavia-combi/",
                    "https://avtomarket.ru/catalog/skoda/octavia/5dr-wagon-2009-2013/",
                    "https://www.skoda-major.ru/octavia-combi/tehnicheskie-harakteristiki/",
                    "https://carexpert.ru/models/skoda/octavia_combi/tech/",
                    "https://www.skoda-portal.ru/shkoda-oktaviya-kombi-universal-2021-praktichnyj-i-kachestvennyj-semejnyj-avtomobil/",
                    "https://www.auto-mgn.ru/catalog/skoda/octavia/wagon/tech/",
                    "https://bibipedia.info/tech_harakteristiki/skoda/octavia_combi",
                    "https://auto.ironhorse.ru/skoda-octavia-combi-3_3469.html",
                    "https://fastmb.ru/testdrive/1782-skoda-octavia-combi-2017-dolgozhdannoe-obnovlenie-universalnogo-cheha.html",
                    "https://roadres.com/skoda/octavia-3-combi/tech/",
                    "https://adom.ru/skoda/octavia-combi",
                    "https://www.gazeta-a.ru/autocatalog/skoda/octavia_(2017-2019)/20_tdi_combi/",
                    "https://www.drive.ru/brands/skoda/models/2017/octavia_combi/14_dsg_ambition",
                    "https://autorating.ru/cars/skoda/octavia-combi-2017/",
                    "https://riaauto.ru/skoda/octavia-combi/tth"
                ]
            ],
            "купить шкода рапид старый оскол" => [
                "sites" => [
                    "https://www.avito.ru/staryy_oskol/avtomobili/skoda/rapid-asgbagicaktgtg2emsjitg2urig",
                    "https://auto.ru/staryy_oskol/cars/skoda/rapid/all/",
                    "https://stariy-oskol.drom.ru/skoda/rapid/",
                    "https://stariy-oskol.autovsalone.ru/cars/skoda/rapid",
                    "https://staryy-oskol.110km.ru/prodazha/skoda/rapid/",
                    "https://moravia-motors.ru/models/rapid",
                    "https://staryy-oskol.mbib.ru/skoda/rapid",
                    "https://v-starom-oskole.kupit-auto.com/new/skoda/rapid_new",
                    "https://skoda-oskol.tmgauto.ru/",
                    "https://staryj-oskol.ab-club.ru/catalog/skoda/rapid/",
                    "https://stariy-oskol.cardana.ru/auto/skoda/rapid.html",
                    "https://stariy-oskol.abc-auto.ru/skoda/rapid/",
                    "https://staryy-oskol.b-kredit.com/catalog/skoda/rapid_new/",
                    "http://stariy-oskol.lst-group.ru/new/skoda/rapid_hatch5dr/",
                    "https://cars.skoda-avto.ru/?dealerid=rusc00842,rusc01749",
                    "https://avto.mitula.ru/avto/skoda-rapid-2016-%d1%81%d1%82%d0%b0%d1%80%d1%8b%d0%b9-%d0%be%d1%81%d0%ba%d0%be%d0%bb",
                    "http://sobut.ru/staryy-oskol/skoda-rapid-obyavleniya/",
                    "https://avto-dilery.ru/shkoda-staryj-oskol/",
                    "https://rydo.ru/staryy-oskol/auto-skoda-rapid/",
                    "https://belgorod.autospot.ru/brands/skoda/rapid_ii/liftback/price/"
                ]
            ],
            "шкода рапид старый оскол" => [
                "sites" => [
                    "https://www.avito.ru/staryy_oskol/avtomobili/skoda/rapid-asgbagicaktgtg2emsjitg2urig",
                    "https://auto.ru/staryy_oskol/cars/skoda/rapid/all/",
                    "https://stariy-oskol.drom.ru/skoda/rapid/",
                    "https://stariy-oskol.autovsalone.ru/cars/skoda/rapid",
                    "https://moravia-motors.ru/models/rapid",
                    "https://skoda-oskol.tmgauto.ru/",
                    "https://staryy-oskol.110km.ru/prodazha/skoda/rapid/",
                    "https://staryy-oskol.mbib.ru/skoda/rapid",
                    "https://v-starom-oskole.kupit-auto.com/new/skoda/rapid_new",
                    "https://stariy-oskol.cardana.ru/auto/skoda/rapid.html",
                    "https://staryj-oskol.ab-club.ru/catalog/skoda/rapid/",
                    "https://stariy-oskol.abc-auto.ru/skoda/rapid/",
                    "http://stariy-oskol.lst-group.ru/new/skoda/rapid_hatch5dr/",
                    "https://cars.skoda-avto.ru/?dealerid=rusc01749",
                    "https://www.drive2.ru/cars/skoda/rapid/m194/?city=35861",
                    "https://belgorod.autospot.ru/brands/skoda/rapid_ii/liftback/price/",
                    "https://vk.com/skodabelgorod31",
                    "https://oskol.city/news/auto/70133/",
                    "http://www.skodamir.ru/diler/2123-skoda-staryj-oskol.html",
                    "https://rydo.ru/staryy-oskol/auto-skoda-rapid/"
                ]
            ],
            "шкода рапид цены старый оскол" => [
                "sites" => [
                    "https://auto.ru/staryy_oskol/cars/skoda/rapid/all/",
                    "https://www.avito.ru/staryy_oskol/avtomobili/skoda/rapid-asgbagicaktgtg2emsjitg2urig",
                    "https://stariy-oskol.autovsalone.ru/cars/skoda/rapid",
                    "https://stariy-oskol.drom.ru/skoda/rapid/",
                    "https://staryy-oskol.110km.ru/prodazha/skoda/rapid/",
                    "https://www.skoda-avto.ru/models/rapid/price",
                    "https://stariy-oskol.cardana.ru/auto/skoda/rapid.html",
                    "https://v-starom-oskole.kupit-auto.com/new/skoda/rapid_new",
                    "https://moravia-motors.ru/models/rapid",
                    "https://staryj-oskol.ab-club.ru/catalog/skoda/rapid/",
                    "https://skoda-oskol.tmgauto.ru/",
                    "http://stariy-oskol.lst-group.ru/new/skoda/rapid_hatch5dr/",
                    "https://mbib.ru/obl-belgorodskaya/skoda/rapid/used",
                    "https://belgorod.autospot.ru/brands/skoda/rapid_ii/liftback/price/",
                    "https://belgorod.carso.ru/skoda/rapid",
                    "https://carsdo.ru/skoda/rapid/",
                    "https://staryy-oskol.b-kredit.com/catalog/skoda/",
                    "https://avto.mitula.ru/avto/skoda-rapid-2016-%d1%81%d1%82%d0%b0%d1%80%d1%8b%d0%b9-%d0%be%d1%81%d0%ba%d0%be%d0%bb",
                    "https://rydo.ru/staryy-oskol/auto-skoda-rapid/",
                    "https://avto-dilery.ru/shkoda-staryj-oskol/"
                ]
            ],
            "шкода рапид 2022 старый оскол" => [
                "sites" => [
                    "https://auto.ru/staryy_oskol/cars/skoda/rapid/2022-year/all/",
                    "https://www.avito.ru/staryy_oskol/avtomobili/novyy/skoda/rapid-asgbagica0sgfmbmaec2dz6zkok2dzsuka",
                    "https://stariy-oskol.autovsalone.ru/cars/skoda/rapid",
                    "https://staryy-oskol.110km.ru/prodazha/skoda/rapid/year-2022/",
                    "https://moravia-motors.ru/models/rapid",
                    "https://skoda-oskol.tmgauto.ru/",
                    "https://stariy-oskol.abc-auto.ru/skoda/rapid-20new/",
                    "https://staryy-oskol.b-kredit.com/catalog/skoda/rapid_old/nalichie/",
                    "https://stariy-oskol.drom.ru/skoda/rapid/new/",
                    "https://v-starom-oskole.kupit-auto.com/new/skoda/rapid_new",
                    "https://stariy-oskol.allnewcars.ru/skoda/rapid-new/",
                    "https://www.skoda-avto.ru/models/rapid",
                    "https://staryj-oskol.ab-club.ru/catalog/skoda/rapid-535655/",
                    "https://stariy-oskol.cardana.ru/auto/skoda/rapid.html",
                    "http://stariy-oskol.lst-group.ru/new/skoda/rapid_hatch5dr/",
                    "https://mbib.ru/obl-belgorodskaya/skoda/rapid/used",
                    "https://oskol.city/news/auto/70133/",
                    "https://belgorod.autospot.ru/brands/skoda/rapid_ii/liftback/price/",
                    "https://avto-dilery.ru/shkoda-staryj-oskol/",
                    "https://nz-cars.ru/cars/skoda/rapid/"
                ]
            ],
            "skoda superb технические характеристики" => [
                "sites" => [
                    "https://www.drom.ru/catalog/skoda/superb/",
                    "https://www.skoda-avto.ru/models/superb2015/technology",
                    "https://auto.ru/catalog/cars/skoda/superb/",
                    "http://www.autonet.ru/auto/ttx/skoda/superb",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_superb",
                    "https://avtomarket.ru/catalog/skoda/superb/",
                    "https://skoda.medved-vostok.ru/models/superb/technology",
                    "https://skoda-favorit.ru/models/superb/technology",
                    "https://110km.ru/tth/skoda/superb/",
                    "https://rolf-skoda.ru/models/superb/technology",
                    "https://sevavto.ru/models/superb/technology",
                    "https://www.skoda-major.ru/superb/tehnicheskie-harakteristiki/",
                    "https://www.auto-dd.ru/skoda-superb-2020/",
                    "https://drive-skoda.ru/superb/3-tehnicheskie-harakteristiki",
                    "https://carexpert.ru/models/skoda/superb/tech/",
                    "https://skoda-wagner.ru/models/superb/technology",
                    "https://autospot.ru/brands/skoda/512/technical-parameters/",
                    "https://skoda-autoug.ru/models/superb/technology",
                    "https://fastmb.ru/testdrive/3895-obzor-shkoda-superb-2020-tehnicheskie-harakteristiki-i-foto.html",
                    "https://skoda-gradavto.ru/models/superb/technology"
                ]
            ],
            "skoda superb характеристики" => [
                "sites" => [
                    "https://www.drom.ru/catalog/skoda/superb/",
                    "https://www.skoda-avto.ru/models/superb2015/technology",
                    "https://auto.ru/catalog/cars/skoda/superb/",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_superb",
                    "https://skoda-favorit.ru/models/superb/technology",
                    "http://www.autonet.ru/auto/ttx/skoda/superb",
                    "https://avtomarket.ru/catalog/skoda/superb/",
                    "https://skoda.medved-vostok.ru/models/superb/technology",
                    "https://www.drive.ru/test-drive/skoda/5d2f1970ec05c4ff4a000138.html",
                    "https://rolf-skoda.ru/models/superb/technology",
                    "https://www.gazeta-a.ru/autocatalog/skoda/superb/",
                    "https://www.skoda-major.ru/superb/tehnicheskie-harakteristiki/",
                    "https://110km.ru/tth/skoda/superb/",
                    "https://www.auto-dd.ru/skoda-superb-2020/",
                    "https://fastmb.ru/testdrive/3895-obzor-shkoda-superb-2020-tehnicheskie-harakteristiki-i-foto.html",
                    "https://autospot.ru/brands/skoda/512/technical-parameters/",
                    "https://drive-skoda.ru/superb/3-tehnicheskie-harakteristiki",
                    "https://avilon.ru/brands/skoda/superb/i/sedan/tehnicheskie-harakteristiki/",
                    "https://3dnews.ru/948214/obzor-skoda-superb-praktichnost-s-premialnim-ottenkom",
                    "https://carexpert.ru/models/skoda/superb/tech/"
                ]
            ],
            "шкода суперб технические характеристики" => [
                "sites" => [
                    "https://www.drom.ru/catalog/skoda/superb/",
                    "https://www.skoda-avto.ru/models/superb2015/technology",
                    "https://auto.ru/catalog/cars/skoda/superb/",
                    "https://avtomarket.ru/catalog/skoda/superb/",
                    "http://www.autonet.ru/auto/ttx/skoda/superb",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_superb",
                    "https://skoda-favorit.ru/models/superb/technology",
                    "https://skoda.medved-vostok.ru/models/superb/technology",
                    "https://t-motors-skoda.ru/models/superb/technology",
                    "https://rolf-skoda.ru/models/superb/technology",
                    "https://carexpert.ru/models/skoda/superb/tech/",
                    "https://sevavto.ru/models/superb/technology",
                    "https://www.auto-dd.ru/skoda-superb-2020/",
                    "https://www.gazeta-a.ru/autocatalog/skoda/superb/",
                    "https://drive-skoda.ru/superb/3-tehnicheskie-harakteristiki",
                    "https://110km.ru/tth/skoda/superb/",
                    "https://www.skoda-major.ru/superb/tehnicheskie-harakteristiki/",
                    "https://favorit-motors.ru/catalog/new/skoda/superb/tehnicheskie-harakteristiki/",
                    "http://www.motorpage.ru/skoda/superb/last/",
                    "https://bibipedia.info/tech_harakteristiki/skoda/superb"
                ]
            ],
            "шкода суперб характеристики" => [
                "sites" => [
                    "https://www.drom.ru/catalog/skoda/superb/",
                    "https://www.skoda-avto.ru/models/superb2015/technology",
                    "https://auto.ru/catalog/cars/skoda/superb/",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_superb",
                    "https://avtomarket.ru/catalog/skoda/superb/",
                    "https://skoda-favorit.ru/models/superb/technology",
                    "http://www.autonet.ru/auto/ttx/skoda/superb",
                    "https://t-motors-skoda.ru/models/superb/technology",
                    "https://fastmb.ru/testdrive/3895-obzor-shkoda-superb-2020-tehnicheskie-harakteristiki-i-foto.html",
                    "https://110km.ru/tth/skoda/superb/",
                    "https://rolf-skoda.ru/models/superb/technology",
                    "https://www.drive.ru/test-drive/skoda/5d2f1970ec05c4ff4a000138.html",
                    "https://www.auto-dd.ru/skoda-superb-2020/",
                    "https://drive-skoda.ru/superb/3-tehnicheskie-harakteristiki",
                    "https://www.skoda-major.ru/superb/tehnicheskie-harakteristiki/",
                    "https://autospot.ru/brands/skoda/512/technical-parameters/",
                    "https://www.gazeta-a.ru/autocatalog/skoda/superb/",
                    "https://avto-russia.ru/autos/skoda/skoda_superb.html",
                    "https://avilon.ru/brands/skoda/superb/i/sedan/tehnicheskie-harakteristiki/",
                    "https://carexpert.ru/models/skoda/superb/tech/"
                ]
            ],
            "skoda тест драйв" => [
                "sites" => [
                    "https://www.skoda-avto.ru/test-drive",
                    "https://www.drive.ru/brands/skoda/test-drive",
                    "https://auto.ru/video/cars/skoda/",
                    "https://auto.mail.ru/testdrives/skoda/",
                    "https://www.drom.ru/info/test-drive/skoda/",
                    "https://www.youtube.com/playlist?list=plm5p3mp_yt76aqqzyujj11cilpzgj95pt",
                    "https://www.zr.ru/cars/skoda/tests/",
                    "http://www.motorpage.ru/skoda/test-drives/",
                    "https://avtomarket.ru/testdrives/skoda/",
                    "https://www.kolesa.ru/test-drive/veteran-skoda-kodiaq-protiv-novenkogo-chery-tiggo-8-pro-max-ostatki-sladki",
                    "http://www.autonavigator.ru/guides/test-drive/skoda/",
                    "https://motor.ru/testdrives/skoda-octavia-2021.htm",
                    "https://carsdo.ru/skoda/octavia/test-drive/",
                    "https://110km.ru/art/skoda/testdrives/",
                    "https://skoda-favorit.ru/articles/test-drive",
                    "https://www.drive2.ru/e/bmnvaeaaaie",
                    "https://avtotachki.com/tag/test-drives-skoda/",
                    "https://carexpert.ru/testdrive/skoda/",
                    "http://video-test-drive.ru/m/skoda",
                    "https://somanyhorses.ru/test-drayv-skoda-octavia-2020-trepeschite-nemcy/"
                ]
            ],
            "новая шкода тест драйв" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/karoq2020/test-drive",
                    "https://www.drive.ru/brands/skoda/test-drive",
                    "https://auto.ru/video/cars/skoda/",
                    "https://www.youtube.com/watch?v=lyitt2-2dxm",
                    "https://www.zr.ru/cars/skoda/tests/",
                    "https://www.drom.ru/info/test-drive/skoda/",
                    "https://auto.mail.ru/testdrives/skoda/",
                    "https://motor.ru/testdrives/skoda-octavia-2021.htm",
                    "https://www.drive2.ru/b/563861724512388497/",
                    "https://carsdo.ru/skoda/octavia/test-drive/",
                    "https://www.kolesa.ru/test-drive/veteran-skoda-kodiaq-protiv-novenkogo-chery-tiggo-8-pro-max-ostatki-sladki",
                    "https://dzen.ru/media/real_car_stories/pervyi-testdraiv-novoi-skoda-octavia-2020-uznaem-vsiu-pravdu-5f69f49ef52b7a188115ab8b",
                    "https://ru.motor1.com/reviews/408819/skoda-octavia-combi-2020-test/",
                    "https://29.ru/text/auto/2020/11/27/69579993/",
                    "https://ngs.ru/text/auto/2020/09/19/69472207/",
                    "https://smotrim.ru/article/2607843",
                    "https://somanyhorses.ru/test-drayv-skoda-octavia-2020-trepeschite-nemcy/",
                    "http://www.motorpage.ru/skoda/test-drives/",
                    "https://www.avtovzglyad.ru/avto/test/2021-02-02-farshirovannyj-perets-test-drajv-novoj-skoda-octavia/",
                    "https://74.ru/text/auto/2020/11/27/69579923/"
                ]
            ],
            "тест драйв шкода" => [
                "sites" => [
                    "https://www.skoda-avto.ru/test-drive",
                    "https://www.drive.ru/brands/skoda/test-drive",
                    "https://auto.ru/video/cars/skoda/",
                    "https://www.zr.ru/cars/skoda/tests/",
                    "https://www.drom.ru/info/test-drive/skoda/",
                    "https://www.youtube.com/playlist?list=plm5p3mp_yt76aqqzyujj11cilpzgj95pt",
                    "https://auto.mail.ru/testdrives/skoda/",
                    "http://www.motorpage.ru/skoda/test-drives/",
                    "https://avtomarket.ru/testdrives/skoda/",
                    "https://www.kolesa.ru/test-drive/veteran-skoda-kodiaq-protiv-novenkogo-chery-tiggo-8-pro-max-ostatki-sladki",
                    "http://www.autonavigator.ru/guides/test-drive/skoda/",
                    "https://skoda-favorit.ru/articles/test-drive",
                    "https://110km.ru/art/skoda/testdrives/",
                    "https://www.drive2.ru/b/563861724512388497/",
                    "https://www.autopanorama.ru/test-drives/skoda/karoq/",
                    "https://carsdo.ru/skoda/octavia/test-drive/",
                    "https://motor.ru/testdrives/skoda-octavia-2021.htm",
                    "https://dvizhok.su/auto/test-drajv-skoda-octavia-iv-kompaktnyij-biznes-klass",
                    "https://tavto.ru/test-drives/brands/skoda/",
                    "https://carexpert.ru/testdrive/skoda/"
                ]
            ],
            "купить шкода кодиак белгород" => [
                "sites" => [
                    "https://www.avito.ru/belgorod/avtomobili/skoda/kodiaq-asgbagicaktgtg2emsjitg3wqcg",
                    "https://auto.ru/belgorod/cars/skoda/kodiaq/all/",
                    "https://moravia-motors.ru/models/kodiaq",
                    "https://belgorod.drom.ru/skoda/kodiaq/",
                    "https://belgorod.autovsalone.ru/cars/skoda/kodiaq",
                    "https://belgorod.carso.ru/skoda/kodiaq",
                    "https://belgorod.autospot.ru/brands/skoda/kodiaq/suv/price/",
                    "https://carsdo.ru/skoda/kodiaq/belgorod/",
                    "https://cars.skoda-avto.ru/kodiaq/",
                    "https://belgorod.newautosalon.ru/kodiaq/",
                    "https://belgorod.riaauto.ru/skoda/kodiaq",
                    "https://belgorod.110km.ru/prodazha/skoda/kodiaq/poderzhannie/",
                    "https://mbib.ru/obl-belgorodskaya/skoda/kodiaq",
                    "https://v-belgorode.kupit-auto.com/new/skoda/kodiaq",
                    "https://www.gazeta-a.ru/autosearch/belgorod/skoda/kodiaq/",
                    "https://belgorod.incom-auto.ru/auto/skoda/kodiaq/",
                    "https://autosalon-skoda.com/auto/skoda/kodiaq_kodiaq",
                    "https://www.autoskd.ru/models/kodiaq",
                    "https://avto.mitula.ru/avto/skoda-kodiaq-%d0%b1%d0%b5%d0%bb%d0%b3%d0%be%d1%80%d0%be%d0%b4",
                    "https://skoda-s-auto.ru/models/kodiaq"
                ]
            ],
            "шкода кодиак белгород" => [
                "sites" => [
                    "https://moravia-motors.ru/models/kodiaq",
                    "https://auto.ru/belgorod/cars/skoda/kodiaq/all/",
                    "https://www.avito.ru/belgorod/avtomobili/skoda/kodiaq-asgbagicaktgtg2emsjitg3wqcg",
                    "https://belgorod.drom.ru/skoda/kodiaq/",
                    "https://belgorod.autovsalone.ru/cars/skoda/kodiaq",
                    "https://belgorod.carso.ru/skoda/kodiaq",
                    "https://belgorod.autospot.ru/brands/skoda/kodiaq/suv/price/",
                    "https://carsdo.ru/skoda/kodiaq/belgorod/",
                    "https://belgorod.newautosalon.ru/kodiaq/",
                    "https://belgorod.riaauto.ru/skoda/kodiaq",
                    "https://cars.skoda-avto.ru/kodiaq/",
                    "https://mbib.ru/obl-belgorodskaya/skoda/kodiaq",
                    "https://v-belgorode.kupit-auto.com/new/skoda/kodiaq",
                    "https://www.gazeta-a.ru/autosearch/belgorod/skoda/kodiaq/",
                    "https://belgorod.110km.ru/prodazha/skoda/kodiaq/poderzhannie/",
                    "https://www.rosso-sk.ru/models/kodiaq",
                    "https://belgorod.incom-auto.ru/auto/skoda/kodiaq/",
                    "https://belgorod.rrt-automarket.ru/new-cars/skoda/kodiaq/",
                    "https://skoda-vozrojdenie.ru/models/kodiaq",
                    "https://belgorod.masmotors.ru/car/skoda/kodiaq"
                ]
            ],
            "шкода кодиак белгород цены" => [
                "sites" => [
                    "https://auto.ru/belgorod/cars/skoda/kodiaq/all/",
                    "https://moravia-motors.ru/models/kodiaq",
                    "https://www.avito.ru/belgorod/avtomobili/skoda/kodiaq-asgbagicaktgtg2emsjitg3wqcg",
                    "https://belgorod.autovsalone.ru/cars/skoda/kodiaq",
                    "https://belgorod.drom.ru/skoda/kodiaq/",
                    "https://belgorod.autospot.ru/brands/skoda/kodiaq/suv/price/",
                    "https://belgorod.carso.ru/skoda/kodiaq",
                    "https://carsdo.ru/skoda/kodiaq/belgorod/",
                    "https://cars.skoda-avto.ru/kodiaq/",
                    "https://belgorod.newautosalon.ru/kodiaq/",
                    "https://belgorod.riaauto.ru/skoda/kodiaq",
                    "https://mbib.ru/obl-belgorodskaya/skoda/kodiaq",
                    "https://v-belgorode.kupit-auto.com/new/skoda/kodiaq",
                    "https://www.gazeta-a.ru/autosearch/belgorod/skoda/kodiaq/",
                    "https://www.rosso-sk.ru/models/kodiaq",
                    "https://skoda-vozrojdenie.ru/models/kodiaq",
                    "https://belgorod.110km.ru/prodazha/skoda/kodiaq/poderzhannie/",
                    "https://belgorod.incom-auto.ru/auto/skoda/kodiaq/",
                    "https://krona-auto.ru/models/kodiaq/price",
                    "https://belgorod.rrt-automarket.ru/new-cars/skoda/kodiaq/"
                ]
            ],
            "купить шкоду октавию белгород" => [
                "sites" => [
                    "https://www.avito.ru/belgorod/avtomobili/skoda/octavia-asgbagicaktgtg2emsjitg2ercg",
                    "https://auto.ru/belgorod/cars/skoda/octavia/all/",
                    "https://belgorod.drom.ru/skoda/octavia/",
                    "https://moravia-motors.ru/models/octavia",
                    "https://belgorod.autovsalone.ru/cars/skoda/octavia",
                    "https://belgorod.mbib.ru/skoda/octavia/used",
                    "https://belgorod.110km.ru/prodazha/skoda/octavia/",
                    "https://belgorod.carso.ru/skoda/octavia",
                    "https://carsdo.ru/skoda/octavia/belgorod/",
                    "https://belgorod.ab-club.ru/catalog/skoda/octavia/",
                    "https://belgorod.autospot.ru/brands/skoda/octavia_iv/liftback/price/",
                    "https://belgorod.riaauto.ru/skoda/octavia-a8",
                    "https://cars.skoda-avto.ru/?dealerid=rusc00842",
                    "https://quto.ru/inventory/belgorodskayaoblast/skoda/octavia",
                    "https://www.njcar.ru/prices-partners/belgorod/skoda/octavia/all/",
                    "https://belgorod.incom-auto.ru/auto/skoda/octavia/",
                    "https://chehia-avto.ru/models/octavia",
                    "http://sobut.ru/belgorodskaya-oblast/skoda-octavia-obyavleniya/",
                    "https://rydo.ru/belgorodskaya-oblast/auto-skoda-octavia/",
                    "https://car.ru/auto/31/skoda/octavia/"
                ]
            ],
            "купить шкоду октавия в белгороде" => [
                "sites" => [
                    "https://www.avito.ru/belgorod/avtomobili/skoda/octavia-asgbagicaktgtg2emsjitg2ercg",
                    "https://auto.ru/belgorod/cars/skoda/octavia/all/",
                    "https://moravia-motors.ru/models/octavia",
                    "https://belgorod.drom.ru/skoda/octavia/",
                    "https://belgorod.autovsalone.ru/cars/skoda/octavia",
                    "https://belgorod.mbib.ru/skoda/octavia/used",
                    "https://belgorod.110km.ru/prodazha/skoda/octavia/",
                    "https://carsdo.ru/skoda/octavia/belgorod/",
                    "https://belgorod.carso.ru/skoda/octavia",
                    "https://belgorod.autospot.ru/brands/skoda/octavia_iv/liftback/price/",
                    "https://belgorod.riaauto.ru/skoda/octavia-a8",
                    "https://belgorod.ab-club.ru/catalog/skoda/octavia/",
                    "https://cars.skoda-avto.ru/?dealerid=rusc00842,rusc01749",
                    "https://skoda-vozrojdenie.ru/models/octavia",
                    "https://belgorod.cardana.ru/auto/skoda/octavia_combi.html",
                    "https://quto.ru/inventory/belgorodskayaoblast/skoda/octavia",
                    "https://www.njcar.ru/prices-partners/belgorod/skoda/octavia/all/",
                    "https://belgorod.keyauto-probeg.ru/used/skoda/octavia/",
                    "https://avto.mitula.ru/avto/skoda-octavia-2018-%d0%b1%d0%b5%d0%bb%d0%b3%d0%be%d1%80%d0%be%d0%b4",
                    "https://belgorod.autodmir.ru/offers/skoda/octavia/used/"
                ]
            ],
            "шкода октавия белгород" => [
                "sites" => [
                    "https://moravia-motors.ru/models/octavia",
                    "https://auto.ru/belgorod/cars/skoda/octavia/all/",
                    "https://www.avito.ru/belgorod/avtomobili/skoda/octavia-asgbagicaktgtg2emsjitg2ercg",
                    "https://belgorod.drom.ru/skoda/octavia/",
                    "https://belgorod.mbib.ru/skoda/octavia/used",
                    "https://belgorod.autovsalone.ru/cars/skoda/octavia",
                    "https://belgorod.110km.ru/prodazha/skoda/octavia/",
                    "https://carsdo.ru/skoda/octavia/belgorod/",
                    "https://belgorod.carso.ru/skoda/octavia-old",
                    "https://belgorod.autospot.ru/brands/skoda/octavia_iv/liftback/price/",
                    "https://www.drive2.ru/cars/skoda/octavia/m2473/?city=34581",
                    "https://belgorod.riaauto.ru/skoda/octavia-a8",
                    "https://belgorod.ab-club.ru/catalog/skoda/octavia/",
                    "https://cars.skoda-avto.ru/?dealerid=rusc00842",
                    "https://belgorod.newautosalon.ru/skoda-octavia/",
                    "https://quto.ru/inventory/belgorodskayaoblast/skoda/octavia",
                    "https://www.njcar.ru/prices-partners/belgorod/skoda/octavia/all/",
                    "https://belgorod.incom-auto.ru/auto/skoda/octavia/",
                    "https://vk.com/skodabelgorod31",
                    "https://belgorod.keyauto-probeg.ru/used/skoda/octavia/"
                ]
            ],
            "техническое обслуживание автомобилей шкода" => [
                "sites" => [
                    "https://www.skoda-avto.ru/service/to-repair",
                    "https://autospot.ru/autoservice/to/skoda/",
                    "https://rolf-skoda.ru/service/to-repair",
                    "https://www.autoservice-skoda.ru/rapeir/to/",
                    "https://www.major-auto.ru/service/skoda/",
                    "https://zoon.ru/msk/autoservice/type/tehnicheskoe_obsluzhivanie_avtomobilya-skoda/",
                    "https://www.avtobam.ru/services/skoda",
                    "https://skoda-favorit.ru/service/regulations-to",
                    "https://www.evis-motors.ru/uslugi/tehnicheskoe-obsluzhivanie/skoda/",
                    "https://ddcar.ru/skoda/tehobsluzhivanie-avtomobilya",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d0%b3%d0%b4%d0%b5+%d0%bf%d1%80%d0%be%d0%b9%d1%82%d0%b8+%d1%82%d0%b5%d1%85%d0%be%d0%b1%d1%81%d0%bb%d1%83%d0%b6%d0%b8%d0%b2%d0%b0%d0%bd%d0%b8%d0%b5+%d0%b0%d0%b2%d1%82%d0%be%d0%bc%d0%be%d0%b1%d0%b8%d0%bb%d1%8f+%d1%88%d0%ba%d0%be%d0%b4%d0%b0",
                    "https://rolf-center.ru/service/skoda/to/",
                    "https://skoda-avtoruss.ru/service/apps/calculator-to",
                    "https://skoda-kuntsevo.ru/service/to-repair",
                    "https://gm-vostok.ru/servis-shkoda",
                    "https://servisy-skoda.ru/",
                    "https://www.autocity-sk.ru/service/to-repair",
                    "https://www.vse-avtoservisy.ru/tekhnicheskoe-obsluzhivanie-skoda/",
                    "https://www.bogemia-skd.ru/service/reglament-to",
                    "https://autoreshenie.ru/planovoe-to_skoda_013/"
                ]
            ],
            "техническое обслуживание шкода" => [
                "sites" => [
                    "https://www.skoda-avto.ru/service/to-repair",
                    "https://autospot.ru/autoservice/to/skoda/",
                    "https://rolf-skoda.ru/service/to-repair",
                    "https://www.autoservice-skoda.ru/rapeir/to/",
                    "https://skoda-favorit.ru/service/regulations-to",
                    "https://www.avtobam.ru/services/skoda",
                    "https://rolf-center.ru/service/skoda/to/",
                    "https://www.major-auto.ru/service/skoda/",
                    "https://skoda-avtoruss.ru/service/apps/calculator-to",
                    "https://www.evis-motors.ru/uslugi/tehnicheskoe-obsluzhivanie/skoda/",
                    "https://skoda-kuntsevo.ru/service/to-repair",
                    "https://www.bogemia-skd.ru/service/reglament-to",
                    "https://www.vse-avtoservisy.ru/tekhnicheskoe-obsluzhivanie-skoda/",
                    "https://zoon.ru/msk/autoservice/type/reglamentnoe_tehnicheskoe_obsluzhivanie-skoda/",
                    "https://favorit-motors.ru/technical-center/regulations/skoda/",
                    "https://ddcar.ru/skoda/tehobsluzhivanie-avtomobilya",
                    "https://www.autoskd.ru/service/to-repair",
                    "https://www.autocity-sk.ru/service/apps/calculator-to",
                    "https://rolf-veshki.ru/service/skoda/to/",
                    "https://gm-vostok.ru/servis-shkoda"
                ]
            ],
            "техническое обслуживание skoda" => [
                "sites" => [
                    "https://www.skoda-avto.ru/service/to-repair",
                    "https://rolf-skoda.ru/service/to-repair",
                    "https://autospot.ru/autoservice/to/skoda/",
                    "https://www.avtobam.ru/services/skoda",
                    "https://skoda-favorit.ru/service/regulations-to",
                    "https://www.autoservice-skoda.ru/rapeir/to/",
                    "https://www.major-auto.ru/service/skoda/",
                    "https://skoda-kuntsevo.ru/service/to-repair",
                    "https://www.evis-motors.ru/uslugi/tehnicheskoe-obsluzhivanie/skoda/",
                    "https://rolf-center.ru/service/skoda/to/",
                    "https://www.drive2.ru/experience/skoda?t=10",
                    "https://ddcar.ru/skoda/tehobsluzhivanie-avtomobilya",
                    "https://zoon.ru/msk/autoservice/type/reglamentnoe_tehnicheskoe_obsluzhivanie-skoda/",
                    "https://gm-vostok.ru/servis-shkoda",
                    "https://favorit-motors.ru/technical-center/regulations/skoda/",
                    "https://www.bogemia-skd.ru/service/reglament-to",
                    "https://www.vse-avtoservisy.ru/tekhnicheskoe-obsluzhivanie-skoda/",
                    "https://www.autocity-sk.ru/service/apps/calculator-to",
                    "https://servisy-skoda.ru/",
                    "https://autonomia.ru/repair/slesarnyj-remont/tehnicheskoe-obsluzhivanie_skoda"
                ]
            ],
            "škoda kodiaq технические характеристики" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq/technology",
                    "https://www.drom.ru/catalog/skoda/kodiaq/",
                    "https://auto.ru/catalog/cars/skoda/kodiaq/20839003/20839055/specifications/",
                    "https://www.ixbt.com/car/skoda-kodiaq-review.html",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_kodiaq",
                    "https://t-motors-skoda.ru/models/kodiaq/technology",
                    "https://skoda-kodiaq.ru/tehnicheskie-harakteristiki.html",
                    "https://drive-skoda.ru/kodiaq/tehnicheskie-harakteristiki",
                    "https://www.atlant-motors.ru/models/kodiaq/technology",
                    "https://avtomarket.ru/catalog/skoda/kodiaq/",
                    "https://www.skoda-major.ru/kodiaq/tehnicheskie-harakteristiki/",
                    "https://autospot.ru/brands/skoda/kodiaq/suv/spec/",
                    "https://110km.ru/tth/skoda/kodiaq/",
                    "http://www.autonet.ru/auto/ttx/skoda/kodiaq",
                    "https://www.drive.ru/brands/skoda/models/2021/kodiaq",
                    "https://skoda-motorauto.ru/kodiaq-specifications/",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/tehnicheskie-harakteristiki/",
                    "https://auto.ironhorse.ru/kodiaq-1_14727.html",
                    "https://avtonam.ru/tech-skoda/char-kodiaq/",
                    "https://www.auto-dd.ru/skoda-kodiaq-2022/"
                ]
            ],
            "škoda kodiaq характеристики" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/kodiaq/technology",
                    "https://auto.ru/catalog/cars/skoda/kodiaq/20839003/20839055/specifications/",
                    "https://www.drom.ru/catalog/skoda/kodiaq/",
                    "https://www.ixbt.com/car/skoda-kodiaq-review.html",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_kodiaq",
                    "https://skoda-favorit.ru/models/kodiaq/technology",
                    "https://www.drive.ru/test-drive/skoda/584126e3ec05c41653000139.html",
                    "https://skoda-kodiaq.ru/tehnicheskie-harakteristiki.html",
                    "https://www.atlant-motors.ru/models/kodiaq/technology",
                    "https://www.skoda-major.ru/kodiaq/tehnicheskie-harakteristiki/",
                    "https://autospot.ru/brands/skoda/kodiaq/suv/spec/",
                    "https://t-motors-skoda.ru/models/kodiaq/technology",
                    "https://drive-skoda.ru/kodiaq/tehnicheskie-harakteristiki",
                    "https://110km.ru/tth/skoda/kodiaq/",
                    "https://skoda-gradavto.ru/models/kodiaq/technology",
                    "https://skoda-motorauto.ru/kodiaq-specifications/",
                    "https://avtomarket.ru/catalog/skoda/kodiaq/",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/tehnicheskie-harakteristiki/",
                    "https://carsdo.ru/skoda/kodiaq/",
                    "https://skoda-kuntsevo.ru/models/kodiaq"
                ]
            ],
            "купить шкода октавия старый оскол" => [
                "sites" => [
                    "https://www.avito.ru/staryy_oskol/avtomobili/skoda/octavia-asgbagicaktgtg2emsjitg2ercg",
                    "https://auto.ru/staryy_oskol/cars/skoda/octavia/all/",
                    "https://stariy-oskol.drom.ru/skoda/octavia/",
                    "https://staryy-oskol.mbib.ru/skoda/octavia/used",
                    "https://stariy-oskol.autovsalone.ru/cars/skoda/octavia",
                    "https://staryy-oskol.110km.ru/prodazha/skoda/octavia/poderzhannie/",
                    "https://octavia-oskol.tmgauto.ru/",
                    "https://staryj-oskol.ab-club.ru/catalog/skoda/octavia/",
                    "https://moravia-motors.ru/",
                    "https://stariy-oskol.abc-auto.ru/skoda/octavia/",
                    "https://cars.skoda-avto.ru/?dealerid=rusc00842,rusc01749",
                    "https://www.indexus.ru/staryy_oskol/transport/avtomobili/skoda",
                    "https://staryy-oskol.b-kredit.com/used/skoda/octavia-i/",
                    "http://sobut.ru/staryy-oskol/skoda-octavia-obyavleniya/",
                    "https://vk.com/skodabelgorod31",
                    "https://car.ru/staryiy-oskol/skoda/octavia/",
                    "https://skoda-vozrojdenie.ru/models/octavia",
                    "https://avto-dilery.ru/shkoda-staryj-oskol/",
                    "https://rydo.ru/staryy-oskol/auto-skoda-octavia-scout/",
                    "https://oskol.zoon.ru/autoservice/type/kupit_skoda_octavia/"
                ]
            ],
            "шкода октавия старый оскол" => [
                "sites" => [
                    "https://www.avito.ru/staryy_oskol/avtomobili/skoda/octavia-asgbagicaktgtg2emsjitg2ercg",
                    "https://auto.ru/staryy_oskol/cars/skoda/octavia/all/",
                    "https://stariy-oskol.drom.ru/skoda/octavia/",
                    "https://moravia-motors.ru/",
                    "https://octavia-oskol.tmgauto.ru/",
                    "https://stariy-oskol.autovsalone.ru/cars/skoda/octavia",
                    "https://staryy-oskol.mbib.ru/skoda/octavia/used",
                    "https://staryj-oskol.ab-club.ru/catalog/skoda/octavia/",
                    "https://staryy-oskol.110km.ru/prodazha/skoda/octavia/poderzhannie/",
                    "https://cars.skoda-avto.ru/?dealerid=rusc00842,rusc01749",
                    "https://stariy-oskol.abc-auto.ru/skoda/octavia/",
                    "https://vk.com/skodabelgorod31",
                    "https://www.drive2.ru/cars/skoda/octavia/m2473/?city=35861",
                    "https://yandex.ru/maps/org/koda_moraviya_motors/1301511506/",
                    "https://www.indexus.ru/staryy_oskol/transport/avtomobili/skoda",
                    "https://staryy-oskol.b-kredit.com/catalog/skoda/",
                    "https://auto.catalogd.ru/staryj-oskol/moraviya_centr_stariy_oskol_skoda/",
                    "https://staryyoskol.zapster.ru/skoda/octavia",
                    "https://avto-dilery.ru/shkoda-staryj-oskol/",
                    "http://carscan24.ru/dilers/skoda/stoskol/moraviya-centr/"
                ]
            ],
            "шкода суперб белгород" => [
                "sites" => [
                    "https://www.avito.ru/belgorod/avtomobili/skoda/superb-asgbagicaktgtg2emsjitg3assg",
                    "https://auto.ru/belgorod/cars/skoda/superb/all/",
                    "https://moravia-motors.ru/models/superb",
                    "https://belgorod.drom.ru/skoda/superb/",
                    "https://belgorod.autovsalone.ru/cars/skoda/superb",
                    "https://belgorod.carso.ru/skoda/superb",
                    "https://belgorod.autospot.ru/brands/skoda/superb_generation/liftback/price/",
                    "https://belgorod.newautosalon.ru/superb-fl/",
                    "https://belgorod.mbib.ru/skoda/superb",
                    "https://belgorodskaya-oblast.110km.ru/prodazha/skoda/superb/",
                    "https://belgorod.riaauto.ru/skoda/superb",
                    "https://belgorod.abc-auto.ru/skoda/superb/",
                    "https://belgorod.avanta-avto-credit.ru/cars/skoda/superb/",
                    "https://belgorod.cardana.ru/auto/skoda/superb_hatch.html",
                    "https://belgorod.ab-club.ru/catalog/skoda/superb/",
                    "https://carsdo.ru/skoda/superb/belgorod/",
                    "https://belgorod.b-kredit.com/catalog/skoda/superb_new/",
                    "https://www.skoda-avto.ru/models/superb",
                    "https://www.njcar.ru/prices-partners/belgorod/skoda/superb/all/",
                    "https://belgorod.autodmir.ru/offers/skoda/superb/"
                ]
            ],
            "шкода суперб купить белгород" => [
                "sites" => [
                    "https://www.avito.ru/belgorod/avtomobili/skoda/superb-asgbagicaktgtg2emsjitg3assg",
                    "https://auto.ru/belgorod/cars/skoda/superb/used/",
                    "https://belgorod.drom.ru/skoda/superb/",
                    "https://moravia-motors.ru/models/superb",
                    "https://belgorod.autovsalone.ru/cars/skoda/superb",
                    "https://belgorod.carso.ru/skoda/superb",
                    "https://belgorod.110km.ru/prodazha/skoda/superb/poderzhannie/",
                    "https://belgorod.mbib.ru/skoda/superb",
                    "https://belgorod.autospot.ru/brands/skoda/superb_generation/liftback/price/",
                    "https://belgorod.newautosalon.ru/superb-fl/",
                    "https://belgorod.abc-auto.ru/skoda/superb/",
                    "https://belgorod.riaauto.ru/skoda/superb",
                    "https://cars.skoda-avto.ru/superb",
                    "https://belgorod.cardana.ru/auto/skoda/superb_hatch.html",
                    "https://belgorod.avanta-avto-credit.ru/cars/skoda/superb/",
                    "https://belgorod.ab-club.ru/catalog/skoda/superb/",
                    "https://carsdo.ru/skoda/superb/belgorod/",
                    "https://belgorod.b-kredit.com/catalog/skoda/superb_new/",
                    "https://v-belgorode.kupit-auto.com/new/skoda/superb",
                    "http://belgorod.lst-group.ru/new/skoda/superb_sedan/"
                ]
            ],
            "skoda rapid 2022 технические характеристики" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid/technology",
                    "https://www.drom.ru/catalog/skoda/rapid/2022/",
                    "https://auto.ru/catalog/cars/skoda/rapid/21738448/21738487/specifications/",
                    "https://naavtotrasse.ru/skoda/skoda-rapid-2022.html",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-rapid-2022",
                    "https://cenyavto.com/skoda-rapid-2022/",
                    "https://skoda-favorit.ru/models/rapid/technology",
                    "https://locman-skoda.ru/m/1b5fa776-76e5-43d0-bad8-5becc1c67512/pdf/catalogs/rapid-catalogue-new2022.pdf",
                    "https://www.rosso-sk.ru/models/rapid/technology",
                    "https://rolf-skoda.ru/models/rapid/technology",
                    "https://autompv.ru/new-auto/48749-skoda-rapid-2021.html",
                    "https://sigma-skoda.ru/models/rapid/technology",
                    "https://roadres.com/skoda/rapid/tech/",
                    "https://110km.ru/tth/skoda/rapid/year/2022/",
                    "https://autoiwc.ru/skoda/skoda-rapid.html",
                    "https://favorit-motors.ru/catalog/new/skoda/new_rapid/tehnicheskie-harakteristiki/",
                    "https://www.allcarz.ru/skoda-rapid-2021/",
                    "https://skoda-autopraga.ru/models/rapid/technology",
                    "https://skoda-centr.ru/rapid/complect/",
                    "https://volkswagen-car.ru/2022/02/10/novaya-shkoda-rapid-2022/"
                ]
            ],
            "škoda rapid 2022 характеристики" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid/technology",
                    "https://www.drom.ru/catalog/skoda/rapid/2022/",
                    "https://naavtotrasse.ru/skoda/skoda-rapid-2022.html",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/skoda-rapid-2022",
                    "https://cenyavto.com/skoda-rapid-2022/",
                    "https://autompv.ru/new-auto/48749-skoda-rapid-2021.html",
                    "https://skoda-favorit.ru/models/rapid/technology",
                    "https://auto.ru/catalog/cars/skoda/rapid/21738448/21738487/specifications/",
                    "https://rolf-skoda.ru/models/rapid/technology",
                    "https://www.rosso-sk.ru/models/rapid/technology",
                    "https://rulikolesa.ru/novaya-shkoda-rapid-2022-modelnogo-goda/",
                    "https://roadres.com/skoda/rapid/tech/",
                    "https://www.allcarz.ru/skoda-rapid-2021/",
                    "https://110km.ru/tth/skoda/rapid/year/2022/",
                    "https://favorit-motors.ru/catalog/new/skoda/new_rapid/tehnicheskie-harakteristiki/",
                    "https://autoiwc.ru/skoda/skoda-rapid.html",
                    "https://sigma-skoda.ru/models/rapid/technology",
                    "https://skoda-kors.ru/models/rapid/technology",
                    "https://skoda-autopraga.ru/models/rapid/technology",
                    "https://skoda-kanavto.ru/models/rapid/technology"
                ]
            ],
            "skoda rapid технические характеристики" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid/technology",
                    "https://www.drom.ru/catalog/skoda/rapid/",
                    "https://auto.ru/catalog/cars/skoda/rapid/21738448/21738487/specifications/",
                    "https://skoda-s-auto.ru/models/rapid/technology",
                    "https://kuntsevo.com/skoda/rapid/specifications/",
                    "https://sigma-skoda.ru/models/rapid/technology",
                    "https://skoda-kanavto.ru/models/rapid/technology",
                    "https://rolf-skoda.ru/models/rapid/technology",
                    "https://www.skoda-major.ru/rapid/tehnicheskie-harakteristiki/",
                    "https://autospot.ru/brands/skoda/682/technical-parameters/",
                    "https://skoda-motorauto.ru/rapid-specifications/",
                    "https://www.drive.ru/brands/skoda/models/2019/rapid",
                    "https://aspec-lider.ru/models/rapid/technology",
                    "http://www.autonet.ru/auto/ttx/skoda/rapid",
                    "https://drive-skoda.ru/rapid/tehnicheskie-harakteristiki",
                    "https://110km.ru/tth/skoda/rapid/",
                    "https://fastmb.ru/testdrive/5004-obzor-skoda-rapid-2020-2021-tehnicheskie-harakteristiki-i-foto.html",
                    "https://skoda-elvis.ru/models/rapid/technology",
                    "https://avtomarket.ru/catalog/skoda/rapid/",
                    "https://www.ventus.ru/models/rapid/technology"
                ]
            ],
            "škoda rapid характеристики" => [
                "sites" => [
                    "https://www.skoda-avto.ru/models/rapid/technology",
                    "https://www.drom.ru/catalog/skoda/rapid/",
                    "https://auto.ru/catalog/cars/skoda/rapid/21738448/21738487/specifications/",
                    "https://skoda-s-auto.ru/models/rapid/technology",
                    "https://sigma-skoda.ru/models/rapid/technology",
                    "https://ru.wikipedia.org/wiki/%c5%a0koda_rapid_(2012)",
                    "https://skoda-kanavto.ru/models/rapid/technology",
                    "https://chehia-avto.ru/models/rapid/technology",
                    "https://fastmb.ru/testdrive/5004-obzor-skoda-rapid-2020-2021-tehnicheskie-harakteristiki-i-foto.html",
                    "https://kuntsevo.com/skoda/rapid/specifications/",
                    "https://www.autocity-sk.ru/models/rapid/technology",
                    "https://skoda-favorit.ru/models/rapid/technology",
                    "https://autotaf.ru/skoda-rapid-obzor/",
                    "https://www.drive.ru/brands/skoda/models/2019/rapid",
                    "https://avtomarket.ru/catalog/skoda/rapid/",
                    "https://skoda.medved-vostok.ru/models/rapid/technology",
                    "https://www.skoda-podolsk.ru/models/rapid/technology",
                    "https://autospot.ru/brands/skoda/682/technical-parameters/",
                    "https://skoda-elvis.ru/models/rapid/technology",
                    "https://t-motors-skoda.ru/models/rapid/technology"
                ]
            ],
            "шкода карок старый оскол" => [
                "sites" => [
                    "https://auto.ru/staryy_oskol/cars/skoda/karoq/all/",
                    "https://stariy-oskol.autovsalone.ru/cars/skoda/karoq",
                    "https://www.avito.ru/staryy_oskol/avtomobili/skoda/karoq-asgbagicaktgtg2emsjitg26stu",
                    "https://moravia-motors.ru/models/karoq",
                    "https://stariy-oskol.drom.ru/new/skoda/karoq/",
                    "https://cars.skoda-avto.ru/karoq/",
                    "https://v-starom-oskole.kupit-auto.com/new/skoda/karoq",
                    "https://stariy-oskol.cardana.ru/auto/skoda/karoq.html",
                    "https://staryy-oskol.mbib.ru/skoda/karoq/used",
                    "https://staryy-oskol.110km.ru/prodazha/skoda/karoq/",
                    "https://staryy-oskol.b-kredit.com/catalog/skoda/karoq/",
                    "https://www.drive2.ru/cars/skoda/karoq/m3204/?city=35861",
                    "https://oskol.city/news/auto/66696/",
                    "https://belgorod.autospot.ru/brands/skoda/karoq/suv/price/",
                    "https://staryj-oskol.ab-club.ru/catalog/skoda/karoq/",
                    "https://yandex.ru/maps/org/koda_moraviya_motors/1301511506/",
                    "https://auto.catalogd.ru/staryj-oskol/moraviya_centr_stariy_oskol_skoda/",
                    "https://carsdo.ru/skoda/karoq/",
                    "https://avto-dilery.ru/shkoda-staryj-oskol/",
                    "https://rolf-skoda.ru/models/karoq"
                ]
            ],
            "кузовные детали шкода" => [
                "sites" => [
                    "https://kuzovnoy.ru/autoparts/skoda/",
                    "https://am-parts.ru/katalog/skoda/skoda-octavia/octavia-a5/detali-kuzova",
                    "https://autodubok.ru/catalog/skoda/octavia_oktaviya/2013_a7/",
                    "https://baza.drom.ru/moskva/sell_spare_parts/+/%e4%e5%f2%e0%eb%e8+%ea%f3%e7%ee%e2%e0/model/skoda/",
                    "https://www.avito.ru/moskva/zapchasti_i_aksessuary/zapchasti/dlya_avtomobiley/skoda/kuzov/kuzov_po_chastyam-asgbagicbuqkjkwj~gpidmy1azid5oyc4lynnpko",
                    "https://moscow.kuzovik.ru/catalog/skoda/",
                    "https://euroauto.ru/auto/cars/skoda/octavia/octavia_a5_1z-2004-2013/kuzov_naruzhnie_elementi/",
                    "https://autonomia.ru/skoda/kuzovnye-detali",
                    "https://texbot.ru/catalog/kuzov/skoda/",
                    "https://zonez.ru/zapchasti_skoda.htm",
                    "https://www.autocompas.ru/catalog/kuzovnye-zapchasti/skoda/",
                    "https://izap24.ru/avto/skoda/karoq/9345/ctg-2-kuzovnyie-detali/",
                    "https://redcar.com.ru/catalog/brand/skoda",
                    "https://edg-parts.ru/kuzovniye-detali-skoda-octavia-a8-mk4",
                    "https://cargasm.ru/catalog/skoda/",
                    "https://bibinet.ru/part/moskva/skoda/all/kuzovnye-detali/",
                    "https://msk.blizko.ru/predl/transport/autoparts/zapchasti_skoda/kuzovnye_detali_skoda",
                    "https://www.port3.ru/catalogs/obschaya-shema-kuzovnyie-detali-11865/skoda",
                    "https://megaautoparts.ru/category58-zapchasti-skoda/",
                    "https://f-avto.ru/kuzovnye-zapchasti/skoda"
                ]
            ],
            "шкода белгород авто с пробегом" => [
                "sites" => [
                    "https://www.avito.ru/belgorod/avtomobili/s_probegom/skoda-asgbagicaksgfmjmaec2dz6zka",
                    "https://auto.ru/belgorod/cars/skoda/used/",
                    "https://belgorod.drom.ru/skoda/used/all/",
                    "https://belgorod.110km.ru/vybor/skoda/kupit-s-probegom-poderzhannie-belgorod/",
                    "https://belgorod.ml-respect.ru/car/skoda/",
                    "https://mbib.ru/obl-belgorodskaya/skoda",
                    "https://belgorod.keyauto-probeg.ru/used/skoda/",
                    "https://belgorod.ab-club.ru/catalog/skoda/",
                    "https://www.njcar.ru/prices-partners/belgorod/skoda/all/",
                    "https://belgorod.irr.ru/cars/passenger/skoda/",
                    "https://avto.mitula.ru/avto/skoda-%d0%b1%d0%b5%d0%bb%d0%b3%d0%be%d1%80%d0%be%d0%b4",
                    "https://car.ru/belgorod/skoda/all/",
                    "https://moravia-motors.ru/",
                    "https://belgorod.autospot.ru/brands/skoda/",
                    "https://www.aviauto.ru/belgorod/skoda",
                    "https://rydo.ru/belgorod/auto-skoda/",
                    "https://belgorod.autodmir.ru/offers/skoda/",
                    "http://po-krupnomu.ru/belgorodskaya-oblast/skoda/",
                    "https://quto.ru/inventory/belgorodskayaoblast/skoda",
                    "http://mirkupit.ru/belgorod/skoda/"
                ]
            ],
            "шкода кодиак старый оскол" => [
                "sites" => [
                    "https://auto.ru/staryy_oskol/cars/skoda/kodiaq/used/",
                    "https://www.avito.ru/staryy_oskol/avtomobili/skoda/kodiaq-asgbagicaktgtg2emsjitg3wqcg",
                    "https://stariy-oskol.autovsalone.ru/cars/skoda/kodiaq",
                    "https://stariy-oskol.drom.ru/skoda/kodiaq/",
                    "https://cars.skoda-avto.ru/kodiaq/",
                    "https://stariy-oskol.newautosalon.ru/kodiaq/",
                    "http://stariy-oskol.lst-group.ru/new/skoda/kodiaq/",
                    "https://moravia-motors.ru/models/kodiaq",
                    "https://stariy-oskol.cardana.ru/auto/skoda/kodiaq.html",
                    "https://staryy-oskol.110km.ru/prodazha/skoda/kodiaq/",
                    "https://v-starom-oskole.kupit-auto.com/new/skoda/kodiaq",
                    "https://mbib.ru/obl-belgorodskaya/skoda/kodiaq",
                    "https://belgorod.autospot.ru/brands/skoda/kodiaq/suv/price/",
                    "http://www.skodamir.ru/diler/2123-skoda-staryj-oskol.html",
                    "https://carsdo.ru/skoda/kodiaq/",
                    "https://favorit-motors.ru/catalog/new/skoda/kodiaq/",
                    "https://skodakodiaq.club/dilery-shkoda/",
                    "https://www.rosso-sk.ru/models/kodiaq",
                    "https://autovn.ru/models/kodiaq",
                    "https://skoda-orehovo.ru/models/kodiaq/price"
                ]
            ],
            "шкода тест драйв автомат" => [
                "sites" => [
                    "https://www.drive2.ru/b/553179694170636448/",
                    "https://www.drive.ru/test-drive/skoda/5902ef6dec05c4fa330000ee.html",
                    "https://www.youtube.com/watch?v=pxd3vkoysuw",
                    "https://www.kolesa.ru/test-drive/bogach-bednyak-test-skoda-rapid-1-6-at",
                    "https://74.ru/text/auto/2020/11/27/69579923/",
                    "https://motor.ru/testdrives/ruskaroq.htm",
                    "https://www.zr.ru/cars/skoda/-/skoda-octavia/tests/",
                    "https://auto.mail.ru/testdrives/skoda/",
                    "https://carsdo.ru/skoda/octavia/test-drive/",
                    "https://auto.ru/video/cars/skoda/octavia/",
                    "https://www.drom.ru/info/test-drive/skoda/",
                    "https://www.skoda-avto.ru/blogs/novaya-skoda-octavia%e2%80%93test-drajv-bestsellera",
                    "https://autoreview.ru/articles/sprint-test/restaylingovaya-octavia",
                    "https://5koleso.ru/tests/skoda-octavia-gospozha-praktichnost/",
                    "https://29.ru/text/auto/2020/11/27/69579993/",
                    "https://dvizhok.su/auto/test-drajv-skoda-octavia-1.6-mpi-vmesto-1.2-tsi",
                    "https://matador.tech/articles/skoda-octavia-cetvertaa-vtoraa-ili-pervaa",
                    "https://bezrulya.ru/magazine/news/test-drive-skoda-rapid-1_6-automatic-metkie-indeyci-878/",
                    "https://optomobuv.ru/testy/novaya-shkoda-oktaviya-foto.html",
                    "http://www.motorpage.ru/skoda/octavia/last/test-drives/"
                ]
            ]
        ];

        $willClustered = [];
        $clusters = [];
        foreach ($jayParsedAry as $phrase => $item) {
            foreach ($jayParsedAry as $phrase2 => $item2) {
                if (isset($willClustered[$phrase2])) {
                    continue;
                } else if (isset($this->clusters[$phrase])) {
                    foreach ($this->clusters[$phrase] as $target => $elem) {
                        if (count(array_intersect($item2['sites'], $elem['sites'])) >= 14) {
                            $clusters[$phrase][$phrase2] = ['sites' => $item2['sites']];
                            $willClustered[$phrase2] = true;
                            break;
                        }
                    }
                } else if (count(array_intersect($item['sites'], $item2['sites'])) >= 14) {
                    $clusters[$phrase][$phrase2] = ['sites' => $item2['sites']];
                    $willClustered[$phrase2] = true;
                }
            }
        }

        foreach ($clusters as $keyPhrase => $cluster) {
            if (count($cluster) > 1) {
                continue;
            }
            foreach ($clusters as $anotherKeyPhrase => $anotherCluster) {
                if ($keyPhrase === $anotherKeyPhrase || count($anotherCluster) < 1) {
                    continue;
                }
                foreach ($anotherCluster as $phrase => $item) {
                    if (count(array_intersect($item['sites'], $cluster[array_key_first($cluster)]['sites'])) > 8) {
//                        dump($phrase);
//                        dd(array_intersect($item['sites'], $cluster[array_key_first($cluster)]['sites']));
                        $clusters[$anotherKeyPhrase] = array_merge_recursive($cluster, $anotherCluster);
                        unset($clusters[$keyPhrase]);
                        continue 3;
                    }
                }
            }
        }
        dd($clusters);
    });
});
