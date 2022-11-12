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

        $jayParsedAry = [
            "taos volkswagen цена" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/taos.html",
                    "https://auto.ru/cars/volkswagen/taos/all/",
                    "https://www.avito.ru/belgorodskaya_oblast/avtomobili/volkswagen/taos-asgbagicaktgtg24msjitg3orls",
                    "https://vw-triumf.ru/models/taos/",
                    "https://carsdo.ru/volkswagen/taos/",
                    "https://vw-rolf.ru/models/taos/prices/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/taos",
                    "https://belgorod.autospot.ru/brands/volkswagen/taos/suv/price/",
                    "https://belgorod.carso.ru/volkswagen/taos",
                    "https://volkswagen-taos.ru/price.html",
                    "https://autoreview.ru/news/krossover-volkswagen-taos-komplektacii-i-ceny",
                    "https://www.major-vw.ru/models/taos/",
                    "https://auto.drom.ru/volkswagen/taos/",
                    "https://www.drive.ru/brands/volkswagen/models/2021/taos",
                    "https://belgorod.newautosalon.ru/volkswagen-taos/",
                    "https://motor.ru/news/volkswagen-taos-price-07-07-2021.htm",
                    "https://vw-avtoruss.ru/models/taos/",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/volkswagen-taos-2022",
                    "https://gt-news.ru/volkswagen/taos-2021/",
                    "https://naavtotrasse.ru/volkswagen/volkswagen-taos-2022.html"
                ]
            ],
            "volkswagen golf" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/new-golf.html",
                    "https://auto.ru/belgorod/cars/volkswagen/golf/all/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/golf-asgbagicaktgtg24msjitg3ipig",
                    "https://ru.wikipedia.org/wiki/volkswagen_golf",
                    "https://belgorod.drom.ru/volkswagen/golf/",
                    "https://www.drive2.ru/cars/volkswagen/golf/m1470/",
                    "https://en.wikipedia.org/wiki/volkswagen_golf",
                    "https://vw-triumf.ru/models/golf-new/",
                    "https://www.drive.ru/test-drive/volkswagen/5df9fdb3ec05c4802000000e.html",
                    "https://wroom.ru/cars/volkswagen/golf/history",
                    "https://all-auto.org/5961-volkswagen-golf.html",
                    "https://auto.mail.ru/catalog/volkswagen/golf/",
                    "https://quto.ru/volkswagen/golf",
                    "https://1gai.ru/publ/522552-volkswagen-golf-vse-8-pokoleniy-legendy.html",
                    "https://www.zr.ru/cars/volkswagen/-/volkswagen-golf/",
                    "https://belgorod.110km.ru/prodazha/volkswagen/golf/",
                    "https://autoiwc.ru/volkswagen/volkswagen-golf.html",
                    "https://www.vw.com/en/models/golf-gti.html",
                    "https://www.auto-dd.ru/volkswagen-golf/",
                    "https://autopedia.fandom.com/ru/wiki/volkswagen_golf"
                ]
            ],
            "volkswagen golf купить" => [
                "sites" => [
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/golf-asgbagicaktgtg24msjitg3ipig",
                    "https://auto.ru/belgorod/cars/volkswagen/golf/used/",
                    "https://belgorod.drom.ru/volkswagen/golf/",
                    "https://www.volkswagen.ru/ru/models/new-golf.html",
                    "https://vw-triumf.ru/models/golf-new/",
                    "https://belgorod.110km.ru/prodazha/volkswagen/golf/",
                    "https://mbib.ru/obl-belgorodskaya/volkswagen/golf",
                    "https://belgorod.carso.ru/volkswagen/golf",
                    "https://carsdo.ru/volkswagen/golf/belgorod/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/golf_hetchbek",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/golf/",
                    "https://belgorod.autodmir.ru/offers/volkswagen/golf/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/golf/",
                    "https://kupiprodai.ru/auto/cars/param66_3686",
                    "https://www.drive2.ru/cars/volkswagen/golf/m1470/?sort=selling",
                    "https://belgorod.keyauto-probeg.ru/used/volkswagen/golf/",
                    "https://car.ru/belgorod/volkswagen/golf/",
                    "https://belgorod.autospot.ru/brands/volkswagen/",
                    "https://www.major-vw.ru/models/golf-new/",
                    "https://www.avilon-vw.ru/models/golf-new/"
                ]
            ],
            "volkswagen passat alltrack" => [
                "sites" => [
                    "https://auto.ru/belgorod/cars/volkswagen/passat-alltrack/all/",
                    "https://www.volkswagen.ru/ru/models/passat-alltrack.html",
                    "https://www.drive2.ru/cars/volkswagen/passat_alltrack/m2347/",
                    "https://vw-triumf.ru/models/passat_alltrack/",
                    "https://5koleso.ru/tests/volkswagen-passat-alltrack-velikij-prostoj/",
                    "https://www.drive.ru/test-drive/volkswagen/4f61f07709b6021a3f000053.html",
                    "https://mobile-review.com/all/reviews/auto/test-volkswagen-passat-alltrack-interesnyj-universal/",
                    "https://www.drom.ru/catalog/volkswagen/passat/239149/",
                    "https://www.zr.ru/content/articles/904068-volkswagen-passat-alltrack-chu/",
                    "https://motor.ru/testdrives/alltracklong2.htm",
                    "https://www.auto-dd.ru/vw-passat-alltrack/",
                    "https://belgorod.abc-auto.ru/volkswagen/alltrack/",
                    "https://www.youtube.com/watch?v=r98t2h5utrq",
                    "https://belgorod.carso.ru/volkswagen/passat-alltrack",
                    "https://carsdo.ru/volkswagen/passat-alltrack/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/passat-alltrack",
                    "https://www.avito.ru/all/avtomobili?q=volkswagen+passat+alltrack",
                    "https://dzen.ru/media/amru/chugunnyi-most-test-volkswagen-passat-alltrack-5ac228bb7ddde86d7b4d18e8",
                    "https://otoba.ru/auto/vw/passat-b8-alltrack.html",
                    "https://autoreview.ru/news/volkswagen-passat-alltrack-vernulsya-v-rossiyu-ob-yavlena-cena"
                ]
            ],
            "volkswagen polo" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/polo-new.html",
                    "https://auto.ru/belgorod/cars/volkswagen/polo/all/",
                    "https://ru.wikipedia.org/wiki/volkswagen_polo",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/polo-asgbagicaktgtg24msjitg2irsg",
                    "https://belgorod.drom.ru/volkswagen/polo/",
                    "https://vw-triumf.ru/models/polo-new/",
                    "https://www.drive2.ru/cars/volkswagen/polo_sedan/g2966/",
                    "https://carsdo.ru/volkswagen/polo-sedan/belgorod/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/polo",
                    "https://belgorod.autospot.ru/brands/volkswagen/polo/liftback/price/",
                    "https://www.drive.ru/test-drive/volkswagen/5f16e030ec05c4421c0000db.html",
                    "https://www.zr.ru/cars/volkswagen/-/volkswagen-polo/",
                    "https://quto.ru/volkswagen/polo",
                    "https://belgorod.carso.ru/volkswagen/polo",
                    "https://otzovik.com/reviews/avtomobil_volkswagen_polo_sedan_2010/2/",
                    "https://en.wikipedia.org/wiki/volkswagen_polo",
                    "https://www.kolesa.ru/article/pyat-veshhej-za-kotorye-lyubyat-i-nenavidyat-volkswagen-polo",
                    "https://auto.mail.ru/catalog/volkswagen/polo/",
                    "https://belgorod.newautosalon.ru/volkswagen-polo/",
                    "https://irecommend.ru/catalog/reviews/15-20472"
                ]
            ],
            "volkswagen polo купить" => [
                "sites" => [
                    "https://auto.ru/belgorod/cars/volkswagen/polo/all/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/polo-asgbagicaktgtg24msjitg2irsg",
                    "https://cars.volkswagen.ru/polo/",
                    "https://belgorod.drom.ru/volkswagen/polo/",
                    "https://vw-triumf.ru/models/polo-new/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/polo",
                    "https://belgorod.autospot.ru/brands/volkswagen/polo/liftback/price/",
                    "https://belgorod.carso.ru/volkswagen/polo",
                    "https://carsdo.ru/volkswagen/polo-sedan/belgorod/",
                    "https://belgorod.abc-auto.ru/volkswagen/polo_sedan/",
                    "https://mbib.ru/obl-belgorodskaya/volkswagen/polo",
                    "https://belgorod.riaauto.ru/volkswagen/polo",
                    "https://belgorod.110km.ru/prodazha/volkswagen/polo/poderzhannie/",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/polo/",
                    "https://belgorod.newautosalon.ru/volkswagen-polo/",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/polo/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/polo/",
                    "https://belgorod.rrt-automarket.ru/new-cars/volkswagen/polo/",
                    "https://quto.ru/inventory/belgorodskayaoblast/volkswagen/polo",
                    "https://autosalon-vw.ru/auto/volkswagen/new-polo_sedan"
                ]
            ],
            "volkswagen polo цена" => [
                "sites" => [
                    "https://auto.ru/belgorod/cars/volkswagen/polo/all/",
                    "https://www.volkswagen.ru/ru/models/polo-new.html",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/polo-asgbagicaktgtg24msjitg2irsg",
                    "https://belgorod.drom.ru/volkswagen/polo/?order=price",
                    "https://vw-triumf.ru/models/polo-new/",
                    "http://commercial.volkswagen-belgorod.ru/models/polo-new/prices/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/polo",
                    "https://carsdo.ru/volkswagen/polo-sedan/",
                    "https://belgorod.carso.ru/volkswagen/polo",
                    "https://belgorod.riaauto.ru/volkswagen/polo",
                    "https://belgorod.newautosalon.ru/volkswagen-polo/",
                    "https://belgorod.mbib.ru/volkswagen/polo/used",
                    "https://belgorod.110km.ru/prodazha/volkswagen/polo/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/polo/",
                    "https://autospot.ru/brands/volkswagen/polo/liftback/price/",
                    "https://wroom.ru/cars/volkswagen/polo-ru/price",
                    "https://carsdb.ru/volkswagen/polo-sedan/",
                    "https://belgorod.rrt-automarket.ru/new-cars/volkswagen/polo/",
                    "https://quto.ru/volkswagen/polo",
                    "https://autosalon-vw.ru/auto/volkswagen/new-polo_sedan"
                ]
            ],
            "volkswagen taos" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/taos.html",
                    "https://www.drom.ru/info/test-drive/volkswagen-taos-84803.html",
                    "https://mag.auto.ru/article/vse-plyusy-iminusy-volkswagen-taos-podrobnyy-test/",
                    "https://vw-triumf.ru/models/taos/",
                    "https://ru.wikipedia.org/wiki/volkswagen_taos",
                    "https://www.drive2.ru/cars/volkswagen/taos/m3448/",
                    "https://autoreview.ru/articles/pervaya-vstrecha/taosizm",
                    "https://www.avito.ru/belgorodskaya_oblast/avtomobili/volkswagen/taos-asgbagicaktgtg24msjitg3orls",
                    "https://www.zr.ru/content/articles/929649-novyj-krossover-vw-taos-obzor-i-video/",
                    "https://www.vw.com/en/models/taos.html",
                    "https://mobile-review.com/all/reviews/auto/test-volkswagen-taos-horoshij-kompaktnyj-krossover/",
                    "https://volkswagen-taos.ru/",
                    "https://www.drive.ru/brands/volkswagen/models/2021/taos",
                    "https://motor.ru/testdrives/volkswagen-taos-first-test.htm",
                    "https://avtoexperts.ru/article/volkswagen-taos-shest-dostoinstv-i-tri-neozhidanny-h-nedostatka/",
                    "https://www.auto-dd.ru/volkswagen-taos/",
                    "https://carsdo.ru/volkswagen/taos/",
                    "https://belgorod.autospot.ru/brands/volkswagen/taos/suv/price/",
                    "https://www.kolesa.ru/test-drive/test-volkswagen-taos-stoit-li-pereplachivat-za-klona-shkody-karoq",
                    "https://en.wikipedia.org/wiki/volkswagen_taos"
                ]
            ],
            "volkswagen teramont" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/teramont-new.html",
                    "https://auto.ru/cars/volkswagen/teramont/all/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/teramont-asgbagicaktgtg24msjitg3yssg",
                    "https://volkswagen.drom.ru/teramont/",
                    "https://www.drive.ru/test-drive/volkswagen/58f09e27ec05c40c3f0000d5.html",
                    "https://www.drive2.ru/cars/volkswagen/teramont/m3112/",
                    "https://vw-triumf.ru/models/teramont_new/",
                    "https://ru.wikipedia.org/wiki/volkswagen_teramont",
                    "https://www.youtube.com/watch?v=wg7ngip-j54",
                    "https://3dnews.ru/978379/obzor-volkswagen-teramont-semero-po-lavkam",
                    "https://avtoexperts.ru/article/volkswagen-teramont-bol-shoj-amerikanskij-krossover/",
                    "https://dzen.ru/media/carexpert.ru/stoit-li-pokupat-volkswagen-teramont-s-atmosfernikom-v6-5e4713344ce04c746cbe9d69",
                    "https://www.auto-dd.ru/volkswagen-teramont-2022/",
                    "https://belgorod.carso.ru/volkswagen/teramont",
                    "https://5koleso.ru/tests/dvizhimaya-nedvizhimost/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/teramont",
                    "https://ru.motor1.com/reviews/377017/volkswagen-teramont-vybiraem-mezhdu-dvumya-v6-i-turbochetverkoj/",
                    "https://autoreview.ru/articles/pervaya-vstrecha/teramonster",
                    "https://belgorod.autospot.ru/brands/volkswagen/teramont_i/suv/price/",
                    "https://belgorod.abc-auto.ru/volkswagen/terramont/"
                ]
            ],
            "volkswagen tiguan" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/tiguan-new.html",
                    "https://auto.ru/belgorod/cars/volkswagen/tiguan/all/",
                    "https://belgorod.drom.ru/volkswagen/tiguan/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/tiguan-asgbagicaktgtg24msjitg2ssig",
                    "https://vw-triumf.ru/models/tiguan_fl/",
                    "https://ru.wikipedia.org/wiki/volkswagen_tiguan",
                    "https://www.drive2.ru/cars/volkswagen/tiguan/m1487/",
                    "https://www.vw.com/en/models/tiguan.html",
                    "https://www.drive.ru/brands/volkswagen/models/2020/tiguan",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/tiguan",
                    "https://www.youtube.com/watch?v=x8qsnrwtquq",
                    "https://en.wikipedia.org/wiki/volkswagen_tiguan",
                    "https://carsdo.ru/volkswagen/tiguan/",
                    "https://belgorod.autospot.ru/brands/volkswagen/tiguan_2020/suv/price/",
                    "https://www.zr.ru/cars/volkswagen/-/volkswagen-tiguan/reviews/",
                    "https://otzovik.com/reviews/avtomobil_volkswagen_tiguan_vnedorozhnik_2010/",
                    "https://mobile-review.com/all/reviews/auto/test-volkswagen-tiguan-2021-komfortnyj-semejnyj-krossover/",
                    "https://auto.mail.ru/catalog/volkswagen/tiguan/",
                    "https://quto.ru/volkswagen/tiguan",
                    "https://3dnews.ru/951644/obzor-volkswagen-tiguan-s-oglyadkoy-na-premium"
                ]
            ],
            "volkswagen touareg" => [
                "sites" => [
                    "https://auto.ru/belgorod/cars/volkswagen/touareg/all/",
                    "https://www.volkswagen.ru/ru/models/touareg-exclusive.html",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/touareg-asgbagicaktgtg24msjitg2wsig",
                    "https://ru.wikipedia.org/wiki/volkswagen_touareg",
                    "https://belgorod.drom.ru/volkswagen/touareg/",
                    "https://vw-triumf.ru/models/touareg/",
                    "https://www.drive2.ru/cars/volkswagen/touareg/m1488/",
                    "https://www.drive.ru/test-drive/volkswagen/5b03e046ec05c4cd0c0000ea.html",
                    "https://en.wikipedia.org/wiki/volkswagen_touareg",
                    "https://belgorod.autospot.ru/brands/volkswagen/touareg/suv/price/",
                    "https://www.zr.ru/cars/volkswagen/-/volkswagen-touareg/",
                    "https://carsdo.ru/volkswagen/touareg/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/touareg",
                    "https://www.auto-dd.ru/volkswagen-touareg-3-2020/",
                    "https://autoiwc.ru/volkswagen/volkswagen-touareg.html",
                    "https://quto.ru/volkswagen/touareg",
                    "https://belgorod.110km.ru/prodazha/volkswagen/touareg/",
                    "https://motor.ru/testdrives/newtouareg.htm",
                    "https://pikabu.ru/story/yevolyutsiya_volkswagen_touareg_7269900",
                    "https://wroom.ru/cars/volkswagen/touareg/history"
                ]
            ],
            "volkswagen touareg новый цена" => [
                "sites" => [
                    "https://cars.volkswagen.ru/touareg/",
                    "https://auto.ru/cars/volkswagen/touareg/new/",
                    "https://vw-triumf.ru/models/touareg/",
                    "https://carsdo.ru/volkswagen/touareg/",
                    "https://belgorod.drom.ru/volkswagen/touareg/new/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/touareg",
                    "https://belgorod.autospot.ru/brands/volkswagen/touareg/suv/price/",
                    "http://commercial.volkswagen-belgorod.ru/models/touareg/",
                    "https://belgorod.abc-auto.ru/volkswagen/touareg/",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/touareg/",
                    "https://belgorod.carso.ru/volkswagen/touareg-old",
                    "https://www.avito.ru/all/avtomobili/novyy/volkswagen/touareg-asgbagica0sgfmbmaec2dbizkok2dbcyka",
                    "https://belgorod.riaauto.ru/volkswagen/touareg",
                    "https://vw-oskol.ru/models/touareg/prices/",
                    "https://www.major-vw.ru/models/touareg/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/touareg/",
                    "http://belgorod.lst-group.ru/new/volkswagen/touareg/",
                    "https://carsdb.ru/volkswagen/touareg/",
                    "https://avtomir.ru/new-cars/volkswagen/touareg/",
                    "https://vw-avtoruss.ru/models/touareg/"
                ]
            ],
            "volkswagen touareg характеристики" => [
                "sites" => [
                    "https://www.drom.ru/catalog/volkswagen/touareg/",
                    "https://auto.ru/catalog/cars/volkswagen/touareg/",
                    "https://www.volkswagen.ru/ru/models/touareg-exclusive/tech.html",
                    "https://ru.wikipedia.org/wiki/volkswagen_touareg",
                    "https://avtomarket.ru/catalog/volkswagen/touareg/",
                    "https://www.major-vw.ru/models/touareg/specifications/",
                    "https://autoiwc.ru/volkswagen/volkswagen-touareg.html",
                    "https://www.drive.ru/test-drive/volkswagen/5b03e046ec05c4cd0c0000ea.html",
                    "https://110km.ru/tth/volkswagen/touareg/",
                    "https://vwspb.ru/models/touareg/specifications/",
                    "https://translate.yandex.ru/translate?lang=en-ru&url=https%3a%2f%2fen.wikipedia.org%2fwiki%2ftouraeg&view=c",
                    "http://www.autonet.ru/auto/ttx/volkswagen/touareg",
                    "https://autospot.ru/brands/volkswagen/touareg/suv/spec/",
                    "https://fastmb.ru/testdrive/4115-tehnicheskie-harakteristiki-folksvagen-tuareg-2018-2020-i-rashod-topliva.html",
                    "https://autotrade-ag.ru/models/touareg/specifications/",
                    "https://auto.kolesa.ru/all-auto/volkswagen/touareg/characteristics",
                    "https://adom.ru/volkswagen/touareg/tth",
                    "http://vw-reynmotors.ru/models/touareg/specifications/",
                    "https://www.auto-dd.ru/volkswagen-touareg-3-2020/",
                    "https://favorit-motors.ru/catalog/new/volkswagen/touareg/tehnicheskie-harakteristiki/"
                ]
            ],
            "volkswagen touareg цена" => [
                "sites" => [
                    "https://auto.ru/belgorod/cars/volkswagen/touareg/all/",
                    "https://cars.volkswagen.ru/touareg/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/touareg-asgbagicaktgtg24msjitg2wsig",
                    "https://belgorod.drom.ru/volkswagen/touareg/",
                    "https://vw-triumf.ru/models/touareg/",
                    "https://carsdo.ru/volkswagen/touareg/",
                    "https://belgorod.autospot.ru/brands/volkswagen/touareg/suv/price/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/touareg",
                    "https://belgorod.carso.ru/volkswagen/touareg-old",
                    "https://belgorod.110km.ru/prodazha/volkswagen/touareg/",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/touareg/",
                    "http://commercial.volkswagen-belgorod.ru/models/touareg/",
                    "https://belgorod.riaauto.ru/volkswagen/touareg",
                    "https://belgorod.mbib.ru/volkswagen/touareg/used",
                    "http://belgorod.lst-group.ru/new/volkswagen/touareg/",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/touareg/",
                    "https://vw-oskol.ru/models/touareg/prices/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/touareg/",
                    "https://www.drive.ru/brands/volkswagen/models/2018/touareg",
                    "https://carsdb.ru/volkswagen/touareg/"
                ]
            ],
            "volkswagen белгород" => [
                "sites" => [
                    "https://vw-triumf.ru/",
                    "https://auto.ru/belgorod/cars/volkswagen/all/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen-asgbagicautgtg24msg",
                    "https://belgorod.drom.ru/volkswagen/",
                    "https://vk.com/triumfvw",
                    "https://2gis.ru/belgorod/search/volkswagen",
                    "https://yandex.ru/maps/org/ofitsialny_diler_volkswagen_avtotsentr_triumf/1340090677/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen",
                    "https://belgorod.autospot.ru/brands/volkswagen/",
                    "https://belgorod.zoon.ru/autoservice/type/avtosalon-volkswagen/",
                    "https://belgorod.carso.ru/volkswagen",
                    "https://belgorod.110km.ru/prodazha/volkswagen/",
                    "https://belgorod.big-book-avto.ru/avtosalon-volkswagen/",
                    "http://belgorod.avtotochki.ru/catalog/avtosalon-volkswagen/pt7c351964077248vm95/",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/",
                    "https://belgorod.jsprav.ru/avtoservisy-volkswagen/",
                    "https://www.drive2.ru/cars/volkswagen/?city=34581",
                    "https://mbib.ru/obl-belgorodskaya/volkswagen",
                    "https://autosalon-s.ru/avtosalony/belgorod/volkswagen",
                    "https://dilert.ru/volkswagen/volkswagen-belgorod/"
                ]
            ],
            "volkswagen белгород официальный дилер" => [
                "sites" => [
                    "https://vw-triumf.ru/",
                    "https://vk.com/triumfvw",
                    "https://yandex.ru/maps/org/ofitsialny_diler_volkswagen_avtotsentr_triumf/1340090677/",
                    "https://belgorod.autovsalone.ru/cars/sellers/triumf",
                    "https://2gis.ru/belgorod/firm/70000001018724741",
                    "https://auto.drom.ru/vw-triumf/",
                    "https://belgorod.zoon.ru/autoservice/ofitsialnyj_diler_volkswagen_avtotsentr_triumf_na_magistralnoj_ulitse_12/price/",
                    "https://auto.ru/diler-oficialniy/cars/all/avtocentr_triumf_volkswagen_belgorod/",
                    "https://www.yell.ru/belgorod/com/volkswagen-triumf-belgorod_11975126/reviews/",
                    "https://belgorod.110km.ru/dilery-salony/actriumphvw.html",
                    "https://belgorod.big-book-avto.ru/avtosalon-volkswagen/",
                    "https://dilert.ru/volkswagen/volkswagen-belgorod/avtocentr_triumf_volkswagen_belgorod/",
                    "https://autosalon-s.ru/avtosalony/belgorod/avtolyux-volkswagen-belgorod",
                    "http://belgorod.avtotochki.ru/catalog/avtosalon-volkswagen/pt7c351964077248vm95/",
                    "https://bezrulya.ru/dealers/list/volkswagen/6364/",
                    "https://qnx.org.ru/belgorod/avto-belgorod/avtoaksessuary-belgorod/ofitsialnyj-diler-volkswagen-avtotsentr-triumf-avtosalon-v-belgorode/",
                    "https://belgorod.flamp.ru/firm/triumf_avtocentr_oficialnyjj_diler_volkswagen-70000001018724741",
                    "https://autoleak.ru/dealers/volkswagen/belgorod/avtolyuks-ul-serafimovicha-d-65/",
                    "http://bel.rusdealers.ru/salon/saloon_4486.html",
                    "https://cardilers.ru/belgorod/volkswagen/"
                ]
            ],
            "volkswagen в белгороде купить" => [
                "sites" => [
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen-asgbagicautgtg24msg",
                    "https://auto.ru/belgorod/cars/volkswagen/all/",
                    "https://vw-triumf.ru/",
                    "https://belgorod.drom.ru/volkswagen/all/",
                    "http://commercial.volkswagen-belgorod.ru/models/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen",
                    "https://belgorod.110km.ru/prodazha/volkswagen/",
                    "https://belgorod.autospot.ru/brands/volkswagen/",
                    "https://belgorod.carso.ru/volkswagen",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/",
                    "https://2gis.ru/belgorod/search/volkswagen",
                    "https://mbib.ru/obl-belgorodskaya/volkswagen",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/",
                    "https://vk.com/triumfvw",
                    "https://belgorod.keyauto-probeg.ru/used/volkswagen/",
                    "https://belgorod.ml-respect.ru/car/volkswagen/",
                    "https://cars.avtocod.ru/belgorod/avto-s-probegom/volkswagen/",
                    "https://belgorod.cardana.ru/auto/models/volkswagen.html",
                    "https://yandex.ru/maps/org/ofitsialny_diler_volkswagen_avtotsentr_triumf/1340090677/",
                    "https://avto-dilery.ru/folksvagen-belgorod/"
                ]
            ],
            "авто фольксваген тигуан новый" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/tiguan-new.html",
                    "https://auto.ru/belgorod/cars/volkswagen/tiguan/new/",
                    "https://belgorod.drom.ru/volkswagen/tiguan/new/",
                    "https://vw-triumf.ru/models/tiguan_fl/",
                    "https://carsdo.ru/volkswagen/tiguan/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/tiguan",
                    "https://belgorod.carso.ru/volkswagen/tiguan-new",
                    "https://belgorod.autospot.ru/brands/volkswagen/tiguan_2020/suv/price/",
                    "https://belgorod.abc-auto.ru/volkswagen/tiguan-2021/",
                    "https://belgorod.riaauto.ru/volkswagen/tiguan",
                    "https://www.auto-dd.ru/volkswagen-tiguan-2021/",
                    "https://www.drive.ru/brands/volkswagen/models/2020/tiguan",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/tiguan_new/",
                    "https://naavtotrasse.ru/volkswagen/volkswagen-tiguan-2022.html",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/volkswagen-tiguan-2022",
                    "http://belgorod.lst-group.ru/new/volkswagen/tiguan/",
                    "https://autoiwc.ru/volkswagen/volkswagen-tiguan.html",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/tiguan-2021/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/tiguan-asgbagicaktgtg24msjitg2ssig",
                    "https://belgorod.110km.ru/prodazha/volkswagen/tiguan/novie/"
                ]
            ],
            "автомобиль фольксваген туарег" => [
                "sites" => [
                    "https://auto.ru/belgorod/cars/volkswagen/touareg/all/",
                    "https://cars.volkswagen.ru/touareg/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/touareg-asgbagicaktgtg24msjitg2wsig",
                    "https://belgorod.drom.ru/volkswagen/touareg/",
                    "https://ru.wikipedia.org/wiki/volkswagen_touareg",
                    "https://vw-triumf.ru/models/touareg/",
                    "https://translate.yandex.ru/translate?lang=en-ru&url=https%3a%2f%2fen.wikipedia.org%2fwiki%2ftouraeg&view=c",
                    "https://www.drive2.ru/cars/volkswagen/touareg/m1488/",
                    "https://carsdo.ru/volkswagen/touareg/",
                    "https://belgorod.autospot.ru/brands/volkswagen/touareg/suv/price/",
                    "https://www.zr.ru/cars/volkswagen/-/volkswagen-touareg/",
                    "https://www.drive.ru/test-drive/volkswagen/5b03e046ec05c4cd0c0000ea.html",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/touareg",
                    "https://autoiwc.ru/volkswagen/volkswagen-touareg.html",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/touareg/",
                    "https://wroom.ru/cars/volkswagen/touareg/history",
                    "https://belgorod.110km.ru/prodazha/volkswagen/touareg/",
                    "https://www.auto-dd.ru/volkswagen-touareg-3-2020/",
                    "https://quto.ru/volkswagen/touareg",
                    "https://naavtotrasse.ru/volkswagen/novyj-volkswagen-touareg-otzyvy.html"
                ]
            ],
            "автосалон фольксваген" => [
                "sites" => [
                    "https://vw-triumf.ru/",
                    "https://www.volkswagen.ru/ru/dealers.html",
                    "https://yandex.ru/maps/org/ofitsialny_diler_volkswagen_avtotsentr_triumf/1340090677/",
                    "https://2gis.ru/belgorod/search/volkswagen",
                    "https://vk.com/triumfvw",
                    "https://belgorod.drom.ru/dealers/volkswagen/",
                    "https://belgorod.zoon.ru/autoservice/type/avtosalon-volkswagen/",
                    "https://belgorod.autovsalone.ru/cars/sellers/search-volkswagen",
                    "https://belgorod.big-book-avto.ru/avtosalon-volkswagen/",
                    "https://auto.ru/belgorod/cars/volkswagen/all/",
                    "http://belgorod.avtotochki.ru/catalog/avtosalon-volkswagen/pt7c351964077248vm95/",
                    "https://belgorod.autospot.ru/dealermap/any/volkswagen/",
                    "https://belgorod.carso.ru/volkswagen",
                    "https://belgorod.110km.ru/dilery-salony/volkswagen/",
                    "https://vw-oskol.ru/",
                    "https://carsdb.ru/volkswagen/d/belgorod/",
                    "https://www.autodrive.ru/belgorod/autosalon/volkswagen/",
                    "https://belgorod.hipdir.com/avtosalon-volkswagen/",
                    "http://belgorod.autoneva.ru/avtosalony/volkswagen/",
                    "https://belgorod.keyauto-probeg.ru/used/volkswagen/"
                ]
            ],
            "белгород фольксваген туарег купить" => [
                "sites" => [
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/touareg-asgbagicaktgtg24msjitg2wsig",
                    "https://auto.ru/belgorod/cars/volkswagen/touareg/used/",
                    "https://vw-triumf.ru/models/touareg/",
                    "https://belgorod.drom.ru/volkswagen/touareg/",
                    "https://mbib.ru/obl-belgorodskaya/volkswagen/touareg",
                    "http://commercial.volkswagen-belgorod.ru/models/touareg/",
                    "https://belgorod.110km.ru/prodazha/volkswagen/touareg/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/touareg",
                    "https://belgorod.autospot.ru/brands/volkswagen/touareg/suv/price/",
                    "https://belgorod.abc-auto.ru/volkswagen/touareg/",
                    "https://carsdo.ru/volkswagen/touareg/belgorod/",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/touareg/",
                    "https://belgorod.carso.ru/volkswagen/touareg-old",
                    "https://belgorod.riaauto.ru/volkswagen/touareg",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/touareg/",
                    "http://belgorod.lst-group.ru/new/volkswagen/touareg/",
                    "https://belgorod.newautosalon.ru/volkswagen-touareg-business/",
                    "https://www.gazeta-a.ru/autosearch/belgorod/volkswagen/touareg/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/touareg/",
                    "https://www.m.njcar.ru/prices-partners/belgorod/volkswagen/touareg/all/"
                ]
            ],
            "гарантийное обслуживание автомобилей фольксваген" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/service/manufacturer-warranty.html",
                    "https://www.volkswagen.ru/ru/offers/warranty/manufacturer-warranty.html",
                    "https://vagdrive.com/threads/chto-nuzhno-znat-kazhdomu-potrebitelju-ili-8-zabluzhdenij-o-garantii-volkswagen.1216/",
                    "https://www.volkswagen-commercial.ru/ru/service/guarantee/manufacturer-warranty.html",
                    "https://dzen.ru/media/kua1102/zavodskaia-garantiia-u-avtomobilei-marki-volkswagen-est-li-skrytye-usloviia-5ff8614daf142f0b17d29da6",
                    "https://vw.avto-city.ru/owners/service/usloviya-predostavleniya-garantii-i-tekhnicheskogo-obsluzhivaniya-avto-volkswagen/",
                    "https://www.drive2.ru/l/612083280849669438/",
                    "https://www.zr.ru/content/articles/933793-ushli-brendy-garantiya-ne-dejs/",
                    "https://www.germanika.ru/owners/service/warranty/",
                    "https://auto.mail.ru/article/84644-kak-byit-vladeltsam-svezhih-inomarok-ushedshih-iz/",
                    "https://forum.tiguans.ru/threads/otkaz-ot-to-u-dilera-i-vopros-garantii.39868/",
                    "http://www.polosedan-club.com/threads/%d0%93%d0%b0%d1%80%d0%b0%d0%bd%d1%82%d0%b8%d1%8f-%d0%bd%d0%b0-%d0%9f%d0%be%d0%bb%d0%be-%d0%a1%d0%b5%d0%b4%d0%b0%d0%bd.454/",
                    "https://polosedan.ru/ps_wiki.php?w=91",
                    "https://www.avtovzglyad.ru/avto/avtoprom/2022-05-26-volkswagen-prodlevaet-sroki-garantijnogo-remonta-svoih-mashin-v-rossii/",
                    "https://www.drive2.com/l/575824411022590309/",
                    "https://www.volkswagen-petersburg.ru/owners/service/warranty/",
                    "https://5koleso.ru/avtopark/prodlennaya-garantiya-volkswagen-chto-ona-daet-i-kakie-est-ogranicheniya/",
                    "https://avilon.ru/brands/volkswagen/guaranty/",
                    "https://avtodigitals.ru/chto-budet-s-tekhobsluzhivaniem-iz-za-sankcij/",
                    "https://www.vw-wolf.ru/owners/post-warranty/"
                ]
            ],
            "дилеры фольксваген белгород" => [
                "sites" => [
                    "https://vw-triumf.ru/",
                    "https://2gis.ru/belgorod/search/volkswagen",
                    "https://belgorod.drom.ru/dealers/volkswagen/",
                    "https://vk.com/triumfvw",
                    "https://yandex.ru/maps/org/ofitsialny_diler_volkswagen_avtotsentr_triumf/1340090677/",
                    "https://auto.ru/belgorod/dilery/cars/volkswagen/new/",
                    "https://belgorod.autovsalone.ru/cars/sellers/triumf",
                    "https://belgorod.zoon.ru/autoservice/type/avtosalon-volkswagen/",
                    "https://belgorod.big-book-avto.ru/avtosalon-volkswagen/",
                    "http://belgorod.avtotochki.ru/catalog/avtosalon-volkswagen/pt7c351964077248vm95/",
                    "https://www.autodrive.ru/belgorod/autosalon/dealers/volkswagen/",
                    "https://dilert.ru/volkswagen/volkswagen-belgorod/",
                    "https://carsdb.ru/volkswagen/d/belgorod/",
                    "https://autosalon-s.ru/avtosalony/belgorod/volkswagen",
                    "https://belgorod.autospot.ru/dealermap/any/volkswagen/",
                    "http://www.bibika.ru/31/official_dealers/volkswagen",
                    "https://belgorodskaya-oblast.110km.ru/dilery-salony/volkswagen/",
                    "https://carpis.ru/of_dealers/belgorod/volkswagen/",
                    "https://cardilers.ru/belgorod/volkswagen/",
                    "http://belgorod.avteon.ru/volkswagen"
                ]
            ],
            "замена задних колодок фольксваген" => [
                "sites" => [
                    "https://www.drive2.ru/l/3371279/",
                    "https://www.youtube.com/watch?v=-nbvldbsudc",
                    "https://www.drive2.com/l/1910762/",
                    "https://vwts.ru/articles/brake/vw_passat_b6_zadnie_kolodki.html",
                    "https://www.zr.ru/content/articles/783874-zamena-kolodok-tormoznyx-mexanizmov-zadnix-koles-volkswagen-polo/",
                    "https://polovod.com/service/16-zamena-zadnih-tormoznyh-kolodok.html",
                    "https://blog-volkswagen.ru/articles/zamena-zadnih-tormoznyh-kolodok-na-vw-jetta-v/",
                    "https://bel.vse-avtoservisy.ru/zamena-zadnih-tormoznyih-kolodok-volkswagen/",
                    "https://pulse.mail.ru/article/kak-samostoyatelno-bez-kompyutera-zamenit-zadnie-kolodki-na-avtomobile-s-elektronnym-ruchnikom-3655333272821967170-8402327175855707428/",
                    "https://autoto.org/volkswagen/polo/9-zamena-zadnih-tormoznyh-kolodok-polo.html",
                    "https://carsclick.ru/volkswagen/obsluzhivanie/zamena-zadnih-kolodok-folksvagen-tiguan/",
                    "https://passatworld.ru/showthread.php/153010-zamena-zadnih-tormoznyh-diskov-i-kolodok-(otchet-s-foto)",
                    "https://krutilvertel.com/zamena-zadnego-tormoznogo-diska-tormoznyh-kolodok-vw-passat-b6",
                    "https://forum.tiguans.ru/threads/svoimi-rukami-menjaem-zadnie-tormoznye-kolodki-1-4tsi.47200/",
                    "https://avto-mechanik.ru/zamena-zadnih-tormoznyh-kolodok-passat-b3/",
                    "https://mehnic.ru/volkswagen/zamena-zadnikh-tormoznykh-diskov-i-kolodok-vw-passat-b6",
                    "https://detali-opt.ru/articles/zamena-zadnih-tormoznyh-kolodok-na-volkswagen-polo.html",
                    "https://dzen.ru/media/autonomia/zamena-zadnih-tormoznyh-kolodok-i-barabanov-volkswagen-polo-sedan-instrukciia--video-600fd11c8dfe7b3b2d939d95",
                    "https://carmanuals.ru/volkswagen/volkswagen-passat-b5/tormoznaya-sistema/zamena-zadnih-tormoznyh-kolodok",
                    "https://avtomonitor.ru/zadnie-kolodki-vw-polo.html"
                ]
            ],
            "замена масла фольксваген" => [
                "sites" => [
                    "https://belgorod.zoon.ru/autoservice/type/zamena_masla_v_dvigatele-volkswagen/",
                    "http://belgorod.avtotochki.ru/catalog/zamena-masla-volkswagen/pt1c351964077248s713vm95/",
                    "https://www.drive2.ru/l/623450306935522279/",
                    "https://www.youtube.com/watch?v=nkjdhfav9su",
                    "https://autoservisivse.ru/belgorod/zamena-masla/volkswagen/",
                    "https://www.drive2.com/l/8059435/",
                    "https://carsclick.ru/volkswagen/obsluzhivanie/motornoe-maslo/",
                    "https://servicesauto.ru/belgorod/zamena-masla/volkswagen/",
                    "https://uslugi.yandex.ru/4-belgorod/category?text=%d0%b7%d0%b0%d0%bc%d0%b5%d0%bd%d0%b0%20%d0%bc%d0%b0%d1%81%d0%bb%d0%b0%20%d1%84%d0%be%d0%bb%d1%8c%d0%ba%d1%81%d0%b2%d0%b0%d0%b3%d0%b5%d0%bd%20%d0%bf%d0%be%d0%bb%d0%be",
                    "https://volkswagen.centr.services/belgorod/%d0%b7%d0%b0%d0%bc%d0%b5%d0%bd%d0%b0-%d0%bc%d0%b0%d1%81%d0%bb%d0%b0",
                    "https://belgorod.hipdir.com/zamena-masla-volkswagen/",
                    "https://www.zr.ru/content/articles/784042-zamena-masla-v-dvigatele-maslyanogo-filtra-volkswagen-polo/",
                    "https://www.autodrive.ru/belgorod/autoservices/volkswagen/zamena-masla/",
                    "https://vwts.ru/articles/engine/cfna-zamena-masla-i-filtrov-vw-polo-sedan.html",
                    "https://polovod.com/service/11-zamena-masla.html",
                    "https://2gis.ru/belgorod/search/%d0%a3%d1%81%d0%bb%d1%83%d0%b3%d0%b8%20%d0%bf%d0%be%20%d0%b7%d0%b0%d0%bc%d0%b5%d0%bd%d0%b5%20%d0%bc%d0%b0%d1%81%d0%bb%d0%b0/rubricid/7900",
                    "https://autozaliv.ru/service/zamena-oil-motor/pomenyat-maslo-v-dvigatele-volkswagen-polo-sedan",
                    "https://prosmazku.ru/zamena/zamena-masla-v-polo-sedan",
                    "https://bel.vse-avtoservisy.ru/ekspress-zamena-masla-volkswagen/",
                    "https://oilspec.ru/zamena/v-dvigatele/polo-sedan"
                ]
            ],
            "купить volkswagen" => [
                "sites" => [
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen-asgbagicautgtg24msg",
                    "https://auto.ru/belgorod/cars/volkswagen/all/",
                    "https://www.volkswagen.ru/",
                    "https://belgorod.drom.ru/volkswagen/all/",
                    "https://vw-triumf.ru/",
                    "https://belgorod.autospot.ru/brands/volkswagen/",
                    "https://belgorod.carso.ru/volkswagen",
                    "https://belgorod.autovsalone.ru/cars/volkswagen",
                    "https://belgorod.110km.ru/prodazha/volkswagen/",
                    "http://commercial.volkswagen-belgorod.ru/models/",
                    "https://mbib.ru/obl-belgorodskaya/volkswagen",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/",
                    "https://vw-oskol.ru/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/",
                    "https://carsdo.ru/volkswagen/",
                    "https://belgorod.autodmir.ru/offers/volkswagen/",
                    "https://quto.ru/volkswagen",
                    "https://cars.vw-avtoruss.ru/",
                    "https://www.major-auto.ru/models/volkswagen/",
                    "https://www.drive2.ru/cars/volkswagen/?sort=selling"
                ]
            ],
            "купить volkswagen golf в белгороде" => [
                "sites" => [
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/golf-asgbagicaktgtg24msjitg3ipig",
                    "https://auto.ru/belgorod/cars/volkswagen/golf/all/",
                    "https://belgorod.drom.ru/volkswagen/golf/",
                    "https://vw-triumf.ru/models/golf-new/",
                    "https://belgorod.110km.ru/prodazha/volkswagen/golf/",
                    "https://mbib.ru/obl-belgorodskaya/volkswagen/golf",
                    "https://carsdo.ru/volkswagen/golf/belgorod/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/golf",
                    "https://belgorod.carso.ru/volkswagen/golf",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/golf/",
                    "https://www.volkswagen.ru/ru/models/new-golf.html",
                    "https://www.m.njcar.ru/prices-partners/belgorod/volkswagen/golf/all/",
                    "https://belgorod.autodmir.ru/offers/volkswagen/golf/",
                    "https://rydo.ru/belgorod/auto-volkswagen-golf/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/golf/",
                    "https://car.ru/belgorod/volkswagen/golf/",
                    "https://www.wiweb.ru/belgorod/auto/cars/volkswagen/golf",
                    "http://po-krupnomu.ru/belgorod/volkswagen-golf/",
                    "https://belgorod.autospot.ru/brands/volkswagen/",
                    "https://belgorod.zoon.ru/autoservice/type/kupit_volkswagen_golf/"
                ]
            ],
            "купить авто фольксваген" => [
                "sites" => [
                    "https://auto.ru/belgorod/cars/volkswagen/used/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen-asgbagicautgtg24msg",
                    "https://www.volkswagen.ru/",
                    "https://belgorod.drom.ru/volkswagen/all/",
                    "https://vw-triumf.ru/",
                    "https://belgorod.autospot.ru/brands/volkswagen",
                    "https://belgorod.autovsalone.ru/cars/volkswagen",
                    "https://belgorod.carso.ru/volkswagen",
                    "https://belgorod.110km.ru/vybor/volkswagen/kupit-s-probegom-poderzhannie-belgorod/",
                    "https://mbib.ru/obl-belgorodskaya/volkswagen",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/",
                    "https://carsdo.ru/volkswagen/",
                    "https://belgorod.ml-respect.ru/car/volkswagen/",
                    "https://vw-oskol.ru/models/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/",
                    "https://quto.ru/volkswagen",
                    "https://2gis.ru/belgorod/search/volkswagen",
                    "https://belgorod.cardana.ru/auto/models/volkswagen.html",
                    "https://belgorod.autodmir.ru/offers/volkswagen/",
                    "https://cars.vw-avtoruss.ru/"
                ]
            ],
            "купить новый фольксваген" => [
                "sites" => [
                    "https://cars.volkswagen.ru/",
                    "https://vw-triumf.ru/",
                    "https://auto.ru/belgorod/cars/volkswagen/new/",
                    "https://www.avito.ru/belgorodskaya_oblast/avtomobili/novyy/volkswagen-asgbagicaksgfmbmaec2dbizka",
                    "https://belgorod.autovsalone.ru/cars/volkswagen",
                    "https://belgorod.drom.ru/volkswagen/new/",
                    "https://belgorod.autospot.ru/brands/volkswagen/",
                    "https://belgorod.carso.ru/volkswagen",
                    "http://commercial.volkswagen-belgorod.ru/models/",
                    "https://carsdo.ru/volkswagen/",
                    "https://belgorodskaya-oblast.110km.ru/vybor/volkswagen/kupit-novie-belgorodskaya-oblast/",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/",
                    "https://belgorod.riaauto.ru/volkswagen",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/",
                    "https://belgorod.cardana.ru/auto/models/volkswagen.html",
                    "https://belgorod.rrt-automarket.ru/new-cars/volkswagen/",
                    "https://cars.vw-avtoruss.ru/",
                    "https://belgorod.newautosalon.ru/volkswagen-passat/",
                    "https://quto.ru/volkswagen",
                    "https://avilon.ru/brands/volkswagen/"
                ]
            ],
            "купить новый фольксваген поло" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/polo-new.html",
                    "https://vw-triumf.ru/models/polo-new/",
                    "https://auto.ru/belgorod/cars/volkswagen/polo/new/",
                    "https://m.avito.ru/belgorod/avtomobili/novyy/volkswagen/polo-asgbagica0sgfmbmaec2dbizkok2dyitka",
                    "https://belgorod.drom.ru/volkswagen/polo/new/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/polo",
                    "https://belgorod.carso.ru/volkswagen/polo-new",
                    "https://carsdo.ru/volkswagen/polo-sedan/belgorod/",
                    "https://belgorod.autospot.ru/brands/volkswagen/polo/liftback/price/",
                    "https://belgorod.abc-auto.ru/volkswagen/polo-new-2020/",
                    "https://belgorod.newautosalon.ru/volkswagen-polo/",
                    "https://belgorod.riaauto.ru/volkswagen/polo",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/polo_new/",
                    "https://belgorod.rrt-automarket.ru/new-cars/volkswagen/polo/",
                    "https://belgorod.110km.ru/prodazha/volkswagen/polo/novie/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/polo-new/",
                    "https://autosalon-vw.ru/auto/volkswagen/new-polo_sedan",
                    "https://vw-rolf.ru/models/polo-new/prices/",
                    "https://www.major-vw.ru/models/polo-new/",
                    "https://vw-motor.ru/models/polo-new/prices/"
                ]
            ],
            "купить новый фольксваген тигуан" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/tiguan-new.html",
                    "https://vw-triumf.ru/models/tiguan_fl/",
                    "https://auto.ru/belgorod/cars/volkswagen/tiguan/new/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/tiguan",
                    "https://belgorod.drom.ru/volkswagen/tiguan/new/",
                    "https://belgorod.autospot.ru/brands/volkswagen/tiguan_2020/suv/price/",
                    "https://belgorod.carso.ru/volkswagen/tiguan",
                    "https://carsdo.ru/volkswagen/tiguan/belgorod/",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/tiguan/nalichie/",
                    "http://commercial.volkswagen-belgorod.ru/models/tiguan_fl/",
                    "https://belgorod.abc-auto.ru/volkswagen/tiguan-2021/",
                    "https://belgorod.newautosalon.ru/volkswagen-tiguan/",
                    "https://belgorod.riaauto.ru/volkswagen/tiguan",
                    "http://belgorod.lst-group.ru/new/volkswagen/tiguan/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/tiguan-2021/",
                    "https://belgorod.110km.ru/prodazha/volkswagen/tiguan/novie/",
                    "https://belgorod.cardana.ru/auto/volkswagen/tiguan.html",
                    "https://belgorod.mbib.ru/volkswagen/tiguan",
                    "https://vw-avtoruss.ru/models/tiguan_fl/",
                    "https://www.drive.ru/brands/volkswagen/models/2020/tiguan"
                ]
            ],
            "купить фольксваген" => [
                "sites" => [
                    "https://auto.ru/belgorod/cars/volkswagen/all/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen-asgbagicautgtg24msg",
                    "https://www.volkswagen.ru/",
                    "https://vw-triumf.ru/",
                    "https://belgorod.drom.ru/volkswagen/all/",
                    "https://belgorod.autospot.ru/brands/volkswagen/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen",
                    "https://belgorod.carso.ru/volkswagen",
                    "https://belgorod.110km.ru/prodazha/volkswagen/",
                    "https://mbib.ru/obl-belgorodskaya/volkswagen",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/",
                    "https://vw-oskol.ru/",
                    "https://carsdo.ru/volkswagen/",
                    "https://belgorod.riaauto.ru/volkswagen",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/",
                    "https://belgorod.autodmir.ru/offers/volkswagen/used/",
                    "https://quto.ru/volkswagen",
                    "https://belgorod.big-book-avto.ru/avtosalon-volkswagen/",
                    "https://www.major-auto.ru/models/volkswagen/",
                    "https://www.drive2.ru/cars/volkswagen/?sort=selling"
                ]
            ],
            "купить фольксваген в белгороде" => [
                "sites" => [
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen-asgbagicautgtg24msg",
                    "https://vw-triumf.ru/",
                    "https://auto.ru/belgorod/cars/volkswagen/all/",
                    "https://belgorod.drom.ru/volkswagen/all/",
                    "http://commercial.volkswagen-belgorod.ru/models/",
                    "https://belgorod.110km.ru/prodazha/volkswagen/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen",
                    "https://belgorod.autospot.ru/brands/volkswagen/",
                    "https://mbib.ru/obl-belgorodskaya/volkswagen/used",
                    "https://belgorod.carso.ru/volkswagen",
                    "https://2gis.ru/belgorod/search/volkswagen",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/",
                    "https://vk.com/triumfvw",
                    "https://belgorod.ml-respect.ru/car/volkswagen/",
                    "https://belgorod.autodmir.ru/offers/volkswagen/",
                    "https://www.volkswagen.ru/",
                    "https://vw-oskol.ru/",
                    "https://car.ru/belgorod/volkswagen/all/",
                    "https://avto-dilery.ru/folksvagen-belgorod/"
                ]
            ],
            "купить фольксваген в белгородской области" => [
                "sites" => [
                    "https://www.avito.ru/belgorodskaya_oblast/avtomobili/volkswagen-asgbagicautgtg24msg",
                    "https://auto.ru/belgorodskaya_oblast/cars/volkswagen/all/",
                    "https://auto.drom.ru/region31/volkswagen/",
                    "https://mbib.ru/obl-belgorodskaya/volkswagen",
                    "https://belgorodskaya-oblast.110km.ru/prodazha/volkswagen/",
                    "https://vw-triumf.ru/",
                    "https://quto.ru/inventory/belgorodskayaoblast/volkswagen",
                    "https://belgorod.autospot.ru/brands/volkswagen/",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/",
                    "https://car.ru/auto/31/volkswagen/all/",
                    "http://commercial.volkswagen-belgorod.ru/models/",
                    "https://belgorod.autodmir.ru/offers/volkswagen/",
                    "http://po-krupnomu.ru/belgorodskaya-oblast/volkswagen/",
                    "https://rydo.ru/belgorodskaya-oblast/auto-volkswagen/",
                    "https://vw-oskol.ru/",
                    "https://novbu.ru/belgorodskaya-oblast/volkswagen/",
                    "http://mirkupit.ru/belgorodskaya-oblast/volkswagen/",
                    "https://belgorodskaya-obl.irr.ru/cars/passenger/used/volkswagen/",
                    "https://vk.com/avtobelogorie.probeg",
                    "https://www.bips.ru/used/volkswagen"
                ]
            ],
            "купить фольксваген гольф" => [
                "sites" => [
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/golf-asgbagicaktgtg24msjitg3ipig",
                    "https://auto.ru/belgorod/cars/volkswagen/golf/used/",
                    "https://belgorod.drom.ru/volkswagen/golf/",
                    "https://www.volkswagen.ru/ru/models/new-golf.html",
                    "https://vw-triumf.ru/models/golf-new/",
                    "https://belgorod.110km.ru/prodazha/volkswagen/golf/",
                    "https://mbib.ru/obl-belgorodskaya/volkswagen/golf",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/golf",
                    "https://belgorod.carso.ru/volkswagen/golf",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/golf/",
                    "https://belgorod.riaauto.ru/volkswagen/golf",
                    "https://belgorod.autodmir.ru/offers/volkswagen/golf/",
                    "https://belgorod.avanta-avto-credit.ru/cars/volkswagen/golf/",
                    "https://belgorod.keyauto-probeg.ru/used/volkswagen/golf/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/golf/",
                    "https://kupiprodai.ru/auto/cars/param66_3686",
                    "https://car.ru/auto/volkswagen/golf/",
                    "https://quto.ru/volkswagen/golf",
                    "https://rydo.ru/belgorodskaya-oblast/auto-volkswagen-golf/",
                    "https://www.drive2.ru/cars/volkswagen/golf/m1470/?sort=selling"
                ]
            ],
            "купить фольксваген поло" => [
                "sites" => [
                    "https://auto.ru/belgorod/cars/volkswagen/polo/all/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/polo-asgbagicaktgtg24msjitg2irsg",
                    "https://www.volkswagen.ru/ru/models/polo-new.html",
                    "https://belgorod.drom.ru/volkswagen/polo/",
                    "https://vw-triumf.ru/models/polo-new/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/polo",
                    "https://belgorod.autospot.ru/brands/volkswagen/polo/liftback/price/",
                    "https://belgorod.mbib.ru/volkswagen/polo/used",
                    "https://carsdo.ru/volkswagen/polo-sedan/belgorod/",
                    "https://belgorod.carso.ru/volkswagen/polo",
                    "https://belgorod.110km.ru/prodazha/volkswagen/polo/poderzhannie/",
                    "https://belgorod.abc-auto.ru/volkswagen/polo_sedan/",
                    "https://belgorod.newautosalon.ru/volkswagen-polo/",
                    "https://belgorod.riaauto.ru/volkswagen/polo",
                    "https://belgorod.rrt-automarket.ru/new-cars/volkswagen/polo/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/polo/",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/polo/",
                    "https://vw-motor.ru/models/polo-new/prices/",
                    "https://belgorod.autodmir.ru/offers/volkswagen/polo/used/",
                    "https://www.major-vw.ru/models/polo-new/"
                ]
            ],
            "купить фольксваген поло в белгороде" => [
                "sites" => [
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/polo-asgbagicaktgtg24msjitg2irsg",
                    "https://auto.ru/belgorod/cars/volkswagen/polo/all/",
                    "https://vw-triumf.ru/models/polo-new/",
                    "https://belgorod.drom.ru/volkswagen/polo/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/polo",
                    "http://commercial.volkswagen-belgorod.ru/models/polo-new/prices/",
                    "https://carsdo.ru/volkswagen/polo-sedan/belgorod/",
                    "https://belgorod.carso.ru/volkswagen/polo",
                    "https://belgorod.mbib.ru/volkswagen/polo/used",
                    "https://belgorod.110km.ru/prodazha/volkswagen/polo/poderzhannie/",
                    "https://belgorod.autospot.ru/brands/volkswagen/polo/liftback/price/",
                    "https://belgorod.newautosalon.ru/volkswagen-polo/",
                    "https://belgorod.riaauto.ru/volkswagen/polo",
                    "https://belgorod.rrt-automarket.ru/new-cars/volkswagen/polo/",
                    "https://belgorod.abc-auto.ru/volkswagen/polo_sedan/",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/polo/",
                    "https://cars.volkswagen.ru/polo/",
                    "https://belgorod.autodmir.ru/offers/volkswagen/polo/akpp/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/polo/",
                    "https://www.njcar.ru/prices-partners/belgorod/volkswagen/polo/all/"
                ]
            ],
            "купить фольксваген поло у официального дилера" => [
                "sites" => [
                    "https://vw-triumf.ru/models/polo-new/",
                    "https://cars.volkswagen.ru/polo/",
                    "http://commercial.volkswagen-belgorod.ru/models/polo-new/prices/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/polo",
                    "https://carsdo.ru/volkswagen/polo-sedan/belgorod/",
                    "https://belgorod.abc-auto.ru/volkswagen/polo_sedan/",
                    "https://auto.ru/belgorod/cars/volkswagen/polo/all/",
                    "https://belgorod.carso.ru/volkswagen/polo-new",
                    "https://belgorod.newautosalon.ru/volkswagen-polo/",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/polo_new/",
                    "https://belgorod.autospot.ru/brands/volkswagen/polo/liftback/price/",
                    "https://www.major-vw.ru/models/polo-new/",
                    "https://belgorod.riaauto.ru/volkswagen/polo",
                    "https://autosalon-vw.ru/auto/volkswagen/new-polo_sedan",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/polo/",
                    "https://vw-rolf.ru/models/polo-new/",
                    "https://belgorod.rrt-automarket.ru/new-cars/volkswagen/polo/",
                    "https://belgorod.avanta-avto-credit.ru/cars/volkswagen/polo-2019/",
                    "https://belgorod.drom.ru/volkswagen/polo/new/",
                    "https://www.avilon-vw.ru/models/polo-new/"
                ]
            ],
            "купить фольксваген тигуан" => [
                "sites" => [
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/tiguan-asgbagicaktgtg24msjitg2ssig",
                    "https://auto.ru/belgorod/cars/volkswagen/tiguan/used/",
                    "https://cars.volkswagen.ru/tiguan/",
                    "https://belgorod.drom.ru/volkswagen/tiguan/",
                    "https://vw-triumf.ru/models/tiguan_fl/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/tiguan",
                    "http://commercial.volkswagen-belgorod.ru/models/tiguan_fl/",
                    "https://belgorod.autospot.ru/brands/volkswagen/tiguan_2020/suv/price/",
                    "https://belgorod.110km.ru/prodazha/volkswagen/tiguan/poderzhannie/",
                    "https://belgorod.mbib.ru/volkswagen/tiguan",
                    "https://belgorod.carso.ru/volkswagen/tiguan",
                    "https://belgorod.abc-auto.ru/volkswagen/tiguan/",
                    "https://belgorod.riaauto.ru/volkswagen/tiguan",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/tiguan_new/",
                    "https://carsdo.ru/volkswagen/tiguan/",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/tiguan/",
                    "https://belgorod.cardana.ru/auto/volkswagen/tiguan.html",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/tiguan/",
                    "https://belgorod.autodmir.ru/offers/volkswagen/tiguan/",
                    "https://www.rolf.ru/cars/new/volkswagen/tiguan_new/"
                ]
            ],
            "купить фольксваген туарег" => [
                "sites" => [
                    "https://www.avito.ru/belgorodskaya_oblast/avtomobili/volkswagen/touareg-asgbagicaktgtg24msjitg2wsig",
                    "https://auto.ru/belgorod/cars/volkswagen/touareg/all/",
                    "https://belgorod.drom.ru/volkswagen/touareg/",
                    "https://cars.volkswagen.ru/touareg/",
                    "https://vw-triumf.ru/models/touareg/",
                    "https://belgorod.autospot.ru/brands/volkswagen/touareg/suv/price/",
                    "https://belgorod.110km.ru/prodazha/volkswagen/touareg/",
                    "https://belgorod.mbib.ru/volkswagen/touareg",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/touareg",
                    "https://carsdo.ru/volkswagen/touareg/belgorod/",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/touareg/",
                    "https://belgorod.carso.ru/volkswagen/touareg-old",
                    "https://belgorod.riaauto.ru/volkswagen/touareg",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/touareg/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/touareg/",
                    "https://belgorod.autodmir.ru/offers/volkswagen/touareg/",
                    "https://www.major-vw.ru/models/touareg/",
                    "https://kupiprodai.ru/auto/cars/param66_3708",
                    "https://belgorod.keyauto-probeg.ru/used/volkswagen/touareg/",
                    "https://avilon.ru/brands/volkswagen/touareg/"
                ]
            ],
            "купить фольксваген туарег новый" => [
                "sites" => [
                    "https://cars.volkswagen.ru/touareg/",
                    "https://auto.ru/belgorod/cars/volkswagen/touareg/new/",
                    "https://vw-triumf.ru/models/touareg/",
                    "https://belgorod.drom.ru/volkswagen/touareg/new/",
                    "https://www.avito.ru/belgorodskaya_oblast/avtomobili/novyy/volkswagen/touareg",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/touareg",
                    "https://belgorod.autospot.ru/brands/volkswagen/touareg/suv/price/",
                    "https://carsdo.ru/volkswagen/touareg/belgorod/",
                    "http://commercial.volkswagen-belgorod.ru/models/touareg/",
                    "https://belgorod.abc-auto.ru/volkswagen/touareg/",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/touareg/",
                    "https://belgorod.carso.ru/volkswagen/touareg-old",
                    "http://belgorod.lst-group.ru/new/volkswagen/touareg/",
                    "https://vw-oskol.ru/models/touareg/prices/",
                    "https://www.major-vw.ru/models/touareg/",
                    "https://belgorod.riaauto.ru/volkswagen/touareg",
                    "https://belgorod.newautosalon.ru/volkswagen-touareg-business/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/touareg/",
                    "https://avilon.ru/brands/volkswagen/touareg/",
                    "https://belgorod.110km.ru/prodazha/volkswagen/touareg/novie/"
                ]
            ],
            "машина фольксваген туарег" => [
                "sites" => [
                    "https://auto.ru/belgorod/cars/volkswagen/touareg/all/",
                    "https://cars.volkswagen.ru/touareg/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/touareg-asgbagicaktgtg24msjitg2wsig",
                    "https://belgorod.drom.ru/volkswagen/touareg/",
                    "https://ru.wikipedia.org/wiki/volkswagen_touareg",
                    "https://vw-triumf.ru/models/touareg/",
                    "https://www.drive2.ru/cars/volkswagen/touareg/m1488/",
                    "https://translate.yandex.ru/translate?lang=en-ru&url=https%3a%2f%2fen.wikipedia.org%2fwiki%2ftouraeg&view=c",
                    "https://belgorod.autospot.ru/brands/volkswagen/touareg/suv/price/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/touareg",
                    "https://www.drive.ru/test-drive/volkswagen/5b03e046ec05c4cd0c0000ea.html",
                    "https://carsdo.ru/volkswagen/touareg/",
                    "https://www.zr.ru/cars/volkswagen/-/volkswagen-touareg/",
                    "https://autoiwc.ru/volkswagen/volkswagen-touareg.html",
                    "https://belgorod.110km.ru/prodazha/volkswagen/touareg/",
                    "https://www.auto-dd.ru/volkswagen-touareg-3-2020/",
                    "https://auto.mail.ru/reviews/volkswagen/touareg/",
                    "https://quto.ru/volkswagen/touareg",
                    "https://wroom.ru/cars/volkswagen/touareg/history",
                    "https://www.kolesa.ru/article/voennaya-versiya-plokhaya-prokhodimost-i-zvanie-pervogo-mify-i-fakty-o-volkswagen-touareg-i"
                ]
            ],
            "новый фольксваген гольф" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/new-golf.html",
                    "https://vw-triumf.ru/models/golf-new/",
                    "https://www.drive.ru/test-drive/volkswagen/5df9fdb3ec05c4802000000e.html",
                    "https://auto.ru/catalog/cars/volkswagen/golf/21700369/21700419/",
                    "https://naavtotrasse.ru/volkswagen/volkswagen-golf-2022.html",
                    "https://carsdo.ru/volkswagen/golf/",
                    "https://www.zr.ru/content/articles/921419-volkswagen-golf-2020-pervyj-test-drajv/",
                    "https://www.drom.ru/info/test-drive/vw-golf-gti-viii-82094.html",
                    "https://www.auto-dd.ru/volkswagen-golf/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/golf",
                    "https://www.drive2.ru/e/b5wxaeaaana",
                    "https://autoreview.ru/articles/pervaya-vstrecha/vw-golf-8",
                    "https://cenyavto.com/volkswagen-golf-2022/",
                    "https://belgorod.abc-auto.ru/volkswagen/golf-2021/",
                    "https://belgorod.carso.ru/volkswagen/golf-new",
                    "https://autoiwc.ru/volkswagen/volkswagen-golf.html",
                    "https://belgorod.newautosalon.ru/volkswagen-golf/",
                    "https://gt-news.ru/volkswagen/golf-8-2021/",
                    "https://wylsa.com/predstavlen-volkswagen-golf-vosmogo-pokoleniya-on-nemnogo-strashnyj/",
                    "https://rg.ru/2021/05/16/novyj-volkswagen-golf-stoit-li-on-svoih-deneg.html"
                ]
            ],
            "новый фольксваген тигуан" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/tiguan-new.html",
                    "https://auto.ru/belgorod/cars/volkswagen/tiguan/new/",
                    "https://vw-triumf.ru/models/tiguan_fl/",
                    "https://belgorod.drom.ru/volkswagen/tiguan/new/",
                    "https://carsdo.ru/volkswagen/tiguan/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/tiguan",
                    "https://www.drive.ru/brands/volkswagen/models/2020/tiguan",
                    "https://www.auto-dd.ru/volkswagen-tiguan-2021/",
                    "https://belgorod.autospot.ru/brands/volkswagen/tiguan_2020/suv/price/",
                    "https://naavtotrasse.ru/volkswagen/volkswagen-tiguan-2022.html",
                    "https://belgorod.carso.ru/volkswagen/tiguan-new",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/volkswagen-tiguan-2022",
                    "https://mobile-review.com/all/reviews/auto/test-volkswagen-tiguan-2021-komfortnyj-semejnyj-krossover/",
                    "https://gt-news.ru/volkswagen/volkswagen-tiguan-2023/",
                    "https://belgorod.newautosalon.ru/volkswagen-tiguan/",
                    "https://belgorod.riaauto.ru/volkswagen/tiguan",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/tiguan-asgbagicaktgtg24msjitg2ssig",
                    "https://www.zr.ru/content/articles/926790-sensory-privet/",
                    "https://www.drive2.ru/b/583240616951940076/",
                    "https://www.allcarz.ru/vw-tiguan-2021/"
                ]
            ],
            "новый фольксваген туарег" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/touareg-exclusive.html",
                    "https://auto.ru/belgorod/cars/volkswagen/touareg/new/",
                    "https://vw-triumf.ru/models/touareg/",
                    "https://belgorod.drom.ru/volkswagen/touareg/new/",
                    "https://carsdo.ru/volkswagen/touareg/",
                    "https://www.drive.ru/test-drive/volkswagen/5b03e046ec05c4cd0c0000ea.html",
                    "https://www.auto-dd.ru/volkswagen-touareg-3-2020/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/touareg",
                    "https://naavtotrasse.ru/volkswagen/volkswagen-touareg-2022.html",
                    "http://commercial.volkswagen-belgorod.ru/models/touareg/",
                    "https://autoiwc.ru/volkswagen/volkswagen-touareg.html",
                    "https://belgorod.autospot.ru/brands/volkswagen/touareg/suv/price/",
                    "https://gt-news.ru/volkswagen/vw-touareg-2021/",
                    "https://cenyavto.com/volkswagen-touareg-2022/",
                    "https://www.youtube.com/watch?v=fdkiwgcsgly",
                    "https://ilovecross.com/volkswagen-touareg-2021/",
                    "https://www.avito.ru/all/avtomobili/novyy/volkswagen/touareg-asgbagica0sgfmbmaec2dbizkok2dbcyka",
                    "https://motor.ru/testdrives/newtouareg.htm",
                    "https://www.drive2.ru/e/bvorgeaaate",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/touareg/"
                ]
            ],
            "обслуживание автомобилей фольксваген" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/service/maintenance.html",
                    "https://vw-triumf.ru/owners/service/",
                    "https://belgorod.zoon.ru/autoservice/type/volkswagen/",
                    "https://bel.vse-avtoservisy.ru/volkswagen/",
                    "https://volkswagen.centr.services/belgorod",
                    "https://2gis.ru/belgorod/search/volkswagen",
                    "https://belgorod.jsprav.ru/avtoservisy-volkswagen/",
                    "https://uslugi.yandex.ru/4-belgorod/category?text=%d0%b3%d0%b4%d0%b5+%d0%bf%d1%80%d0%be%d0%b9%d1%82%d0%b8+%d1%82%d0%be+%d1%84%d0%be%d0%bb%d1%8c%d0%ba%d1%81%d0%b2%d0%b0%d0%b3%d0%b5%d0%bd",
                    "http://commercial.volkswagen-belgorod.ru/owners/service/",
                    "https://belgorod.autospot.ru/autoservice/to/volkswagen/",
                    "https://www.servicebox.ru/belgorod/volkswagen/",
                    "https://carpis.ru/autoservice/belgorod/volkswagen/",
                    "https://vse-sto.ru/belgorod/sto/volkswagen/",
                    "https://belgorod.fitauto.ru/services/remont-i-obsluzhivanie-avtomobilej-volkswagen/",
                    "https://servicesauto.ru/belgorod/to/volkswagen/",
                    "http://belgorod.avtotochki.ru/catalog/avtoservis-volkswagen/pt1c351964077248vm95/",
                    "https://belgorod.big-book-avto.ru/remont-i-obsluzhivanie-volkswagen/",
                    "https://kulibin-auto-service.ru/autos/volkswagen/",
                    "https://sto-kontakt.ru/",
                    "https://belgorod.hipdir.com/avtoservis-volkswagen/"
                ]
            ],
            "обслуживание фольксваген" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/service/maintenance.html",
                    "https://vw-triumf.ru/owners/service/",
                    "https://bel.vse-avtoservisy.ru/volkswagen/",
                    "https://2gis.ru/belgorod/search/volkswagen%20%d1%81%d0%b5%d1%80%d0%b2%d0%b8%d1%81",
                    "https://belgorod.zoon.ru/autoservice/type/volkswagen/",
                    "https://belgorod.jsprav.ru/avtoservisy-volkswagen/",
                    "https://belgorod.spravker.ru/avtoservisy-volkswagen/",
                    "https://volkswagen.centr.services/belgorod",
                    "https://belgorod.autospot.ru/autoservice/to/volkswagen/",
                    "https://www.servicebox.ru/belgorod/volkswagen/",
                    "http://commercial.volkswagen-belgorod.ru/owners/service/",
                    "https://belgorod.fitauto.ru/services/remont-evropeiskih-avto/volkswagen/",
                    "https://belgorod.big-book-avto.ru/remont-i-obsluzhivanie-volkswagen/",
                    "https://carpis.ru/autoservice/belgorod/volkswagen/",
                    "https://autoservisivse.ru/belgorod/tehnicheskoe-obsluzhivanie/volkswagen/",
                    "https://servicesauto.ru/belgorod/to/volkswagen/",
                    "https://belgorod.hipdir.com/avtoservis-volkswagen/",
                    "https://carsclick.ru/volkswagen/obsluzhivanie/osobennosti-prohozhdeniya-to-avtomobilem-folksvagen/",
                    "https://dzen.ru/media/autonomia/chto-meniat-i-na-kakom-probege-tablica-provedeniia-to-volkswagen-5ed8ae2b3f6743720623e0cd",
                    "https://kulibin-auto-service.ru/services-menu/remont-avtomobiley-volkswagen-v-belgorode-folksvagen-servis/"
                ]
            ],
            "продажа фольксваген поло" => [
                "sites" => [
                    "https://auto.ru/belgorod/cars/volkswagen/polo/all/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/polo-asgbagicaktgtg24msjitg2irsg",
                    "https://belgorod.drom.ru/volkswagen/polo/",
                    "https://cars.volkswagen.ru/polo/",
                    "https://vw-triumf.ru/models/polo-new/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/polo",
                    "https://mbib.ru/obl-belgorodskaya/volkswagen/polo",
                    "https://belgorod.autospot.ru/brands/volkswagen/polo/liftback/price/",
                    "https://carsdo.ru/volkswagen/polo-sedan/belgorod/",
                    "https://belgorod.110km.ru/prodazha/volkswagen/polo/poderzhannie/",
                    "https://belgorod.carso.ru/volkswagen/polo",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/polo/",
                    "https://belgorod.abc-auto.ru/volkswagen/polo_sedan/",
                    "https://belgorod.newautosalon.ru/volkswagen-polo/",
                    "https://belgorod.rrt-automarket.ru/new-cars/volkswagen/polo/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/polo/",
                    "https://belgorod.autodmir.ru/offers/volkswagen/polo/akpp/",
                    "https://kupiprodai.ru/auto/cars/param66_3701",
                    "https://rydo.ru/belgorodskaya-oblast/auto-volkswagen-polo/",
                    "https://www.kolesa.ru/article/pyat-veshhej-za-kotorye-lyubyat-i-nenavidyat-volkswagen-polo"
                ]
            ],
            "продажа фольксваген тигуан" => [
                "sites" => [
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/tiguan-asgbagicaktgtg24msjitg2ssig",
                    "https://auto.ru/belgorod/cars/volkswagen/tiguan/used/",
                    "https://belgorod.drom.ru/volkswagen/tiguan/",
                    "https://cars.volkswagen.ru/tiguan/",
                    "https://vw-triumf.ru/models/tiguan_fl/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/tiguan",
                    "https://belgorod.110km.ru/prodazha/volkswagen/tiguan/poderzhannie/",
                    "https://belgorod.mbib.ru/volkswagen/tiguan",
                    "https://belgorod.autospot.ru/brands/volkswagen/tiguan_2020/suv/price/",
                    "http://commercial.volkswagen-belgorod.ru/models/tiguan_fl/",
                    "https://carsdo.ru/volkswagen/tiguan/belgorod/",
                    "https://belgorod.carso.ru/volkswagen/tiguan",
                    "https://belgorod.riaauto.ru/volkswagen/tiguan",
                    "https://belgorod.newautosalon.ru/volkswagen-tiguan/",
                    "https://belgorod.abc-auto.ru/volkswagen/tiguan/",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/tiguan_new/",
                    "http://belgorod.lst-group.ru/new/volkswagen/tiguan/",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/tiguan/",
                    "https://belgorod.autodmir.ru/offers/volkswagen/tiguan/",
                    "https://belgorod.cardana.ru/auto/volkswagen/tiguan.html"
                ]
            ],
            "ремонт volkswagen" => [
                "sites" => [
                    "https://uslugi.yandex.ru/4-belgorod/category?text=%d1%80%d0%b5%d0%bc%d0%be%d0%bd%d1%82+%d1%84%d0%be%d0%bb%d1%8c%d0%ba%d1%81%d0%b2%d0%b0%d0%b3%d0%b5%d0%bd",
                    "https://belgorod.zoon.ru/autoservice/type/volkswagen/",
                    "https://bel.vse-avtoservisy.ru/volkswagen/",
                    "https://2gis.ru/belgorod/search/%d0%90%d0%b2%d1%82%d0%be%d1%81%d0%b5%d1%80%d0%b2%d0%b8%d1%81%20vag%20(%d0%b2%d0%b0%d0%b3)%20%d0%b2%20%d0%91%d0%b5%d0%bb%d0%b3%d0%be%d1%80%d0%be%d0%b4%d0%b5",
                    "https://belgorod.jsprav.ru/avtoservisy-volkswagen/",
                    "https://www.avito.ru/belgorod?q=%d1%80%d0%b5%d0%bc%d0%be%d0%bd%d1%82+vag",
                    "https://vw-triumf.ru/owners/service/",
                    "https://volkswagen.centr.services/belgorod",
                    "https://www.youtube.com/playlist?list=plsvbqlaqoj8eu_b6w-iwcdvwofvvdzgll",
                    "https://www.servicebox.ru/belgorod/volkswagen/",
                    "http://belgorod.avtotochki.ru/catalog/avtoservis-volkswagen/pt1c351964077248vm95/",
                    "https://autoservice131.ru/remont/volkswagen",
                    "https://carpis.ru/autoservice/belgorod/volkswagen/",
                    "https://belgorod.big-book-avto.ru/remont-i-obsluzhivanie-volkswagen/",
                    "https://driff.ru/service/sto/belgorod/volkswagen/",
                    "https://vse-sto.ru/belgorod/sto/volkswagen/",
                    "https://autoservisivse.ru/belgorod/volkswagen/",
                    "https://totadres.ru/belgorod/remont_volkswagen",
                    "https://belgorod.fitauto.ru/services/remont-evropeiskih-avto/volkswagen/",
                    "https://vwts.ru/manuals.html"
                ]
            ],
            "ремонт двигателя фольксваген" => [
                "sites" => [
                    "https://belgorod.zoon.ru/autoservice/type/remont_dvigatelya-volkswagen/",
                    "https://bel.vse-avtoservisy.ru/remont-dvigatelya-volkswagen/",
                    "https://uslugi.yandex.ru/4-belgorod/category?text=%d1%80%d0%b5%d0%bc%d0%be%d0%bd%d1%82+%d0%b4%d0%b2%d0%b8%d0%b3%d0%b0%d1%82%d0%b5%d0%bb%d1%8f+%d1%84%d0%be%d0%bb%d1%8c%d0%ba%d1%81%d0%b2%d0%b0%d0%b3%d0%b5%d0%bd",
                    "http://belgorod.avtotochki.ru/catalog/pemont-dvigatelya-volkswagen/pt1c351964077248s3vm95/",
                    "https://www.drive2.ru/l/573748533069351260/",
                    "https://autoservisivse.ru/belgorod/remont-dvigatelya/volkswagen/",
                    "https://servicesauto.ru/belgorod/remont-dvigatelya/volkswagen/",
                    "https://www.youtube.com/watch?v=wef0xxjuyng",
                    "https://razborka.org/avtoservis/belgorod/remont-dvigatelya/volkswagen",
                    "https://www.yell.ru/belgorod/top/remont-dvigatelya-volkswagen/",
                    "https://volkswagen.centr.services/belgorod/%d1%80%d0%b5%d0%bc%d0%be%d0%bd%d1%82-%d0%b4%d0%b2%d0%b8%d0%b3%d0%b0%d1%82%d0%b5%d0%bb%d1%8f",
                    "https://vse-sto.ru/belgorod/sto/remont-dvigatelja/volkswagen/",
                    "https://www.autodrive.ru/belgorod/autoservices/volkswagen/remont-dvigateley/",
                    "https://belgorod.hipdir.com/remont-dvigateley-volkswagen/",
                    "https://belgorod.jsprav.ru/avtoservisy-volkswagen/",
                    "https://www.drive2.com/l/7210686/",
                    "https://belgorod.fitauto.ru/services/remont-dvigatelya/volkswagen/",
                    "https://belgorod.masterdel.ru/master/remont-dvigatelya-folksvagen/",
                    "https://vwts.ru/articles/engine/awy-azq-bme-bmd-bbm-bzg-ceva-chfa-cgpa-kapitalny-remont-dvigateley.html",
                    "https://carpis.ru/autoservice/belgorod/volkswagen/"
                ]
            ],
            "ремонт кпп фольксваген" => [
                "sites" => [
                    "https://belgorod.zoon.ru/autoservice/type/remont_mkpp-volkswagen/",
                    "https://uslugi.yandex.ru/4-belgorod/category?text=%d1%80%d0%b5%d0%bc%d0%be%d0%bd%d1%82+%d0%bc%d0%ba%d0%bf%d0%bf+%d1%84%d0%be%d0%bb%d1%8c%d0%ba%d1%81%d0%b2%d0%b0%d0%b3%d0%b5%d0%bd",
                    "https://bel.vse-avtoservisy.ru/remont-kpp-volkswagen/",
                    "https://www.drive2.ru/l/560002438698893473/",
                    "https://www.avito.ru/belgorodskaya_oblast?q=%d1%80%d0%b5%d0%bc%d0%be%d0%bd%d1%82+%d0%bc%d0%ba%d0%bf%d0%bf+vw+t4",
                    "http://belgorod.avtotochki.ru/catalog/remont-mkpp-volkswagen/pt1c351964077248s4vm95/",
                    "https://razborka.org/avtoservis/belgorod/remont-mkpp/volkswagen",
                    "https://vwts.ru/articles/trans/mkpp-02t-pereborka-korobki-zamena-podshipnikov.html",
                    "https://www.drive2.com/l/590130156811602326/",
                    "https://www.youtube.com/watch?v=twmaa5qdcoi",
                    "https://servicesauto.ru/belgorod/remont-mkpp/volkswagen/",
                    "https://vse-sto.ru/belgorod/sto/volkswagen/remont-kpp/",
                    "https://top-sto.ru/belgorod/garages/remont-kpp/volkswagen",
                    "https://carpis.ru/autoservice/belgorod/volkswagen/",
                    "https://belgorod.localrepair.ru/sto/service/remont-kpp",
                    "https://etlib.ru/auto/volkswagen-57/korobka-peredach-97/mkpp-101",
                    "https://mirjetta.ru/obsluzhivaem-i-remontiruem/remont/kpp/",
                    "https://sto-belgorod.ru/remont-korobki-peredach-belgorod.html",
                    "https://fanclub-vw-bus.ru/forum/viewtopic.php?t=9883&start=20",
                    "https://belgorod.jsprav.ru/remont-zamena-i-diagnostika-korobki-peredach/"
                ]
            ],
            "ремонт фольксваген" => [
                "sites" => [
                    "https://belgorod.zoon.ru/autoservice/type/volkswagen/",
                    "https://uslugi.yandex.ru/4-belgorod/category?text=%d1%80%d0%b5%d0%bc%d0%be%d0%bd%d1%82+%d1%84%d0%be%d0%bb%d1%8c%d0%ba%d1%81%d0%b2%d0%b0%d0%b3%d0%b5%d0%bd",
                    "https://belgorod.jsprav.ru/avtoservisy-volkswagen/",
                    "https://bel.vse-avtoservisy.ru/volkswagen/",
                    "https://2gis.ru/belgorod/search/%d0%90%d0%b2%d1%82%d0%be%d1%81%d0%b5%d1%80%d0%b2%d0%b8%d1%81%20vag%20(%d0%b2%d0%b0%d0%b3)%20%d0%b2%20%d0%91%d0%b5%d0%bb%d0%b3%d0%be%d1%80%d0%be%d0%b4%d0%b5",
                    "https://www.avito.ru/belgorod/predlozheniya_uslug?q=%d1%80%d0%b5%d0%bc%d0%be%d0%bd%d1%82+vw",
                    "https://vw-triumf.ru/owners/service/",
                    "https://belgorod.spravker.ru/avtoservisy-volkswagen/",
                    "https://www.servicebox.ru/belgorod/volkswagen/",
                    "https://volkswagen.centr.services/belgorod",
                    "https://totadres.ru/belgorod/remont_volkswagen",
                    "https://carpis.ru/autoservice/belgorod/volkswagen/",
                    "https://autoservisivse.ru/belgorod/volkswagen/",
                    "https://www.youtube.com/playlist?list=plsvbqlaqoj8eu_b6w-iwcdvwofvvdzgll",
                    "https://belgorod.big-book-avto.ru/remont-i-obsluzhivanie-volkswagen/",
                    "https://autoservice131.ru/remont/volkswagen",
                    "https://belgorod.hipdir.com/remont-avtomobiley-volkswagen/",
                    "https://servicesauto.ru/belgorod/volkswagen/",
                    "http://belgorod.avtotochki.ru/catalog/avtoservis-volkswagen/pt1c351964077248vm95/",
                    "https://driff.ru/service/sto/belgorod/volkswagen/"
                ]
            ],
            "ремонт фольксваген белгород" => [
                "sites" => [
                    "https://belgorod.zoon.ru/autoservice/type/volkswagen/",
                    "https://belgorod.jsprav.ru/avtoservisy-volkswagen/",
                    "https://bel.vse-avtoservisy.ru/volkswagen/",
                    "https://2gis.ru/belgorod/search/%d0%90%d0%b2%d1%82%d0%be%d1%81%d0%b5%d1%80%d0%b2%d0%b8%d1%81%20vag%20(%d0%b2%d0%b0%d0%b3)%20%d0%b2%20%d0%91%d0%b5%d0%bb%d0%b3%d0%be%d1%80%d0%be%d0%b4%d0%b5",
                    "https://volkswagen.centr.services/belgorod",
                    "https://uslugi.yandex.ru/4-belgorod/category?text=%d1%80%d0%b5%d0%bc%d0%be%d0%bd%d1%82+%d1%84%d0%be%d0%bb%d1%8c%d0%ba%d1%81%d0%b2%d0%b0%d0%b3%d0%b5%d0%bd",
                    "https://vw-triumf.ru/owners/service/",
                    "https://carpis.ru/autoservice/belgorod/volkswagen/",
                    "https://belgorod.big-book-avto.ru/remont-i-obsluzhivanie-volkswagen/",
                    "https://www.servicebox.ru/belgorod/volkswagen/avtoservisy/",
                    "https://razborka.org/avtoservis/belgorod/remont/volkswagen",
                    "http://belgorod.avtotochki.ru/catalog/avtoservis-volkswagen/pt1c351964077248vm95/",
                    "https://vse-sto.ru/belgorod/sto/volkswagen/",
                    "https://bigauto.info/belgorod/volkswagen/",
                    "https://www.avito.ru/belgorod?q=%d1%80%d0%b5%d0%bc%d0%be%d0%bd%d1%82+vag",
                    "https://driff.ru/service/sto/belgorod/volkswagen/",
                    "https://servicesauto.ru/belgorod/volkswagen/",
                    "https://www.autodrive.ru/belgorod/autoservices/volkswagen/",
                    "https://top-sto.ru/belgorod/garages/volkswagen",
                    "https://autoservisivse.ru/belgorod/volkswagen/"
                ]
            ],
            "сервис фольксваген" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/service/official-service.html",
                    "https://vw-triumf.ru/owners/service/",
                    "https://belgorod.zoon.ru/autoservice/type/volkswagen/",
                    "https://2gis.ru/belgorod/search/volkswagen%20%d1%81%d0%b5%d1%80%d0%b2%d0%b8%d1%81",
                    "https://bel.vse-avtoservisy.ru/volkswagen/",
                    "https://belgorod.jsprav.ru/avtoservisy-volkswagen/",
                    "https://volkswagen.centr.services/belgorod",
                    "https://belgorod.spravker.ru/avtoservisy-volkswagen/",
                    "https://www.servicebox.ru/belgorod/volkswagen/",
                    "http://commercial.volkswagen-belgorod.ru/owners/service/",
                    "https://belgorod.big-book-avto.ru/remont-i-obsluzhivanie-volkswagen/",
                    "https://carpis.ru/autoservice/belgorod/volkswagen/",
                    "http://belgorod.avtotochki.ru/catalog/avtoservis-volkswagen/pt1c351964077248vm95/",
                    "https://belgorod.hipdir.com/avtoservis-volkswagen/",
                    "https://belgorod.fitauto.ru/services/remont-evropeiskih-avto/volkswagen/",
                    "https://kulibin-auto-service.ru/autos/volkswagen/",
                    "https://servicesauto.ru/belgorod/to/volkswagen/",
                    "https://belgorod.riaauto.ru/service/volkswagen",
                    "https://vw-norden.ru/owners/",
                    "https://vw-oskol.ru/owners/"
                ]
            ],
            "сервис фольксваген белгород" => [
                "sites" => [
                    "https://belgorod.zoon.ru/autoservice/type/volkswagen/",
                    "https://vw-triumf.ru/owners/service/",
                    "https://2gis.ru/belgorod/search/volkswagen%20%d1%81%d0%b5%d1%80%d0%b2%d0%b8%d1%81",
                    "https://belgorod.jsprav.ru/avtoservisy-volkswagen/",
                    "https://volkswagen.centr.services/belgorod",
                    "https://carpis.ru/autoservice/belgorod/volkswagen/",
                    "https://bel.vse-avtoservisy.ru/volkswagen/",
                    "https://belgorod.big-book-avto.ru/remont-i-obsluzhivanie-volkswagen/",
                    "https://www.servicebox.ru/belgorod/volkswagen/avtoservisy/",
                    "https://belgorod.spravker.ru/avtoservisy-volkswagen/",
                    "http://commercial.volkswagen-belgorod.ru/owners/service/",
                    "https://vse-sto.ru/belgorod/sto/volkswagen/",
                    "http://belgorod.avtotochki.ru/catalog/avtoservis-volkswagen/pt1c351964077248vm95/",
                    "https://driff.ru/service/sto/belgorod/volkswagen/",
                    "https://servicesauto.ru/belgorod/volkswagen/",
                    "https://belgorod.hipdir.com/avtoservis-volkswagen/",
                    "https://service-area.ru/catalog/belgorod/volkswagen/",
                    "https://belgorod.spravka.ru/avto/avtoservisy-i-avtotexcentry/avtoservisy-volkswagen",
                    "https://razborka.org/avtoservis/belgorod/remont/volkswagen",
                    "https://autoservisivse.ru/belgorod/volkswagen/"
                ]
            ],
            "сервисное обслуживание фольксваген" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/service.html",
                    "https://vw-triumf.ru/owners/service/",
                    "https://2gis.ru/belgorod/search/volkswagen%20%d1%81%d0%b5%d1%80%d0%b2%d0%b8%d1%81",
                    "https://belgorod.zoon.ru/autoservice/type/volkswagen/",
                    "https://bel.vse-avtoservisy.ru/volkswagen/",
                    "https://belgorod.jsprav.ru/avtoservisy-volkswagen/",
                    "http://commercial.volkswagen-belgorod.ru/owners/service/",
                    "https://volkswagen.centr.services/belgorod",
                    "https://www.servicebox.ru/belgorod/volkswagen/",
                    "https://belgorod.spravker.ru/avtoservisy-volkswagen/",
                    "https://carpis.ru/autoservice/belgorod/volkswagen/",
                    "https://autoservisivse.ru/belgorod/tehnicheskoe-obsluzhivanie/volkswagen/",
                    "https://belgorod.fitauto.ru/services/remont-evropeiskih-avto/volkswagen/",
                    "http://belgorod.avtotochki.ru/catalog/avtoservis-volkswagen/pt1c351964077248vm95/",
                    "https://belgorod.autospot.ru/autoservice/to/volkswagen/",
                    "https://belgorod.big-book-avto.ru/remont-i-obsluzhivanie-volkswagen/",
                    "https://belgorod.hipdir.com/avtoservis-volkswagen/",
                    "https://servicesauto.ru/belgorod/to/volkswagen/",
                    "https://kulibin-auto-service.ru/services-menu/remont-avtomobiley-volkswagen-v-belgorode-folksvagen-servis/",
                    "https://vw-avroraavto.ru/owners/service/service/"
                ]
            ],
            "фольксваген альтрек" => [
                "sites" => [
                    "https://auto.ru/cars/volkswagen/passat-alltrack/all/",
                    "https://www.volkswagen.ru/ru/models/passat-alltrack.html",
                    "https://www.drive2.ru/cars/volkswagen/passat_alltrack/m2347/",
                    "https://vw-triumf.ru/models/passat_alltrack/",
                    "https://www.drive.ru/test-drive/volkswagen/4f61f07709b6021a3f000053.html",
                    "https://www.drom.ru/catalog/volkswagen/passat/119045/",
                    "https://5koleso.ru/tests/volkswagen-passat-alltrack-velikij-prostoj/",
                    "https://mobile-review.com/all/reviews/auto/test-volkswagen-passat-alltrack-interesnyj-universal/",
                    "https://www.avito.ru/all/avtomobili?q=volkswagen+passat+alltrack",
                    "https://www.youtube.com/watch?v=kzki9bq6_xy",
                    "https://motor.ru/testdrives/alltracklong2.htm",
                    "https://www.zr.ru/content/articles/904068-volkswagen-passat-alltrack-chu/",
                    "https://www.auto-dd.ru/vw-passat-alltrack/",
                    "https://carsdo.ru/volkswagen/passat-alltrack/",
                    "https://otoba.ru/auto/vw/passat-b8-alltrack.html",
                    "https://autoreview.ru/news/volkswagen-passat-alltrack-vernulsya-v-rossiyu-ob-yavlena-cena",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/passat-alltrack",
                    "https://belgorod.carso.ru/volkswagen/passat-alltrack",
                    "https://belgorod.abc-auto.ru/volkswagen/alltrack/",
                    "https://auto.ironhorse.ru/category/europe/vw-volkswagen/passat/passat-alltrack?comments=1"
                ]
            ],
            "фольксваген альтрек цена" => [
                "sites" => [
                    "https://auto.ru/cars/volkswagen/passat-alltrack/all/",
                    "https://www.volkswagen.ru/ru/models/passat-alltrack.html",
                    "https://vw-triumf.ru/models/passat_alltrack/",
                    "https://www.avito.ru/all/avtomobili?q=volkswagen+passat+alltrack",
                    "https://carsdo.ru/volkswagen/passat-alltrack/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/passat-alltrack",
                    "https://belgorod.carso.ru/volkswagen/passat-alltrack",
                    "https://autoreview.ru/news/volkswagen-passat-alltrack-vernulsya-v-rossiyu-ob-yavlena-cena",
                    "https://belgorod.abc-auto.ru/volkswagen/alltrack/",
                    "https://belgorod.avanta-avto-credit.ru/cars/volkswagen/passat-alltrack/",
                    "https://vw-avtoruss.ru/models/passat_alltrack/",
                    "https://belgorod.riaauto.ru/volkswagen/passat-alltrack",
                    "https://www.drive.ru/brands/volkswagen/models/2019/passat_alltrack",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/passat-alltrack/",
                    "https://www.auto-dd.ru/vw-passat-alltrack/",
                    "https://www.mobile.de/ru/%d0%b0%d0%b2%d1%82%d0%be%d0%bc%d0%be%d0%b1%d0%b8%d0%bb%d1%8c/volkswagen-passat-alltrack/vhc:car,ms1:25200_62_",
                    "https://www.autode.net/volkswagen/passat_alltrack",
                    "https://carsdb.ru/volkswagen/passat-alltrack/",
                    "https://vw-rolf.ru/models/passat_alltrack/",
                    "https://www.bips.ru/volkswagen/passat-alltrack"
                ]
            ],
            "фольксваген белгород" => [
                "sites" => [
                    "https://vw-triumf.ru/",
                    "https://auto.ru/belgorod/cars/volkswagen/all/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen-asgbagicautgtg24msg",
                    "https://2gis.ru/belgorod/search/volkswagen",
                    "https://belgorod.drom.ru/volkswagen/",
                    "https://vk.com/triumfvw",
                    "https://yandex.ru/maps/org/ofitsialny_diler_volkswagen_avtotsentr_triumf/1340090677/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen",
                    "https://belgorod.autospot.ru/brands/volkswagen/",
                    "https://belgorod.carso.ru/volkswagen",
                    "https://belgorod.zoon.ru/autoservice/type/avtosalon-volkswagen/",
                    "https://belgorod.110km.ru/vybor/volkswagen/kupit-s-probegom-poderzhannie-belgorod/",
                    "https://belgorod.mbib.ru/volkswagen",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/",
                    "https://belgorod.big-book-avto.ru/avtosalon-volkswagen/",
                    "https://belgorod.keyauto-probeg.ru/used/volkswagen/",
                    "https://belgorod.jsprav.ru/avtoservisy-volkswagen/",
                    "http://belgorod.avtotochki.ru/catalog/avtosalon-volkswagen/pt7c351964077248vm95/",
                    "https://avto-dilery.ru/folksvagen-belgorod/",
                    "https://belgorod.riaauto.ru/volkswagen"
                ]
            ],
            "фольксваген белгород официальный" => [
                "sites" => [
                    "https://vw-triumf.ru/",
                    "https://vk.com/triumfvw",
                    "https://yandex.ru/maps/org/ofitsialny_diler_volkswagen_avtotsentr_triumf/1340090677/",
                    "https://auto.drom.ru/vw-triumf/",
                    "https://2gis.ru/belgorod/firm/70000001018724741",
                    "https://belgorod.autovsalone.ru/cars/sellers/triumf",
                    "https://belgorod.zoon.ru/autoservice/ofitsialnyj_diler_volkswagen_avtotsentr_triumf_na_magistralnoj_ulitse_12/",
                    "https://auto.ru/belgorod/dilery/cars/volkswagen/new/",
                    "https://belgorod.big-book-avto.ru/avtosalon-volkswagen/",
                    "https://belgorod.110km.ru/dilery-salony/actriumphvw.html",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen-asgbagicautgtg24msg",
                    "https://dilert.ru/volkswagen/volkswagen-belgorod/avtocentr_triumf_volkswagen_belgorod/",
                    "https://www.yell.ru/belgorod/com/volkswagen-triumf-belgorod_11975126/",
                    "https://bezrulya.ru/dealers/list/volkswagen/6364/",
                    "http://belgorod.avtotochki.ru/catalog/avtosalon-volkswagen/pt7c351964077248vm95/",
                    "https://www.volkswagen.ru/ru/models.html",
                    "https://avto-dilery.ru/folksvagen-belgorod/",
                    "https://autosalon-s.ru/avtosalony/belgorod/avtolyux-volkswagen-belgorod",
                    "https://vw-oskol.ru/",
                    "http://carscan24.ru/dilers/volkswagen/belgorod/avtocentr-triumf-volkswagen-belgorod/"
                ]
            ],
            "фольксваген белгород официальный дилер" => [
                "sites" => [
                    "https://vw-triumf.ru/",
                    "https://vk.com/triumfvw",
                    "https://yandex.ru/maps/org/ofitsialny_diler_volkswagen_avtotsentr_triumf/1340090677/",
                    "https://belgorod.autovsalone.ru/cars/sellers/triumf",
                    "https://auto.drom.ru/vw-triumf/",
                    "https://2gis.ru/belgorod/firm/70000001018724741",
                    "https://belgorod.zoon.ru/autoservice/ofitsialnyj_diler_volkswagen_avtotsentr_triumf_na_magistralnoj_ulitse_12/price/",
                    "https://auto.ru/diler-oficialniy/cars/all/avtocentr_triumf_volkswagen_belgorod/",
                    "https://belgorod.110km.ru/dilery-salony/actriumphvw.html",
                    "https://www.yell.ru/belgorod/com/volkswagen-triumf-belgorod_11975126/reviews/",
                    "https://belgorod.big-book-avto.ru/avtosalon-volkswagen/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen-asgbagicautgtg24msg",
                    "https://dilert.ru/volkswagen/volkswagen-belgorod/avtocentr_triumf_volkswagen_belgorod/",
                    "https://bezrulya.ru/dealers/list/volkswagen/6364/",
                    "http://belgorod.avtotochki.ru/catalog/avtosalon-volkswagen/pt7c351964077248vm95/",
                    "https://carsdb.ru/volkswagen/d/belgorod/",
                    "https://autoleak.ru/dealers/volkswagen/belgorod/avtolyuks-ul-serafimovicha-d-65/",
                    "https://autosalon-s.ru/avtosalony/belgorod/avtolyux-volkswagen-belgorod",
                    "https://avto-dilery.ru/folksvagen-belgorod/",
                    "https://belgorod.flamp.ru/firm/triumf_avtocentr_oficialnyjj_diler_volkswagen-70000001018724741"
                ]
            ],
            "фольксваген белгород цены" => [
                "sites" => [
                    "https://vw-triumf.ru/",
                    "https://auto.ru/belgorod/cars/volkswagen/all/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen-asgbagicautgtg24msg",
                    "https://belgorod.drom.ru/volkswagen/",
                    "http://commercial.volkswagen-belgorod.ru/models/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen",
                    "https://belgorod.autospot.ru/brands/volkswagen/",
                    "https://belgorod.carso.ru/volkswagen",
                    "https://belgorod.110km.ru/vybor/volkswagen/kupit-novie-belgorod/",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/",
                    "https://mbib.ru/obl-belgorodskaya/volkswagen",
                    "https://belgorod.cardana.ru/auto/models/volkswagen.html",
                    "https://www.volkswagen.ru/ru/models.html",
                    "https://avto-dilery.ru/folksvagen-belgorod/",
                    "https://carsdo.ru/volkswagen/polo-sedan/belgorod/",
                    "https://belgorod.keyauto-probeg.ru/used/volkswagen/",
                    "https://belgorod.zoon.ru/autoservice/ofitsialnyj_diler_volkswagen_avtotsentr_triumf_na_magistralnoj_ulitse_12/price/",
                    "https://belgorod.newautosalon.ru/volkswagen-passat/",
                    "https://vk.com/triumfvw",
                    "https://dilert.ru/volkswagen/volkswagen-belgorod/avtocentr_triumf_volkswagen_belgorod/"
                ]
            ],
            "фольксваген гольф" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/new-golf.html",
                    "https://auto.ru/belgorod/cars/volkswagen/golf/all/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/golf-asgbagicaktgtg24msjitg3ipig",
                    "https://ru.wikipedia.org/wiki/volkswagen_golf",
                    "https://belgorod.drom.ru/volkswagen/golf/",
                    "https://vw-triumf.ru/models/golf-new/",
                    "https://www.drive2.ru/cars/volkswagen/golf/m1470/",
                    "https://translate.yandex.ru/translate?lang=en-ru&url=https%3a%2f%2fen.wikipedia.org%2fwiki%2fvolkswagen_golf&view=c",
                    "https://www.drive.ru/test-drive/volkswagen/5df9fdb3ec05c4802000000e.html",
                    "https://www.auto-dd.ru/volkswagen-golf/",
                    "https://all-auto.org/5961-volkswagen-golf.html",
                    "https://wroom.ru/cars/volkswagen/golf/history",
                    "https://www.zr.ru/cars/volkswagen/-/volkswagen-golf/",
                    "https://auto.mail.ru/catalog/volkswagen/golf/",
                    "https://quto.ru/volkswagen/golf",
                    "https://carsdo.ru/volkswagen/golf/",
                    "https://naavtotrasse.ru/volkswagen/volkswagen-golf-2022.html",
                    "https://1gai.ru/publ/522552-volkswagen-golf-vse-8-pokoleniy-legendy.html",
                    "https://belgorod.110km.ru/prodazha/volkswagen/golf/",
                    "https://avtomarket.ru/catalog/volkswagen/golf/"
                ]
            ],
            "фольксваген гольф новый цена" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/new-golf.html",
                    "https://carsdo.ru/volkswagen/golf/",
                    "https://vw-triumf.ru/models/golf-new/",
                    "https://auto.ru/cars/volkswagen/golf/new/",
                    "https://belgorod.carso.ru/volkswagen/golf",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/golf",
                    "https://belgorod.abc-auto.ru/volkswagen/golf-2021/",
                    "https://belgorod.newautosalon.ru/volkswagen-golf/",
                    "https://belgorod.riaauto.ru/volkswagen/golf",
                    "https://carsdb.ru/volkswagen/golf/",
                    "https://naavtotrasse.ru/volkswagen/volkswagen-golf-2022.html",
                    "https://auto.drom.ru/volkswagen/golf/new/",
                    "https://www.major-vw.ru/models/golf-new/",
                    "https://avilon.ru/brands/volkswagen/golf/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/golf/",
                    "https://avanta-avto-credit.ru/cars/volkswagen/golf/",
                    "https://autoreview.ru/news/novyy-volkswagen-golf-vse-taki-vyhodit-na-rossiyskiy-rynok-ceny",
                    "https://dzen.ru/media/automaniac/folksvagen-oficialno-obiavil-ceny-na-novyi-vw-golf-8-dlia-rossii-i-on-dostupen-dlia-zakaza-608b9f9f892d3c009a1312af",
                    "https://aksa-auto.ru/catalog/volkswagen/golf-new/gti",
                    "https://cenyavto.com/volkswagen-golf-2022/"
                ]
            ],
            "фольксваген гольф цена" => [
                "sites" => [
                    "https://auto.ru/belgorod/cars/volkswagen/golf/all/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/golf-asgbagicaktgtg24msjitg3ipig",
                    "https://www.volkswagen.ru/ru/models/new-golf.html",
                    "https://belgorod.drom.ru/volkswagen/golf/",
                    "https://vw-triumf.ru/models/golf-new/",
                    "https://carsdo.ru/volkswagen/golf/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/golf",
                    "https://belgorod.110km.ru/prodazha/volkswagen/golf/",
                    "https://belgorod.carso.ru/volkswagen/golf",
                    "https://mbib.ru/obl-belgorodskaya/volkswagen/golf",
                    "https://belgorod.riaauto.ru/volkswagen/golf",
                    "https://belgorod.newautosalon.ru/volkswagen-golf/",
                    "https://quto.ru/volkswagen/golf",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/golf/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/golf/",
                    "https://carsdb.ru/volkswagen/golf/",
                    "https://avanta-avto-credit.ru/cars/volkswagen/golf/",
                    "https://belgorod.keyauto-probeg.ru/used/volkswagen/golf/",
                    "https://belgorod.autodmir.ru/offers/volkswagen/golf/",
                    "https://naavtotrasse.ru/volkswagen/volkswagen-golf-2022.html"
                ]
            ],
            "фольксваген поло" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/polo-new.html",
                    "https://auto.ru/belgorod/cars/volkswagen/polo/all/",
                    "https://ru.wikipedia.org/wiki/volkswagen_polo",
                    "https://vw-triumf.ru/models/polo-new/",
                    "https://belgorod.drom.ru/volkswagen/polo/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/polo-asgbagicaktgtg24msjitg2irsg",
                    "https://www.drive2.ru/cars/volkswagen/polo_sedan/g2966/",
                    "https://carsdo.ru/volkswagen/polo-sedan/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/polo",
                    "https://www.drive.ru/test-drive/volkswagen/5f16e030ec05c4421c0000db.html",
                    "https://translate.yandex.ru/translate?lang=en-ru&url=https%3a%2f%2fen.wikipedia.org%2fwiki%2fvw_polo&view=c",
                    "https://www.zr.ru/cars/volkswagen/-/volkswagen-polo/",
                    "https://belgorod.autospot.ru/brands/volkswagen/polo/liftback/price/",
                    "https://belgorod.carso.ru/volkswagen/polo",
                    "https://quto.ru/volkswagen/polo",
                    "https://www.kolesa.ru/article/pyat-veshhej-za-kotorye-lyubyat-i-nenavidyat-volkswagen-polo",
                    "https://auto.mail.ru/catalog/volkswagen/polo/",
                    "https://otzovik.com/reviews/avtomobil_volkswagen_polo_sedan_2010/",
                    "https://belgorod.newautosalon.ru/volkswagen-polo/",
                    "https://www.youtube.com/watch?v=-x2spc_refi"
                ]
            ],
            "фольксваген поло белгород" => [
                "sites" => [
                    "https://vw-triumf.ru/models/polo-new/",
                    "https://auto.ru/belgorod/cars/volkswagen/polo/all/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/polo-asgbagicaktgtg24msjitg2irsg",
                    "https://belgorod.drom.ru/volkswagen/polo/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/polo",
                    "https://belgorod.autospot.ru/brands/volkswagen/polo/liftback/price/",
                    "https://carsdo.ru/volkswagen/polo-sedan/belgorod/",
                    "https://belgorod.carso.ru/volkswagen/polo",
                    "https://belgorod.newautosalon.ru/volkswagen-polo/",
                    "https://mbib.ru/obl-belgorodskaya/volkswagen/polo",
                    "https://belgorod.rrt-automarket.ru/new-cars/volkswagen/polo/",
                    "https://belgorod.abc-auto.ru/volkswagen/polo_sedan/",
                    "https://belgorod.riaauto.ru/volkswagen/polo",
                    "https://belgorod.110km.ru/prodazha/volkswagen/polo/poderzhannie/",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/polo_new/",
                    "https://www.drive2.ru/cars/volkswagen/polo_sedan/m1925/?city=34581",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/polo/",
                    "https://belgorod.avanta-avto-credit.ru/cars/volkswagen/polo-2019/",
                    "https://www.volkswagen.ru/ru/models/polo-new.html",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/polo/"
                ]
            ],
            "фольксваген поло белгород официальный дилер" => [
                "sites" => [
                    "https://vw-triumf.ru/models/polo-new/",
                    "https://vw-triumf.ru/",
                    "https://vk.com/triumfvw",
                    "https://yandex.ru/maps/org/ofitsialny_diler_volkswagen_avtotsentr_triumf/1340090677/",
                    "https://auto.drom.ru/vw-triumf/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/polo",
                    "https://carsdo.ru/volkswagen/polo-sedan/belgorod/",
                    "https://belgorod.zoon.ru/autoservice/ofitsialnyj_diler_volkswagen_avtotsentr_triumf_na_magistralnoj_ulitse_12/price/",
                    "https://2gis.ru/belgorod/firm/70000001018724741",
                    "https://auto.ru/belgorod/cars/volkswagen/polo/all/",
                    "https://m.avito.ru/belgorod/avtomobili/novyy/volkswagen/polo-asgbagica0sgfmbmaec2dbizkok2dyitka",
                    "https://belgorod.newautosalon.ru/volkswagen-polo/",
                    "https://belgorod.abc-auto.ru/volkswagen/polo_sedan/",
                    "https://belgorod.carso.ru/volkswagen/polo",
                    "https://autosalon-vw.ru/auto/volkswagen/new-polo_sedan",
                    "https://belgorod.rrt-automarket.ru/new-cars/volkswagen/polo/",
                    "https://bezrulya.ru/dealers/list/volkswagen/6364/",
                    "https://belgorod.110km.ru/dilery-salony/actriumphvw.html",
                    "https://autosalon-s.ru/avtosalony/belgorod/avtolyux-volkswagen-belgorod",
                    "https://vw-oskol.ru/"
                ]
            ],
            "фольксваген поло комплектации и цены" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/polo-new.html",
                    "https://auto.ru/belgorod/cars/volkswagen/polo/all/",
                    "https://carsdo.ru/volkswagen/polo-sedan/",
                    "https://vw-triumf.ru/models/polo-new/",
                    "http://commercial.volkswagen-belgorod.ru/models/polo-new/prices/",
                    "https://www.drom.ru/catalog/volkswagen/polo/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/polo",
                    "https://belgorod.carso.ru/volkswagen/polo",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/polo-asgbagicaktgtg24msjitg2irsg",
                    "https://belgorod.autospot.ru/brands/volkswagen/polo/liftback/price/",
                    "https://belgorod.newautosalon.ru/volkswagen-polo/",
                    "https://belgorod.abc-auto.ru/volkswagen/polo_sedan/",
                    "https://belgorod.riaauto.ru/volkswagen/polo",
                    "https://carsdb.ru/volkswagen/polo-sedan/",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/volkswagen-polo-2022",
                    "https://belgorod.avanta-avto-credit.ru/cars/volkswagen/polo/komplektacii/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/polo-new/",
                    "https://auto.kolesa.ru/all-auto/volkswagen/polo-sedan/configuration",
                    "https://quto.ru/volkswagen/polo",
                    "https://wroom.ru/cars/volkswagen/polo-ru/price"
                ]
            ],
            "фольксваген поло новый цена" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/polo-new.html",
                    "https://vw-triumf.ru/models/polo-new/",
                    "https://auto.ru/belgorod/cars/volkswagen/polo/new/",
                    "https://belgorod.drom.ru/volkswagen/polo/new/",
                    "https://m.avito.ru/belgorod/avtomobili/novyy/volkswagen/polo-asgbagica0sgfmbmaec2dbizkok2dyitka",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/polo",
                    "https://carsdo.ru/volkswagen/polo-sedan/",
                    "http://commercial.volkswagen-belgorod.ru/models/polo-new/prices/",
                    "https://belgorod.carso.ru/volkswagen/polo-new",
                    "https://belgorod.autospot.ru/brands/volkswagen/polo/liftback/price/",
                    "https://belgorod.abc-auto.ru/volkswagen/polo-new-2020/",
                    "https://belgorod.riaauto.ru/volkswagen/polo",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/polo_new/",
                    "https://belgorod.newautosalon.ru/volkswagen-polo/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/polo-new/",
                    "https://carsdb.ru/volkswagen/polo-sedan/",
                    "https://belgorod.rrt-automarket.ru/new-cars/volkswagen/polo/",
                    "https://belgorod.110km.ru/prodazha/volkswagen/polo/novie/",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/volkswagen-polo-2022",
                    "https://wroom.ru/cars/volkswagen/polo-ru/price"
                ]
            ],
            "фольксваген поло цена" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/polo-new.html",
                    "https://auto.ru/belgorod/cars/volkswagen/polo/all/",
                    "https://belgorod.drom.ru/volkswagen/polo/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/polo-asgbagicaktgtg24msjitg2irsg",
                    "https://vw-triumf.ru/models/polo-new/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/polo",
                    "http://commercial.volkswagen-belgorod.ru/models/polo-new/prices/",
                    "https://belgorod.autospot.ru/brands/volkswagen/polo/liftback/price/",
                    "https://carsdo.ru/volkswagen/polo-sedan/",
                    "https://belgorod.carso.ru/volkswagen/polo",
                    "https://belgorod.abc-auto.ru/volkswagen/polo_sedan/",
                    "https://belgorod.newautosalon.ru/volkswagen-polo/",
                    "https://belgorod.riaauto.ru/volkswagen/polo",
                    "https://belgorod.110km.ru/prodazha/volkswagen/polo/novie/",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/polo/",
                    "https://belgorod.mbib.ru/volkswagen/polo/used",
                    "https://wroom.ru/cars/volkswagen/polo-ru/price",
                    "https://belgorod.rrt-automarket.ru/new-cars/volkswagen/polo/",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/polo/",
                    "https://carsdb.ru/volkswagen/polo-sedan/"
                ]
            ],
            "фольксваген поло цена новой машины" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/polo-new.html",
                    "https://vw-triumf.ru/models/polo-new/",
                    "https://auto.ru/belgorod/cars/volkswagen/polo/new/",
                    "https://m.avito.ru/belgorod/avtomobili/novyy/volkswagen/polo-asgbagica0sgfmbmaec2dbizkok2dyitka",
                    "https://carsdo.ru/volkswagen/polo-sedan/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/polo",
                    "https://belgorod.drom.ru/volkswagen/polo/new/",
                    "https://belgorod.autospot.ru/brands/volkswagen/polo/liftback/price/",
                    "http://commercial.volkswagen-belgorod.ru/models/polo-new/prices/",
                    "https://belgorod.carso.ru/volkswagen/polo",
                    "https://belgorod.abc-auto.ru/volkswagen/polo-new-2020/",
                    "https://belgorod.newautosalon.ru/volkswagen-polo/",
                    "https://belgorod.riaauto.ru/volkswagen/polo",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/polo_new/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/polo-new/",
                    "https://carsdb.ru/volkswagen/polo-sedan/",
                    "https://belgorod.rrt-automarket.ru/new-cars/volkswagen/polo/",
                    "https://wroom.ru/cars/volkswagen/polo-ru/price",
                    "https://topruscar.ru/komplektatsii-i-tseny/2022/volkswagen-polo-2022",
                    "https://www.major-vw.ru/models/polo-new/"
                ]
            ],
            "фольксваген таос купить" => [
                "sites" => [
                    "https://www.avito.ru/belgorodskaya_oblast/avtomobili/volkswagen/taos-asgbagicaktgtg24msjitg3orls",
                    "https://auto.ru/cars/volkswagen/taos/all/",
                    "https://www.volkswagen.ru/ru/models/taos.html",
                    "https://vw-triumf.ru/models/taos/",
                    "https://auto.drom.ru/volkswagen/taos/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/taos",
                    "https://belgorod.autospot.ru/brands/volkswagen/taos/suv/price/",
                    "https://carsdo.ru/volkswagen/taos/",
                    "https://belgorod.carso.ru/volkswagen/taos",
                    "https://belgorod.riaauto.ru/volkswagen/taos",
                    "https://www.major-vw.ru/models/taos/",
                    "https://belgorod.abc-auto.ru/volkswagen/taos/",
                    "https://vw-rolf.ru/models/taos/prices/",
                    "https://vw-avtoruss.ru/models/taos/",
                    "https://belgorod.newautosalon.ru/volkswagen-taos/",
                    "https://mbib.ru/volkswagen/taos/used",
                    "https://avtomir.ru/new-cars/volkswagen/taos/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/taos/",
                    "https://volkswagen-taos.ru/price.html",
                    "https://avilon.ru/brands/volkswagen/taos/"
                ]
            ],
            "фольксваген терамонт" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/teramont-new.html",
                    "https://auto.ru/cars/volkswagen/teramont/all/",
                    "https://volkswagen.drom.ru/teramont/",
                    "https://vw-triumf.ru/models/teramont_new/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/teramont-asgbagicaktgtg24msjitg3yssg",
                    "https://www.drive2.ru/cars/volkswagen/teramont/m3112/",
                    "https://www.drive.ru/test-drive/volkswagen/58f09e27ec05c40c3f0000d5.html",
                    "https://ru.wikipedia.org/wiki/volkswagen_teramont",
                    "https://www.youtube.com/watch?v=wg7ngip-j54",
                    "https://3dnews.ru/978379/obzor-volkswagen-teramont-semero-po-lavkam",
                    "https://www.auto-dd.ru/volkswagen-teramont-2022/",
                    "https://belgorod.carso.ru/volkswagen/teramont",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/teramont",
                    "https://avtoexperts.ru/article/volkswagen-teramont-bol-shoj-amerikanskij-krossover/",
                    "https://autoreview.ru/articles/pervaya-vstrecha/teramonster",
                    "https://fastmb.ru/testdrive/4909-obzor-volkswagen-teramont-2021-tehnicheskie-harakteristiki-i-komplektaciya.html",
                    "https://ru.motor1.com/reviews/377017/volkswagen-teramont-vybiraem-mezhdu-dvumya-v6-i-turbochetverkoj/",
                    "https://autospot.ru/brands/volkswagen/teramont_i/suv/price/",
                    "https://clubteramont.ru/",
                    "https://belgorod.abc-auto.ru/volkswagen/terramont/"
                ]
            ],
            "фольксваген тигуан" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/tiguan-new.html",
                    "https://auto.ru/belgorod/cars/volkswagen/tiguan/all/",
                    "https://belgorod.drom.ru/volkswagen/tiguan/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/tiguan-asgbagicaktgtg24msjitg2ssig",
                    "https://ru.wikipedia.org/wiki/volkswagen_tiguan",
                    "https://vw-triumf.ru/models/tiguan_fl/",
                    "https://www.drive2.ru/cars/volkswagen/tiguan/m1487/",
                    "https://translate.yandex.ru/translate?lang=en-ru&url=https%3a%2f%2fen.wikipedia.org%2fwiki%2ftiguan&view=c",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/tiguan",
                    "https://carsdo.ru/volkswagen/tiguan/",
                    "https://www.drive.ru/brands/volkswagen/models/2020/tiguan",
                    "https://www.youtube.com/watch?v=x8qsnrwtquq",
                    "https://belgorod.autospot.ru/brands/volkswagen/tiguan_2020/suv/price/",
                    "https://www.auto-dd.ru/volkswagen-tiguan-2021/",
                    "https://mobile-review.com/all/reviews/auto/test-volkswagen-tiguan-2021-komfortnyj-semejnyj-krossover/",
                    "https://all-auto.org/20964-volkswagen-tiguan.html",
                    "https://belgorod.carso.ru/volkswagen/tiguan",
                    "https://www.zr.ru/cars/volkswagen/-/volkswagen-tiguan/reviews/",
                    "https://otzovik.com/reviews/avtomobil_volkswagen_tiguan_vnedorozhnik_2010/",
                    "https://forum.tiguans.ru/"
                ]
            ],
            "фольксваген тигуан в наличии у официальных дилеров" => [
                "sites" => [
                    "https://cars.volkswagen.ru/tiguan/",
                    "https://vw-triumf.ru/models/tiguan_fl/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/tiguan",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/tiguan/nalichie/",
                    "https://belgorod.carso.ru/volkswagen/tiguan",
                    "https://carsdo.ru/volkswagen/tiguan/belgorod/",
                    "http://commercial.volkswagen-belgorod.ru/models/tiguan_fl/",
                    "https://belgorod.abc-auto.ru/volkswagen/tiguan/",
                    "https://auto.ru/belgorod/cars/volkswagen/tiguan/all/",
                    "https://belgorod.newautosalon.ru/volkswagen-tiguan/",
                    "http://belgorod.lst-group.ru/new/volkswagen/tiguan/",
                    "https://autoprestus.ru/purchase/available-cars/tiguan_fl/",
                    "https://belgorod.autospot.ru/brands/volkswagen/tiguan_2020/suv/price/",
                    "https://www.major-vw.ru/models/tiguan_fl/",
                    "https://belgorod.riaauto.ru/volkswagen/tiguan",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/tiguan-2021/",
                    "https://avtomir.ru/new-cars/volkswagen/tiguan/",
                    "https://belgorod.drom.ru/volkswagen/tiguan/new/",
                    "https://vw-avtoruss.ru/models/tiguan_fl/",
                    "https://belgorod.cardana.ru/auto/volkswagen/tiguan.html"
                ]
            ],
            "фольксваген тигуан комплектации и цены официальный дилер" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/tiguan-new.html",
                    "https://vw-triumf.ru/models/tiguan_fl/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/tiguan",
                    "http://commercial.volkswagen-belgorod.ru/models/tiguan_fl/",
                    "https://carsdo.ru/volkswagen/tiguan/",
                    "https://belgorod.abc-auto.ru/volkswagen/tiguan/",
                    "https://auto.ru/belgorod/cars/volkswagen/tiguan/new/",
                    "https://www.major-vw.ru/models/tiguan_fl/",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/tiguan/nalichie/",
                    "https://belgorod.carso.ru/volkswagen/tiguan",
                    "https://belgorod.newautosalon.ru/volkswagen-tiguan/",
                    "https://belgorod.riaauto.ru/volkswagen/tiguan",
                    "http://belgorod.lst-group.ru/new/volkswagen/tiguan/",
                    "https://belgorod.autospot.ru/brands/volkswagen/tiguan_2020/suv/price/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/tiguan-2021/",
                    "https://vw-avtoruss.ru/models/tiguan_fl/",
                    "https://belgorod.avanta-avto-credit.ru/cars/volkswagen/tiguan-2016/",
                    "https://vw-rolf.ru/models/tiguan_fl/prices/",
                    "https://autosalon-vw.ru/auto/volkswagen/tiguan-new_cuv",
                    "https://belgorod.cardana.ru/auto/volkswagen/tiguan.html"
                ]
            ],
            "фольксваген тигуан купить новый цена" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/tiguan-new.html",
                    "https://auto.ru/belgorod/cars/volkswagen/tiguan/new/",
                    "https://vw-triumf.ru/models/tiguan_fl/",
                    "https://belgorod.drom.ru/volkswagen/tiguan/new/",
                    "https://carsdo.ru/volkswagen/tiguan/",
                    "https://belgorod.carso.ru/volkswagen/tiguan",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/tiguan?engine_fuel=%d0%b4%d0%b8%d0%b7%d0%b5%d0%bb%d1%8c",
                    "https://belgorod.autospot.ru/brands/volkswagen/tiguan_2020/suv/price/",
                    "https://belgorod.newautosalon.ru/volkswagen-tiguan/",
                    "https://belgorod.abc-auto.ru/volkswagen/tiguan-2021/",
                    "http://commercial.volkswagen-belgorod.ru/models/tiguan_fl/",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/tiguan/nalichie/",
                    "https://belgorod.riaauto.ru/volkswagen/tiguan",
                    "https://m.avito.ru/all/avtomobili/novyy/volkswagen/tiguan-asgbagica0sgfmbmaec2dbizkok2dzkyka",
                    "http://belgorod.lst-group.ru/new/volkswagen/tiguan/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/tiguan-2021/",
                    "https://belgorod.110km.ru/prodazha/volkswagen/tiguan/novie/",
                    "https://belgorod.avanta-avto-credit.ru/cars/volkswagen/tiguan-2016/",
                    "https://belgorod.cardana.ru/auto/volkswagen/tiguan.html",
                    "https://autosalon-vw.ru/auto/volkswagen/tiguan-new_cuv"
                ]
            ],
            "фольксваген тигуан новый цена" => [
                "sites" => [
                    "https://www.volkswagen.ru/ru/models/tiguan-new.html",
                    "https://auto.ru/belgorod/cars/volkswagen/tiguan/new/",
                    "https://carsdo.ru/volkswagen/tiguan/",
                    "https://vw-triumf.ru/models/tiguan_fl/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/tiguan",
                    "https://belgorod.drom.ru/volkswagen/tiguan/new/",
                    "https://belgorod.carso.ru/volkswagen/tiguan-new",
                    "https://belgorod.autospot.ru/brands/volkswagen/tiguan_2020/suv/price/",
                    "https://belgorod.newautosalon.ru/volkswagen-tiguan/",
                    "https://belgorod.abc-auto.ru/volkswagen/tiguan-2021/",
                    "https://belgorod.riaauto.ru/volkswagen/tiguan",
                    "http://commercial.volkswagen-belgorod.ru/models/tiguan_fl/",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/tiguan/nalichie/",
                    "https://m.avito.ru/all/avtomobili/novyy/volkswagen/tiguan-asgbagica0sgfmbmaec2dbizkok2dzkyka",
                    "http://belgorod.lst-group.ru/new/volkswagen/tiguan/",
                    "https://www.drive.ru/brands/volkswagen/models/2020/tiguan",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/tiguan-2021/",
                    "https://carsdb.ru/volkswagen/tiguan/",
                    "https://wroom.ru/cars/volkswagen/tiguan/price",
                    "https://belgorod.cardana.ru/auto/volkswagen/tiguan.html"
                ]
            ],
            "фольксваген тигуан цена" => [
                "sites" => [
                    "https://auto.ru/belgorod/cars/volkswagen/tiguan/all/",
                    "https://www.volkswagen.ru/ru/models/tiguan-new.html",
                    "https://belgorod.drom.ru/volkswagen/tiguan/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/tiguan-asgbagicaktgtg24msjitg2ssig",
                    "https://vw-triumf.ru/models/tiguan_fl/",
                    "https://carsdo.ru/volkswagen/tiguan/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/tiguan",
                    "https://belgorod.carso.ru/volkswagen/tiguan",
                    "https://belgorod.autospot.ru/brands/volkswagen/tiguan_2020/suv/price/",
                    "http://commercial.volkswagen-belgorod.ru/models/tiguan_fl/",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/tiguan/nalichie/",
                    "https://belgorod.abc-auto.ru/volkswagen/tiguan/",
                    "https://belgorod.newautosalon.ru/volkswagen-tiguan/",
                    "https://belgorod.riaauto.ru/volkswagen/tiguan",
                    "https://belgorod.110km.ru/prodazha/volkswagen/tiguan/poderzhannie/",
                    "http://belgorod.lst-group.ru/new/volkswagen/tiguan/",
                    "https://belgorod.mbib.ru/volkswagen/tiguan",
                    "https://wroom.ru/cars/volkswagen/tiguan/price",
                    "https://www.drive.ru/brands/volkswagen/models/2020/tiguan",
                    "https://belgorod.cardana.ru/auto/volkswagen/tiguan.html"
                ]
            ],
            "фольксваген тигуан цена белгород" => [
                "sites" => [
                    "https://auto.ru/belgorod/cars/volkswagen/tiguan/all/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/tiguan-asgbagicaktgtg24msjitg2ssig",
                    "https://vw-triumf.ru/models/tiguan_fl/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/tiguan",
                    "https://belgorod.drom.ru/volkswagen/tiguan/",
                    "http://commercial.volkswagen-belgorod.ru/models/tiguan_fl/",
                    "https://belgorod.carso.ru/volkswagen/tiguan",
                    "https://carsdo.ru/volkswagen/tiguan/belgorod/",
                    "https://belgorod.newautosalon.ru/volkswagen-tiguan/",
                    "https://belgorod.autospot.ru/brands/volkswagen/tiguan_2020/suv/price/",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/tiguan/nalichie/",
                    "https://belgorod.110km.ru/prodazha/volkswagen/tiguan/poderzhannie/",
                    "https://mbib.ru/obl-belgorodskaya/volkswagen/tiguan",
                    "https://belgorod.cardana.ru/auto/volkswagen/tiguan.html",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/tiguan/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/tiguan/",
                    "https://belgorod.riaauto.ru/volkswagen/tiguan-2019",
                    "https://autosalon-vw.ru/auto/volkswagen/tiguan-new_cuv",
                    "https://car.ru/belgorod/volkswagen/tiguan/",
                    "https://vw-avroraavto.ru/models/tiguan_fl/"
                ]
            ],
            "фольксваген туарег" => [
                "sites" => [
                    "https://auto.ru/belgorod/cars/volkswagen/touareg/all/",
                    "https://www.volkswagen.ru/ru/models/touareg-exclusive.html",
                    "https://belgorod.drom.ru/volkswagen/touareg/",
                    "https://www.avito.ru/belgorodskaya_oblast/avtomobili/volkswagen/touareg-asgbagicaktgtg24msjitg2wsig",
                    "https://ru.wikipedia.org/wiki/volkswagen_touareg",
                    "https://www.drive2.ru/cars/volkswagen/touareg/m1488/",
                    "https://vw-triumf.ru/models/touareg/",
                    "https://translate.yandex.ru/translate?lang=en-ru&url=https%3a%2f%2fen.wikipedia.org%2fwiki%2ftouraeg&view=c",
                    "https://www.drive.ru/test-drive/volkswagen/5b03e046ec05c4cd0c0000ea.html",
                    "https://belgorod.autospot.ru/brands/volkswagen/touareg/suv/price/",
                    "https://carsdo.ru/volkswagen/touareg/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/touareg",
                    "https://www.zr.ru/cars/volkswagen/-/volkswagen-touareg/",
                    "https://www.auto-dd.ru/volkswagen-touareg-3-2020/",
                    "https://quto.ru/volkswagen/touareg",
                    "https://wroom.ru/cars/volkswagen/touareg/history",
                    "https://autoiwc.ru/volkswagen/volkswagen-touareg.html",
                    "https://naavtotrasse.ru/volkswagen/volkswagen-touareg-2022.html",
                    "https://motor.ru/testdrives/newtouareg.htm",
                    "https://www.touareg-club.net/"
                ]
            ],
            "фольксваген туарег белгород" => [
                "sites" => [
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/touareg-asgbagicaktgtg24msjitg2wsig",
                    "https://auto.ru/belgorod/cars/volkswagen/touareg/used/",
                    "https://vw-triumf.ru/models/touareg/",
                    "https://belgorod.drom.ru/volkswagen/touareg/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/touareg",
                    "http://commercial.volkswagen-belgorod.ru/models/touareg/",
                    "https://mbib.ru/obl-belgorodskaya/volkswagen/touareg",
                    "https://belgorod.110km.ru/prodazha/volkswagen/touareg/",
                    "https://belgorod.autospot.ru/brands/volkswagen/touareg/suv/price/",
                    "https://belgorod.abc-auto.ru/volkswagen/touareg/",
                    "https://carsdo.ru/volkswagen/touareg/belgorod/",
                    "https://belgorod.carso.ru/volkswagen/touareg-old",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/touareg/",
                    "https://belgorod.riaauto.ru/volkswagen/touareg",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/touareg/",
                    "https://www.drive2.ru/cars/volkswagen/touareg/m1488/?city=34581&sort=date",
                    "http://belgorod.lst-group.ru/new/volkswagen/touareg/",
                    "https://belgorod.newautosalon.ru/volkswagen-touareg-business/",
                    "https://www.gazeta-a.ru/autosearch/belgorod/volkswagen/touareg/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/touareg/"
                ]
            ],
            "фольксваген туарег цена" => [
                "sites" => [
                    "https://auto.ru/belgorod/cars/volkswagen/touareg/all/",
                    "https://cars.volkswagen.ru/touareg/",
                    "https://www.avito.ru/belgorod/avtomobili/volkswagen/touareg-asgbagicaktgtg24msjitg2wsig",
                    "https://belgorod.drom.ru/volkswagen/touareg/",
                    "https://vw-triumf.ru/models/touareg/",
                    "https://carsdo.ru/volkswagen/touareg/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/touareg",
                    "https://belgorod.autospot.ru/brands/volkswagen/touareg/suv/price/",
                    "https://belgorod.abc-auto.ru/volkswagen/touareg/",
                    "https://belgorod.carso.ru/volkswagen/touareg-old",
                    "https://belgorod.110km.ru/prodazha/volkswagen/touareg/",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/touareg/",
                    "http://commercial.volkswagen-belgorod.ru/models/touareg/",
                    "https://belgorod.mbib.ru/volkswagen/touareg/used",
                    "https://belgorod.riaauto.ru/volkswagen/touareg",
                    "http://belgorod.lst-group.ru/new/volkswagen/touareg/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/touareg/",
                    "https://belgorod.ab-club.ru/catalog/volkswagen/touareg/",
                    "https://carsdb.ru/volkswagen/touareg/",
                    "https://www.gazeta-a.ru/autosearch/belgorod/volkswagen/touareg/"
                ]
            ],
            "фольксваген туарег цена новый" => [
                "sites" => [
                    "https://cars.volkswagen.ru/touareg/",
                    "https://auto.ru/belgorod/cars/volkswagen/touareg/new/",
                    "https://carsdo.ru/volkswagen/touareg/",
                    "https://vw-triumf.ru/models/touareg/",
                    "https://belgorod.drom.ru/volkswagen/touareg/new/",
                    "https://belgorod.autovsalone.ru/cars/volkswagen/touareg",
                    "https://belgorod.autospot.ru/brands/volkswagen/touareg/suv/price/",
                    "https://www.avito.ru/all/avtomobili/novyy/volkswagen/touareg-asgbagica0sgfmbmaec2dbizkok2dbcyka",
                    "https://belgorod.abc-auto.ru/volkswagen/touareg/",
                    "https://belgorod.carso.ru/volkswagen/touareg-old",
                    "https://belgorod.b-kredit.com/catalog/volkswagen/touareg/",
                    "http://commercial.volkswagen-belgorod.ru/models/touareg/",
                    "https://belgorod.riaauto.ru/volkswagen/touareg",
                    "https://vw-oskol.ru/models/touareg/prices/",
                    "https://carsdb.ru/volkswagen/touareg/",
                    "http://belgorod.lst-group.ru/new/volkswagen/touareg/",
                    "https://www.major-vw.ru/models/touareg/",
                    "https://belgorod.incom-auto.ru/auto/volkswagen/touareg/",
                    "https://avilon.ru/brands/volkswagen/touareg/",
                    "https://vw-avtoruss.ru/models/touareg/"
                ]
            ]
        ];

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

        dump($clusters);
        foreach ($clusters as $keyPhrase => $cluster) {
            foreach ($clusters as $anotherKeyPhrase => $anotherCluster) {
                if ($keyPhrase === $anotherKeyPhrase) {
                    continue;
                }

                foreach ($cluster as $key1 => $elems) {
                    foreach ($anotherCluster as $key2 => $anotherElems) {
                        if (count(array_intersect($anotherElems['sites'], $elems['sites'])) >= $minimum) {
                            $clusters[$keyPhrase] = array_merge_recursive($cluster, $anotherCluster);
//                            unset($clusters[$keyPhrase]);
                            unset($clusters[$anotherKeyPhrase]);
                            break 2;
                        }
                    }
                }
            }
        }

        dd($clusters);
    });
});
