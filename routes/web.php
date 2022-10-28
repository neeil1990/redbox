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
    Route::get('/get-cluster-progress/{progress}', 'ClusterController@getProgress')->name('get.cluster.progress');
    Route::get('/destroy-progress/{progress}', 'ClusterController@destroyProgress')->name('destroy.progress');

});

Route::get('/test', function () {
    $str = 'https://xmlstock.com/yandex/xml/?user=9371&amp;key=660fb3c4c831f41ac36637cf3b69031e&amp;query=%D0%BA%D0%BE%D0%BB%D1%8C%D0%BF%D0%BE%D1%81%D0%BA%D0%BE%D0%BF%D1%8B%20%D0%BE%D1%80%D0%B8%D0%BE%D0%BD&amp;groupby=attr=d.mode%3Ddeep.groups-on-page%3D100.docs-in-group%3D1&amp;lr=213&amp;sortby=rlv&amp;page=0';
    dd(html_entity_decode($str));

    $clusters = [
        "20 delta дерматоскоп" => [
            "20 delta дерматоскоп" => [
                [
                    "https://heine.ru.com/product-category/dermatologiya/dermatoskop-delta-20/",
                    "https://medeq.ru/product/dermatoskop-heine-delta-20/11422",
                    "https://shop.heine-med.ru/catalog/dermatoskopy/dermatoskop_delta_20_plus/",
                    "http://www.deal-med.ru/dermatoskop_delta_20.html",
                    "https://almamed.su/category/heine-delta-20-t-tsifrovye-dermatoskopy-germaniya/",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/delta-20-t/delta-20-t-k-26210118/",
                    "https://heine-opto.ru/product/dermatoskop-heine-delta-20t/",
                    "https://td-lab.ru/product/dermatoskop_delta20t/",
                    "https://medmart.pro/products/dermatoskop-heine-delta-20t-s-rukoyatkoj-4usb",
                    "https://vendem.ru/catalog/funktsionalnaya_diagnostika/dermatoskopy/dermatoskop_heine_delta_20/"
                ]
            ],
            "дерматоскоп delta" => [
                [
                    "https://heine.ru.com/product-category/dermatologiya/dermatoskop-delta-20/",
                    "https://medeq.ru/product/dermatoskop-heine-delta-20/11422",
                    "https://shop.heine-med.ru/catalog/dermatoskopy/dermatoskop_delta_20_t/",
                    "http://www.deal-med.ru/dermatoskop_delta_20.html",
                    "https://almamed.su/category/heine-delta-20-t-tsifrovye-dermatoskopy-germaniya/",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/delta-20-t/",
                    "https://www.uni-tec.su/dermatoskopy.html",
                    "https://eurosmed.ru/products/dermatoskop-delta-20-plus",
                    "https://medmart.pro/products/dermatoskop-heine-delta-20t-s-rukoyatkoj-4usb",
                    "https://mttechnica.ru/ufiles/ins/04-dermatoscopes-2018_ru.pdf"
                ]
            ],
            "дерматоскоп delta 20 купить" => [
                [
                    "https://heine.ru.com/product-category/dermatologiya/dermatoskop-delta-20/",
                    "https://shop.heine-med.ru/catalog/dermatoskopy/dermatoskop_delta_20_plus/",
                    "https://medeq.ru/product/dermatoskop-heine-delta-20/11422",
                    "https://almamed.su/category/heine-delta-20-t-tsifrovye-dermatoskopy-germaniya/",
                    "http://www.deal-med.ru/dermatoskop_delta_20.html",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/delta-20-t/delta-20-t-k-26210118/",
                    "https://www.uni-tec.su/dermatoskopy.html",
                    "https://eurosmed.ru/products/dermatoskop-delta-20-plus",
                    "https://medmart.pro/products/dermatoskop-heine-delta-20t-s-rukoyatkoj-4usb",
                    "https://heine-opto.ru/product/dermatoskop-heine-delta-20t/"
                ]
            ],
            "дерматоскоп delta 20 цена" => [
                [
                    "https://heine.ru.com/product-category/dermatologiya/dermatoskop-delta-20/",
                    "https://medeq.ru/product/dermatoskop-heine-delta-20/11422",
                    "http://www.deal-med.ru/dermatoskop_delta_20.html",
                    "https://shop.heine-med.ru/catalog/dermatoskopy/dermatoskop_delta_20_t/",
                    "https://almamed.su/category/heine-delta-20-t-tsifrovye-dermatoskopy-germaniya/",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/delta-20-t/delta-20-t-k-26210118/",
                    "https://www.medrk.ru/shop/diagnosticheskoe-oborudovanie/dermatoskopy/id-23218",
                    "https://vendem.ru/catalog/funktsionalnaya_diagnostika/dermatoskopy/dermatoskop_heine_delta_20/",
                    "https://www.uni-tec.su/dermatoskopy.html",
                    "https://heine-opto.ru/product/dermatoskop-heine-delta-20t/"
                ]
            ],
            "дерматоскоп heine delta" => [
                [
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/delta-20-t/",
                    "https://shop.heine-med.ru/catalog/dermatoskopy/dermatoskop_delta_20_plus/",
                    "https://heine.ru.com/product-category/dermatologiya/dermatoskop-delta-20/",
                    "https://medeq.ru/product/dermatoskop-heine-delta-20-plus/11424",
                    "https://www.uni-tec.su/dermatoskopy.html",
                    "https://almamed.su/category/heine-delta-20-t-tsifrovye-dermatoskopy-germaniya/",
                    "http://www.deal-med.ru/dermatoskop_delta_20.html",
                    "https://mttechnica.ru/ufiles/ins/04-dermatoscopes-2018_ru.pdf",
                    "https://panfundus.ru/catalog/dermatoskopy-heine/derm-delta20-beta/",
                    "https://medmart.pro/products/dermatoskop-heine-delta-20t-s-rukoyatkoj-4usb"
                ]
            ],
            "дерматоскоп heine delta 20" => [
                [
                    "https://heine.ru.com/product-category/dermatologiya/dermatoskop-delta-20/",
                    "https://heine-med.ru/images/stories/pdf/2013/dermatoscope/delta-20.pdf",
                    "https://medeq.ru/product/dermatoskop-heine-delta-20/11422",
                    "http://www.deal-med.ru/dermatoskop_delta_20.html",
                    "https://almamed.su/category/heine-delta-20-t-tsifrovye-dermatoskopy-germaniya/",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/delta-20-t/",
                    "https://heine-opto.ru/product/dermatoskop-heine-delta-20t/",
                    "https://www.uni-tec.su/dermatoskopy.html",
                    "https://vendem.ru/catalog/funktsionalnaya_diagnostika/dermatoskopy/dermatoskop_heine_delta_20/",
                    "https://td-lab.ru/product/dermatoskop_delta20t/"
                ]
            ],
            "дерматоскоп heine delta 20 цена" => [
                [
                    "https://heine.ru.com/product-category/dermatologiya/dermatoskop-delta-20/",
                    "https://shop.heine-med.ru/catalog/dermatoskopy/dermatoskop_delta_20_t/",
                    "http://www.deal-med.ru/dermatoskop_delta_20.html",
                    "https://medeq.ru/product/dermatoskop-heine-delta-20-plus/11424",
                    "https://almamed.su/category/heine-delta-20-t-tsifrovye-dermatoskopy-germaniya/",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/delta-20-t/delta-20-t-k-26210118/",
                    "https://td-lab.ru/product/dermatoskop_delta20t/",
                    "https://vendem.ru/catalog/funktsionalnaya_diagnostika/dermatoskopy/dermatoskop_heine_delta_20/",
                    "https://www.medrk.ru/shop/diagnosticheskoe-oborudovanie/dermatoskopy/id-23218",
                    "https://www.uni-tec.su/dermatoskopy.html"
                ]
            ],
            "дерматоскоп дельта" => [
                [
                    "https://heine.ru.com/product-category/dermatologiya/dermatoskop-delta-20/",
                    "https://shop.heine-med.ru/catalog/dermatoskopy/dermatoskop_delta_20_plus/",
                    "https://medeq.ru/product/dermatoskop-heine-delta-20/11422",
                    "http://www.deal-med.ru/dermatoskop_delta_20.html",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/delta-20-t/",
                    "https://www.uni-tec.su/dermatoskopy.html",
                    "https://almamed.su/category/heine-delta-20-t-tsifrovye-dermatoskopy-germaniya/",
                    "https://eurosmed.ru/products/dermatoskop-delta-20-plus",
                    "https://medmart.pro/products/dermatoskop-heine-delta-20t-s-rukoyatkoj-4usb",
                    "https://mttechnica.ru/ufiles/ins/04-dermatoscopes-2018_ru.pdf"
                ]
            ],
            "дерматоскоп дельта 20" => [
                [
                    "https://heine.ru.com/product-category/dermatologiya/dermatoskop-delta-20/",
                    "https://medeq.ru/product/dermatoskop-heine-delta-20/11422",
                    "https://heine-med.ru/images/stories/pdf/2013/dermatoscope/delta-20.pdf",
                    "http://www.deal-med.ru/dermatoskop_delta_20.html",
                    "https://almamed.su/category/heine-delta-20-t-tsifrovye-dermatoskopy-germaniya/",
                    "https://heine-opto.ru/product/dermatoskop-heine-delta-20t/",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/delta-20-t/",
                    "https://td-lab.ru/product/dermatoskop_delta20t/",
                    "https://eurosmed.ru/products/dermatoskop-delta-20-plus",
                    "https://medmart.pro/products/dermatoskop-heine-delta-20t-s-rukoyatkoj-4usb"
                ]
            ],
            "дерматоскоп дельта купить" => [
                [
                    "https://heine.ru.com/product-category/dermatologiya/dermatoskop-delta-20/",
                    "https://shop.heine-med.ru/catalog/dermatoskopy/",
                    "https://medeq.ru/product/dermatoskop-heine-delta-20/11422",
                    "https://almamed.su/category/heine-delta-20-t-tsifrovye-dermatoskopy-germaniya/",
                    "http://www.deal-med.ru/dermatoskop_delta_20.html",
                    "https://www.uni-tec.su/dermatoskopy.html",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/delta-20-t/",
                    "https://eurosmed.ru/products/dermatoskop-delta-20-plus",
                    "https://medmart.pro/products/dermatoskop-heine-delta-20t-s-rukoyatkoj-4usb",
                    "https://vilmed.ru/catalog/heine-delta-20-plus-tsifrovye-dermatoskopy-s-polyarizatsiey-i-immersiey-germaniya/"
                ]
            ],
            "дерматоскоп дельта цена" => [
                [
                    "https://heine.ru.com/product-category/dermatologiya/dermatoskop-delta-20/",
                    "https://shop.heine-med.ru/catalog/dermatoskopy/",
                    "https://medeq.ru/product/dermatoskop-heine-delta-20-plus/11424",
                    "https://almamed.su/category/heine-delta-20-t-tsifrovye-dermatoskopy-germaniya/",
                    "http://www.deal-med.ru/dermatoskop_delta_20.html",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/delta-20-t/delta-20-t-k-26210118/",
                    "https://www.uni-tec.su/dermatoskopy.html",
                    "https://eurosmed.ru/products/dermatoskop-delta-20-plus",
                    "https://www.medrk.ru/shop/diagnosticheskoe-oborudovanie/dermatoskopy/id-23218",
                    "https://td-lab.ru/product/dermatoskop_delta20t/"
                ]
            ],
            "дерматоскоп медицинский delta 20" => [
                [
                    "https://heine.ru.com/product/dermatoskop-medicinskij-delta-20-usb-perezaryazhaemaya-rukoyatka-veta-tr-bez-kejsa/",
                    "https://medeq.ru/product/dermatoskop-heine-delta-20/11422",
                    "http://www.deal-med.ru/dermatoskop_delta_20.html",
                    "https://shop.heine-med.ru/catalog/dermatoskopy/dermatoskop_delta_20_plus/1554/",
                    "https://almamed.su/category/heine-delta-20-t-tsifrovye-dermatoskopy-germaniya/",
                    "https://td-lab.ru/product/dermatoskop_delta20t/",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/delta-20-t/delta-20-t-k-26210118/",
                    "https://heine-opto.ru/product/dermatoskop-heine-delta-20t/",
                    "https://eurosmed.ru/products/dermatoskop-delta-20-plus",
                    "https://medmart.pro/products/dermatoskop-heine-delta-20t-s-rukoyatkoj-4usb"
                ]
            ],
            "дерматоскоп хайне дельта 20" => [
                [
                    "https://heine.ru.com/product-category/dermatologiya/dermatoskop-delta-20/",
                    "https://heine-med.ru/shop/shop.browse/11.html",
                    "https://medeq.ru/product/dermatoskop-heine-delta-20/11422",
                    "http://www.deal-med.ru/dermatoskop_delta_20.html",
                    "https://almamed.su/category/heine-delta-20-t-tsifrovye-dermatoskopy-germaniya/",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/delta-20-t/",
                    "https://heine-opto.ru/product/dermatoskop-heine-delta-20t/",
                    "https://td-lab.ru/product/dermatoskop_delta20t/",
                    "https://www.uni-tec.su/dermatoskopy.html",
                    "https://vendem.ru/catalog/funktsionalnaya_diagnostika/dermatoskopy/dermatoskop_heine_delta_20/"
                ]
            ],
            "купить дерматоскоп heine delta 20" => [
                [
                    "https://heine.ru.com/product-category/dermatologiya/dermatoskop-delta-20/",
                    "http://www.deal-med.ru/dermatoskop_delta_20.html",
                    "https://shop.heine-med.ru/catalog/dermatoskopy/dermatoskop_delta_20_t/",
                    "https://medeq.ru/product/dermatoskop-heine-delta-20/11422",
                    "https://almamed.su/category/heine-delta-20-t-tsifrovye-dermatoskopy-germaniya/",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/delta-20-t/",
                    "https://www.uni-tec.su/dermatoskopy.html",
                    "https://heine-opto.ru/product/dermatoskop-heine-delta-20t/",
                    "https://td-lab.ru/product/dermatoskop_delta20t/",
                    "https://vendem.ru/catalog/funktsionalnaya_diagnostika/dermatoskopy/dermatoskop_heine_delta_20/"
                ]
            ]
        ],
        "дерматоскоп delta" => [
            "дерматоскоп delta 20 t" => [
                [
                    "https://shop.heine-med.ru/catalog/dermatoskopy/dermatoskop_delta_20_t/",
                    "https://heine.ru.com/product/dermatoskop_delta20t/",
                    "https://almamed.su/category/heine-delta-20-t-tsifrovye-dermatoskopy-germaniya/",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/delta-20-t/",
                    "https://medmart.pro/products/dermatoskop-heine-delta-20t-s-rukoyatkoj-4usb",
                    "https://mttechnica.ru/ufiles/ins/04-dermatoscopes-2018_ru.pdf",
                    "https://www.uni-tec.su/dermatoskopy.html",
                    "https://medeles.ru/dermatologiya/dermatoskop-delta-20-t",
                    "http://www.deal-med.ru/dermatoskop_delta_20.html",
                    "https://panfundus.ru/catalog/dermatoskopy-heine/derm-delta20-beta/"
                ]
            ],
            "дерматоскоп heine delta 20 t" => [
                [
                    "https://shop.heine-med.ru/catalog/dermatoskopy/dermatoskop_delta_20_t/",
                    "https://heine.ru.com/product/dermatoskop_delta20t/",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/delta-20-t/",
                    "https://www.heine.com/en/products/dermatoscopes-and-digital-documentation/dermatoscopes/detail/28744-heine-delta-20t-dermatoscope",
                    "https://almamed.su/category/heine-delta-20-t-tsifrovye-dermatoskopy-germaniya/",
                    "https://heine-opto.ru/product/dermatoskop-heine-delta-20t/",
                    "https://www.uni-tec.su/dermatoskopy.html",
                    "https://medmart.pro/products/dermatoskop-heine-delta-20t-s-rukoyatkoj-4usb",
                    "https://panfundus.ru/catalog/dermatoskopy-heine/derm-delta20-beta/",
                    "https://permedcom.ru/catalog/kosmetologiya-i-dermatovenerologiya/dermatoskopy/heine-delta-20-t-/"
                ]
            ]
        ],
        "дерматоскоп delta 20 plus" => [
            "дерматоскоп delta 20 plus" => [
                [
                    "https://shop.heine-med.ru/catalog/dermatoskopy/dermatoskop_delta_20_plus/",
                    "https://medeq.ru/product/dermatoskop-heine-delta-20-plus/11424",
                    "https://eurosmed.ru/products/dermatoskop-delta-20-plus",
                    "https://www.medrk.ru/shop/diagnosticheskoe-oborudovanie/dermatoskopy/id-23218",
                    "https://heine.ru.com/product-category/dermatologiya/dermatoskop-delta-20/",
                    "https://med-plus.shop/product-dermatoskop-delta-20-plus/",
                    "https://almamed.su/category/heine-delta-20-plus-tsifrovye-dermatoskopy-s-polyarizatsiey-i-immersiey-germaniya/",
                    "https://www.lidermed-ru.com/products/dermatoskop-svetodiodnyj-heine-delta-20-plus",
                    "https://medstore.pro/docs/heine-delta-20-plus-manual-ru.pdf",
                    "https://vilmed.ru/catalog/heine-delta-20-plus-tsifrovye-dermatoskopy-s-polyarizatsiey-i-immersiey-germaniya/"
                ]
            ],
            "дерматоскоп heine delta 20 plus" => [
                [
                    "https://shop.heine-med.ru/catalog/dermatoskopy/dermatoskop_delta_20_plus/",
                    "https://medeq.ru/product/dermatoskop-heine-delta-20-plus/11424",
                    "https://heine.ru.com/product-category/dermatologiya/dermatoskop-delta-20/",
                    "https://eurosmed.ru/products/dermatoskop-delta-20-plus",
                    "https://www.medrk.ru/shop/diagnosticheskoe-oborudovanie/dermatoskopy/id-23218",
                    "https://med-plus.shop/product-dermatoskop-delta-20-plus/",
                    "https://almamed.su/category/heine-delta-20-plus-tsifrovye-dermatoskopy-s-polyarizatsiey-i-immersiey-germaniya/",
                    "https://medstore.pro/docs/heine-delta-20-plus-manual-ru.pdf",
                    "https://www.lidermed-ru.com/products/dermatoskop-svetodiodnyj-heine-delta-20-plus",
                    "https://vendem.ru/catalog/funktsionalnaya_diagnostika/dermatoskopy/dermatoskop_heine_delta_20_plus/"
                ]
            ],
            "дерматоскоп heine delta 20 plus цена" => [
                [
                    "https://shop.heine-med.ru/catalog/dermatoskopy/dermatoskop_delta_20_plus/",
                    "https://medeq.ru/product/dermatoskop-heine-delta-20-plus/11424",
                    "https://heine.ru.com/product-category/dermatologiya/dermatoskop-delta-20/",
                    "https://www.medrk.ru/shop/diagnosticheskoe-oborudovanie/dermatoskopy/id-23218",
                    "https://almamed.su/category/heine-delta-20-plus-tsifrovye-dermatoskopy-s-polyarizatsiey-i-immersiey-germaniya/",
                    "https://med-plus.shop/product-dermatoskop-delta-20-plus/",
                    "http://www.deal-med.ru/dermatoskopy_heine.html",
                    "https://vendem.ru/catalog/funktsionalnaya_diagnostika/dermatoskopy/dermatoskop_heine_delta_20_plus/",
                    "https://eurosmed.ru/products/dermatoskop-delta-20-plus",
                    "https://vilmed.ru/catalog/heine-delta-20-plus-tsifrovye-dermatoskopy-s-polyarizatsiey-i-immersiey-germaniya/"
                ]
            ],
            "дерматоскоп дельта 20 плюс" => [
                [
                    "https://shop.heine-med.ru/catalog/dermatoskopy/dermatoskop_delta_20_plus/",
                    "https://medeq.ru/product/dermatoskop-heine-delta-20-plus/11424",
                    "https://heine.ru.com/product-category/dermatologiya/dermatoskop-delta-20/",
                    "https://eurosmed.ru/products/dermatoskop-delta-20-plus",
                    "https://www.medrk.ru/shop/diagnosticheskoe-oborudovanie/dermatoskopy/id-23218",
                    "https://almamed.su/category/heine-delta-20-plus-tsifrovye-dermatoskopy-s-polyarizatsiey-i-immersiey-germaniya/",
                    "https://med-plus.shop/product-dermatoskop-delta-20-plus/",
                    "https://www.lidermed-ru.com/products/dermatoskop-svetodiodnyj-heine-delta-20-plus",
                    "https://medstore.pro/docs/heine-delta-20-plus-manual-ru.pdf",
                    "https://vendem.ru/catalog/funktsionalnaya_diagnostika/dermatoskopy/dermatoskop_heine_delta_20_plus/"
                ]
            ]
        ],
        "дерматоскоп delta 20 купить" => [
            "дерматоскоп delta купить" => [
                [
                    "https://shop.heine-med.ru/catalog/dermatoskopy/",
                    "https://heine.ru.com/product-category/dermatologiya/dermatoskop-delta-20/",
                    "https://medeq.ru/product/dermatoskop-heine-delta-20-plus/11424",
                    "https://almamed.su/category/heine-delta-20-t-tsifrovye-dermatoskopy-germaniya/",
                    "http://www.deal-med.ru/dermatoskopy_heine.html",
                    "https://www.uni-tec.su/dermatoskopy.html",
                    "https://eurosmed.ru/products/dermatoskop-delta-20-plus",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/delta-20-t/delta-20-t-k-26210118/",
                    "https://vilmed.ru/catalog/dermatoskopy-heine-germaniya/",
                    "https://heine-opto.ru/product/dermatoskop-heine-delta-30/"
                ]
            ]
        ],
        "дерматоскоп heine" => [
            "дерматоскоп heine" => [
                [
                    "https://shop.heine-med.ru/catalog/dermatoskopy/",
                    "https://heine.ru.com/product-category/dermatologiya/",
                    "https://medeq.ru/store/kosmetologiya/dermatoskopy/filters/brand-116",
                    "http://www.deal-med.ru/dermatoskopy_heine.html",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/",
                    "https://almamed.su/category/dermatoskopy-heine-germaniya/",
                    "https://heine-opto.ru/cat/dermatoskopy/",
                    "https://panfundus.ru/catalog/prochee/dermatoskopy-heine/",
                    "https://vilmed.ru/catalog/dermatoskopy-heine-germaniya/",
                    "https://www.uni-tec.su/dermatoskopy.html"
                ]
            ],
            "дерматоскоп хайне" => [
                [
                    "https://heine.ru.com/product-category/dermatologiya/",
                    "https://shop.heine-med.ru/catalog/dermatoskopy/",
                    "https://medeq.ru/store/kosmetologiya/dermatoskopy/filters/brand-116",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/",
                    "http://www.deal-med.ru/dermatoskopy_heine.html",
                    "https://almamed.su/category/dermatoskopy-heine-germaniya/",
                    "https://heine-opto.ru/cat/dermatoskopy/",
                    "https://panfundus.ru/catalog/prochee/dermatoskopy-heine/",
                    "https://vilmed.ru/catalog/dermatoskopy-heine-germaniya/",
                    "https://www.uni-tec.su/dermatoskopy.html"
                ]
            ],
            "дерматоскоп хайне купить" => [
                [
                    "https://heine.ru.com/product-category/dermatologiya/",
                    "https://medeq.ru/store/kosmetologiya/dermatoskopy/filters/brand-116",
                    "https://shop.heine-med.ru/catalog/dermatoskopy/",
                    "http://www.deal-med.ru/dermatoskopy_heine.html",
                    "https://almamed.su/category/dermatoskopy-heine-germaniya/",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/",
                    "https://vilmed.ru/catalog/dermatoskopy-heine-germaniya/",
                    "https://panfundus.ru/catalog/prochee/dermatoskopy-heine/",
                    "https://www.avito.ru/moskva?q=%d0%b4%d0%b5%d1%80%d0%bc%d0%b0%d1%82%d0%be%d1%81%d0%ba%d0%be%d0%bf",
                    "https://heine-opto.ru/cat/dermatoskopy/"
                ]
            ],
            "дерматоскопы heine купить" => [
                [
                    "https://shop.heine-med.ru/catalog/dermatoskopy/",
                    "https://heine.ru.com/product-category/dermatologiya/",
                    "https://medeq.ru/store/kosmetologiya/dermatoskopy/filters/brand-116",
                    "https://almamed.su/category/dermatoskopy-heine-germaniya/",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/",
                    "http://www.deal-med.ru/dermatoskopy_heine.html",
                    "https://vilmed.ru/catalog/dermatoskopy-heine-germaniya/",
                    "https://heine-opto.ru/cat/dermatoskopy/",
                    "https://panfundus.ru/catalog/prochee/dermatoskopy-heine/",
                    "https://www.uni-tec.su/dermatoskopy.html"
                ]
            ]
        ],
        "дерматоскоп heine mini" => [
            "дерматоскоп heine mini" => [
                [
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/mini-3000/d-00178106/",
                    "https://medeq.ru/product/dermatoskop-heine-mini-3000-led/7084",
                    "https://shop.heine-med.ru/catalog/dermatoskopy/dermatoskop_mini_3000_xhl_led/",
                    "https://heine.ru.com/product-category/dermatologiya/dermatoskop-mini3000/",
                    "https://heine-opto.ru/product/dermatoskop-heine-mini-3000-led/",
                    "http://www.deal-med.ru/dermatoskopy_heine.html",
                    "https://permedcom.ru/catalog/kosmetologiya-i-dermatovenerologiya/dermatoskopy/heine-mini-3000-led/",
                    "https://almamed.su/category/heine-mini-3000-karmannyy-dermatoskop-germaniya/",
                    "https://vilmed.ru/catalog/dermatoskopy-heine-germaniya/",
                    "https://medmart.pro/products/dermatoskop-heine-mini-3000-led-so-shkaloj-i-kejsom"
                ]
            ],
            "дерматоскоп heine mini 3000" => [
                [
                    "https://medeq.ru/product/dermatoskop-heine-mini-3000-led/7084",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/mini-3000/d-00178106/",
                    "https://heine.ru.com/product-category/dermatologiya/dermatoskop-mini3000/",
                    "https://shop.heine-med.ru/catalog/dermatoskopy/dermatoskop_mini_3000_xhl_led/",
                    "http://www.deal-med.ru/dermatoskop_3000_d109.html",
                    "https://heine-opto.ru/product/dermatoskop-heine-mini-3000-led/",
                    "https://permedcom.ru/catalog/kosmetologiya-i-dermatovenerologiya/dermatoskopy/heine-mini-3000-led/",
                    "https://almamed.su/category/heine-mini-3000-karmannyy-dermatoskop-germaniya/",
                    "https://www.medrk.ru/shop/diagnosticheskoe-oborudovanie/dermatoskopy/id-23220",
                    "https://www.heine.com/en/products/dermatoscopes-and-digital-documentation/dermatoscopes/detail/31553-heine-mini-3000-led-dermatoscope"
                ]
            ],
            "дерматоскоп heine mini 3000 led" => [
                [
                    "https://medeq.ru/product/dermatoskop-heine-mini-3000-led/7084",
                    "https://heine.ru.com/product/dermatoskop-mini-3000-led/",
                    "https://heine-med.ru/shop/shop.browse/16.html",
                    "http://www.deal-med.ru/dermatoskop_mini_3000_led_s_prinadlezhnostiami.html",
                    "https://heine-opto.ru/product/dermatoskop-heine-mini-3000-led/",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/mini-3000/d-00178106/",
                    "https://permedcom.ru/catalog/kosmetologiya-i-dermatovenerologiya/dermatoskopy/heine-mini-3000-led/",
                    "https://almamed.su/category/heine-mini-3000-karmannyy-dermatoskop-germaniya/",
                    "https://medmart.pro/products/dermatoskop-heine-mini-3000-led-so-shkaloj-i-kejsom",
                    "https://www.heine.com/en/products/dermatoscopes-and-digital-documentation/dermatoscopes/detail/31553-heine-mini-3000-led-dermatoscope"
                ]
            ],
            "дерматоскоп mini 3000" => [
                [
                    "https://medeq.ru/product/dermatoskop-heine-mini-3000-led/7084",
                    "https://shop.heine-med.ru/catalog/dermatoskopy/dermatoskop_mini_3000_xhl_led/",
                    "https://heine.ru.com/product-category/dermatologiya/dermatoskop-mini3000/",
                    "http://www.deal-med.ru/dermatoskop_3000_d109.html",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/mini-3000/d-00178106/",
                    "https://almamed.su/product/dermatoskop-mini-3000-so-shkaloy-d-00178109/",
                    "https://heine-opto.ru/product/dermatoskop-heine-mini-3000-led/",
                    "https://permedcom.ru/catalog/kosmetologiya-i-dermatovenerologiya/dermatoskopy/heine-mini-3000-led/",
                    "https://td-lab.ru/product/dermatoskop-mini-3000-led/",
                    "https://medeles.ru/dermatologiya/dermatoskop-mini-3000"
                ]
            ]
        ],
        "дерматоскоп heine mini 3000 led" => [
            "дерматоскоп mini 3000 led" => [
                [
                    "https://medeq.ru/product/dermatoskop-heine-mini-3000-led/7084",
                    "https://heine.ru.com/product/dermatoskop-mini-3000-led/",
                    "https://shop.heine-med.ru/catalog/dermatoskopy/dermatoskop_mini_3000_xhl_led/1299/",
                    "http://www.deal-med.ru/dermatoskop_mini_3000_led_s_prinadlezhnostiami.html",
                    "https://heine-opto.ru/product/dermatoskop-heine-mini-3000-led/",
                    "https://almamed.su/product/dermatoskop-mini-3000led-c-prinadlezhnostyami-d-00878109-heine-germaniya/",
                    "https://permedcom.ru/catalog/kosmetologiya-i-dermatovenerologiya/dermatoskopy/heine-mini-3000-led/",
                    "https://eurosmed.ru/products/dermatoskop-mini-3000-led",
                    "https://medeles.ru/dermatologiya/dermatoskop-mini-3000-led",
                    "https://stkraft.com/dermatologiya/dermatoskopy/heine-5/mini-3000-led/"
                ]
            ]
        ],
        "дерматоскоп купить" => [
            "дерматоскоп купить" => [
                [
                    "https://medeq.ru/store/kosmetologiya/dermatoskopy",
                    "https://almamed.su/category/dermatoskopy/",
                    "http://www.deal-med.ru/dermatoskopy.html",
                    "https://www.avito.ru/moskva?q=%d0%b4%d0%b5%d1%80%d0%bc%d0%b0%d1%82%d0%be%d1%81%d0%ba%d0%be%d0%bf",
                    "https://www.ozon.ru/highlight/dermatoskopy-291583/",
                    "https://medmart.pro/catalog/dermatoskopy",
                    "https://shop.heine-med.ru/catalog/dermatoskopy/",
                    "https://aliexpress.ru/popular/dermatoscope.html",
                    "https://mpamed-shop.ru/dermatoskopy/",
                    "https://stkraft.com/dermatologiya/dermatoskopy/"
                ]
            ],
            "дерматоскоп медицинский" => [
                [
                    "https://medeq.ru/store/kosmetologiya/dermatoskopy",
                    "http://www.deal-med.ru/dermatoskopy.html",
                    "https://almamed.su/category/dermatoskopy/",
                    "https://mpamed-shop.ru/dermatoskopy/",
                    "https://medmart.pro/catalog/dermatoskopy",
                    "https://www.ozon.ru/highlight/dermatoskopy-291583/",
                    "http://medtehural.ru/oborudovanie/dermatoskopy",
                    "https://foodandhealth.ru/medodezhda-i-pribory/dermatoskop/",
                    "https://www.medcomp.ru/catalog/oborudovanie/diagnostika/dermatoskopy/",
                    "https://www.avito.ru/moskva?q=%d0%b4%d0%b5%d1%80%d0%bc%d0%b0%d1%82%d0%be%d1%81%d0%ba%d0%be%d0%bf"
                ]
            ],
            "дерматоскоп ручной" => [
                [
                    "https://medeq.ru/store/kosmetologiya/dermatoskopy",
                    "https://medmart.pro/catalog/dermatoskopy-ruchnye",
                    "https://almamed.su/category/dermatoskopy/",
                    "https://foodandhealth.ru/medodezhda-i-pribory/dermatoskop/",
                    "https://aliexpress.ru/popular/dermatoscope.html",
                    "https://www.avito.ru/moskva?q=%d0%b4%d0%b5%d1%80%d0%bc%d0%b0%d1%82%d0%be%d1%81%d0%ba%d0%be%d0%bf",
                    "http://www.deal-med.ru/dermatoskopy.html",
                    "https://www.ozon.ru/highlight/dermatoskopy-291583/",
                    "https://mpamed-shop.ru/dermatoskopy/",
                    "https://heine-med.ru/images/stories/pdf/choose-dermatoscope/choose-dermatoscope-2019.pdf"
                ]
            ],
            "дерматоскоп стоимость" => [
                [
                    "https://medeq.ru/store/kosmetologiya/dermatoskopy",
                    "https://almamed.su/category/dermatoskopy/",
                    "https://www.ozon.ru/highlight/dermatoskopy-291583/",
                    "http://www.deal-med.ru/dermatoskopy.html",
                    "https://www.avito.ru/moskva?q=%d0%b4%d0%b5%d1%80%d0%bc%d0%b0%d1%82%d0%be%d1%81%d0%ba%d0%be%d0%bf",
                    "https://medmart.pro/catalog/dermatoskopy",
                    "https://mpamed-shop.ru/dermatoskopy/",
                    "https://shop.heine-med.ru/catalog/dermatoskopy/",
                    "https://stkraft.com/dermatologiya/dermatoskopy/",
                    "https://eurosmed.ru/catalog/dermatoskopy"
                ]
            ],
            "дерматоскоп цена" => [
                [
                    "https://medeq.ru/store/kosmetologiya/dermatoskopy",
                    "https://www.avito.ru/moskva?q=%d0%b4%d0%b5%d1%80%d0%bc%d0%b0%d1%82%d0%be%d1%81%d0%ba%d0%be%d0%bf",
                    "https://almamed.su/category/dermatoskopy/",
                    "https://www.ozon.ru/highlight/dermatoskopy-291583/",
                    "http://www.deal-med.ru/dermatoskopy.html",
                    "https://medmart.pro/catalog/dermatoskopy",
                    "https://stkraft.com/dermatologiya/dermatoskopy/",
                    "https://mpamed-shop.ru/dermatoskopy/",
                    "https://shop.heine-med.ru/catalog/dermatoskopy/",
                    "https://aliexpress.ru/popular/dermatoscope.html"
                ]
            ],
            "дерматоскоп цена купить" => [
                [
                    "https://medeq.ru/store/kosmetologiya/dermatoskopy",
                    "https://www.avito.ru/moskva?q=%d0%b4%d0%b5%d1%80%d0%bc%d0%b0%d1%82%d0%be%d1%81%d0%ba%d0%be%d0%bf",
                    "https://almamed.su/category/dermatoskopy/",
                    "http://www.deal-med.ru/dermatoskopy.html",
                    "https://www.ozon.ru/highlight/dermatoskopy-291583/",
                    "https://aliexpress.ru/popular/dermatoscope.html",
                    "https://shop.heine-med.ru/catalog/dermatoskopy/",
                    "https://mpamed-shop.ru/dermatoskopy/",
                    "https://medmart.pro/catalog/dermatoskopy",
                    "https://eurosmed.ru/catalog/dermatoskopy"
                ]
            ],
            "дерматоскоп электронный" => [
                [
                    "https://medeq.ru/store/kosmetologiya/dermatoskopy",
                    "https://medmart.pro/catalog/dermatoskopy",
                    "https://aliexpress.ru/popular/dermatoscope.html",
                    "https://foodandhealth.ru/medodezhda-i-pribory/dermatoskop/",
                    "https://almamed.su/category/dermatoskopy/",
                    "https://www.avito.ru/moskva?q=%d0%b4%d0%b5%d1%80%d0%bc%d0%b0%d1%82%d0%be%d1%81%d0%ba%d0%be%d0%bf",
                    "https://market.yandex.ru/search?text=%d0%bf%d0%be%d1%80%d1%82%d0%b0%d1%82%d0%b8%d0%b2%d0%bd%d1%8b%d0%b9%20%d1%86%d0%b8%d1%84%d1%80%d0%be%d0%b2%d0%be%d0%b9%20%d0%b4%d0%b5%d1%80%d0%bc%d0%b0%d1%82%d0%be%d1%81%d0%ba%d0%be%d0%bf",
                    "https://www.medcomp.ru/catalog/oborudovanie/diagnostika/dermatoskopy/",
                    "http://www.deal-med.ru/dermatoskopy.html",
                    "https://mpamed-shop.ru/dermatoskopy/"
                ]
            ],
            "цифровой дерматоскоп" => [
                [
                    "https://medeq.ru/store/kosmetologiya/dermatoskopy",
                    "https://almamed.su/category/dermatoskopy/",
                    "https://medmart.pro/catalog/dermatoskopy-tsifrovye",
                    "https://heine-med.ru/shop/shop.browse/3.html",
                    "https://foodandhealth.ru/medodezhda-i-pribory/dermatoskop/",
                    "https://aliexpress.ru/popular/dermatoscope.html",
                    "https://www.avito.ru/moskva?q=%d0%b4%d0%b5%d1%80%d0%bc%d0%b0%d1%82%d0%be%d1%81%d0%ba%d0%be%d0%bf",
                    "http://medtehural.ru/oborudovanie/dermatoskopy",
                    "http://www.deal-med.ru/dermatoskopy.html",
                    "https://www.medcomp.ru/catalog/oborudovanie/diagnostika/dermatoskopy/"
                ]
            ]
        ],
        "дерматоскоп медицинский" => [
            "дерматоскоп оптический" => [
                [
                    "https://medeq.ru/store/kosmetologiya/dermatoskopy",
                    "https://medmart.pro/catalog/dermatoskopy-opticheskie",
                    "http://www.deal-med.ru/dermatoskopy.html",
                    "https://www.medcomp.ru/catalog/oborudovanie/diagnostika/dermatoskopy/",
                    "http://medtehural.ru/oborudovanie/dermatoskopy",
                    "https://heine-med.ru/images/stories/pdf/choose-dermatoscope/choose-dermatoscope-2019.pdf",
                    "https://mpamed-shop.ru/dermatoskopy/",
                    "https://atismed.ru/kosmetologicheskoe-oborudovanie/dermatoskopy",
                    "https://almamed.su/category/dermatoskopy/",
                    "https://market.yandex.ru/search?text=%d0%be%d0%bf%d1%82%d0%b8%d1%87%d0%b5%d1%81%d0%ba%d0%b8%d0%b9%20%d0%b4%d0%b5%d1%80%d0%bc%d0%b0%d1%82%d0%be%d1%81%d0%ba%d0%be%d0%bf%20%d1%81%20%d0%bf%d0%be%d0%bb%d1%8f%d1%80%d0%b8%d0%b7%d0%b0%d1%86%d0%b8%d0%b5%d0%b9%20%d1%80%d1%83%d1%87%d0%bd%d0%be%d0%b9"
                ]
            ]
        ]
    ];
    dd($clusters);
});
