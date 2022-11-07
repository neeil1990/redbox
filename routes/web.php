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
//        $t = [
//            "https://leroymerlin.ru/catalogue/shtukaturki/dlya-naruzhnyh-rabot/",
//            "https://uslugi.yandex.ru/213-moscow/category?text=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
//            "https://market.yandex.ru/search?text=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d0%b0%d1%8f%20%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%b4%d0 ▶",
//            "https://dom-i-remont.info/posts/fasad-doma/vidy-fasadnyh-shtukaturok-harakteristiki-lidery-i-sekrety-otdelki/",
//            "https://st-par.ru/info/stati-o-sukhikh-smesyakh/shtukaturka-fasada/",
//            "https://moscow.petrovich.ru/catalog/1447/fasadnye-shtukaturki/",
//            "https://m.avito.ru/moskva/predlozheniya_uslug?query=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
//            "https://m-strana.ru/articles/otdelka-fasada-doma-shtukaturkoy/",
//            "https://1pofasady.ru/shtukaturka/oshtukaturivanie-fasada",
//            "https://kronotech.ru/fasadnye-raboty/shtukaturka-fasada",
//            "https://stroy-podskazka.ru/shtukaturka/fasadnaya/",
//            "https://www.youtube.com/playlist?list=pluivzwm_q9lz9x7bf9mxin8zpkdwtn_of",
//            "https://www.prof-fasady.ru/catalog/shtukaturka-fasada/",
//            "https://profi.ru/remont/malyarnye-shtukaturnye-raboty/shtukatury/shtukaturka-sten/shtukaturka-fasada/",
//            "https://fasadblog.ru/shtukaturnyj-fasad/",
//            "https://www.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/fasadnaya/",
//            "https://inistroy.com/facade/",
//            "https://dekoriko.ru/shtukaturka/fasadnaya/",
//            "https://dimax.su/uslugi/shtukaturka-fasada/",
//            "https://prodekorsten.com/vyravnivanie/fasada/shtukaturka-fasada.html",
//        ];
//        $s = [
//            "https://leroymerlin.ru/catalogue/shtukaturki/dlya-naruzhnyh-rabot/",
//            "https://market.yandex.ru/search?text=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d0%b0%d1%8f%20%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%b4%d0 ▶",
//            "https://uslugi.yandex.ru/213-moscow/category?text=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
//            "https://moscow.petrovich.ru/catalog/1447/fasadnye-shtukaturki/",
//            "https://stroy-podskazka.ru/shtukaturka/fasadnaya/",
//            "https://st-par.ru/info/stati-o-sukhikh-smesyakh/shtukaturka-fasada/",
//            "https://www.ozon.ru/category/shtukaturka-dlya-naruzhnyh-rabot/",
//            "https://m-strana.ru/articles/otdelka-fasada-doma-shtukaturkoy/",
//            "https://www.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/fasadnaya/",
//            "https://dom-i-remont.info/posts/fasad-doma/vidy-fasadnyh-shtukaturok-harakteristiki-lidery-i-sekrety-otdelki/",
//            "https://www.avito.ru/moskva/uslugi?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
//            "https://kronotech.ru/fasadnye-raboty/shtukaturka-fasada",
//            "https://dimax.su/uslugi/shtukaturka-fasada/",
//            "https://1pofasady.ru/shtukaturka/oshtukaturivanie-fasada",
//            "https://www.forumhouse.ru/journal/articles/4446-shtukaturka-fasada-doma",
//            "https://expert-deco.ru/catalog/fasadnaya-shtukaturka/",
//            "https://www.prof-fasady.ru/catalog/shtukaturka-fasada/",
//            "https://www.strd.ru/suhie_smesi/shtukaturki/dla_fasada/",
//            "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/kakuyu-vybrat-shtukaturku-dlya-fasada.html",
//            "https://7dach.ru/natashapetrova/shtukaturka-dlya-fasada-praktichno-nadezhno-krasivo-164829.html",
//        ];
//
//        dd(array_intersect($t, $s));
        $jayParsedAry = [
            "декоративная отделка фасадов" => [
                "sites" => [
                    "https://stroy-podskazka.ru/dom/otdelka-fasada/",
                    "https://www.forumhouse.ru/journal/themes/66-varianty-otdelki-fasada-populyarnye-vidy-konstruktiv-osobennosti",
                    "https://m-strana.ru/articles/chem-nedorogo-otdelat-fasad-doma/",
                    "https://realty.rbc.ru/news/61a7952a9a7947039b719b08",
                    "https://srbu.ru/otdelochnye-materialy/1950-varianty-otdelki-fasada-chastnogo-doma.html",
                    "https://market.yandex.ru/search?text=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b9%20%d0%b4%d0%b5%d0%ba%d0%be%d1%80%20%d0%b4%d0%bb%d1%8f%20%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%bd%d0%be%d0%b9%20%d0%be%d1%82%d0%b4%d0%b5%d0%bb%d0%ba%d0%b8%20%d0%b4%d0%be%d0%bc%d0%b0",
                    "https://vekroof.ru/articles/luchshie-materialy-dlya-otdelki-fasada/",
                    "https://strbani.ru/fasad-doma/",
                    "https://dizlandshafta.ru/dizajn/doma/varianty-otdelki-fasada/",
                    "https://domstrousam.ru/sovremennye-materialy-dlya-fasada-doma-foto/",
                    "https://remstroiblog.ru/natalia/2017/03/07/10-materialov-dlya-otdelki-fasada-chastnogo-doma/",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b9+%d0%b4%d0%b5%d0%ba%d0%be%d1%80",
                    "https://artfasad.com/fasad/",
                    "https://www.grandline.ru/informaciya/fasad-chastnogo-doma-otdelka/",
                    "https://www.ivd.ru/dizajn-i-dekor/zagorodnyj-dom/kak-ukrasit-fasad-60-realnyx-variantov-22971",
                    "https://planken.guru/otdelka-i-montazh-fasadov/dekorativnaya-otdelka-fasadov-raznoobrazie-otdelochnyh-materialov.html",
                    "http://remoo.ru/fasad/fasady-domov",
                    "https://www.avito.ru/moskva_i_mo?q=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b9+%d0%b4%d0%b5%d0%ba%d0%be%d1%80",
                    "https://dekofasad.ru/decor.html",
                    "https://kronotech.ru/fasadnye-raboty/otdelka-fasada"
                ]
            ],
            "декоративная штукатурка для фасадов" => [
                "sites" => [
                    "https://leroymerlin.ru/catalogue/shtukaturki/dekorativnye-shtukaturki-dlya-fasadov/",
                    "https://st-par.ru/info/stati-o-sukhikh-smesyakh/dekorativnaya-fasadnaya-shtukaturka-vidy-po-sostavu-i-fakture-tekhnologiya-naneseniya/",
                    "https://www.ozon.ru/category/dekorativnaya-shtukaturka-dlya-fasadov/",
                    "https://market.yandex.ru/search?text=%d0%b4%d0%b5%d0%ba%d0%be%d1%80%d0%b0%d1%82%d0%b8%d0%b2%d0%bd%d0%b0%d1%8f%20%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d0%b0%d1%8f%20%d0%b4%d0%bb%d1%8f%20%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%bd%d1%8b%d1%85%20%d1%80%d0%b0%d0%b1%d0%be%d1%82%20%d1%86%d0%b5%d0%bd%d0%b0",
                    "https://m-strana.ru/design/dekorativnaya-shtukaturka-dlya-fasada-doma-vidy-i-osobennosti/",
                    "https://moscow.petrovich.ru/catalog/6654/fasadnaya-dekorativnaya-shtukaturka/",
                    "https://www.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/fasadnaya/",
                    "https://stroy-podskazka.ru/shtukaturka/fasadnaya-dekorativnaya/",
                    "https://expert-deco.ru/catalog/fasadnaya-shtukaturka/",
                    "https://glavsnab.net/dekorativnaya-fasadnaya-shtukaturka",
                    "https://vgtkraska.ru/katalog/dekor?page=2",
                    "https://dekoriko.ru/shtukaturka/fasadnaya-dekorativnaya/",
                    "https://dekormos.ru/35-fasadnye-shtukaturki",
                    "https://msk.blizko.ru/predl/construction/decoration/smesi/shtukaturki_dekorativny/f:33528_dlia-fasadov",
                    "https://sanmarco-vernici.ru/fasadnye-shtukaturki/",
                    "https://abk-fasad.ru/catalog/uteplenie-fasadov/decorative-shtukaturki",
                    "https://dessa-decor.ru/catalog/fasadnye_shtukaturki/",
                    "https://www.baufasad.ru/catalog/dekorativnaya_shtukaturka_dlya_mokrogo_fasada/",
                    "https://ok7.ru/fasadnaya_shtukaturka/",
                    "https://vetonit.com/blog/vse-pro-shtukaturki/fasadnaya-dekorativnaya-shtukaturka-vidy-i-tekhnologiya-rabot"
                ]
            ],
            "декоративная штукатурка короед цена" => [
                "sites" => [
                    "https://leroymerlin.ru/catalogue/shtukaturki/koroed/",
                    "https://market.yandex.ru/search?text=%d0%b4%d0%b5%d0%ba%d0%be%d1%80%d0%b0%d1%82%d0%b8%d0%b2%d0%bd%d0%b0%d1%8f%20%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4%20%d1%86%d0%b5%d0%bd%d0%b0",
                    "https://www.ozon.ru/category/shtukaturka-koroed/",
                    "https://www.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/koroed/",
                    "https://www.avito.ru/moskva/dlya_doma_i_dachi?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                    "https://st-par.ru/catalog/dekorativnye-shtukaturki/koroed/",
                    "https://www.stroyportal.ru/catalog/section-shtukaturka-koroed-7581/",
                    "https://moskva.regmarkets.ru/shtukaturka-koroed/",
                    "https://moscow.petrovich.ru/catalog/6654/dekorativnaya-shtukaturka-koroed/",
                    "https://www.mirkrasok.ru/catalog/shtukaturki_dekorativnye_i_fakturnye_kraski-effekt_koroed/work_type-is-naruzhnye_raboty/",
                    "https://www.baufasad.ru/catalog/dekorativnaya_shtukaturka_dlya_mokrogo_fasada/filter/texture-is-koroed/",
                    "https://msk.pulscen.ru/price/110514-shtukaturka/f:62056_dlia-fasada&62057_koroied",
                    "https://www.strd.ru/suhie_smesi/dekorativnie_stukaturki/koroed/",
                    "https://msk.blizko.ru/predl/construction/decoration/smesi/shtukaturki_dekorativny/f:166_koroied",
                    "https://www.gipsoplita.ru/otdelochnye-materialy/dekorativnaja-shtukaturka/shtukaturka-koroed/",
                    "https://glavsnab.net/shtukaturka-koroed",
                    "https://kraskitorg.ru/collection/fakturnaya-shtukaturka-koroed",
                    "https://arhitektor.ru/s-shtukaturka-koroed/",
                    "https://www.sdvor.com/moscow/s/shtukaturka-dekorativnaja-koroed21",
                    "https://bau-store.ru/stroitelnyye-materialy/shtukaturka-koroed/"
                ]
            ],
            "короед воронеж" => [
                "sites" => [
                    "https://voronezh.leroymerlin.ru/catalogue/shtukaturki/koroed/",
                    "https://www.avito.ru/voronezh?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                    "https://market.yandex.ru/search?text=%d0%ba%d1%83%d0%bf%d0%b8%d1%82%d1%8c%20%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d1%83%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4%20%d0%b2%20%d0%b2%d0%be%d1%80%d0%be%d0%bd%d0%b5%d0%b6%d0%b5",
                    "https://voronezh.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/koroed/",
                    "https://voronezh.regmarkets.ru/shtukaturka-koroed/",
                    "https://kraski36.ru/shtukaturka-koroed-voronezh/",
                    "https://uslugi.yandex.ru/193-voronezh/category?text=%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                    "https://voronezh.stroyportal.ru/catalog/section-shtukaturka-koroed-7581/",
                    "http://fasad36.ru/catalog/koroed/",
                    "https://voronezh.pulscen.ru/price/110514-shtukaturka/f:62057_koroied",
                    "https://stroidom36.ru/shtukaturka-koroed/",
                    "https://lidecor.ru/category/pokrytiya-koroed/",
                    "https://36-fasad.ru/nashi-uslugi/otdelka-dekorativnoj-shtukaturkoj",
                    "https://voronezh.satom.ru/k/dekorativnye-shtukaturki-koroed/",
                    "https://voronezh.blizko.ru/predl/construction/decoration/smesi/shtukaturki_dekorativny/f:166_koroied",
                    "https://voronezh.dommalera.ru/catalog/materialy_dlya_dekora/shtukaturki_dekorativnye_1/koroed_1/",
                    "http://stroitelnye-materialy-v-voronezhe.ru/shtukaturka-koroed",
                    "https://www.ozon.ru/category/shtukaturki-koroed/",
                    "http://xn--b1adccftyeadasf.xn--p1ai/shtukaturki-i-gruntovki-76/shtukaturka/",
                    "https://www.castorama.ru/catalogsearch/result/?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4"
                ]
            ],
            "короед цена" => [
                "sites" => [
                    "https://leroymerlin.ru/catalogue/shtukaturki/koroed/",
                    "https://market.yandex.ru/search?text=%d0%b4%d0%b5%d0%ba%d0%be%d1%80%d0%b0%d1%82%d0%b8%d0%b2%d0%bd%d0%b0%d1%8f%20%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4%20%d0%b4%d0%bb%d1%8f%20%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%bd%d1%8b%d1%85%20%d1%80%d0%b0%d0%b1%d0%be%d1%82%20%d1%86%d0%b5%d0%bd%d0%b0",
                    "https://www.ozon.ru/category/shtukaturka-koroed/",
                    "https://www.avito.ru/moskva/dlya_doma_i_dachi?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                    "https://moscow.petrovich.ru/catalog/6654/dekorativnaya-shtukaturka-koroed/",
                    "https://www.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/koroed/",
                    "https://www.stroyportal.ru/catalog/section-shtukaturka-koroed-7581/",
                    "https://moskva.regmarkets.ru/shtukaturka-koroed/",
                    "https://st-par.ru/catalog/dekorativnye-shtukaturki/koroed/",
                    "https://www.mirkrasok.ru/catalog/shtukaturki_dekorativnye_i_fakturnye_kraski-effekt_koroed/",
                    "https://msk.pulscen.ru/price/110514-shtukaturka/f:62057_koroied",
                    "https://msk.blizko.ru/predl/construction/decoration/smesi/shtukaturki_dekorativny/f:166_koroied",
                    "https://www.sdvor.com/moscow/s/shtukaturka-dekorativnaja-koroed21",
                    "https://glavsnab.net/shtukaturka-koroed",
                    "https://www.strd.ru/suhie_smesi/dekorativnie_stukaturki/koroed/",
                    "https://moskva.satom.ru/k/dekorativnye-shtukaturki-koroed/",
                    "https://www.gipsoplita.ru/otdelochnye-materialy/dekorativnaja-shtukaturka/shtukaturka-koroed/",
                    "https://bau-store.ru/stroitelnyye-materialy/shtukaturka-koroed/",
                    "https://moscow.promportal.su/tags/19407/shtukaturka-koroed/",
                    "https://moskeram.ru/catalog/sukhie_smesi/shtukaturka_fasadnaya/filter/fasadnaya_shtukaturka_koroyed/"
                ]
            ],
            "короед штукатурка" => [
                "sites" => [
                    "https://leroymerlin.ru/catalogue/shtukaturki/koroed/",
                    "https://market.yandex.ru/search?text=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                    "https://www.ozon.ru/category/shtukaturki-koroed/",
                    "https://dg-home.ru/blog/shtukaturka-koroed-v-dizajne_b655130/",
                    "https://www.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/koroed/",
                    "https://www.ivd.ru/stroitelstvo-i-remont/otdelocnye-materialy/nanesenie-dekorativnoj-shtukaturki-koroed-osnovnye-etapy-raboty-38491",
                    "https://moscow.petrovich.ru/catalog/6654/dekorativnaya-shtukaturka-koroed/",
                    "https://teplogalaxy.ru/shtukaturka-koroed/",
                    "https://dzen.ru/media/goodwillstroi/dekorativnaia-shtukaturka-koroed-vidy-i-sostav-tehnologiia-5f9951dfbaf78e79e76abd64",
                    "https://dekoriko.ru/shtukaturka/koroed/",
                    "https://m-strana.ru/articles/kak-nanosit-koroed-na-fasad/",
                    "https://moskva.regmarkets.ru/shtukaturka-koroed/",
                    "https://www.mirkrasok.ru/catalog/shtukaturki_dekorativnye_i_fakturnye_kraski-effekt_koroed/work_type-is-naruzhnye_raboty/",
                    "https://www.stroyportal.ru/catalog/section-shtukaturka-koroed-7581/",
                    "https://hozsektor.com/shtukaturka-koroed-foto-video-harakteristika-dostoinstva-i-nedostatki",
                    "https://st-par.ru/catalog/dekorativnye-shtukaturki/koroed/",
                    "https://www.avito.ru/moskva?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                    "https://lafoy.ru/shtukaturka-koroed-50-foto-748",
                    "https://kronotech.ru/publications/nanosit-dekorativnuyu-shtukaturku-koroed",
                    "https://trizio.ru/shtukaturka-koroed-50-foto-834"
                ]
            ],
            "короед штукатурка воронеж" => [
                "sites" => [
                    "https://voronezh.leroymerlin.ru/catalogue/shtukaturki/dekorativnye-shtukaturki-koroed/",
                    "https://www.avito.ru/voronezh?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                    "https://market.yandex.ru/search?text=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4%20%d0%b2%20%d0%b2%d0%be%d1%80%d0%be%d0%bd%d0%b5%d0%b6%d0%b5%20%d1%86%d0%b5%d0%bd%d1%8b",
                    "https://voronezh.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/koroed/",
                    "https://voronezh.regmarkets.ru/shtukaturka-koroed/",
                    "https://voronezh.stroyportal.ru/catalog/section-shtukaturka-koroed-7581/",
                    "https://kraski36.ru/shtukaturka-koroed-voronezh/",
                    "https://voronezh.pulscen.ru/price/110514-shtukaturka/f:62057_koroied",
                    "https://voronezh.dommalera.ru/catalog/materialy_dlya_dekora/shtukaturki_dekorativnye_1/koroed_1/",
                    "https://voronezh.blizko.ru/predl/construction/decoration/smesi/shtukaturki_dekorativny/f:166_koroied",
                    "http://fasad36.ru/catalog/koroed/",
                    "https://www.ozon.ru/category/shtukaturki-koroed/",
                    "https://www.castorama.ru/catalogsearch/result/?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                    "https://voronezh.satom.ru/k/dekorativnye-shtukaturki-koroed/",
                    "http://stroitelnye-materialy-v-voronezhe.ru/shtukaturka-koroed",
                    "https://lidecor.ru/category/pokrytiya-koroed/",
                    "https://voronezh.yavitrina.ru/shtukaturka-koroed",
                    "https://stroybaza-vrn.ru/katalog/suhie-stroitelmie-smesi/%d0%b4%d0%b5%d0%ba%d0%be%d1%80%d0%b0%d1%82%d0%b8%d0%b2%d0%bd%d0%b0%d1%8f-%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0",
                    "https://craftflex.ru/catalog/sukhie-stroitelnye-smesi/koroed/",
                    "https://voronezh.compumir.ru/shtukaturka-koroed"
                ]
            ],
            "короед штукатурка цена" => [
                "sites" => [
                    "https://leroymerlin.ru/catalogue/shtukaturki/koroed/",
                    "https://market.yandex.ru/search?text=%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4%20%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d1%86%d0%b5%d0%bd%d0%b0%20%d0%b7%d0%b0%20%d0%bc%d0%b5%d1%88%d0%be%d0%ba%20%d0%bc%d0%be%d1%81%d0%ba%d0%b2%d0%b0",
                    "https://www.ozon.ru/category/shtukaturki-koroed/",
                    "https://www.avito.ru/moskva/dlya_doma_i_dachi?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                    "https://moskva.regmarkets.ru/shtukaturka-koroed/",
                    "https://www.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/koroed/",
                    "https://moscow.petrovich.ru/catalog/6654/dekorativnaya-shtukaturka-koroed/",
                    "https://www.stroyportal.ru/catalog/section-shtukaturka-koroed-7581/",
                    "https://st-par.ru/catalog/dekorativnye-shtukaturki/koroed/",
                    "https://msk.pulscen.ru/price/110514-shtukaturka/f:62057_koroied",
                    "https://www.mirkrasok.ru/catalog/shtukaturki_dekorativnye_i_fakturnye_kraski-effekt_koroed/",
                    "https://www.strd.ru/suhie_smesi/dekorativnie_stukaturki/koroed/",
                    "https://msk.blizko.ru/predl/construction/decoration/smesi/shtukaturki_dekorativny/f:34399_koroied&68946_dlia-naruzhnykh-rabot",
                    "https://glavsnab.net/shtukaturka-koroed",
                    "https://moskva.satom.ru/k/dekorativnye-shtukaturki-koroed/",
                    "https://kraskitorg.ru/collection/fakturnaya-shtukaturka-koroed",
                    "https://www.baustof.ru/catalog/koroed/",
                    "https://www.dommalera.ru/catalog/materialy_dlya_dekora/shtukaturki_dekorativnye_1/s_effektom_koroeda/",
                    "https://bau-store.ru/stroitelnyye-materialy/shtukaturka-koroed/",
                    "https://snab-rezerv.ru/products/shtukaturka-koroed"
                ]
            ],
            "короед штукатурка цена работы за м2 воронеж" => [
                "sites" => [
                    "https://www.avito.ru/voronezh/predlozheniya_uslug?q=%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                    "https://uslugi.yandex.ru/193-voronezh/category?text=%d0%be%d1%82%d0%b4%d0%b5%d0%bb%d0%ba%d0%b0+%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4%d0%be%d0%bc+%d1%86%d0%b5%d0%bd%d0%b0+%d1%80%d0%b0%d0%b1%d0%be%d1%82%d1%8b+%d0%b7%d0%b0+%d0%bc2",
                    "https://voronezh.myguru.ru/services/fasadnye-raboty/nanesenie-shtukaturki-koroed/",
                    "https://stroidom36.ru/shtukaturka-koroed/",
                    "https://36-fasad.ru/nashi-uslugi/otdelka-dekorativnoj-shtukaturkoj",
                    "https://uslugio.com/voronezh/1/9/koroed-shtukaturka",
                    "https://voronezh.remont-kvartir-klyuch.ru/prices-otdelochnye-raboty/",
                    "https://voronezh.trade-services.ru/services/fasadnye-raboty/shtukaturka-koroed/",
                    "https://vrn.masterdel.ru/master/shtukaturka-koroed/",
                    "https://vrn.profi.ru/remont/fasadnye-raboty-koroedom/",
                    "https://dekor36.com/otdelka-fasadov-shtukaturkoj.html",
                    "https://remont-otdelka-36.ru/prices",
                    "https://www.zakazremonta.ru/master/voronezh/shtukaturka/shtukaturka_koroed_tsena_raboty_za_m/",
                    "https://stranauslug.ru/voronezh/fasad-koroed/",
                    "http://fasad36.ru/services/stoimost-uslug/",
                    "http://teplofasad36.ru/prajs-list",
                    "https://kronvest.net/voronezh/plaster-fasad",
                    "http://voronezh.alpbond.org/koroed-shtukaturka-cena-raboty/",
                    "https://zoon.ru/voronezh/m/shtukaturivanie_fasada/",
                    "https://portaluslug.ru/ru_voronezhskaya-oblast/s-koroed-shtukaturka-7586"
                ]
            ],
            "купить вату для утепления фасада" => [
                "sites" => [
                    "https://leroymerlin.ru/catalogue/teploizolyaciya/mineralnaya-vata/",
                    "https://market.yandex.ru/search?text=%d0%ba%d0%b0%d0%bc%d0%b5%d0%bd%d0%bd%d0%b0%d1%8f%20%d0%b2%d0%b0%d1%82%d0%b0%20%d0%b4%d0%bb%d1%8f%20%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d1%8f%20%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0%20%d1%86%d0%b5%d0%bd%d0%b0%20%d0%b2%20%d0%bc%d0%be%d1%81%d0%ba%d0%b2%d0%b5",
                    "https://shop.tn.ru/teploizoljacija/kamennaja-vata-dlya-fasadov",
                    "https://www.ozon.ru/category/teploizolyatsiya-mineralnaya-vata/",
                    "https://www.avito.ru/moskva_i_mo?q=%d0%bc%d0%b8%d0%bd%d0%b2%d0%b0%d1%82%d0%b0+%d0%b4%d0%bb%d1%8f+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
                    "https://st-par.ru/catalog/mineralnaya_vata/dlya_fasada/",
                    "https://glavsnab.net/bazaltovaya-vata-dlya-fasada",
                    "https://www.stroyportal.ru/catalog/price-kamennaya-vata-dlya-utepleniya-fasada-56517/",
                    "https://ism-mos.ru/tag/uteplenie-fasada/pod-shtukaturku/",
                    "https://www.strd.ru/uteplitel/mineralnaya-vata/",
                    "https://moscow.petrovich.ru/catalog/11935/",
                    "https://abk-fasad.ru/catalog/uteplenie-fasadov/insulation",
                    "https://shopmat.ru/teploizolyaciya/bazaltovyy-uteplitel/plity-vata-dlya-utepleniya-fasada/",
                    "https://msk.blizko.ru/predl/construction/building/insulant/heatinsulation/100821/f:38509_dlia-fasadov",
                    "https://shop.rockwool.ru/teploizolyaciya/dlya-fasada-doma.html",
                    "https://www.sdvor.com/moscow/category/mineralnaja-vata-5532",
                    "https://moskva.regmarkets.ru/vata-dlya-utepleniya-fasada/",
                    "https://www.tstn.ru/shop/teploizolyatsiya/kamennaya-vata/",
                    "https://www.vseinstrumenti.ru/stroitelnye-materialy/izolyatsionnye/utepliteli/mineralnaya-vata/",
                    "https://uteplitel-shop.ru/kamennaja-vata/kamennaja-vata-pod-shtukaturku/"
                ]
            ],
            "материалы для отделки фасада" => [
                "sites" => [
                    "https://m-strana.ru/articles/chem-nedorogo-otdelat-fasad-doma/",
                    "https://www.forumhouse.ru/journal/themes/66-varianty-otdelki-fasada-populyarnye-vidy-konstruktiv-osobennosti",
                    "https://remstroiblog.ru/natalia/2017/03/07/10-materialov-dlya-otdelki-fasada-chastnogo-doma/",
                    "https://market.yandex.ru/search?text=%d0%bc%d0%b0%d1%82%d0%b5%d1%80%d0%b8%d0%b0%d0%bb%d1%8b%20%d0%b4%d0%bb%d1%8f%20%d0%be%d1%82%d0%b4%d0%b5%d0%bb%d0%ba%d0%b8%20%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
                    "https://stroy-podskazka.ru/materialy/fasadnye-luchshaya-oblicovka/",
                    "https://realty.rbc.ru/news/61a7952a9a7947039b719b08",
                    "https://domstrousam.ru/sovremennye-materialy-dlya-fasada-doma-foto/",
                    "https://www.alta-profil.ru/client-center/articles/sravnenie-novinok-na-rynke-fasadnyh-materialov/",
                    "https://www.grandline.ru/informaciya/fasad-chastnogo-doma-otdelka/",
                    "https://vekroof.ru/articles/luchshie-materialy-dlya-otdelki-fasada/",
                    "https://leroymerlin.ru/catalogue/fasadnye-paneli/",
                    "https://www.strd.ru/fasadi/",
                    "https://domof.ru/articles/kakoy-material-vybrat-dlya-otdelki-fasada-zdaniya/",
                    "https://www.ozon.ru/category/otdelochnye-materialy-dlya-fasada/",
                    "https://alfakrov.com/blog/sovety_pokupatelyam/chem_obshit_dom_snaruzhi_deshevo_i_krasivo_foto_tseny_kharakteristiki_i_top_7_luchshikh_materialov/",
                    "https://kronotech.ru/publications/otdelka-fasada-chastnogo-doma",
                    "http://remoo.ru/fasad/fasady-domov",
                    "https://dzen.ru/media/tablichnik/luchshie-materialy-dlia-otdelki-fasada-chastnogo-doma-5f0ffd317e2b585adad67632",
                    "https://everest-dom.com/blog/materialy-dlya-otdelki-fasada",
                    "https://zod07.ru/statji/kak-vybrat-fasadnye-materialy-dlya-otdelki-doma-snaruzhi"
                ]
            ],
            "материалы для отделки фасада дома" => [
                "sites" => [
                    "https://m-strana.ru/articles/chem-nedorogo-otdelat-fasad-doma/",
                    "https://www.forumhouse.ru/journal/themes/66-varianty-otdelki-fasada-populyarnye-vidy-konstruktiv-osobennosti",
                    "https://remstroiblog.ru/natalia/2017/03/07/10-materialov-dlya-otdelki-fasada-chastnogo-doma/",
                    "https://stroy-podskazka.ru/materialy/fasadnye-luchshaya-oblicovka/",
                    "https://domstrousam.ru/sovremennye-materialy-dlya-fasada-doma-foto/",
                    "https://www.alta-profil.ru/client-center/articles/sravnenie-novinok-na-rynke-fasadnyh-materialov/",
                    "https://markakachestva.ru/rating-of/2247-luchshie-materialy-dlja-oblicovki-fasada.html",
                    "https://market.yandex.ru/search?text=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b5%20%d0%bc%d0%b0%d1%82%d0%b5%d1%80%d0%b8%d0%b0%d0%bb%d1%8b%20%d0%b4%d0%bb%d1%8f%20%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%bd%d0%be%d0%b9%20%d0%be%d0%b1%d0%bb%d0%b8%d1%86%d0%be%d0%b2%d0%ba%d0%b8%20%d0%b4%d0%be%d0%bc%d0%b0",
                    "https://realty.rbc.ru/news/61a7952a9a7947039b719b08",
                    "https://vekroof.ru/articles/luchshie-materialy-dlya-otdelki-fasada/",
                    "https://www.grandline.ru/informaciya/fasad-chastnogo-doma-otdelka/",
                    "https://cvet-dom.ru/dachnyy-dom/top-materialov-dlya-otdelki-fasada-dom",
                    "https://srbu.ru/otdelochnye-materialy/1950-varianty-otdelki-fasada-chastnogo-doma.html",
                    "https://dzen.ru/media/tablichnik/luchshie-materialy-dlia-otdelki-fasada-chastnogo-doma-5f0ffd317e2b585adad67632",
                    "http://remoo.ru/fasad/fasady-domov",
                    "https://dizlandshafta.ru/dizajn/doma/varianty-otdelki-fasada/",
                    "https://design-homes.ru/stroitelstvo-i-remont/nedorogo-fasad-doma",
                    "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/otdelochnye-materialy-dlya-fasadov-chastnyx-domov.html",
                    "https://domof.ru/articles/kakoy-material-vybrat-dlya-otdelki-fasada-zdaniya/",
                    "https://alfakrov.com/blog/sovety_pokupatelyam/chem_obshit_dom_snaruzhi_deshevo_i_krasivo_foto_tseny_kharakteristiki_i_top_7_luchshikh_materialov/"
                ]
            ],
            "материалы для отделки фасадов частных домов" => [
                "sites" => [
                    "https://m-strana.ru/articles/chem-nedorogo-otdelat-fasad-doma/",
                    "https://www.forumhouse.ru/journal/themes/66-varianty-otdelki-fasada-populyarnye-vidy-konstruktiv-osobennosti",
                    "https://stroy-podskazka.ru/materialy/fasadnye-luchshaya-oblicovka/",
                    "https://remstroiblog.ru/natalia/2017/03/07/10-materialov-dlya-otdelki-fasada-chastnogo-doma/",
                    "https://domstrousam.ru/sovremennye-materialy-dlya-fasada-doma-foto/",
                    "https://www.alta-profil.ru/client-center/articles/sravnenie-novinok-na-rynke-fasadnyh-materialov/",
                    "https://market.yandex.ru/search?text=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b5%20%d0%bc%d0%b0%d1%82%d0%b5%d1%80%d0%b8%d0%b0%d0%bb%d1%8b%20%d0%b4%d0%bb%d1%8f%20%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%bd%d0%be%d0%b9%20%d0%be%d0%b1%d0%bb%d0%b8%d1%86%d0%be%d0%b2%d0%ba%d0%b8%20%d0%b4%d0%be%d0%bc%d0%b0",
                    "https://srbu.ru/otdelochnye-materialy/1950-varianty-otdelki-fasada-chastnogo-doma.html",
                    "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/otdelochnye-materialy-dlya-fasadov-chastnyx-domov.html",
                    "https://dzen.ru/media/tablichnik/luchshie-materialy-dlia-otdelki-fasada-chastnogo-doma-5f0ffd317e2b585adad67632",
                    "https://www.grandline.ru/informaciya/fasad-chastnogo-doma-otdelka/",
                    "https://realty.rbc.ru/news/61a7952a9a7947039b719b08",
                    "https://vekroof.ru/articles/luchshie-materialy-dlya-otdelki-fasada/",
                    "https://dizlandshafta.ru/dizajn/doma/varianty-otdelki-fasada/",
                    "http://remoo.ru/fasad/fasady-domov",
                    "https://markakachestva.ru/rating-of/2247-luchshie-materialy-dlja-oblicovki-fasada.html",
                    "https://www.tn.ru/journal/chem-otdelat-fasad-chastnogo-doma-podrobnyy-gayd-po-populyarnym-materialam/",
                    "https://alfakrov.com/blog/sovety_pokupatelyam/chem_obshit_dom_snaruzhi_deshevo_i_krasivo_foto_tseny_kharakteristiki_i_top_7_luchshikh_materialov/",
                    "https://kronotech.ru/publications/otdelka-fasada-chastnogo-doma",
                    "https://www.bazaznaniyst.ru/varianty-krasivoj-i-deshevoj-obshivki-doma-snaruzhi/"
                ]
            ],
            "материалы для фасадной отделки дома" => [
                "sites" => [
                    "https://m-strana.ru/articles/chem-nedorogo-otdelat-fasad-doma/",
                    "https://stroy-podskazka.ru/materialy/fasadnye-luchshaya-oblicovka/",
                    "https://www.forumhouse.ru/journal/themes/66-varianty-otdelki-fasada-populyarnye-vidy-konstruktiv-osobennosti",
                    "https://remstroiblog.ru/natalia/2017/03/07/10-materialov-dlya-otdelki-fasada-chastnogo-doma/",
                    "https://domstrousam.ru/sovremennye-materialy-dlya-fasada-doma-foto/",
                    "https://www.alta-profil.ru/client-center/articles/sravnenie-novinok-na-rynke-fasadnyh-materialov/",
                    "https://market.yandex.ru/search?text=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b5%20%d0%bc%d0%b0%d1%82%d0%b5%d1%80%d0%b8%d0%b0%d0%bb%d1%8b%20%d0%b4%d0%bb%d1%8f%20%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%bd%d0%be%d0%b9%20%d0%be%d0%b1%d0%bb%d0%b8%d1%86%d0%be%d0%b2%d0%ba%d0%b8%20%d0%b4%d0%be%d0%bc%d0%b0",
                    "https://realty.rbc.ru/news/61a7952a9a7947039b719b08",
                    "https://vekroof.ru/articles/luchshie-materialy-dlya-otdelki-fasada/",
                    "https://www.grandline.ru/informaciya/fasad-chastnogo-doma-otdelka/",
                    "https://markakachestva.ru/rating-of/2247-luchshie-materialy-dlja-oblicovki-fasada.html",
                    "https://srbu.ru/otdelochnye-materialy/1950-varianty-otdelki-fasada-chastnogo-doma.html",
                    "https://domof.ru/articles/kakoy-material-vybrat-dlya-otdelki-fasada-zdaniya/",
                    "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/otdelochnye-materialy-dlya-fasadov-chastnyx-domov.html",
                    "https://dizlandshafta.ru/dizajn/doma/varianty-otdelki-fasada/",
                    "https://dzen.ru/media/tablichnik/luchshie-materialy-dlia-otdelki-fasada-chastnogo-doma-5f0ffd317e2b585adad67632",
                    "http://remoo.ru/fasad/fasady-domov",
                    "https://www.bazaznaniyst.ru/varianty-krasivoj-i-deshevoj-obshivki-doma-snaruzhi/",
                    "https://alfakrov.com/blog/sovety_pokupatelyam/chem_obshit_dom_snaruzhi_deshevo_i_krasivo_foto_tseny_kharakteristiki_i_top_7_luchshikh_materialov/",
                    "https://design-homes.ru/stroitelstvo-i-remont/nedorogo-fasad-doma"
                ]
            ],
            "материалы для фасадных работ" => [
                "sites" => [
                    "https://realty.rbc.ru/news/61a7952a9a7947039b719b08",
                    "https://m-strana.ru/articles/chem-nedorogo-otdelat-fasad-doma/",
                    "https://market.yandex.ru/search?text=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b5%20%d0%bc%d0%b0%d1%82%d0%b5%d1%80%d0%b8%d0%b0%d0%bb%d1%8b%20%d0%b4%d0%bb%d1%8f%20%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%bd%d0%be%d0%b9%20%d0%be%d0%b1%d0%bb%d0%b8%d1%86%d0%be%d0%b2%d0%ba%d0%b8%20%d0%b4%d0%be%d0%bc%d0%b0",
                    "https://remstroiblog.ru/natalia/2017/03/07/10-materialov-dlya-otdelki-fasada-chastnogo-doma/",
                    "https://www.forumhouse.ru/journal/themes/66-varianty-otdelki-fasada-populyarnye-vidy-konstruktiv-osobennosti",
                    "https://stroy-podskazka.ru/materialy/fasadnye-luchshaya-oblicovka/",
                    "https://domstrousam.ru/sovremennye-materialy-dlya-fasada-doma-foto/",
                    "https://vekroof.ru/articles/luchshie-materialy-dlya-otdelki-fasada/",
                    "https://leroymerlin.ru/catalogue/fasadnye-paneli/",
                    "https://psk-holding.ru/catalog/fasad/",
                    "https://www.ozon.ru/category/otdelochnye-materialy-dlya-fasada/",
                    "http://remoo.ru/fasad/fasady-domov",
                    "https://krishafasad.ru/shop/fasadnye-materialy/",
                    "https://www.grandline.ru/informaciya/fasad-chastnogo-doma-otdelka/",
                    "https://srbu.ru/otdelochnye-materialy/1950-varianty-otdelki-fasada-chastnogo-doma.html",
                    "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/naruzhnaya-otdelka-doma-varianty.html",
                    "https://www.alta-profil.ru/client-center/articles/sravnenie-novinok-na-rynke-fasadnyh-materialov/",
                    "https://mk4s.ru/fasadnye-materialy/",
                    "https://zod07.ru/statji/kak-vybrat-fasadnye-materialy-dlya-otdelki-doma-snaruzhi",
                    "https://www.strd.ru/fasadi/"
                ]
            ],
            "мокрый фасад" => [
                "sites" => [
                    "https://m-strana.ru/articles/chto-takoe-mokryy-fasad/",
                    "https://www.forumhouse.ru/journal/articles/4688-tehnologiya-mokryi-fasad",
                    "https://kronotech.ru/fasadnye-raboty/mokryy-fasad",
                    "https://stroy-podskazka.ru/materialy/mokryj-fasad/",
                    "https://fasad-exp.ru/uteplenie/mokryy-fasad-tekhnologiya.html",
                    "http://remoo.ru/fasad/mokryj-fasad-tekhnologiya",
                    "https://www.avito.ru/moskva?q=%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9+%d1%84%d0%b0%d1%81%d0%b0%d0%b4",
                    "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/texnologiya-utepleniya-mokryj-fasad.html",
                    "https://www.youtube.com/watch?v=g9lmk6gon2y",
                    "https://optimfasad.ru/mokryj-fasad",
                    "https://www.kp.ru/guide/mokryi-fasad.html",
                    "https://zen.yandex.ru/media/rmnt/mokryi-fasad-obzor-harakteristiki-i-osobennosti-montaja-6047a457a6c3965eb485ac17",
                    "https://frontfacade.com/vidy-materialov/shtukaturka/rukovodstvo-po-ustrojstvu-mokrogo-fasada.html",
                    "https://expertfasada.ru/fasad/mokryj-fasad/mokryj-fasad/",
                    "https://volgaproekt.ru/stati/eksterer/chto-takoe-mokryy-fasad-i-kak-ustroen-plyusy-i-minusy-primeneniya.html",
                    "https://spk-fasad.ru/stati/171-mokryy-fasad.html",
                    "https://v-teplo.ru/tekhnologiya-mokryj-fasad.html",
                    "https://stroyhelper.ru/mokryy-fasad/",
                    "https://www.fasadbau.com/mokrii-fasad/",
                    "https://mos-stroi-alians.ru/uslugi/fasadnye_raboty/mokryj-fasad/"
                ]
            ],
            "мокрый фасад воронеж" => [
                "sites" => [
                    "https://www.avito.ru/voronezh/predlozheniya_uslug?q=%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9+%d1%84%d0%b0%d1%81%d0%b0%d0%b4",
                    "https://dekor36.com/mokriy-fasad.html",
                    "https://uslugi.yandex.ru/193-voronezh/category?text=%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9+%d1%84%d0%b0%d1%81%d0%b0%d0%b4",
                    "http://fasad36.ru/services/mokryy-fasad/",
                    "https://36-fasad.ru/nashi-uslugi/mokryj-fasad",
                    "https://kronvest.net/voronezh/wet-fasad",
                    "https://vrn.profi.ru/remont/montazh-mokrogo-fasada/",
                    "http://xn--36-glchqd5adeocin.xn--p1ai/mokryi-fasad.html",
                    "https://voronezh.vse-podklyuch.ru/stroitelstvo/oblitsovka-fasadov/mokryy-fasad/",
                    "https://uslugio.com/voronezh/1/9/mokryy-fasad",
                    "http://teplofasad36.ru/morriy-fasad",
                    "https://sezrem.ru/mokryj-fasad/",
                    "http://index-fs.ru/otdelka-mokrym-fasadom",
                    "https://fasad-rem.ru/services/%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9-%d1%84%d0%b0%d1%81%d0%b0%d0%b4/",
                    "https://voronezh.urfomarket.ru/montazh_mokrogo_fasada_pod_klyuch.php",
                    "https://voronezh.stroyportal.ru/catalog/price-mokryy-fasad-6979/",
                    "https://rskpanorama.com/uslugi/otdelochnye-raboty/montazh-mokrogo-fasada/",
                    "https://art-fasad36.ru/technologies/mokryj-fasad",
                    "https://voronezh.leroymerlin.ru/catalogue/shtukaturki/mokryy-fasad-s-utepleniem/",
                    "https://www.remontnik.ru/voronezh/uteplenie_fasadov_mokryi_fasad/"
                ]
            ],
            "мокрый фасад стоимость работ" => [
                "sites" => [
                    "https://optimfasad.ru/mokryj-fasad-cena-rabot",
                    "https://www.avito.ru/moskva/uslugi?q=%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9+%d1%84%d0%b0%d1%81%d0%b0%d0%b4",
                    "https://kronotech.ru/prays-mokryy-fasad",
                    "http://www.n-dom.ru/uteplenie-fasada/mokryj-fasad-827",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9+%d1%84%d0%b0%d1%81%d0%b0%d0%b4",
                    "https://profi.ru/remont/montazh-mokrogo-fasada/",
                    "https://www.fasadbau.com/mokrii-fasad/",
                    "https://www.prof-fasady.ru/catalog/mokryj-fasad/",
                    "https://optimumbuilding.ru/mokryi-fasad",
                    "https://tsk-gr.ru/fasad/",
                    "https://msk-krovli.ru/fasadnye-raboty/mokryj-fasad/",
                    "https://lkgstroi.ru/stoimost/tehnologiya-mokryj-fasad/",
                    "https://www.stroyremfasad.ru/fasadnye-raboty/uteplenie-fasada/mokrogo/",
                    "https://betterstroy.ru/fasadnye-raboty/mokryj-fasad/",
                    "https://fasadrf.ru/mokryfasad/",
                    "https://alpbond.org/mokryj-fasad/",
                    "https://hidropro.ru/services/fasadnye-raboty/mokryy-fasad-stoimost-rabot/",
                    "https://topstroy-remont.ru/fasadnye-raboty/montazh-mokrogo-fasada",
                    "https://kronvest.net/wet-fasad",
                    "https://mos-stroi-alians.ru/uslugi/fasadnye_raboty/mokryj-fasad/"
                ]
            ],
            "мокрый фасад стоимость работ за м2" => [
                "sites" => [
                    "https://optimfasad.ru/mokryj-fasad-cena-rabot",
                    "https://kronotech.ru/prays-mokryy-fasad",
                    "http://www.n-dom.ru/uteplenie-fasada/mokryj-fasad-827",
                    "https://www.avito.ru/moskva/uslugi?q=%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9+%d1%84%d0%b0%d1%81%d0%b0%d0%b4",
                    "https://optimumbuilding.ru/fasadnye-raboty",
                    "https://profi.ru/remont/montazh-mokrogo-fasada/",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9+%d1%84%d0%b0%d1%81%d0%b0%d0%b4",
                    "https://tsk-gr.ru/fasad/",
                    "https://www.fasadbau.com/mokrii-fasad/",
                    "https://www.prof-fasady.ru/catalog/mokryj-fasad/",
                    "https://alpbond.org/mokryj-fasad/",
                    "https://fasadrf.ru/prays_list/",
                    "https://lkgstroi.ru/stoimost/tehnologiya-mokryj-fasad/",
                    "https://topstroy-remont.ru/fasadnye-raboty/montazh-mokrogo-fasada",
                    "https://msk-krovli.ru/fasadnye-raboty/mokryj-fasad/",
                    "https://www.stroyremfasad.ru/fasadnye-raboty/uteplenie-fasada/mokrogo/",
                    "https://hidropro.ru/services/fasadnye-raboty/mokryy-fasad-stoimost-rabot/",
                    "https://betterstroy.ru/fasadnye-raboty/mokryj-fasad/",
                    "https://zod07.ru/fasadnye-raboty/otdelka-i-shtukaturka-fasadov/mokryj-fasad-pod-klyuch-v-moskve-tsena",
                    "https://mos-stroi-alians.ru/uslugi/fasadnye_raboty/mokryj-fasad/"
                ]
            ],
            "мокрый фасад цена" => [
                "sites" => [
                    "https://optimfasad.ru/mokryj-fasad-cena-rabot",
                    "https://www.avito.ru/moskva/uslugi?q=%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9+%d1%84%d0%b0%d1%81%d0%b0%d0%b4",
                    "http://www.n-dom.ru/uteplenie-fasada/mokryj-fasad-827",
                    "https://kronotech.ru/prays-mokryy-fasad",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9+%d1%84%d0%b0%d1%81%d0%b0%d0%b4",
                    "https://tsk-gr.ru/fasad/",
                    "https://www.prof-fasady.ru/catalog/mokryj-fasad/",
                    "https://lkgstroi.ru/stoimost/tehnologiya-mokryj-fasad/",
                    "https://betterstroy.ru/fasadnye-raboty/mokryj-fasad/",
                    "https://fasadrf.ru/mokryfasad/",
                    "https://www.fasadbau.com/mokrii-fasad/",
                    "https://mos-stroi-alians.ru/uslugi/fasadnye_raboty/mokryj-fasad/",
                    "https://www.stroyremfasad.ru/fasadnye-raboty/uteplenie-fasada/mokrogo/",
                    "https://optimumbuilding.ru/mokryi-fasad",
                    "https://alpbond.org/mokryj-fasad/",
                    "https://profi.ru/remont/montazh-mokrogo-fasada/",
                    "https://msk-krovli.ru/fasadnye-raboty/mokryj-fasad/",
                    "https://topstroy-remont.ru/fasadnye-raboty/montazh-mokrogo-fasada",
                    "https://hidropro.ru/services/fasadnye-raboty/mokryy-fasad-stoimost-rabot/",
                    "https://www.remontnik.ru/moskva/uteplenie_fasadov_mokryi_fasad/"
                ]
            ],
            "мокрый фасад цена за м2" => [
                "sites" => [
                    "https://optimfasad.ru/mokryj-fasad-cena-rabot",
                    "https://www.avito.ru/moskva/uslugi?q=%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9+%d1%84%d0%b0%d1%81%d0%b0%d0%b4",
                    "https://kronotech.ru/prays-mokryy-fasad",
                    "http://www.n-dom.ru/uteplenie-fasada/mokryj-fasad-827",
                    "https://www.prof-fasady.ru/catalog/mokryj-fasad/",
                    "https://tsk-gr.ru/fasad/",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d1%80%d0%b0%d1%81%d1%86%d0%b5%d0%bd%d0%ba%d0%b8%20%d0%bd%d0%b0%20%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9%20%d1%84%d0%b0%d1%81%d0%b0%d0%b4",
                    "https://lkgstroi.ru/stoimost/tehnologiya-mokryj-fasad/",
                    "https://profi.ru/remont/montazh-mokrogo-fasada/",
                    "https://fasadrf.ru/mokryfasad/",
                    "https://www.fasadbau.com/mokrii-fasad/",
                    "https://optimumbuilding.ru/mokryi-fasad",
                    "https://msk-krovli.ru/fasadnye-raboty/mokryj-fasad/",
                    "https://alpbond.org/mokryj-fasad/",
                    "https://www.stroyremfasad.ru/fasadnye-raboty/uteplenie-fasada/mokrogo/",
                    "https://betterstroy.ru/fasadnye-raboty/mokryj-fasad/",
                    "https://mos-stroi-alians.ru/uslugi/fasadnye_raboty/mokryj-fasad/",
                    "https://topstroy-remont.ru/fasadnye-raboty/montazh-mokrogo-fasada",
                    "https://kronvest.net/wet-fasad",
                    "https://zod07.ru/fasadnye-raboty/otdelka-i-shtukaturka-fasadov/mokryj-fasad-pod-klyuch-v-moskve-tsena"
                ]
            ],
            "мокрый фасад цена за метр" => [
                "sites" => [
                    "https://optimfasad.ru/mokryj-fasad-cena-rabot",
                    "https://www.avito.ru/moskva/uslugi?q=%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9+%d1%84%d0%b0%d1%81%d0%b0%d0%b4",
                    "https://kronotech.ru/prays-mokryy-fasad",
                    "http://www.n-dom.ru/uteplenie-fasada/mokryj-fasad-827",
                    "https://optimumbuilding.ru/fasadnye-raboty",
                    "https://fasadrf.ru/mokryfasad/",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9+%d1%84%d0%b0%d1%81%d0%b0%d0%b4",
                    "https://tsk-gr.ru/fasad/",
                    "https://www.prof-fasady.ru/catalog/mokryj-fasad/",
                    "https://lkgstroi.ru/stoimost/tehnologiya-mokryj-fasad/",
                    "https://msk-krovli.ru/fasadnye-raboty/mokryj-fasad/",
                    "https://betterstroy.ru/fasadnye-raboty/mokryj-fasad/",
                    "https://mos-stroi-alians.ru/uslugi/fasadnye_raboty/mokryj-fasad/",
                    "https://www.stroyremfasad.ru/fasadnye-raboty/uteplenie-fasada/mokrogo/",
                    "https://hidropro.ru/services/fasadnye-raboty/mokryy-fasad-stoimost-rabot/",
                    "https://alpbond.org/mokryj-fasad/",
                    "https://profi.ru/remont/montazh-mokrogo-fasada/",
                    "https://zod07.ru/fasadnye-raboty/otdelka-i-shtukaturka-fasadov/mokryj-fasad-pod-klyuch-v-moskve-tsena",
                    "https://topstroy-remont.ru/fasadnye-raboty/montazh-mokrogo-fasada",
                    "https://www.fasadbau.com/mokrii-fasad/"
                ]
            ],
            "монтаж мокрого фасада цена" => [
                "sites" => [
                    "http://www.n-dom.ru/uteplenie-fasada/mokryj-fasad-827",
                    "https://www.avito.ru/moskva?q=%d0%bc%d0%be%d0%bd%d1%82%d0%b0%d0%b6+%d0%bc%d0%be%d0%ba%d1%80%d0%be%d0%b3%d0%be+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
                    "https://kronotech.ru/prays-mokryy-fasad",
                    "https://optimfasad.ru/mokryj-fasad-cena-rabot",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d1%80%d0%b0%d1%81%d1%86%d0%b5%d0%bd%d0%ba%d0%b8%20%d0%bd%d0%b0%20%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9%20%d1%84%d0%b0%d1%81%d0%b0%d0%b4",
                    "https://www.prof-fasady.ru/catalog/mokryj-fasad/",
                    "https://www.fasadbau.com/mokrii-fasad/",
                    "https://fasadrf.ru/mokryfasad/",
                    "https://profi.ru/remont/montazh-mokrogo-fasada/",
                    "https://tsk-gr.ru/fasad/",
                    "https://www.stroyremfasad.ru/fasadnye-raboty/uteplenie-fasada/mokrogo/",
                    "https://optimumbuilding.ru/mokryi-fasad",
                    "https://topstroy-remont.ru/fasadnye-raboty/montazh-mokrogo-fasada",
                    "https://betterstroy.ru/fasadnye-raboty/mokryj-fasad/",
                    "https://mos-stroi-alians.ru/uslugi/fasadnye_raboty/mokryj-fasad/",
                    "https://alpbond.org/mokryj-fasad/",
                    "https://msk-krovli.ru/fasadnye-raboty/mokryj-fasad/",
                    "https://lkgstroi.ru/stoimost/tehnologiya-mokryj-fasad/",
                    "https://kronvest.net/wet-fasad",
                    "https://moscow.urfomarket.ru/montazh_mokrogo_fasada.php"
                ]
            ],
            "облицовка фасада" => [
                "sites" => [
                    "https://m-strana.ru/articles/chem-nedorogo-otdelat-fasad-doma/",
                    "https://www.forumhouse.ru/journal/themes/66-varianty-otdelki-fasada-populyarnye-vidy-konstruktiv-osobennosti",
                    "https://stroy-podskazka.ru/dom/otdelka-fasada/",
                    "https://realty.rbc.ru/news/61a7952a9a7947039b719b08",
                    "https://remstroiblog.ru/natalia/2017/03/07/10-materialov-dlya-otdelki-fasada-chastnogo-doma/",
                    "https://www.avito.ru/moskva/predlozheniya_uslug?q=%d0%be%d0%b1%d0%bb%d0%b8%d1%86%d0%be%d0%b2%d0%ba%d0%b0+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
                    "https://profi.ru/remont/fasadnye-raboty/remont-fasadov/oblicovka-fasadov/",
                    "https://spk-fasad.ru/oblicovka-fasadov.html",
                    "https://www.grandline.ru/informaciya/fasad-chastnogo-doma-otdelka/",
                    "https://kronotech.ru/fasadnye-raboty/otdelka-fasada",
                    "http://remoo.ru/fasad/fasady-domov",
                    "https://domstrousam.ru/sovremennye-materialy-dlya-fasada-doma-foto/",
                    "https://zoon.ru/msk/m/oblitsovka_fasada/",
                    "https://www.alta-profil.ru/client-center/articles/otdelka-fasada-doma/",
                    "https://dizlandshafta.ru/dizajn/doma/varianty-otdelki-fasada/",
                    "https://market.yandex.ru/search?text=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b5%20%d0%bc%d0%b0%d1%82%d0%b5%d1%80%d0%b8%d0%b0%d0%bb%d1%8b%20%d0%b4%d0%bb%d1%8f%20%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%bd%d0%be%d0%b9%20%d0%be%d0%b1%d0%bb%d0%b8%d1%86%d0%be%d0%b2%d0%ba%d0%b8%20%d0%b4%d0%be%d0%bc%d0%b0",
                    "https://everest-dom.com/blog/oblicovka-fasada-doma",
                    "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/kakoj-material-deshevle-i-luchshe-dlya-oblicovki-fasada-doma-obzor-top-9-populyarnyx-materialov.html",
                    "https://srbu.ru/otdelochnye-materialy/1950-varianty-otdelki-fasada-chastnogo-doma.html",
                    "https://prestige-fasad.ru/stati/oblitsovka-fasadov"
                ]
            ],
            "отделка мокрый фасад" => [
                "sites" => [
                    "https://m-strana.ru/articles/chto-takoe-mokryy-fasad/",
                    "https://stroy-podskazka.ru/materialy/mokryj-fasad/",
                    "https://www.forumhouse.ru/journal/articles/10434-pochemu-mokryy-fasad-vsegda-budet-vostrebovan-i-kak-sdelat-pravilno-chtoby-ne-pozhalet",
                    "https://fasad-exp.ru/uteplenie/mokryy-fasad-tekhnologiya.html",
                    "http://remoo.ru/fasad/mokryj-fasad-tekhnologiya",
                    "https://kronotech.ru/fasadnye-raboty/mokryy-fasad",
                    "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/texnologiya-utepleniya-mokryj-fasad.html",
                    "https://www.avito.ru/moskva/uslugi?q=%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9+%d1%84%d0%b0%d1%81%d0%b0%d0%b4",
                    "https://dzen.ru/media/rmnt/mokryi-fasad-obzor-harakteristiki-i-osobennosti-montaja-6047a457a6c3965eb485ac17",
                    "https://stroyka-gid.ru/fasad/tekhnologiya-mokryj-fasad.html",
                    "https://optimfasad.ru/mokryj-fasad",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d1%83%d1%81%d1%82%d1%80%d0%be%d0%b9%d1%81%d1%82%d0%b2%d0%be%20%d0%bc%d0%be%d0%ba%d1%80%d0%be%d0%b3%d0%be%20%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
                    "https://frontfacade.com/vidy-materialov/shtukaturka/rukovodstvo-po-ustrojstvu-mokrogo-fasada.html",
                    "https://expertfasada.ru/fasad/mokryj-fasad/mokryj-fasad/",
                    "https://www.kp.ru/guide/mokryi-fasad.html",
                    "https://www.zaggo.ru/article/stroitel_stvo/steny/shag_za_shagom.html",
                    "https://fasadwiki.ru/shtukaturka/mokryj-fasad",
                    "https://pro-karkas.ru/facade/wet-facade/",
                    "https://www.fasadbau.com/mokrii-fasad/",
                    "https://stroyhelper.ru/mokryy-fasad/"
                ]
            ],
            "отделка фасада" => [
                "sites" => [
                    "https://www.forumhouse.ru/journal/themes/66-varianty-otdelki-fasada-populyarnye-vidy-konstruktiv-osobennosti",
                    "https://m-strana.ru/articles/chem-nedorogo-otdelat-fasad-doma/",
                    "https://www.avito.ru/moskva?q=%d0%be%d1%82%d0%b4%d0%b5%d0%bb%d0%ba%d0%b0+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
                    "https://stroy-podskazka.ru/dom/otdelka-fasada/",
                    "http://remoo.ru/fasad/fasady-domov",
                    "https://uslugi.yandex.ru/213-moscow/category/remont-i-stroitelstvo/fasadnyie-rabotyi--1981",
                    "https://remstroiblog.ru/natalia/2017/03/07/10-materialov-dlya-otdelki-fasada-chastnogo-doma/",
                    "https://www.grandline.ru/informaciya/fasad-chastnogo-doma-otdelka/",
                    "https://www.alta-profil.ru/client-center/articles/otdelka-fasada-doma/",
                    "https://domstrousam.ru/sovremennye-materialy-dlya-fasada-doma-foto/",
                    "https://realty.rbc.ru/news/61a7952a9a7947039b719b08",
                    "https://vekroof.ru/articles/luchshie-materialy-dlya-otdelki-fasada/",
                    "https://dizlandshafta.ru/dizajn/doma/varianty-otdelki-fasada/",
                    "https://profi.ru/remont/fasadnye-raboty/remont-fasadov/oblicovka-fasadov/",
                    "https://kronotech.ru/publications/otdelka-fasada-chastnogo-doma",
                    "https://optima-fasad.ru/otdelka-fasada/",
                    "https://srbu.ru/otdelochnye-materialy/1950-varianty-otdelki-fasada-chastnogo-doma.html",
                    "https://www.prof-fasady.ru/catalog/fasad-doma/otdelka/chastnogo/",
                    "https://pikabu.ru/story/kakoy_material_luchshe_dlya_otdelki_fasada_doma_6671254",
                    "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/naruzhnaya-otdelka-doma-varianty.html"
                ]
            ],
            "отделка фасада декоративной штукатуркой" => [
                "sites" => [
                    "https://m-strana.ru/articles/otdelka-fasada-doma-shtukaturkoy/",
                    "https://st-par.ru/info/stati-o-sukhikh-smesyakh/dekorativnaya-fasadnaya-shtukaturka-vidy-po-sostavu-i-fakture-tekhnologiya-naneseniya/",
                    "https://stroy-podskazka.ru/shtukaturka/fasadnaya-dekorativnaya/",
                    "https://market.yandex.ru/journal/expertise/kak-rabotat-s-dekorativnoy-fasadnoy-shtukaturkoy",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d0%be%d1%82%d0%b4%d0%b5%d0%bb%d0%ba%d0%b0%20%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%be%d0%b2%20%d0%b4%d0%b5%d0%ba%d0%be%d1%80%d0%b0%d1%82%d0%b8%d0%b2%d0%bd%d0%be%d0%b9%20%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%be%d0%b9",
                    "https://dekoriko.ru/shtukaturka/fasadnaya-dekorativnaya/",
                    "https://www.youtube.com/playlist?list=plfnu_l24_wpwleooppfl0uvf2vuio8yny",
                    "https://expert-dacha.pro/stroitelstvo/steny/otdelka-fasada/shtukaturka/vidy-dekorativnoj-sht.html",
                    "https://fasadwiki.ru/shtukaturka/dekorativnaya-shtukaturka-fasada",
                    "https://www.svoyidoma.ru/dekorativnaja-shtukaturka-dlja-fasadov-vid-i-svojstva-pljus-i-minus-otdelki-fasada-shtukaturkoj/",
                    "https://strir.ru/naruzhnaya-otdelka/fasadnaya-dekorativnaya-shtukaturka",
                    "https://1pofasady.ru/dekor/primenenie-fasadnoy-dekorativnoy-shtukaturki",
                    "https://na-dache.pro/dom/66053-dekorativnaja-shtukaturka-na-fasade-doma-74-foto.html",
                    "https://idei.club/45883-dekorativnaja-shtukaturka-fasada-146-foto.html",
                    "https://profi.ru/remont/dekorativnaya-shtukaturka-fasada/",
                    "https://fasadblog.ru/shtukaturnyj-fasad/",
                    "https://roomester.ru/dom/fasadnaya-shtukaturka-dlya-naruzhnyh-rabot.html",
                    "https://dom-i-remont.info/posts/fasad-doma/vidy-fasadnyh-shtukaturok-harakteristiki-lidery-i-sekrety-otdelki/",
                    "https://bazafasada.ru/fasad-chastnogo-doma/vidy-dekorativnoj-shtukaturki-dlya-fasada-doma-rekomendatsii-i-sovety.html",
                    "https://dekorshtukaturka.ru/dekorativnaya-shtukaturka/fasadnaya-dekorativnaya-shtukaturka"
                ]
            ],
            "отделка фасада дома" => [
                "sites" => [
                    "https://m-strana.ru/articles/chem-nedorogo-otdelat-fasad-doma/",
                    "https://www.forumhouse.ru/journal/themes/66-varianty-otdelki-fasada-populyarnye-vidy-konstruktiv-osobennosti",
                    "https://uslugi.yandex.ru/213-moscow/category/remont-i-stroitelstvo/fasadnyie-rabotyi--1981",
                    "https://stroy-podskazka.ru/dom/otdelka-fasada/",
                    "https://remstroiblog.ru/natalia/2017/03/07/10-materialov-dlya-otdelki-fasada-chastnogo-doma/",
                    "https://www.alta-profil.ru/client-center/articles/otdelka-fasada-doma/",
                    "https://realty.rbc.ru/news/61a7952a9a7947039b719b08",
                    "https://domstrousam.ru/sovremennye-materialy-dlya-fasada-doma-foto/",
                    "https://dizlandshafta.ru/dizajn/doma/varianty-otdelki-fasada/",
                    "https://www.grandline.ru/informaciya/fasad-chastnogo-doma-otdelka/",
                    "https://srbu.ru/otdelochnye-materialy/1950-varianty-otdelki-fasada-chastnogo-doma.html",
                    "https://strbani.ru/fasad-doma/",
                    "https://vekroof.ru/articles/luchshie-materialy-dlya-otdelki-fasada/",
                    "https://stroyka-gid.ru/fasad/otdelka-fasada-doma-kakoy-material-luchshe.html",
                    "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/kakoj-material-deshevle-i-luchshe-dlya-oblicovki-fasada-doma-obzor-top-9-populyarnyx-materialov.html",
                    "https://www.tn.ru/journal/chem-otdelat-fasad-chastnogo-doma-podrobnyy-gayd-po-populyarnym-materialam/",
                    "https://www.hata.by/articles/otdelka_fasada_doma-9122/",
                    "https://market.yandex.ru/search?text=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b5%20%d0%bc%d0%b0%d1%82%d0%b5%d1%80%d0%b8%d0%b0%d0%bb%d1%8b%20%d0%b4%d0%bb%d1%8f%20%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%bd%d0%be%d0%b9%20%d0%be%d0%b1%d0%bb%d0%b8%d1%86%d0%be%d0%b2%d0%ba%d0%b8%20%d0%b4%d0%be%d0%bc%d0%b0",
                    "https://www.avito.ru/moskva/predlozheniya_uslug?q=%d0%be%d0%b1%d0%bb%d0%b8%d1%86%d0%be%d0%b2%d0%ba%d0%b0+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
                    "https://dzen.ru/media/tablichnik/luchshie-materialy-dlia-otdelki-fasada-chastnogo-doma-5f0ffd317e2b585adad67632"
                ]
            ],
            "отделка фасада дома штукатуркой" => [
                "sites" => [
                    "https://m-strana.ru/articles/otdelka-fasada-doma-shtukaturkoy/",
                    "https://st-par.ru/info/stati-o-sukhikh-smesyakh/shtukaturka-fasada/",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
                    "https://www.forumhouse.ru/journal/articles/8227-shtukaturka-na-fasade-koroed-barashek-shuba-vybor-i-nanesenie",
                    "https://stroy-podskazka.ru/shtukaturka/fasadnaya/",
                    "https://1pofasady.ru/shtukaturka/oshtukaturivanie-fasada",
                    "https://www.youtube.com/playlist?list=plfnu_l24_wpwleooppfl0uvf2vuio8yny",
                    "https://market.yandex.ru/journal/expertise/kak-rabotat-s-dekorativnoy-fasadnoy-shtukaturkoy",
                    "https://zod07.ru/fasadnye-raboty/otdelka-i-shtukaturka-fasadov/otdelka-fasada-doma-shtukaturkoj",
                    "https://dom-i-remont.info/posts/fasad-doma/vidy-fasadnyh-shtukaturok-harakteristiki-lidery-i-sekrety-otdelki/",
                    "https://dekoriko.ru/shtukaturka/fasada/",
                    "https://fasadblog.ru/shtukaturnyj-fasad/",
                    "https://geostart.ru/post/18255",
                    "https://roomester.ru/dom/fasadnaya-shtukaturka-dlya-naruzhnyh-rabot.html",
                    "https://gidstroitelstva.ru/otdelka-fasada-shtukaturkoj/",
                    "https://zen.yandex.ru/media/id/5cf6c577051e5a00aef88a5d/otdelka-fasada-doma-fasadnoi-shtukaturkoi-5d90e13992414d00af5b7a75",
                    "https://kronotech.ru/fasadnye-raboty/shtukaturka-fasada",
                    "https://fasadwiki.ru/shtukaturka/dekorativnaya-shtukaturka-fasada",
                    "https://expert-dacha.pro/stroitelstvo/steny/otdelka-fasada/shtukaturka/kakaya-luchshe-vidy.html",
                    "https://greensector.ru/stroitelstvo-i-remont/shtukaturka-fasada-doma-tekhnologiya-otdelki-vidy-shtukaturok-i-ceny.html"
                ]
            ],
            "отделка фасада частного дома" => [
                "sites" => [
                    "https://m-strana.ru/articles/chem-nedorogo-otdelat-fasad-doma/",
                    "https://stroy-podskazka.ru/dom/otdelka-fasada/",
                    "https://www.forumhouse.ru/journal/themes/66-varianty-otdelki-fasada-populyarnye-vidy-konstruktiv-osobennosti",
                    "https://remstroiblog.ru/natalia/2017/03/07/10-materialov-dlya-otdelki-fasada-chastnogo-doma/",
                    "https://domstrousam.ru/sovremennye-materialy-dlya-fasada-doma-foto/",
                    "https://dizlandshafta.ru/dizajn/doma/varianty-otdelki-fasada/",
                    "https://www.alta-profil.ru/client-center/articles/sravnenie-novinok-na-rynke-fasadnyh-materialov/",
                    "https://www.grandline.ru/informaciya/fasad-chastnogo-doma-otdelka/",
                    "https://srbu.ru/otdelochnye-materialy/1950-varianty-otdelki-fasada-chastnogo-doma.html",
                    "https://uslugi.yandex.ru/213-moscow/category/remont-i-stroitelstvo/fasadnyie-rabotyi--1981",
                    "https://strbani.ru/fasad-doma/",
                    "https://proremdom.ru/services/otdelochnye-raboty/otdelochnye-raboty-fasada/",
                    "https://realty.rbc.ru/news/61a7952a9a7947039b719b08",
                    "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/naruzhnaya-otdelka-doma-varianty.html",
                    "https://design-homes.ru/stroitelstvo-i-remont/nedorogo-fasad-doma",
                    "http://remoo.ru/fasad/fasady-domov",
                    "https://pix-feed.com/krasivye-fasady-chastnyh-domov/",
                    "https://homemyhome.ru/otdelka-fasada-chastnogo-doma.html",
                    "https://www.ksu-nordwest.ru/services/otdelka-fasada/",
                    "https://www.tn.ru/journal/chem-otdelat-fasad-chastnogo-doma-podrobnyy-gayd-po-populyarnym-materialam/"
                ]
            ],
            "отделка фасадов воронеж" => [
                "sites" => [
                    "https://www.avito.ru/voronezh/predlozheniya_uslug?q=%d0%be%d1%82%d0%b4%d0%b5%d0%bb%d0%ba%d0%b0+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%be%d0%b2",
                    "https://uslugi.yandex.ru/193-voronezh/category/remont-i-stroitelstvo/fasadnyie-rabotyi--1981",
                    "https://2gis.ru/voronezh/search/%d0%a4%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b5%20%d1%80%d0%b0%d0%b1%d0%be%d1%82%d1%8b/rubricid/648",
                    "https://www.cmlt.ru/ads--rubric-402-servicetype-30444",
                    "https://36-fasad.ru/",
                    "https://dekor36.com/fasadi.html",
                    "https://zoon.ru/voronezh/m/fasadnye_raboty-8147/",
                    "https://vrn.profi.ru/remont/fasadnye-raboty/",
                    "https://stroidom36.ru/otdelka-fasada/",
                    "https://uslugio.com/voronezh/1/9/fasadnye-raboty",
                    "http://fasad36.ru/services/otdelka-fasada/",
                    "https://kronvest.net/voronezh/remont-fasadov",
                    "https://www.remontnik.ru/voronezh/fasadnye_raboty/",
                    "https://novostroy1.ru/fasadnye-raboty",
                    "http://teplofasad36.ru/otdelka-fasada/",
                    "http://xn--36-glchqd5adeocin.xn--p1ai/fasadnye-raboty.html",
                    "https://voronezh.myguru.ru/services/fasadnye-raboty/fasadnaya-otdelka-snaruzhi/",
                    "https://spravkaru.info/voronezh/otdelka_fasadov_fasadnye_raboty",
                    "https://tdvrn.ru/fasadnye-raboty",
                    "https://fasad-com.ru/"
                ]
            ],
            "отделка фасадов короед" => [
                "sites" => [
                    "https://m-strana.ru/articles/kak-nanosit-koroed-na-fasad/",
                    "https://www.avito.ru/moskva_i_mo/predlozheniya_uslug?q=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d1%8b+%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                    "https://stroy-podskazka.ru/dom/fasad/shtukaturka-koroed/",
                    "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/fasad-koroed.html",
                    "https://www.cottedge.com/vneshnyaya-otdelka-doma/dekorativnaya-shtukaturka-koroed.php",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d0%be%d1%82%d0%b4%d0%b5%d0%bb%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4%d0%be%d0%bc%20%d1%86%d0%b5%d0%bd%d0%b0%20%d1%80%d0%b0%d0%b1%d0%be%d1%82%d1%8b%20%d0%b7%d0%b0%20%d0%bc2",
                    "https://expert-dacha.pro/stroitelstvo/steny/otdelka-fasada/shtukaturka/koroed.html",
                    "https://optimfasad.ru/koroed",
                    "https://profi.ru/remont/fasadnye-raboty-koroedom/",
                    "https://lafoy.ru/shtukaturka-koroed-50-foto-748",
                    "http://remoo.ru/materialy/tekhnologiya-naneseniya-shtukaturki-koroed",
                    "https://myguru.ru/services/fasadnye-raboty/nanesenie-shtukaturki-koroed/",
                    "https://dzen.ru/media/goodwillstroi/dekorativnaia-shtukaturka-koroed-vidy-i-sostav-tehnologiia-5f9951dfbaf78e79e76abd64",
                    "https://roomester.ru/dom/otdelka-fasada-koroedom.html",
                    "https://kronotech.ru/publications/otdelka-fasada-koroedom",
                    "https://fasad-prosto.ru/fasadnye-sistemy/mokryj-fasad/fasadnaya-shtukaturka/detalnaya-texnologiya-naneseniya-shtukaturki-koroed.html",
                    "https://fasad-exp.ru/vidy-materialov-dlya-otdelki-fasadov/shtukaturka/nanesenie-dekorativnoy-shtukaturki-k.html",
                    "https://rastenija.org/otdelka-fasada-koroedom/",
                    "https://na-dache.pro/dom/73235-fasadnaja-shtukaturka-koroed-domov-144-foto.html",
                    "https://domsdelat.ru/otdelka-vneshnyaya/otdelka-fasada-koroedom-dlya-chastnogo-doma-plyusy-i-minusy.html"
                ]
            ],
            "отделка фасадов штукатуркой" => [
                "sites" => [
                    "https://m-strana.ru/articles/otdelka-fasada-doma-shtukaturkoy/",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
                    "https://st-par.ru/info/stati-o-sukhikh-smesyakh/shtukaturka-fasada/",
                    "https://stroy-podskazka.ru/shtukaturka/fasadnaya/",
                    "https://market.yandex.ru/journal/expertise/kak-rabotat-s-dekorativnoy-fasadnoy-shtukaturkoy",
                    "https://dom-i-remont.info/posts/fasad-doma/vidy-fasadnyh-shtukaturok-harakteristiki-lidery-i-sekrety-otdelki/",
                    "https://www.avito.ru/moskva/uslugi?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
                    "https://1pofasady.ru/shtukaturka/oshtukaturivanie-fasada",
                    "https://fasadblog.ru/shtukaturnyj-fasad/",
                    "https://dekoriko.ru/shtukaturka/fasada/",
                    "https://kronotech.ru/fasadnye-raboty/shtukaturka-fasada",
                    "https://roomester.ru/dom/fasadnaya-shtukaturka-dlya-naruzhnyh-rabot.html",
                    "https://expert-dacha.pro/stroitelstvo/steny/otdelka-fasada/shtukaturka/vidy-dekorativnoj-sht.html",
                    "https://www.youtube.com/playlist?list=plfnu_l24_wpwleooppfl0uvf2vuio8yny",
                    "https://zod07.ru/fasadnye-raboty/otdelka-i-shtukaturka-fasadov/otdelka-fasada-doma-shtukaturkoj",
                    "https://profi.ru/remont/malyarnye-shtukaturnye-raboty/shtukatury/shtukaturka-sten/shtukaturka-fasada/",
                    "https://gidstroitelstva.ru/otdelka-fasada-shtukaturkoj/",
                    "https://fasadwiki.ru/shtukaturka/dekorativnaya-shtukaturka-fasada",
                    "https://zoon.ru/msk/m/shtukaturivanie_fasada/",
                    "https://chastnyjdom.ru/otdelka-fasada-shtukaturkoj/"
                ]
            ],
            "ремонт фасада" => [
                "sites" => [
                    "https://uslugi.yandex.ru/213-moscow/category/remont-i-stroitelstvo/fasadnyie-rabotyi/remont-fasadov--1994",
                    "https://kronotech.ru/fasadnye-raboty/remont-fasada",
                    "https://www.avito.ru/moskva/predlozheniya_uslug?q=%d1%80%d0%b5%d0%bc%d0%be%d0%bd%d1%82+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
                    "https://www.remontnik.ru/moskva/remont_fasadov/",
                    "https://optima-fasad.ru/remont-fasada-moskva/",
                    "https://sk-universal.ru/fasadnye-raboty/remont/",
                    "https://www.cottedge.com/vneshnyaya-otdelka-doma/remont-fasada-doma/",
                    "https://xn--j1adp.xn--80aaoxuhfy3e.xn--p1ai/fasadnyie-rabotyi/remont-fasadov.html",
                    "https://stroyalp.ru/remont_fasad.php",
                    "https://xn------6cdlbpgnjaivdekjdhaflsekp2c7lldf9a.xn--p1ai/%d1%80%d0%b5%d0%bc%d0%be%d0%bd%d1%82-%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%be%d0%b2/",
                    "https://betterstroy.ru/fasadnye-raboty/remont/",
                    "https://alpmos.ru/fasadnye-raboty",
                    "https://korsgrup.ru/remont-fasada",
                    "https://fasad-exp.ru/vidy-materialov-dlya-otdelki-fasadov/remont-fasada-chastnogo-doma.html",
                    "https://uslugio.com/moskva/1/9/remont-fasadov",
                    "https://admaer.ru/blog/articles/osobennosti-remonta-fasada-zdaniya/",
                    "https://poisk-pro.ru/masters/moskva/remont-fasadov",
                    "https://moscow.cataloxy.ru/firms/kw/%d1%80%d0%b5%d0%bc%d0%be%d0%bd%d1%82+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%be%d0%b2+%d0%b7%d0%b4%d0%b0%d0%bd%d0%b8%d0%b9.htm",
                    "http://arkonmos.ru/remont-fasada",
                    "https://efee.ru/price/fasadnie-raboty/"
                ]
            ],
            "ремонт фасада воронеж" => [
                "sites" => [
                    "https://www.avito.ru/voronezh/predlozheniya_uslug?q=%d0%be%d1%82%d0%b4%d0%b5%d0%bb%d0%ba%d0%b0+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%be%d0%b2",
                    "https://uslugi.yandex.ru/193-voronezh/category/remont-i-stroitelstvo/fasadnyie-rabotyi/remont-fasadov--1994",
                    "https://36-fasad.ru/",
                    "https://visota-36.ru/uslugi/fasadnye-raboty/remont-fasadov/",
                    "http://xn--36-glchqd5adeocin.xn--p1ai/fasadnye-raboty.html",
                    "https://www.remontnik.ru/voronezh/remont_fasadov/",
                    "https://novostroy1.ru/fasadnye-raboty",
                    "https://www.cmlt.ru/ads--rubric-402-servicetype-30444",
                    "http://fasad36.ru/services/remont-fasada/",
                    "https://zoon.ru/voronezh/m/fasadnye_raboty-8147/",
                    "https://uslugio.com/voronezh/1/9/fasadnye-raboty",
                    "https://2gis.ru/voronezh/search/%d0%a0%d0%b5%d0%bc%d0%be%d0%bd%d1%82%20%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0%20%d0%b7%d0%b4%d0%b0%d0%bd%d0%b8%d1%8f",
                    "https://vrn.profi.ru/remont/fasadnye-raboty/",
                    "https://kronvest.net/voronezh/fasad",
                    "https://voronezh.myguru.ru/services/fasadnye-raboty/kapitalnye-raboty-po-fasadu/",
                    "https://voronezh.stroyportal.ru/catalog/section-remont-fasadov-5056/",
                    "https://vrn.masterdel.ru/master/fasadnye-raboty/",
                    "https://voronezh.ooskidka.ru/otdelka-fasada/",
                    "https://fasad-com.ru/remont-fasadov/",
                    "https://077.ru/catalog/stroitelstvo-i-remont/fasad-zdaniya-oblicovka-sten-fasadnye-raboty-otdelka"
                ]
            ],
            "ремонт фасада здания" => [
                "sites" => [
                    "https://kronotech.ru/fasadnye-raboty/remont-fasada",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d1%80%d0%b5%d0%bc%d0%be%d0%bd%d1%82+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0+%d0%b7%d0%b4%d0%b0%d0%bd%d0%b8%d1%8f",
                    "https://www.prof-fasady.ru/catalog/fasad-zdanija/remont/",
                    "https://www.avito.ru/moskva/predlozheniya_uslug?q=%d1%80%d0%b5%d0%bc%d0%be%d0%bd%d1%82+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
                    "https://promalper.ru/remont-fasadov.html",
                    "https://mosstroikrovlya.ru/remont-fasada/",
                    "https://www.remontnik.ru/moskva/remont_fasadov/",
                    "https://anturazh.vip/uslugi/fasady/",
                    "https://profi.ru/remont/fasadnye-raboty/remont-fasadov/price/",
                    "https://alpstroygroup.ru/nashi-uslugi/fasadnye-raboty/remont-fasadov",
                    "https://tigris-alp.ru/uslugi/fasadnye-raboty/remont-fasadov/",
                    "https://euroalp.ru/uslugi/fasadnye-raboty/remont-fasadov.html",
                    "https://tehstroy-city.ru/obshhestroitelnyie-rabotyi/fasad/remont-fasada",
                    "https://arteli-stroy.ru/service/restavraciya-fasadov",
                    "https://fasadnik24.ru/remont-fasada-zdaniya/",
                    "https://mos-stroi-alians.ru/uslugi/fasadnye_raboty/rekonstruktsiya-fasadov/",
                    "https://fasadblog.ru/remont/",
                    "https://alpinisti.ru/remont_fasadov/",
                    "https://namik.ru/uslugi/remont_fasadov/",
                    "https://prof-grup.ru/services/fasadnye-raboty/remont-fasadov/"
                ]
            ],
            "ремонт фасада частного дома" => [
                "sites" => [
                    "https://kronotech.ru/fasadnye-raboty/remont-fasada",
                    "https://uslugi.yandex.ru/213-moscow/category/remont-i-stroitelstvo/fasadnyie-rabotyi/remont-fasadov--1994",
                    "https://www.prof-fasady.ru/catalog/fasad-doma/remont/chastnogo/",
                    "https://fasad-exp.ru/vidy-materialov-dlya-otdelki-fasadov/remont-fasada-chastnogo-doma.html",
                    "https://alpstroygroup.ru/nashi-uslugi/fasadnye-raboty/remont-fasadov",
                    "https://profi.ru/remont/fasadnye-raboty/remont-fasadov/",
                    "https://www.cottedge.com/vneshnyaya-otdelka-doma/remont-fasada-doma/",
                    "https://www.avito.ru/moskva/predlozheniya_uslug?q=%d1%80%d0%b5%d0%bc%d0%be%d0%bd%d1%82+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
                    "https://mosstroikrovlya.ru/remont-fasada/",
                    "https://stroy911.ru/remont-fasada/chastnogo-doma/",
                    "https://mos-stroi-alians.ru/uslugi/fasadnye_raboty/rekonstruktsiya-fasadov/",
                    "https://myremontnow.ru/blog/remont-fasada-chastnogo-doma",
                    "https://aograd.ru/remont_fasada/",
                    "https://www.stroyremfasad.ru/fasadnye-raboty/remont-fasada/",
                    "https://fasadblog.ru/remont/",
                    "https://tehstroy-city.ru/obshhestroitelnyie-rabotyi/fasad/remont-fasada",
                    "https://fasadrf.ru/fasadnyeraboty/",
                    "https://www.remontnik.ru/moskva/remont_fasadov/",
                    "https://promalper.ru/remont-fasadov.html",
                    "https://arteli-stroy.ru/service/restavraciya-fasadov"
                ]
            ],
            "силиконовая штукатурка для фасада" => [
                "sites" => [
                    "https://fasad-exp.ru/vidy-materialov-dlya-otdelki-fasadov/shtukaturka/silikonovaya-shtukaturka-dlya-fasada.html",
                    "https://market.yandex.ru/search?text=%d1%81%d0%b8%d0%bb%d0%b8%d0%ba%d0%be%d0%bd%d0%be%d0%b2%d0%b0%d1%8f%20%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%b4%d0%bb%d1%8f%20%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
                    "https://expert-dacha.pro/stroitelstvo/steny/otdelka-fasada/shtukaturka/silikonovaya-sht.html",
                    "https://2proraba.com/steny/shtukaturka/silikonovaya-shtukaturka-dlya-naruzhnyx-rabot-kakaya-luchshe-otzyvy-foto.html",
                    "https://www.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/silikonovaya/",
                    "https://abk-fasad.ru/catalog/uteplenie-fasadov/silicone-plaster",
                    "https://dekoriko.ru/shtukaturka/silikonovaya/",
                    "https://markakachestva.ru/rating-of/7256-luchshie-silikonovye-fasadnye-shtukaturki-rejting.html",
                    "https://bau-store.ru/stroitelnyye-materialy/shtukaturka-silikonovaya/",
                    "https://zen.yandex.ru/media/myfarbe/pochemu-silikonovaia-shtukaturka-luchshaia-sredi-fasadnyh-shtukaturok-610798d9906df03da9cbfc2d",
                    "https://o-fasadah.ru/material/kraska/silikonovaya-shtukaturka-dlya-fasada/",
                    "https://strir.ru/vnutren-otdelka/shtukaturka/silikonovaya-dlya-fasada",
                    "https://decorator.shop/shtukaturka/silikonovaya/",
                    "https://msk.pulscen.ru/price/110514-shtukaturka/f:62054_silikonovaia&62056_dlia-fasada",
                    "https://poshtukaturke.ru/steny-snaruzhi/dekorativnaya-shtukaturka-naruzhnyx-sten/silikonovaya-fasadnaya-shtukaturka.html",
                    "https://moskva.regmarkets.ru/silikatno-silikonovaya-shtukaturka-dlya-fasada/",
                    "https://gsse.ru/product-category/dekorativnye-shtukaturki/silikonovaya-shtukaturka/",
                    "https://lakom-st.ru/catalog/shtukaturki/silikonovie/",
                    "https://kraska.guru/smesi/shtukaturka/silikonovaya-smes.html",
                    "https://ok7.ru/fasadnaya_shtukaturka_silikonovaya/"
                ]
            ],
            "стоимость мокрого фасада под ключ" => [
                "sites" => [
                    "https://kronotech.ru/prays-mokryy-fasad",
                    "http://www.n-dom.ru/uteplenie-fasada/mokryj-fasad-827",
                    "https://www.avito.ru/moskva/uslugi?q=%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9+%d1%84%d0%b0%d1%81%d0%b0%d0%b4",
                    "https://optimfasad.ru/mokryij-fasad-pod-klyuch",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9+%d1%84%d0%b0%d1%81%d0%b0%d0%b4",
                    "https://www.prof-fasady.ru/catalog/mokryj-fasad/",
                    "https://tsk-gr.ru/fasad/",
                    "https://fasadrf.ru/mokryfasad/",
                    "https://www.fasadbau.com/mokrii-fasad/",
                    "https://msk-krovli.ru/fasadnye-raboty/mokryj-fasad/",
                    "https://lkgstroi.ru/stoimost/tehnologiya-mokryj-fasad/",
                    "https://optimumbuilding.ru/mokryi-fasad",
                    "https://profi.ru/remont/montazh-mokrogo-fasada/",
                    "https://aograd.ru/mokryj-fasad/",
                    "https://alpbond.org/mokryj-fasad/",
                    "https://topstroy-remont.ru/fasadnye-raboty/montazh-mokrogo-fasada",
                    "https://betterstroy.ru/fasadnye-raboty/mokryj-fasad/",
                    "https://zod07.ru/fasadnye-raboty/otdelka-i-shtukaturka-fasadov/mokryj-fasad-pod-klyuch-v-moskve-tsena",
                    "https://mos-stroi-alians.ru/uslugi/fasadnye_raboty/mokryj-fasad/",
                    "https://www.stroyremfasad.ru/fasadnye-raboty/uteplenie-fasada/mokrogo/"
                ]
            ],
            "утепление газобетонных стен" => [
                "sites" => [
                    "https://m-strana.ru/articles/uteplenie-doma-iz-gazobetona/",
                    "https://beton-house.com/stroitelstvo/iz-gazobetona/uteplenie-gazobetona/doma-iz-gazobetona-uteplenie-63",
                    "https://www.forumhouse.ru/journal/articles/7507-uteplenie-doma-iz-gazobetona-mineralnoj-vatoj",
                    "https://dzen.ru/media/rospena/kogda-sleduet-utepliat-gazobetonnuiu-stenu-snaruji-619358176785b65d26e7852a",
                    "https://www.stroy-kotedj.ru/blog/uteplenie-doma-iz-gazobetona-snaruzhi/",
                    "https://stroim-domik.org/stroitelstvo/steny/iz-blokov/gazoblok/uteplenie-gb/materialy-dlya-u",
                    "https://strir.ru/uteplenie/gazobetona-snaruzhi-i-vnutri",
                    "http://stroy-gazobeton.ru/85-kak-i-chem-uteplyat-dom-iz-gazobetona",
                    "https://full-houses.ru/osobennosti-utepleniya-doma-iz-gazoblokov/",
                    "https://st-par.ru/info/stati-o-gazobetone/uteplit-dom-iz-gazobetona-snaruzhi/",
                    "https://domsbobrom.com/articles/uteplenie-doma-iz-gazobloka",
                    "https://xn--80accc6ahceydrln.xn--p1ai/info/articles/2021/uteplenie_doma_iz_gazobetona/",
                    "https://1beton.info/maloetazhnoe/otdelka/uteplenie-sten-doma-iz-gazobetona",
                    "https://blokshop.ru/articles/uteplenie-doma-iz-gazobetona-snaruzhi-pravila-materialy-specifika-vypolneniya-rabot/",
                    "https://geostart.ru/post/66275",
                    "https://stroy-podskazka.ru/uteplenie/naruzhnoe/iz-gazobetona/",
                    "https://dekoriko.ru/dom-iz-gazobetona/uteplenie/",
                    "https://www.ytong.ru/uteplyat-li-odnosloynie-steni.php",
                    "https://www.isover.ru/articles/uteplenie-vneshnih-i-vnutrennih-sten-doma-iz-gazobetona-osobennosti",
                    "https://fasad-exp.ru/uteplenie/uteplenie-doma-iz-gazobetona-kak-pravilno-eto-sdelat.html"
                ]
            ],
            "утепление газосиликатных стен" => [
                "sites" => [
                    "https://m-strana.ru/articles/uteplenie-doma-iz-gazobetona/",
                    "https://strir.ru/uteplenie/steny-is-gazosilikatnyh-blokov",
                    "https://dzen.ru/media/stroidom/utepliaem-chastnyi-dom-gazobetonom-i-ne-parimsia-po-pustiakam-poluchaetsia-i-deshevo-i-serdito-i-na-veka-62fad18ab7ecf377b478384b",
                    "https://geostart.ru/post/66275",
                    "https://www.forumhouse.ru/journal/articles/7507-uteplenie-doma-iz-gazobetona-mineralnoj-vatoj",
                    "https://zen.yandex.ru/media/poweredhouse/uteplenie-doma-iz-gazosilikata-vajnye-osobennosti-i-rekomendacii-5fe0e20271b26f4593457c22",
                    "https://www.youtube.com/watch?v=zbi1w-k8ux8",
                    "http://stroy-gazobeton.ru/87-tolshchina-uteplitelya-dlya-gazobetonnogo-doma",
                    "https://beton-house.com/stroitelstvo/iz-gazobetona/uteplenie-gazobetona/doma-iz-gazobetona-uteplenie-63",
                    "https://betonov.com/vidy-betona/gazosilikat/uteplenie-gazosilikatnyh-sten-snaruzhi.html",
                    "https://stroim-domik.org/stroitelstvo/steny/iz-blokov/gazoblok/uteplenie-gb/materialy-dlya-u",
                    "https://www.stroy-kotedj.ru/blog/uteplenie-doma-iz-gazobetona-snaruzhi/",
                    "https://expert-dacha.pro/stroitelstvo/steny/uteplenie-st/gazosilikatnyh-blokov-snaruzhi.html",
                    "https://full-houses.ru/osobennosti-utepleniya-doma-iz-gazoblokov/",
                    "https://znatoktepla.ru/utepliteli/uteplenie-sten-iz-gazosilikatnyh-blokov-snaruzhi.html",
                    "https://1pofasadu.ru/uteplenie/doma-iz-gazosilikatnyh-blokov-snaruzhi.html",
                    "https://obustroeno.club/instrum-i-material/sten-material/blok-yach-beton/60032-kak-uteplit-dom-iz-gazosilikatnyh-blokov",
                    "https://ebtim.com/steny/kak-uteplit-dom-iz-gazosilikata.html",
                    "https://oooprojekt.ru/steny/iz-gazosilikatnyh-blokov/uteplenie",
                    "https://stroybaza.by/news/chem-uteplit-fasad-doma-iz-gazosilikatnykh-blokov/"
                ]
            ],
            "утепление газосиликатных стен снаружи" => [
                "sites" => [
                    "https://strir.ru/uteplenie/steny-is-gazosilikatnyh-blokov",
                    "https://m-strana.ru/articles/uteplenie-doma-iz-gazobetona/",
                    "https://betonov.com/vidy-betona/gazosilikat/uteplenie-gazosilikatnyh-sten-snaruzhi.html",
                    "https://beton-house.com/stroitelstvo/iz-gazobetona/uteplenie-gazobetona/doma-iz-gazobetona-uteplenie-63",
                    "https://geostart.ru/post/66275",
                    "https://1pofasadu.ru/uteplenie/doma-iz-gazosilikatnyh-blokov-snaruzhi.html",
                    "https://www.forumhouse.ru/journal/articles/7507-uteplenie-doma-iz-gazobetona-mineralnoj-vatoj",
                    "https://expert-dacha.pro/stroitelstvo/steny/uteplenie-st/gazosilikatnyh-blokov-snaruzhi.html",
                    "https://www.stroy-kotedj.ru/blog/uteplenie-doma-iz-gazobetona-snaruzhi/",
                    "https://www.youtube.com/watch?v=zbi1w-k8ux8",
                    "https://st-par.ru/info/stati-o-gazobetone/uteplit-dom-iz-gazobetona-snaruzhi/",
                    "https://znatoktepla.ru/utepliteli/uteplenie-sten-iz-gazosilikatnyh-blokov-snaruzhi.html",
                    "https://zen.yandex.ru/media/rospena/kogda-sleduet-utepliat-gazobetonnuiu-stenu-snaruji-619358176785b65d26e7852a",
                    "https://dzen.ru/media/rospena/kak-uteplit-dom-iz-gazobetona-snaruji-i-kak-provesti-raboty-kak-i-chem-uteplit-dom-iz-gazobetona-snaruji-uteplitel-dlia-doma-iz-gazobeton-62fcb292ed351b17f97edd45",
                    "https://stroim-domik.org/stroitelstvo/steny/iz-blokov/gazoblok/uteplenie-gb/materialy-dlya-u",
                    "https://obustroeno.club/instrum-i-material/sten-material/blok-yach-beton/60032-kak-uteplit-dom-iz-gazosilikatnyh-blokov",
                    "https://blokshop.ru/articles/uteplenie-doma-iz-gazobetona-snaruzhi-pravila-materialy-specifika-vypolneniya-rabot/",
                    "https://full-houses.ru/osobennosti-utepleniya-doma-iz-gazoblokov/",
                    "https://ebtim.com/steny/kak-uteplit-dom-iz-gazosilikata.html",
                    "https://stroy-podskazka.ru/uteplenie/naruzhnoe/iz-gazobetona/"
                ]
            ],
            "утепление дома" => [
                "sites" => [
                    "https://m-strana.ru/articles/uteplenie-doma-snaruzhi-materialy-normativy/",
                    "https://journal.tinkoff.ru/guide/teplodom/",
                    "https://www.forumhouse.ru/journal/themes/128-kak-uteplit-fasad-bez-oshibok",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5%20%d0%b4%d0%be%d0%bc%d0%b0",
                    "https://dzen.ru/media/rmnt/kak-uteplit-dom-pravilno-5e6a44074449f63aaa9bef7b",
                    "https://www.youtube.com/playlist?list=plja-j8bdzueq1pzv1anfabllcur7pbnl-",
                    "https://www.avito.ru/moskva/predlozheniya_uslug?q=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5+%d0%b4%d0%be%d0%bc%d0%b0",
                    "https://www.houzz.ru/statyi/tak-mozhno-uteplyaem-zagorodnyy-dom-i-ekonomim-na-otoplenii-stsetivw-vs~117034682",
                    "https://krrot.net/yteplenie-doma-svoimi-rykami/",
                    "https://7dach.ru/oleg_sanko/uteplenie-zagorodnogo-doma-ot-i-do-37487.html",
                    "https://domof.ru/articles/uteplenie-doma-snaruzhi-ili-iznutri/",
                    "https://stroy-podskazka.ru/uteplenie/naruzhnoe/sten/",
                    "https://www.kp.ru/guide/uteplenie-doma.html",
                    "https://ppugarant.ru/service/uteplenie-doma-ppu/",
                    "https://realty.rbc.ru/news/5f3676bd9a794761e14a0ae8",
                    "https://sovet-ingenera.com/otoplenie/uteplenie/uteplenie-chastnogo-doma-snaruzhi.html",
                    "https://market.yandex.ru/journal/expertise/kak-pravilno-uteplit-fasad-i-vipolnit-finishnuju-otdelku",
                    "https://stroitelstvoproektirovanie.com/uteplenie-doma/",
                    "https://www.inmyroom.ru/posts/11305-10-zolotyh-pravil-utepleniya-doma-kotorye-nuzhno-znat",
                    "https://prd.ru/uteplenie-doma/"
                ]
            ],
            "утепление дома воронеж" => [
                "sites" => [
                    "https://m.avito.ru/voronezh/predlozheniya_uslug?query=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5%20%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%be%d0%b2",
                    "https://uslugi.yandex.ru/193-voronezh/category/remont-i-stroitelstvo/fasadnyie-rabotyi/uteplenie-fasadov--1995",
                    "https://xn--b1agd0aean.xn----7sbcsabtrfvtrsje3r.xn--p1ai/",
                    "https://stroidom36.ru/uteplenie-doma/",
                    "https://www.xn--b1agd0aean.xn----8sbaqbsujyzdlu.xn--p1ai/",
                    "http://fasad36.ru/services/uteplenie-fasada/",
                    "https://voronezh.myguru.ru/services/gidroizolyaciya-i-uteplenie/uteplenie-sten/",
                    "https://vrn.profi.ru/remont/fasadnye-raboty/uteplenie-fasadov/",
                    "https://voronezh.ooskidka.ru/uteplenie-fasada/",
                    "https://uslugio.com/voronezh/1/9/uteplenie-doma",
                    "https://voronezh.vse-podklyuch.ru/uslugi/uteplenie-doma/",
                    "https://vrn.latitudo.org/services/uteplenie-fasada/",
                    "https://36-fasad.ru/nashi-uslugi/uteplenie-fasadov-snaruzhi",
                    "https://visota-36.ru/uslugi/fasadnye-raboty/uteplenie-fasadov/",
                    "https://zoon.ru/voronezh/m/teploizolyatsiya_sten/",
                    "https://vrn.masterdel.ru/master/uteplenie-sten/",
                    "https://voronezh.stroyportal.ru/catalog/section-uteplenie-doma-463/",
                    "https://dekor36.com/fasadi.html",
                    "https://voronezh.ppu-uteplenie.ru/uteplenie-domov-penoi-ppu",
                    "https://voronezh.trade-services.ru/services/fasadnye-raboty/uteplenie-fasada-doma/"
                ]
            ],
            "утепление дома снаружи" => [
                "sites" => [
                    "https://m-strana.ru/articles/uteplenie-doma-snaruzhi-materialy-normativy/",
                    "https://www.forumhouse.ru/journal/themes/128-kak-uteplit-fasad-bez-oshibok",
                    "https://uslugi.yandex.ru/213-moscow/category/remont-i-stroitelstvo/fasadnyie-rabotyi/uteplenie-fasadov--1995",
                    "https://stroy-podskazka.ru/uteplenie/naruzhnoe/sten/",
                    "https://sovet-ingenera.com/otoplenie/uteplenie/uteplenie-chastnogo-doma-snaruzhi.html",
                    "https://journal.tinkoff.ru/guide/teplodom/",
                    "https://remont-book.com/uteplenie-sten-doma-snaruzhi/",
                    "https://market.yandex.ru/journal/expertise/kak-pravilno-uteplit-fasad-i-vipolnit-finishnuju-otdelku",
                    "https://srbu.ru/stroitelnye-materialy/1948-chem-luchshe-uteplit-dom-snaruzhi.html",
                    "https://www.bazaznaniyst.ru/nedorogie-materialy-dlya-utepleniya-doma-snaruzhi/",
                    "https://kronotech.ru/publications/uteplenie-fasada-chastnogo-doma-snaruzhi",
                    "https://profi.ru/remont/fasadnye-raboty/uteplenie-fasadov/price/",
                    "https://fasad-exp.ru/uteplenie/materialy-dlya-utepleniya-sten-snaruzhi.html",
                    "http://remoo.ru/fasad/uteplenie-fasada-doma-snaruzhi",
                    "https://strir.ru/uteplenie/sten-snaruzhi",
                    "https://geostart.ru/post/36518",
                    "https://stroyday.ru/stroitelstvo-doma/stroitelnye-materialy/naruzhnyj-uteplitel-dlya-sten.html",
                    "https://www.kp.ru/guide/uteplenie-fasada.html",
                    "https://www.houzz.ru/statyi/tak-mozhno-uteplyaem-zagorodnyy-dom-i-ekonomim-na-otoplenii-stsetivw-vs~117034682",
                    "https://everest-dom.com/blog/uteplenie-sten-doma-snaruzhi"
                ]
            ],
            "утепление и отделка фасада дома" => [
                "sites" => [
                    "https://uslugi.yandex.ru/213-moscow/category/remont-i-stroitelstvo/fasadnyie-rabotyi/uteplenie-fasadov--1995",
                    "https://m-strana.ru/articles/uteplenie-fasada-chastnogo-doma-neobkhodimost-vybor-materialov-oblitsovka/",
                    "https://www.cottedge.com/vneshnyaya-otdelka-doma/uteplenie-fasada/",
                    "https://www.forumhouse.ru/journal/themes/128-kak-uteplit-fasad-bez-oshibok",
                    "https://teplo-facad.ru/",
                    "https://market.yandex.ru/journal/expertise/kak-pravilno-uteplit-fasad-i-vipolnit-finishnuju-otdelku",
                    "https://www.youtube.com/playlist?list=pluivzwm_q9lz2vbendttxwhuhycp_9vhq",
                    "https://profi.ru/remont/fasadnye-raboty/uteplenie-fasadov/price/",
                    "https://stroy-podskazka.ru/uteplenie/naruzhnoe/fasadov/",
                    "https://www.prof-fasady.ru/catalog/fasad-doma/otdelka/i-uteplenie/",
                    "https://moskva.ooskidka.ru/uteplenie-fasada/",
                    "https://zod07.ru/fasadnye-raboty/kamennye-doma-shtukaturnye-fasady/uteplenie-fasadov-domov",
                    "https://www.avito.ru/moskva/uslugi?q=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%be%d0%b2",
                    "https://zoon.ru/msk/m/teploizolyatsiya_fasada/",
                    "https://planken.guru/otdelka-i-montazh-fasadov/teploizolyaciya-i-uteplenie-fasada-zdaniy-i-chastnyh-domov-snaruzhi.html",
                    "https://expertfasada.ru/fasad/uteplenie-fasada/uteplenie-i-otdelka-fasada-doma/",
                    "https://domzastroika.ru/fasad/materialy-i-tehnologija-uteplenija.html",
                    "https://kronotech.ru/fasadnye-raboty/uteplenie-fasadov",
                    "http://remoo.ru/fasad/uteplenie-fasada-doma-snaruzhi",
                    "https://xps.tn.ru/useful/articles/chetyre-varianta-krasivoy-i-nadezhnoy-otdelki-uteplennogo-fasada/"
                ]
            ],
            "утепление и отделка фасадов частных домов" => [
                "sites" => [
                    "https://m-strana.ru/articles/uteplenie-fasada-chastnogo-doma-neobkhodimost-vybor-materialov-oblitsovka/",
                    "https://uslugi.yandex.ru/213-moscow/category/remont-i-stroitelstvo/fasadnyie-rabotyi/uteplenie-fasadov--1995",
                    "https://www.youtube.com/playlist?list=pluivzwm_q9lz2vbendttxwhuhycp_9vhq",
                    "https://teplo-facad.ru/",
                    "https://www.avito.ru/moskva/uslugi?q=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%be%d0%b2",
                    "https://www.forumhouse.ru/journal/themes/128-kak-uteplit-fasad-bez-oshibok",
                    "https://market.yandex.ru/journal/expertise/kak-pravilno-uteplit-fasad-i-vipolnit-finishnuju-otdelku",
                    "https://stroy-podskazka.ru/uteplenie/naruzhnoe/fasadov/",
                    "https://profi.ru/remont/fasadnye-raboty/uteplenie-fasadov/price/",
                    "https://www.cottedge.com/vneshnyaya-otdelka-doma/uteplenie-fasada/",
                    "https://www.prof-fasady.ru/catalog/fasad-doma/otdelka/i-uteplenie/",
                    "https://zoon.ru/msk/m/teploizolyatsiya_fasada/",
                    "https://expertfasada.ru/fasad/uteplenie-fasada/uteplenie-i-otdelka-fasada-doma/",
                    "https://kronotech.ru/fasadnye-raboty/uteplenie-fasadov",
                    "https://optimfasad.ru/uteplenie-fasada-doma",
                    "https://domzastroika.ru/fasad/materialy-i-tehnologija-uteplenija.html",
                    "https://planken.guru/otdelka-i-montazh-fasadov/teploizolyaciya-i-uteplenie-fasada-zdaniy-i-chastnyh-domov-snaruzhi.html",
                    "https://moskva.ooskidka.ru/uteplenie-fasada/",
                    "https://zod07.ru/fasadnye-raboty/kamennye-doma-shtukaturnye-fasady/uteplenie-fasadov-domov",
                    "https://xps.tn.ru/useful/articles/chetyre-varianta-krasivoy-i-nadezhnoy-otdelki-uteplennogo-fasada/"
                ]
            ],
            "утепление кирпичных стен" => [
                "sites" => [
                    "https://m-strana.ru/articles/uteplenie-kirpichnoy-steny-iznutri/",
                    "https://strir.ru/uteplenie/kak-pravilno-uteplit-kirpichnyj-dom-snaruzhi-i-chem",
                    "https://www.ivd.ru/dacha-i-sad/dacnyj-ucastok/uteplenie-kirpichnyh-sten-kak-ego-sdelat-vnutri-i-snaruzhi-doma-44741",
                    "https://stroim-domik.org/stroitelstvo/steny/kirpichnye/uteplenie-s/iznutri",
                    "https://expert-dacha.pro/stroitelstvo/steny/uteplenie-st/naruzhnoe-ut-kirpichnoj.html",
                    "https://www.zaggo.ru/article/stroitel_stvo/steny/zamurovali_.html",
                    "https://dom-s-ymom.org/stroitelstvo/konstruktivnye-resheniya/steny/kirpichnaya/uteplenie-iznutri-i-snaruzhi.html",
                    "https://profiteplo.com/uteplenie/117-kak-uteplit-kirpichnyj-dom-iznutri.html",
                    "https://www.gwd.ru/technology/poleznaya-informatsiya/kak-uteplit-kirpichnyy-dom/",
                    "https://www.penoplex.ru/o-kompanii/presscentr/stati/kak-uteplit-steny-kirpichnogo-doma-iznutri-vybor-materialov/",
                    "https://domnomore.com/uteplenie-kirpichnogo-doma/",
                    "https://stroy-podskazka.ru/uteplenie/naruzhnoe/kirpichnogo-doma/",
                    "https://teplofom.ru/article/insulation-of-houses-of-brick-tips-technology-materials/",
                    "https://www.forumhouse.ru/threads/364305/",
                    "https://izolexpert.ru/teploizolyaciya/uteplenie-kirpichnyx-sten.html",
                    "https://fasad-exp.ru/uteplenie/chem-uteplit-kirpichnyj-dom-snaruzhi-nedorogo.html",
                    "https://www.domrnr.ru/blog/uteplenie-kirpichnogo-doma-vnutri-i-snaruzhi/",
                    "https://dzen.ru/media/fasad_expert/pravilnoe-uteplenie-sten-kirpichnogo-doma-poshagovaia-instrukciia-5c7902290a5f4400b3a2ebf6",
                    "https://www.youtube.com/watch?v=bcslorzihwa",
                    "https://www.houzz.ru/statyi/kak-pravilyno-uteplity-kamennyy-dom-stsetivw-vs~122275192"
                ]
            ],
            "утепление мокрый фасад" => [
                "sites" => [
                    "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/texnologiya-utepleniya-mokryj-fasad.html",
                    "https://www.forumhouse.ru/journal/articles/10434-pochemu-mokryy-fasad-vsegda-budet-vostrebovan-i-kak-sdelat-pravilno-chtoby-ne-pozhalet",
                    "https://m-strana.ru/articles/chto-takoe-mokryy-fasad/",
                    "https://fasad-exp.ru/uteplenie/mokryy-fasad-tekhnologiya.html",
                    "https://kronotech.ru/fasadnye-raboty/mokryy-fasad",
                    "http://remoo.ru/fasad/mokryj-fasad-tekhnologiya",
                    "https://uteplenieplus.ru/kak-uteplit/fasady/texnologiya-utepleniya-mokryj-fasad/",
                    "https://www.youtube.com/watch?v=ejvyrmvmd8a",
                    "https://expertfasada.ru/fasad/mokryj-fasad/uteplitel-dlya-mokrogo-fasada/",
                    "https://www.avito.ru/moskva/uslugi?q=%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9+%d1%84%d0%b0%d1%81%d0%b0%d0%b4",
                    "http://www.n-dom.ru/uteplenie-fasada/mokryj-fasad-827",
                    "http://www.navesfasad.ru/fasad/mokryj-fasad.html",
                    "https://www.kp.ru/guide/mokryi-fasad.html",
                    "https://optimfasad.ru/mokryj-fasad",
                    "https://www.zaggo.ru/article/stroitel_stvo/steny/shag_za_shagom.html",
                    "https://www.rmnt.ru/story/facade/texnologija-mokryy-fasad-vybor-uteplitelja-montazh-svoimi-rukami.1225107/",
                    "https://uteplenievl.ru/uteplenie_mok_fasad/",
                    "https://dzen.ru/media/rmnt/mokryi-fasad-obzor-harakteristiki-i-osobennosti-montaja-6047a457a6c3965eb485ac17",
                    "https://www.tstn.ru/articles/statya_26_fasad_delo_tonkoe_tekhnologiya_ustroystva_fasada_po_mokromu_tipu/",
                    "https://dekofasad.ru/blog/54-mokryi-fasad/montazh/181-tekhnologiya-mokrogo-utepleniya-fasada.html"
                ]
            ],
            "утепление наружной стены" => [
                "sites" => [
                    "https://m-strana.ru/articles/uteplenie-doma-snaruzhi-materialy-normativy/",
                    "https://www.forumhouse.ru/journal/themes/128-kak-uteplit-fasad-bez-oshibok",
                    "https://stroy-podskazka.ru/uteplenie/naruzhnoe/sten/",
                    "https://fasad-exp.ru/uteplenie/materialy-dlya-utepleniya-sten-snaruzhi.html",
                    "https://srbu.ru/stroitelnye-materialy/1707-uteplitel-dlya-sten-doma-snaruzhi.html",
                    "https://stroyday.ru/stroitelstvo-doma/stroitelnye-materialy/naruzhnyj-uteplitel-dlya-sten.html",
                    "https://sovet-ingenera.com/otoplenie/uteplenie/uteplitel-dlya-sten-doma-snaruzhi.html",
                    "https://remont-book.com/uteplenie-sten-doma-snaruzhi/",
                    "https://market.yandex.ru/journal/expertise/kak-pravilno-uteplit-fasad-i-vipolnit-finishnuju-otdelku",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5%20%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%bd%d0%be%d0%b9%20%d1%81%d1%82%d0%b5%d0%bd%d1%8b%20%d0%ba%d0%b2%d0%b0%d1%80%d1%82%d0%b8%d1%80%d1%8b%20%d0%bf%d0%b5%d0%bd%d0%be%d0%bf%d0%be%d0%bb%d0%b8%d1%81%d1%82%d0%b8%d1%80%d0%be%d0%bb%d0%be%d0%bc",
                    "https://kronotech.ru/publications/chem-uteplit-fasad-doma",
                    "https://journal.tinkoff.ru/guide/teplodom/",
                    "https://geostart.ru/post/36518",
                    "https://strir.ru/uteplenie/sten-snaruzhi",
                    "https://domastroika.com/kak-vybrat-luchshij-uteplitel-dlya-naruzhnyh-sten-doma/",
                    "https://7dach.ru/oleg_sanko/uteplenie-zagorodnogo-doma-ot-i-do-37487.html",
                    "https://knauftherm.ru/blog/utepliteli-dlya-naruzhnykh-sten-doma",
                    "https://www.kp.ru/guide/uteplenie-fasada.html",
                    "https://everest-dom.com/blog/uteplenie-sten-doma-snaruzhi",
                    "https://www.houzz.ru/statyi/tak-mozhno-uteplyaem-zagorodnyy-dom-i-ekonomim-na-otoplenii-stsetivw-vs~117034682"
                ]
            ],
            "утепление стен" => [
                "sites" => [
                    "https://m-strana.ru/articles/uteplenie-doma-iznutri-materialy-i-ikh-preimushchestva/",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5%20%d1%81%d1%82%d0%b5%d0%bd%20%d0%b8%20%d0%bf%d0%be%d1%82%d0%be%d0%bb%d0%ba%d0%be%d0%b2%20%d1%86%d0%b5%d0%bd%d0%b0%20%d0%b7%d0%b0%20%d0%bc2",
                    "https://market.yandex.ru/journal/goodsstory/chem-uteplit-steni-v-kvartire-ili-dome",
                    "https://akterm.ru/kak-uteplit-stenu-iznutri",
                    "https://leroymerlin.ru/catalogue/teploizolyaciya/",
                    "https://journal.tinkoff.ru/guide/teplodom/",
                    "https://www.forumhouse.ru/journal/themes/128-kak-uteplit-fasad-bez-oshibok",
                    "https://stroyguru.com/remont-kvartiry/steny/vidy-uteplitelej-dlya-vnutrennih-sten-doma/",
                    "https://www.ozon.ru/category/utepliteli-dlya-sten/",
                    "https://sovet-ingenera.com/otoplenie/uteplenie/kak-uteplit-stenu-v-kvartire.html",
                    "https://www.avito.ru/moskva/uslugi?q=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5+%d1%81%d1%82%d0%b5%d0%bd",
                    "https://stroychik.ru/steny/uteplenie-sten-iznutri",
                    "https://stroy-podskazka.ru/uteplenie/naruzhnoe/sten/",
                    "https://zoon.ru/msk/m/teploizolyatsiya_sten/",
                    "https://www.rmnt.ru/story/isolation/uteplenie-sten-vkvartire-borba-sxolodnoy-stenoy.1047128/",
                    "https://fasad-exp.ru/uteplenie/materialy-dlya-utepleniya-sten-snaruzhi.html",
                    "https://profi.ru/remont/izolyacionnye-raboty/montazh-teploizolyacii/uteplenie-sten/",
                    "https://www.ivd.ru/stroitelstvo-i-remont/steny/kak-uteplit-steny-doma-vybor-materialov-i-tehnologii-montaza-28131",
                    "https://stroyrem-nn.ru/article/uteplenie-sten-doma-iznutri-vybor-materialov-i-osobennosti-montazha",
                    "https://7dach.ru/oleg_sanko/uteplenie-zagorodnogo-doma-ot-i-do-37487.html"
                ]
            ],
            "утепление стен воронеж" => [
                "sites" => [
                    "https://www.avito.ru/voronezh/uslugi?q=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5+%d1%81%d1%82%d0%b5%d0%bd",
                    "https://uslugi.yandex.ru/193-voronezh/category?text=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5+%d0%ba%d0%b2%d0%b0%d1%80%d1%82%d0%b8%d1%80",
                    "https://www.xn--b1agd0aean.xn----8sbaqbsujyzdlu.xn--p1ai/",
                    "https://xn--b1agd0aean.xn----7sbcsabtrfvtrsje3r.xn--p1ai/",
                    "https://voronezh.ooskidka.ru/uteplenie-fasada/",
                    "https://visota-36.ru/uslugi/fasadnye-raboty/uteplenie-fasadov/",
                    "https://vrn.profi.ru/remont/izolyacionnye-raboty/montazh-teploizolyacii/uteplenie-sten/",
                    "http://fasad36.ru/services/uteplenie-sten/",
                    "https://voronezh.myguru.ru/services/gidroizolyaciya-i-uteplenie/uteplenie-sten/",
                    "https://vrn.masterdel.ru/master/uteplenie-sten/",
                    "https://zoon.ru/voronezh/m/teploizolyatsiya_sten/",
                    "https://voronezh.ppu-uteplenie.ru/uteplenie-poliuretanom-sten-doma",
                    "https://uslugio.com/voronezh/1/9/uteplenie-sten",
                    "https://stroidom36.ru/uteplenie-doma/",
                    "https://dekor36.com/fasadi.html",
                    "https://vrn.latitudo.org/services/uteplenie-fasada/",
                    "http://teplofasad36.ru/uteplenie-fasadov",
                    "https://vrn.needspec.ru/mastera-po-remontu/kompleksnyyi-remont/remont-kvartir-i-kottedzheyi/izolyacionnye-raboty/montazh-teploizolyacii/uteplenie-sten",
                    "https://voronezh.leroymerlin.ru/catalogue/teploizolyaciya/",
                    "https://voronezh.skidkom.ru/uslugi/uteplenie-fasadov/"
                ]
            ],
            "утепление стен дома" => [
                "sites" => [
                    "https://m-strana.ru/articles/uteplenie-doma-snaruzhi-materialy-normativy/",
                    "https://www.forumhouse.ru/journal/articles/6462-uteplenie-chastnogo-doma-ishem-universalnyj-uteplitel",
                    "https://remont-book.com/uteplenie-sten-doma-snaruzhi/",
                    "https://journal.tinkoff.ru/guide/teplodom/",
                    "https://fasad-exp.ru/uteplenie/materialy-dlya-utepleniya-sten-snaruzhi.html",
                    "https://stroy-podskazka.ru/uteplenie/naruzhnoe/sten/",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d1%84%d0%b8%d1%80%d0%bc%d1%8b%20%d0%bf%d0%be%20%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d1%8e%20%d0%b4%d0%be%d0%bc%d0%be%d0%b2%20%d1%82%d0%b5%d0%bb",
                    "https://market.yandex.ru/journal/goodsstory/chem-uteplit-steni-v-kvartire-ili-dome",
                    "https://stroyrem-nn.ru/article/uteplenie-sten-doma-iznutri-vybor-materialov-i-osobennosti-montazha",
                    "https://sovet-ingenera.com/otoplenie/uteplenie/uteplitel-dlya-sten-doma-snaruzhi.html",
                    "https://srbu.ru/stroitelnye-materialy/1948-chem-luchshe-uteplit-dom-snaruzhi.html",
                    "https://stroyguru.com/remont-kvartiry/steny/vidy-uteplitelej-dlya-vnutrennih-sten-doma/",
                    "https://7dach.ru/oleg_sanko/uteplenie-zagorodnogo-doma-ot-i-do-37487.html",
                    "https://stroyday.ru/stroitelstvo-doma/stroitelnye-materialy/naruzhnyj-uteplitel-dlya-sten.html",
                    "https://www.houzz.ru/statyi/tak-mozhno-uteplyaem-zagorodnyy-dom-i-ekonomim-na-otoplenii-stsetivw-vs~117034682",
                    "https://leroymerlin.ru/catalogue/teploizolyaciya/",
                    "https://krrot.net/yteplenie-doma-svoimi-rykami/",
                    "https://dzen.ru/media/rmnt/kak-uteplit-dom-pravilno-5e6a44074449f63aaa9bef7b",
                    "https://zoon.ru/msk/m/teploizolyatsiya_sten/",
                    "https://www.kp.ru/guide/uteplenie-doma.html"
                ]
            ],
            "утепление стен минеральной плитой" => [
                "sites" => [
                    "https://m-strana.ru/articles/minvata-dlya-utepleniya-sten/",
                    "https://www.tproekt.com/minplita-eto-chto/",
                    "https://uteplimvse.ru/dlya/sten/texnologia-utepleniya.html",
                    "https://www.ivd.ru/stroitelstvo-i-remont/steny/mineralnaya-vata-dlya-utepleniya-sten-sovety-po-vyboru-i-montazhu-38361",
                    "https://stroy-podskazka.ru/uteplenie/mineralnaya-vata/vidy/",
                    "https://uteplenieplus.ru/kak-uteplit/fasady/texnologiya-utepleniya-mineralnoj-vatoj/",
                    "https://krovgid.com/izolyaciya/minplity.html",
                    "https://stroyguru.com/remont-kvartiry/steny/uteplenie-sten-mineralnoj-vatoj-svoimi-rukami/",
                    "https://kronotech.ru/publications/uteplenie-fasada-minvatoy-snaruzhi",
                    "https://strport.ru/izolyatsionnye-materialy/utepliteli/tekhnologiya-utepleniya-sten-mineralnoi-vatoi",
                    "https://www.forumhouse.ru/journal/articles/9225-effektivnost-odnosloinogo-utepleniya-dokazano-mozhno-ne-uslozhnyat",
                    "https://www.isover.ru/articles/minvata-universalnyj-material-utepleniya-sten",
                    "https://stroim-domik.org/stroitelstvo/steny/kirpichnye/uteplenie-s/minvatoj",
                    "https://strir.ru/uteplenie/minvatoy",
                    "https://www.fermeram.com/uteplenie-sten-vnutri-i-snaruzhi-doma-mineralnoj-vatoj-plotnost-i-razmery.html",
                    "https://x-teplo.ru/uteplenie/steny/uteplenie-sten-mineralnoj-vatoj.html",
                    "https://dzen.ru/media/rmnt/uteplenie-mineralnoi-vatoi-614359b7bc817c1c59e63c3f",
                    "https://domzastroika.ru/walls/uteplenie-fasadov-mineralnoj-vatoj-svoimi-rukami.html",
                    "https://znatoktepla.ru/utepliteli/sten-mineralnoj-vatoj-snaruzhi-i-iznutri.html",
                    "https://stroyday.ru/remont-kvartiry/steny-i-potolok/uteplenie-sten-iznutri-minvatoj-plyus-gipsokarton.html"
                ]
            ],
            "утепление стен пенопластом" => [
                "sites" => [
                    "https://fasad-exp.ru/vidy-materialov-dlya-otdelki-fasadov/shtukaturka/uteplenie-sten-snaruzhi-penoplastom.html",
                    "https://stroyguru.com/remont-kvartiry/kak-uteplit-vnutrennie-i-naruzhnye-steny-penoplastom/",
                    "https://m-strana.ru/articles/uteplenie-penoplastom/",
                    "https://www.youtube.com/watch?v=4issx5difz8",
                    "https://www.ivd.ru/stroitelstvo-i-remont/steny/uteplenie-sten-penoplastom-poshagovaya-instrukciya-i-poleznye-sovety-53681",
                    "https://dzen.ru/media/rmnt/uteplenie-fasada-penoplastom-svoimi-rukami-5ce7c9aadd00af00b25acfcf",
                    "https://stroychik.ru/naruzhnaya-otdelka/uteplenie-fasada-penoplastom",
                    "https://www.forumhouse.ru/journal/explainers/114-pochemu-penoplast-otlichnyj-uteplitel-i-gde-on-proyavit-sebya-luchshe-vsego",
                    "https://stroy-podskazka.ru/penoplast/vse/",
                    "https://stroyday.ru/news/uteplenie-doma-penoplastom-plyusy-i-minusy-lichnyj-opyt-i-podrobnaya-instrukciya.html",
                    "https://knauftherm.ru/blog/uteplenie-fasada-doma-penoplastom",
                    "https://sovet-ingenera.com/otoplenie/uteplenie/uteplenie-sten-penoplastom.html",
                    "https://srbu.ru/stroitelnye-raboty/93-uteplenie-sten-penoplastom-svoimi-rukami.html",
                    "https://expert-dacha.pro/stroitelstvo/steny/uteplenie-st/penoplastom-snaruzhi.html",
                    "https://strir.ru/uteplenie/sten-penopolistirolom",
                    "https://vopros-remont.ru/steny/uteplenie-penoplastom/",
                    "https://domzastroika.ru/walls/instrukciya-po-utepleniyu-penoplastom-iznutri-pomeshheniya.html",
                    "https://moydomik.net/steny-i-perekrytiya/240-vnutrennee-uteplenie-sten-penoplastom-iznutri.html",
                    "https://o-fasadah.ru/material/uteplitel/uteplenie-sten-penoplastom/",
                    "https://pikabu.ru/story/uteplenie_fasada_penopolistirolom_byit_ili_ne_byit_nyuansyi_tonkosti_5788808"
                ]
            ],
            "утепление стен пенополистиролом" => [
                "sites" => [
                    "https://strir.ru/uteplenie/sten-penopolistirolom",
                    "https://expert-dacha.pro/stroitelstvo/steny/uteplenie-st/penopolistirolom-snaruzhi.html",
                    "https://fasad-exp.ru/vidy-materialov-dlya-otdelki-fasadov/shtukaturka/uteplenie-sten-snaruzhi-penoplastom.html",
                    "https://m-strana.ru/articles/uteplenie-penoplastom/",
                    "https://stroyguru.com/remont-kvartiry/kak-uteplit-vnutrennie-i-naruzhnye-steny-penoplastom/",
                    "https://stroy-podskazka.ru/dom/uteplenie/penopolistirolom/",
                    "https://www.forumhouse.ru/journal/articles/6608-energoeffektivnyj-dom-uteplenie-ekstruzionnym-penopolistirolom-rekomendacii-specialista",
                    "https://dzen.ru/media/id/60cb33a630905a3396846873/uteplenie-sten-doma-penopolistirolom-svoimi-rukami-611a70d2edb815714c6bbcca",
                    "https://pikabu.ru/story/uteplenie_fasada_penopolistirolom_byit_ili_ne_byit_nyuansyi_tonkosti_5788808",
                    "https://knauftherm.ru/blog/kak-uteplit-naruzhnye-steny-penopolistirolom",
                    "https://stroychik.ru/naruzhnaya-otdelka/uteplenie-fasada-penoplastom",
                    "https://teplota.guru/teploizolyatsiya/uteplenie-sten-snaruzhi-penopolistirolom.html",
                    "https://www.ivd.ru/stroitelstvo-i-remont/steny/uteplenie-sten-penoplastom-poshagovaya-instrukciya-i-poleznye-sovety-53681",
                    "https://www.rmnt.ru/story/isolation/uteplenie-sten-penopolistirolom-svoimi-rukami.1239614/",
                    "https://stroyday.ru/stroitelstvo-doma/yteplenie-doma/uteplenie-sten-penoplastom-svoimi-rukami.html",
                    "https://otoplenie-expert.com/uteplenie-elementov-zdaniya/uteplenie-fasada-penopolistirolom-tehnologiya.html",
                    "https://www.tproekt.com/kakoj-penoplast-lucse-dla-uteplenia-doma-snaruzi/",
                    "https://tutknow.ru/building/uteplenie/6528-uteplenie-sten-iznutri-penopolistirolom.html",
                    "https://geostart.ru/post/73584",
                    "https://kronotech.ru/publications/uteplenie-doma-penoplastom-snaruzhi"
                ]
            ],
            "утепление стен снаружи" => [
                "sites" => [
                    "https://m-strana.ru/articles/uteplenie-doma-snaruzhi-materialy-normativy/",
                    "https://fasad-exp.ru/uteplenie/materialy-dlya-utepleniya-sten-snaruzhi.html",
                    "https://stroy-podskazka.ru/uteplenie/naruzhnoe/sten/",
                    "https://www.forumhouse.ru/journal/themes/128-kak-uteplit-fasad-bez-oshibok",
                    "https://remont-book.com/uteplenie-sten-doma-snaruzhi/",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5+%d1%81%d1%82%d0%b5%d0%bd+%d1%81%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%b8",
                    "https://stroyday.ru/stroitelstvo-doma/stroitelnye-materialy/naruzhnyj-uteplitel-dlya-sten.html",
                    "https://sovet-ingenera.com/otoplenie/uteplenie/uteplitel-dlya-sten-doma-snaruzhi.html",
                    "https://srbu.ru/stroitelnye-materialy/1948-chem-luchshe-uteplit-dom-snaruzhi.html",
                    "https://everest-dom.com/blog/uteplenie-sten-doma-snaruzhi",
                    "https://strir.ru/uteplenie/sten-snaruzhi",
                    "https://journal.tinkoff.ru/guide/teplodom/",
                    "https://kronotech.ru/publications/uteplenie-fasada-chastnogo-doma-snaruzhi",
                    "https://market.yandex.ru/search?text=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b8%d1%82%d0%b5%d0%bb%d0%b8%20%d0%b4%d0%bb%d1%8f%20%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%bd%d1%8b%d1%85%20%d1%81%d1%82%d0%b5%d0%bd%20%d0%b4%d0%be%d0%bc%d0%b0",
                    "http://remoo.ru/fasad/uteplenie-fasada-doma-snaruzhi",
                    "https://www.houzz.ru/statyi/tak-mozhno-uteplyaem-zagorodnyy-dom-i-ekonomim-na-otoplenii-stsetivw-vs~117034682",
                    "https://www.kp.ru/guide/uteplenie-fasada.html",
                    "https://moydomik.net/steny-i-perekrytiya/236-kak-uteplit-steny-snaruzhi.html",
                    "https://prostroymaterialy.com/kak-uteplit-dom-snaruzhi-nedorogo/",
                    "https://expert-dacha.pro/stroitelstvo/steny/uteplenie-st/snaruzhi.html"
                ]
            ],
            "утепление стен снаружи воронеж" => [
                "sites" => [
                    "https://m.avito.ru/voronezh/predlozheniya_uslug?query=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5%20%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%be%d0%b2",
                    "https://uslugi.yandex.ru/193-voronezh/category/remont-i-stroitelstvo/fasadnyie-rabotyi/uteplenie-fasadov--1995",
                    "https://visota-36.ru/uslugi/fasadnye-raboty/uteplenie-fasadov/",
                    "https://www.xn--b1agd0aean.xn----8sbaqbsujyzdlu.xn--p1ai/",
                    "https://voronezh.ooskidka.ru/uteplenie-fasada/",
                    "http://fasad36.ru/services/uteplenie-fasada/",
                    "https://voronezh.myguru.ru/services/gidroizolyaciya-i-uteplenie/uteplenie-sten/",
                    "https://xn--b1agd0aean.xn----7sbcsabtrfvtrsje3r.xn--p1ai/",
                    "https://vrn.profi.ru/remont/fasadnye-raboty/uteplenie-fasadov/",
                    "https://uslugio.com/voronezh/1/2/uteplenie-fasadov",
                    "https://dekor36.com/fasadi.html",
                    "https://36-fasad.ru/nashi-uslugi/uteplenie-fasadov-snaruzhi",
                    "https://zoon.ru/voronezh/m/teploizolyatsiya_sten/",
                    "https://vrn.masterdel.ru/master/uteplenie_sten_doma_snaruzhi/",
                    "https://sezrem.ru/prajs/prajs-list-na-uteplenie-fasadov/",
                    "https://vrn.latitudo.org/services/uteplenie-fasada/",
                    "https://stroidom36.ru/uteplenie-doma/",
                    "http://voronezh.teplo-dom76.ru/",
                    "https://rusalp-vrn.ru/%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5-%d1%81%d1%82%d0%b5%d0%bd-%d1%81%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%b8/",
                    "https://novostroy1.ru/fasadnye-raboty"
                ]
            ],
            "утепление стен снаружи минеральной ватой" => [
                "sites" => [
                    "https://m-strana.ru/articles/minvata-dlya-utepleniya-sten/",
                    "https://stroy-podskazka.ru/uteplenie/naruzhnoe/mineralnoj-vatoj/",
                    "https://expert-dacha.pro/stroitelstvo/steny/uteplenie-st/snaruzhi-minvatoj-pod-sajding.html",
                    "https://kronotech.ru/publications/uteplenie-fasada-minvatoy-snaruzhi",
                    "https://www.isover.ru/articles/uteplenie-fasadov-doma-minvatoj",
                    "https://uytchasndom.ru/uteplenie-doma/uteplenie-doma-snaruzhi-minvatoj",
                    "https://www.youtube.com/watch?v=i4zemnoksdg",
                    "https://stroyguru.com/remont-kvartiry/steny/uteplenie-sten-mineralnoj-vatoj-svoimi-rukami/",
                    "https://www.ivd.ru/stroitelstvo-i-remont/steny/mineralnaya-vata-dlya-utepleniya-sten-sovety-po-vyboru-i-montazhu-38361",
                    "https://optimfasad.ru/uteplenie-doma-mineralnoj-vatoj",
                    "https://uteplenieplus.ru/kak-uteplit/fasady/texnologiya-utepleniya-mineralnoj-vatoj/",
                    "https://www.forumhouse.ru/journal/articles/9225-effektivnost-odnosloinogo-utepleniya-dokazano-mozhno-ne-uslozhnyat",
                    "https://strir.ru/uteplenie/minvatoy",
                    "https://kakpostroit.by/naruzhnaya-otdelka/uteplenie-steny-minvatoj-snaruzhi.html",
                    "https://klub-masterov.ru/steny/kakuju-minvatu-ispolzovat-dlja-uteplenija-sten-snaruzhi.html",
                    "https://1beton.info/maloetazhnoe/otdelka/uteplenie-fasada-doma-snaruzhi-minvatoj-pod-shtukaturku",
                    "https://www.tproekt.com/pravilnoe-uteplenie-doma-iz-brusa-ili-brevna-minvatoj-plusy-i-minusy-materiala-analiz-cen/",
                    "https://domzastroika.ru/walls/uteplenie-fasadov-mineralnoj-vatoj-svoimi-rukami.html",
                    "https://remstd.ru/archives/uteplenie-sten-snaruzhi-mineralnoy-vatoy-poshagovaya-instruktsiya/",
                    "https://fasad-exp.ru/uteplenie/uteplenie-derevyannogo-doma-minvatoy.html"
                ]
            ],
            "утепление стен снаружи пенопластом" => [
                "sites" => [
                    "https://fasad-exp.ru/vidy-materialov-dlya-otdelki-fasadov/shtukaturka/uteplenie-sten-snaruzhi-penoplastom.html",
                    "https://m-strana.ru/articles/uteplenie-penoplastom/",
                    "https://www.ivd.ru/stroitelstvo-i-remont/steny/uteplenie-sten-penoplastom-poshagovaya-instrukciya-i-poleznye-sovety-53681",
                    "https://stroychik.ru/naruzhnaya-otdelka/uteplenie-fasada-penoplastom",
                    "https://dom-i-remont.info/posts/stenyi/uteplenie-sten-snaruzhi-penoplastom-plyusy-i-minusy-teploizolyaczii/",
                    "https://stroyguru.com/remont-kvartiry/kak-uteplit-vnutrennie-i-naruzhnye-steny-penoplastom/",
                    "https://stroy-podskazka.ru/penoplast/fasadnyj/",
                    "https://dekormyhome.ru/remont-i-oformlenie/kak-pravilno-ytepliat-steny-penoplastom-snaryji-poshagovoe-rykovodstvo.html",
                    "https://dzen.ru/media/rmnt/uteplenie-fasada-penoplastom-svoimi-rukami-5ce7c9aadd00af00b25acfcf",
                    "https://knauftherm.ru/blog/uteplenie-fasada-doma-penoplastom",
                    "https://kronotech.ru/publications/uteplenie-doma-penoplastom-snaruzhi",
                    "https://www.youtube.com/watch?v=360ropduyju",
                    "https://stroyday.ru/news/uteplenie-doma-penoplastom-plyusy-i-minusy-lichnyj-opyt-i-podrobnaya-instrukciya.html",
                    "https://expert-dacha.pro/stroitelstvo/steny/uteplenie-st/penoplastom-snaruzhi.html",
                    "https://strir.ru/uteplenie/sten-penopolistirolom",
                    "https://pikabu.ru/story/uteplenie_fasada_penopolistirolom_byit_ili_ne_byit_nyuansyi_tonkosti_5788808",
                    "https://otoplenie-expert.com/uteplenie-elementov-zdaniya/uteplenie-fasada-penopolistirolom-tehnologiya.html",
                    "https://bazafasada.ru/fasad-chastnogo-doma/vneshnee-uteplenie-sten-penoplastom.html",
                    "https://mr-build.ru/newteplo/kak-uteplit-dom-penoplastom-snaruzi-svoimi-rukami.html",
                    "https://otdelkasten.com/uteplenie/penoplast-dlja-uteplenija-sten-snaruzhi"
                ]
            ],
            "утепление стен снаружи цена" => [
                "sites" => [
                    "https://profi.ru/remont/fasadnye-raboty/uteplenie-fasadov/price/",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5+%d1%81%d1%82%d0%b5%d0%bd+%d1%81%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%b8",
                    "https://www.avito.ru/moskva?q=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%be%d0%b2",
                    "https://xn--80adxhks.xn----7sbqwmjkp7a9c.xn--p1ai/%d0%a3%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5-%d0%ba%d0%b2%d0%b0%d1%80%d1%82%d0%b8%d1%80%d1%8b/",
                    "https://tigris-alp.ru/uslugi/fasadnye-raboty/uteplenie-sten/",
                    "https://zoon.ru/msk/m/teploizolyatsiya_sten/",
                    "https://www.cottedge.com/vneshnyaya-otdelka-doma/uteplenie-fasada/",
                    "https://www.prof-fasady.ru/catalog/fasad-doma/uteplenie/",
                    "https://moskva.ooskidka.ru/uteplenie-fasada/",
                    "https://dab-stroi.ru/uslugi/uteplenie/uteplenie-chastnogo-doma",
                    "https://alpbond.org/uteplenie-panelnogo-doma-snaruzhi/",
                    "https://xn------6cdlbpgnjaivdekjdhaflsekp2c7lldf9a.xn--p1ai/%d1%85%d0%be%d0%bb%d0%be%d0%b4%d0%bd%d0%b0%d1%8f-%d1%81%d1%82%d0%b5%d0%bd%d0%b0/",
                    "http://www.n-dom.ru/uteplenie-fasada/penopolisterol",
                    "https://optimumbuilding.ru/uteplenie-fasada",
                    "https://zod07.ru/fasadnye-raboty/uteplenie-fasadov/",
                    "https://www.xn--80adxhks.xn----8sbaqbsujyzdlu.xn--p1ai/",
                    "https://kronotech.ru/fasadnye-raboty/uteplenie-fasadov",
                    "https://www.ksu-nordwest.ru/price/price-na-otdelku-fasada.php",
                    "https://msk.alpateks.ru/uteplenie-sten/",
                    "https://frontmaster.su/services/uteplenie/"
                ]
            ],
            "утепление фасада" => [
                "sites" => [
                    "https://uslugi.yandex.ru/213-moscow/category/remont-i-stroitelstvo/fasadnyie-rabotyi/uteplenie-fasadov--1995",
                    "https://www.forumhouse.ru/journal/themes/128-kak-uteplit-fasad-bez-oshibok",
                    "https://m-strana.ru/articles/pravila-vybora-fasadnogo-uteplitelya/",
                    "https://market.yandex.ru/journal/expertise/kak-pravilno-uteplit-fasad-i-vipolnit-finishnuju-otdelku",
                    "https://www.kp.ru/guide/uteplenie-fasada.html",
                    "https://teplo-facad.ru/",
                    "https://www.avito.ru/moskva/uslugi?q=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%be%d0%b2",
                    "http://remoo.ru/fasad/uteplenie-fasada-doma-snaruzhi",
                    "https://stroy-podskazka.ru/uteplenie/naruzhnoe/fasadov/",
                    "https://profi.ru/remont/fasadnye-raboty/uteplenie-fasadov/price/",
                    "https://kronotech.ru/fasadnye-raboty/uteplenie-fasadov",
                    "https://abk-fasad.ru/catalog/uteplenie-fasadov",
                    "https://www.youtube.com/playlist?list=pluivzwm_q9lz2vbendttxwhuhycp_9vhq",
                    "https://leroymerlin.ru/catalogue/teploizolyaciya/uteplenie-v-sisteme-shtukaturnyy-fasad/",
                    "https://www.cottedge.com/vneshnyaya-otdelka-doma/uteplenie-fasada/",
                    "https://www.tn.ru/library/poleznaja_informacija/uteplenie_sten/",
                    "https://aograd.ru/uteplenie-fasadov/",
                    "https://moskva.ooskidka.ru/uteplenie-fasada/",
                    "https://domzastroika.ru/fasad/materialy-i-tehnologija-uteplenija.html",
                    "https://www.rockwool.com/ru/products-and-applications/external-wall/"
                ]
            ],
            "утепление фасада воронеж" => [
                "sites" => [
                    "https://m.avito.ru/voronezh/predlozheniya_uslug?query=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5%20%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%be%d0%b2",
                    "https://uslugi.yandex.ru/193-voronezh/category/remont-i-stroitelstvo/fasadnyie-rabotyi/uteplenie-fasadov--1995",
                    "http://fasad36.ru/services/uteplenie-fasada/",
                    "https://www.xn--b1agd0aean.xn----8sbaqbsujyzdlu.xn--p1ai/",
                    "https://voronezh.ooskidka.ru/uteplenie-fasada/",
                    "https://dekor36.com/fasadi.html",
                    "https://vrn.profi.ru/remont/fasadnye-raboty/uteplenie-fasadov/",
                    "https://visota-36.ru/uslugi/fasadnye-raboty/uteplenie-fasadov/",
                    "https://sezrem.ru/prajs/prajs-list-na-uteplenie-fasadov/",
                    "https://xn--80ajyeqbrf.xn--p1ai/",
                    "https://uslugio.com/voronezh/1/2/uteplenie-fasadov",
                    "https://voronezh.myguru.ru/services/fasadnye-raboty/uslugi-po-utepleniyu-fasada-zdaniya/",
                    "https://novostroy1.ru/fasadnye-raboty",
                    "https://xn--b1agd0aean.xn----7sbcsabtrfvtrsje3r.xn--p1ai/",
                    "https://36-fasad.ru/",
                    "https://vrn.latitudo.org/services/uteplenie-fasada/",
                    "https://zoon.ru/voronezh/m/teploizolyatsiya_fasada/",
                    "https://stroidom36.ru/uteplenie-doma/",
                    "https://promoizol.ru/_voronezh/uteplenie-fasada",
                    "https://voronezh.skidkom.ru/uslugi/uteplenie-fasadov/"
                ]
            ],
            "утепление фасада дома" => [
                "sites" => [
                    "https://www.forumhouse.ru/journal/themes/128-kak-uteplit-fasad-bez-oshibok",
                    "https://uslugi.yandex.ru/213-moscow/category/remont-i-stroitelstvo/fasadnyie-rabotyi/uteplenie-fasadov--1995",
                    "https://m-strana.ru/articles/uteplenie-fasada-chastnogo-doma-neobkhodimost-vybor-materialov-oblitsovka/",
                    "https://market.yandex.ru/journal/expertise/kak-pravilno-uteplit-fasad-i-vipolnit-finishnuju-otdelku",
                    "http://remoo.ru/fasad/uteplenie-fasada-doma-snaruzhi",
                    "https://www.kp.ru/guide/uteplenie-fasada.html",
                    "https://kronotech.ru/publications/uteplenie-fasada-chastnogo-doma-snaruzhi",
                    "https://www.avito.ru/moskva/uslugi?q=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%be%d0%b2",
                    "https://profi.ru/remont/fasadnye-raboty/uteplenie-fasadov/price/",
                    "https://stroy-podskazka.ru/uteplenie/naruzhnoe/fasadov/",
                    "https://www.cottedge.com/vneshnyaya-otdelka-doma/uteplenie-fasada/",
                    "https://www.youtube.com/playlist?list=pluivzwm_q9lz2vbendttxwhuhycp_9vhq",
                    "https://fasad-exp.ru/uteplenie/materialy-dlya-utepleniya-sten-snaruzhi.html",
                    "https://zoon.ru/msk/m/teploizolyatsiya_fasada/",
                    "https://domzastroika.ru/fasad/materialy-i-tehnologija-uteplenija.html",
                    "https://optimfasad.ru/uteplenie-fasada-doma",
                    "https://zod07.ru/fasadnye-raboty/uteplenie-fasadov/",
                    "https://planken.guru/otdelka-i-montazh-fasadov/teploizolyaciya-i-uteplenie-fasada-zdaniy-i-chastnyh-domov-snaruzhi.html",
                    "https://stroyday.ru/stroitelstvo-doma/stroitelnye-materialy/naruzhnyj-uteplitel-dlya-sten.html",
                    "https://aograd.ru/uteplenie-fasadov/"
                ]
            ],
            "утепление фасада минватой" => [
                "sites" => [
                    "https://uteplenieplus.ru/kak-uteplit/fasady/texnologiya-utepleniya-mineralnoj-vatoj/",
                    "https://m-strana.ru/articles/naruzhnoe-uteplenie-doma-mineralnoy/",
                    "https://luchiefasady.ru/tehnologiya-utepleniya-fasadov-minvatoy.html",
                    "https://krasnyjdom.com/steny/uteplenie-fasada/minvatoj.html",
                    "https://www.isover.ru/articles/uteplenie-fasadov-doma-minvatoj",
                    "https://kronotech.ru/publications/uteplenie-fasada-minvatoy-snaruzhi",
                    "https://www.youtube.com/watch?v=wgsqkvazum0",
                    "https://expert-dacha.pro/stroitelstvo/steny/uteplenie-st/fasadov-mineralnoj-vatoj.html",
                    "https://aograd.ru/uteplenie-fasadov/minvata/",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5+%d0%bc%d0%b8%d0%bd%d0%b5%d1%80%d0%b0%d0%bb%d1%8c%d0%bd%d0%be%d0%b9+%d0%b2%d0%b0%d1%82%d0%be%d0%b9",
                    "https://zod07.ru/fasadnye-raboty/uteplenie-fasadov/uteplenie-fasada-minvatoj",
                    "https://stroy-podskazka.ru/uteplenie/naruzhnoe/mineralnoj-vatoj/",
                    "https://www.tproekt.com/podrobnoe-opisanie-tehnologii-uteplenia-fasada-minvatoj/",
                    "http://www.n-dom.ru/uteplenie-fasada/mineralnoj-vatoj",
                    "https://kuzmich24.ru/stat_i_i_sovety/uteplenie-fasada-doma-mineralnoj-vatoj/",
                    "https://xn--80ac1bcbgb9aa.xn--p1ai/uteplenie-fasada-mineralnoj-vatoj/",
                    "https://vdomishke.ru/uteplenie-fasada-vatoj/",
                    "https://1pofasadu.ru/uteplenie/fasada-minvatoy-tehnologiya.html",
                    "https://1beton.info/maloetazhnoe/otdelka/uteplenie-fasada-doma-snaruzhi-minvatoj-pod-shtukaturku",
                    "https://uteplix.com/obyekty/fasad/vse-o-tehnologii-utepleniya-fasadov-minvatoj-pod-shtukaturku-na-primere-realnogo-obekta.html"
                ]
            ],
            "утепление фасада минплитой" => [
                "sites" => [
                    "https://uteplenieplus.ru/kak-uteplit/fasady/texnologiya-utepleniya-mineralnoj-vatoj/",
                    "https://m-strana.ru/articles/minvata-dlya-utepleniya-sten/",
                    "https://krasnyjdom.com/steny/uteplenie-fasada/minvatoj.html",
                    "https://plusteplo.ru/uteplenie/fasady/kak-uteplit-fasad-doma-minvatoj-svoimi-rukami.html",
                    "https://kronotech.ru/publications/uteplenie-fasada-minvatoy-snaruzhi",
                    "https://uslugi.yandex.ru/213-moscow/landing/%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5%20%d1%81%d1%82%d0%b5%d0%bd%20%d0%bc%d0%b8%d0%bd%d0%b5%d1%80%d0%b0%d0%bb%d0%be%d0%b2%d0%b0%d1%82%d0%bd%d1%8b%d0%bc%d0%b8%20%d0%bf%d0%bb%d0%b8%d1%82%d0%b0%d0%bc%d0%b8",
                    "https://nav.tn.ru/upload/iblock/150/instruktsiya_po_montazhu_sistem_teploizolyatsii_fasadov_s_tonkim_shtukaturnym_sloem.pdf",
                    "https://luchiefasady.ru/tehnologiya-utepleniya-fasadov-minvatoy.html",
                    "https://1pofasadu.ru/uteplenie/fasada-minvatoy-tehnologiya.html",
                    "https://www.forumhouse.ru/journal/articles/9225-effektivnost-odnosloinogo-utepleniya-dokazano-mozhno-ne-uslozhnyat",
                    "http://www.n-dom.ru/uteplenie-fasada/mineralnoj-vatoj",
                    "https://msk-krovli.ru/fasadnye-raboty/uteplenie-fasada-minvatoj/",
                    "https://2797921.ru/uteplenie-fasada-minvatoj/",
                    "https://www.isover.ru/articles/uteplenie-fasadov-doma-minvatoj",
                    "https://stenaexpert.ru/otdelka/shtukaturka/tekhnologiya-utepleniya-fasada-minvatoj-pod-shtukaturku",
                    "https://expert-dacha.pro/stroitelstvo/steny/uteplenie-st/fasadov-mineralnoj-vatoj.html",
                    "https://stroy-podskazka.ru/uteplenie/naruzhnoe/mineralnoj-vatoj/",
                    "https://uteplix.com/obyekty/fasad/vse-o-tehnologii-utepleniya-fasadov-minvatoj-pod-shtukaturku-na-primere-realnogo-obekta.html",
                    "https://www.air-ventilation.ru/uteplenie-fasada-minvatoy.htm",
                    "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/texnologiya-utepleniya-mokryj-fasad.html"
                ]
            ],
            "утепление фасадов в воронеже" => [
                "sites" => [
                    "https://m.avito.ru/voronezh/predlozheniya_uslug?query=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5%20%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%be%d0%b2",
                    "https://uslugi.yandex.ru/193-voronezh/category/remont-i-stroitelstvo/fasadnyie-rabotyi/uteplenie-fasadov--1995",
                    "http://fasad36.ru/services/uteplenie-fasada/",
                    "https://www.xn--b1agd0aean.xn----8sbaqbsujyzdlu.xn--p1ai/",
                    "https://voronezh.ooskidka.ru/uteplenie-fasada/",
                    "https://dekor36.com/fasadi.html",
                    "https://36-fasad.ru/",
                    "https://sezrem.ru/prajs/prajs-list-na-uteplenie-fasadov/",
                    "https://visota-36.ru/uslugi/fasadnye-raboty/uteplenie-fasadov/",
                    "https://vrn.profi.ru/remont/fasadnye-raboty/uteplenie-fasadov/",
                    "https://novostroy1.ru/fasadnye-raboty",
                    "https://stroidom36.ru/uteplenie-doma/",
                    "https://xn--b1agd0aean.xn----7sbcsabtrfvtrsje3r.xn--p1ai/",
                    "https://voronezh.myguru.ru/services/fasadnye-raboty/uslugi-po-utepleniyu-fasada-zdaniya/",
                    "https://vrn.latitudo.org/services/uteplenie-fasada/",
                    "https://kronvest.net/voronezh/fasad",
                    "https://promoizol.ru/_voronezh/uteplenie-fasada",
                    "http://teplofasad36.ru/uteplenie-fasadov",
                    "https://voronezh.skidkom.ru/uslugi/uteplenie-fasadov/",
                    "https://uslugio.com/voronezh/1/2/uteplenie-fasadov"
                ]
            ],
            "утепление фасадов пенопластом" => [
                "sites" => [
                    "https://stroychik.ru/naruzhnaya-otdelka/uteplenie-fasada-penoplastom",
                    "https://fasad-exp.ru/vidy-materialov-dlya-otdelki-fasadov/shtukaturka/uteplenie-sten-snaruzhi-penoplastom.html",
                    "https://stroy-podskazka.ru/penoplast/fasadnyj/",
                    "https://m-strana.ru/articles/uteplenie-penoplastom/",
                    "https://zen.yandex.ru/media/rmnt/uteplenie-fasada-penoplastom-svoimi-rukami-5ce7c9aadd00af00b25acfcf",
                    "https://knauftherm.ru/blog/uteplenie-fasada-doma-penoplastom",
                    "https://www.avito.ru/moskva?q=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%be%d0%b2+%d0%bf%d0%b5%d0%bd%d0%be%d0%bf%d0%bb%d0%b0%d1%81%d1%82%d0%be%d0%bc",
                    "https://www.youtube.com/watch?v=360ropduyju",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5+%d0%bf%d0%b5%d0%bd%d0%be%d0%bf%d0%bb%d0%b0%d1%81%d1%82%d0%be%d0%bc+%d1%86%d0%b5%d0%bd%d0%b0+%d0%b7%d0%b0+%d0%bc2",
                    "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/uteplenie-fasada-penoplastom-svoimi-rukami.html",
                    "https://dom-i-remont.info/posts/stenyi/uteplenie-sten-snaruzhi-penoplastom-plyusy-i-minusy-teploizolyaczii/",
                    "https://pikabu.ru/story/uteplenie_fasada_penopolistirolom_byit_ili_ne_byit_nyuansyi_tonkosti_5788808",
                    "https://otoplenie-expert.com/uteplenie-elementov-zdaniya/uteplenie-fasada-penopolistirolom-tehnologiya.html",
                    "https://zod07.ru/fasadnye-raboty/uteplenie-fasadov/uteplenie-fasada-penoplastom",
                    "https://www.forumhouse.ru/threads/185220/",
                    "https://postroy-sam.info/fasad-doma/214-tekhnologiya-utepleniya-fasada-penoplastom-html",
                    "https://expertfasada.ru/fasad/uteplenie-fasada/kak-uteplit-fasad-penopolistirolom/",
                    "https://expert-dacha.pro/stroitelstvo/steny/uteplenie-st/fasada-penoplastom.html",
                    "https://myguru.ru/services/fasadnye-raboty/fasadnoe-uteplenie-penoplastom/",
                    "https://moydomik.net/fasad/434-uteplenie-fasada-penoplastom.html"
                ]
            ],
            "утепление фасадов пенополистиролом" => [
                "sites" => [
                    "https://stroychik.ru/naruzhnaya-otdelka/uteplenie-fasada-penoplastom",
                    "https://otoplenie-expert.com/uteplenie-elementov-zdaniya/uteplenie-fasada-penopolistirolom-tehnologiya.html",
                    "https://pikabu.ru/story/uteplenie_fasada_penopolistirolom_byit_ili_ne_byit_nyuansyi_tonkosti_5788808",
                    "https://www.forumhouse.ru/journal/articles/10434-pochemu-mokryy-fasad-vsegda-budet-vostrebovan-i-kak-sdelat-pravilno-chtoby-ne-pozhalet",
                    "https://knauftherm.ru/blog/uteplenie-fasada-doma-penoplastom",
                    "https://stroy-podskazka.ru/penoplast/fasadnyj/",
                    "https://expertfasada.ru/fasad/uteplenie-fasada/kak-uteplit-fasad-penopolistirolom/",
                    "https://www.cottedge.com/vneshnyaya-otdelka-doma/uteplenie-fasada-penopolistirolom/",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0+%d0%bf%d0%b5%d0%bd%d0%be%d0%bf%d0%be%d0%bb%d0%b8%d1%81%d1%82%d0%b8%d1%80%d0%be%d0%bb%d0%be%d0%bc",
                    "https://www.youtube.com/watch?v=5pos-gvlurc",
                    "https://dom-i-remont.info/posts/stenyi/uteplenie-sten-snaruzhi-penoplastom-plyusy-i-minusy-teploizolyaczii/",
                    "https://x-teplo.ru/uteplenie/fasady/texnologiya-penopolistirolom.html",
                    "https://strport.ru/izolyatsionnye-materialy/utepliteli/uteplenie-fasada-penopolistirolom-poshagovaya-instruktsiya",
                    "https://fasad-exp.ru/vidy-materialov-dlya-otdelki-fasadov/shtukaturka/uteplenie-sten-snaruzhi-penoplastom.html",
                    "https://expert-dacha.pro/stroitelstvo/steny/uteplenie-st/fasada-penopolistirolom.html",
                    "https://m-strana.ru/articles/uteplenie-penoplastom/",
                    "https://dzen.ru/media/rmnt/uteplenie-fasada-penoplastom-svoimi-rukami-5ce7c9aadd00af00b25acfcf",
                    "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/uteplenie-fasada-penoplastom-svoimi-rukami.html",
                    "https://zod07.ru/fasadnye-raboty/uteplenie-fasadov/uteplenie-fasada-penoplastom",
                    "https://m.avito.ru/moskva_i_mo?query=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5%20%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%be%d0%b2%20%d0%bf%d0%b5%d0%bd%d0%be%d0%bf%d0%bb%d0%b0%d1%81%d1%82%d0%be%d0%bc"
                ]
            ],
            "утепление фасадов цена" => [
                "sites" => [
                    "https://profi.ru/remont/fasadnye-raboty/uteplenie-fasadov/price/",
                    "https://uslugi.yandex.ru/213-moscow/category/remont-i-stroitelstvo/fasadnyie-rabotyi/uteplenie-fasadov--1995",
                    "https://www.avito.ru/moskva?q=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%be%d0%b2",
                    "https://www.cottedge.com/vneshnyaya-otdelka-doma/uteplenie-fasada/",
                    "http://www.n-dom.ru/uteplenie-fasada/",
                    "https://zoon.ru/msk/m/teploizolyatsiya_fasada/",
                    "https://mosstroikrovlya.ru/uteplenie-fasadov/",
                    "https://zod07.ru/fasadnye-raboty/uteplenie-fasadov/",
                    "https://kronotech.ru/prays-na-fasadnye-raboty-v-moskve",
                    "https://www.prof-fasady.ru/catalog/fasad-doma/uteplenie/",
                    "https://moskva.ooskidka.ru/uteplenie-fasada/",
                    "https://tigris-alp.ru/uslugi/fasadnye-raboty/uteplenie-sten/",
                    "https://www.stroyremfasad.ru/fasadnye-raboty/uteplenie-fasada/",
                    "http://luxelitstroy.ru/price/price_otdelka_fasadov.php",
                    "https://fasadrf.ru/fasadnyeraboty/utepleniefasada/",
                    "https://xn--80adxhks.xn----7sbqwmjkp7a9c.xn--p1ai/%d0%a3%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5-%d0%ba%d0%b2%d0%b0%d1%80%d1%82%d0%b8%d1%80%d1%8b/",
                    "https://www.ksu-nordwest.ru/price/price-na-otdelku-fasada.php",
                    "https://moskva.skidkom.ru/uslugi/uteplenie-fasadov/",
                    "https://klinkerwall.ru/uteplenie-fasada/",
                    "https://optimumbuilding.ru/uteplenie-fasada"
                ]
            ],
            "утепление фасадов частных домов" => [
                "sites" => [
                    "https://m-strana.ru/articles/uteplenie-fasada-chastnogo-doma-neobkhodimost-vybor-materialov-oblitsovka/",
                    "https://www.forumhouse.ru/journal/themes/128-kak-uteplit-fasad-bez-oshibok",
                    "https://uslugi.yandex.ru/213-moscow/category/remont-i-stroitelstvo/fasadnyie-rabotyi/uteplenie-fasadov--1995",
                    "https://www.avito.ru/moskva/uslugi?q=%d1%83%d1%82%d0%b5%d0%bf%d0%bb%d0%b5%d0%bd%d0%b8%d0%b5+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%be%d0%b2",
                    "http://remoo.ru/fasad/uteplenie-fasada-doma-snaruzhi",
                    "https://stroy-podskazka.ru/uteplenie/naruzhnoe/fasadov/",
                    "https://market.yandex.ru/journal/expertise/kak-pravilno-uteplit-fasad-i-vipolnit-finishnuju-otdelku",
                    "https://www.youtube.com/playlist?list=pluivzwm_q9lz2vbendttxwhuhycp_9vhq",
                    "https://optimfasad.ru/uteplenie-fasada-doma",
                    "https://domzastroika.ru/fasad/materialy-i-tehnologija-uteplenija.html",
                    "https://fasad-exp.ru/uteplenie/materialy-dlya-utepleniya-sten-snaruzhi.html",
                    "https://planken.guru/otdelka-i-montazh-fasadov/teploizolyaciya-i-uteplenie-fasada-zdaniy-i-chastnyh-domov-snaruzhi.html",
                    "https://sovet-ingenera.com/otoplenie/uteplenie/uteplenie-chastnogo-doma-snaruzhi.html",
                    "https://frontmaster.su/services/uteplenie/",
                    "https://teplo-facad.ru/",
                    "https://www.kp.ru/guide/uteplenie-fasada.html",
                    "https://journal.tinkoff.ru/guide/teplodom/",
                    "https://kronotech.ru/publications/uteplenie-fasada-chastnogo-doma-snaruzhi",
                    "https://domastroika.com/naibolee-effektivnye-sposoby-utepleniya-fasadov/",
                    "https://profi.ru/remont/fasadnye-raboty/uteplenie-fasadov/price/"
                ]
            ],
            "фасадные материалы" => [
                "sites" => [
                    "https://leroymerlin.ru/catalogue/fasadnye-materialy/",
                    "https://moscow.tnsystem.ru/category/fasadnye-materialy/",
                    "https://moscow.petrovich.ru/catalog/12101/",
                    "https://msk.blizko.ru/predl/construction/building/facade",
                    "https://www.krowlia.ru/catalog/fasadnye-materialy/",
                    "https://msk.pulscen.ru/price/1008-fasadnye-materialy",
                    "https://moskva.satom.ru/t/fasadnye-materialy-4973/",
                    "https://www.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/interer-i-otdelka/fasadnye-paneli/",
                    "https://roofside.ru/product-category/fasadnye-materialy/",
                    "https://komplekto.ru/catalog/fasad/",
                    "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/kakoj-material-deshevle-i-luchshe-dlya-oblicovki-fasada-doma-obzor-top-9-populyarnyx-materialov.html",
                    "https://domcomfort.ru/katalog/fasadnye-materialy",
                    "https://www.tstn.ru/shop/fasady_i_zabory/",
                    "https://domof.ru/articles/kakoy-material-vybrat-dlya-otdelki-fasada-zdaniya/",
                    "https://krovelnii.ru/category/fasadnye-materialy/",
                    "https://srbu.ru/otdelochnye-materialy/1950-varianty-otdelki-fasada-chastnogo-doma.html",
                    "https://zod07.ru/statji/kak-vybrat-fasadnye-materialy-dlya-otdelki-doma-snaruzhi",
                    "https://dzen.ru/media/tablichnik/luchshie-materialy-dlia-otdelki-fasada-chastnogo-doma-5f0ffd317e2b585adad67632",
                    "https://markakachestva.ru/rating-of/2247-luchshie-materialy-dlja-oblicovki-fasada.html",
                    "https://www.rontfasad.ru/catalog/"
                ]
            ],
            "фасадные работы" => [
                "sites" => [
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d0%a4%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b5%20%d1%80%d0%b0%d0%b1%d0%be%d1%82%d1%8b",
                    "https://www.avito.ru/moskva?q=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b5+%d1%80%d0%b0%d0%b1%d0%be%d1%82%d1%8b",
                    "https://2gis.ru/moscow/search/%d0%a4%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b5%20%d1%80%d0%b0%d0%b1%d0%be%d1%82%d1%8b/rubricid/648",
                    "https://profi.ru/remont/fasadnye-raboty/",
                    "https://fasadrf.ru/fasadnyeraboty/",
                    "https://kronotech.ru/fasadnye-raboty",
                    "https://zoon.ru/msk/building/type/fasadnye_raboty/",
                    "https://mosstroikrovlya.ru/fasadnye-raboty/",
                    "https://www.prof-fasady.ru/",
                    "https://zod07.ru/fasadnye-raboty/",
                    "https://www.fasadbau.com/",
                    "https://hidropro.ru/services/fasadnye-raboty/",
                    "https://poisk-pro.ru/masters/moskva/fasadnye-raboty",
                    "https://good-facade.ru/",
                    "https://www.remontnik.ru/moskva/fasadnye_raboty/",
                    "https://www.int-ext.ru/fasadnye-raboty.htm",
                    "https://alonti.ru/moskva/stroy/fasadnye-raboty/",
                    "https://www.stroyportal.ru/catalog/section-fasadnye-raboty-191/",
                    "https://tehstroy-city.ru/obshhestroitelnyie-rabotyi/fasad/czena-fasadnyix-rabot",
                    "https://www.orgpage.ru/moskva/%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b5_%d1%80%d0%b0%d0%b1%d0%be%d1%82%d1%8b/"
                ]
            ],
            "фасадные работы воронеж" => [
                "sites" => [
                    "https://www.avito.ru/voronezh?q=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b5+%d1%80%d0%b0%d0%b1%d0%be%d1%82%d1%8b",
                    "https://uslugi.yandex.ru/193-voronezh/category/remont-i-stroitelstvo/fasadnyie-rabotyi--1981",
                    "https://2gis.ru/voronezh/search/%d0%a4%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b5%20%d1%80%d0%b0%d0%b1%d0%be%d1%82%d1%8b/rubricid/648",
                    "https://zoon.ru/voronezh/m/fasadnye_raboty-8147/",
                    "https://36-fasad.ru/",
                    "https://vrn.profi.ru/remont/fasadnye-raboty/",
                    "https://www.cmlt.ru/ads--rubric-402-servicetype-30444",
                    "https://uslugio.com/voronezh/1/9/fasadnye-raboty",
                    "https://dekor36.com/fasadi.html",
                    "https://kronvest.net/voronezh/fasad",
                    "https://novostroy1.ru/fasadnye-raboty",
                    "https://vrn.masterdel.ru/master/fasadnye-raboty/",
                    "http://xn--36-glchqd5adeocin.xn--p1ai/fasadnye-raboty.html",
                    "http://fasad36.ru/",
                    "https://voronezh.stroyportal.ru/catalog/section-fasadnye-raboty-191/",
                    "https://voronezh.spravka.city/fasadnye-raboty",
                    "https://voronezh.myguru.ru/services/fasadnye-raboty/",
                    "https://voronezhskaya.spravochnika.ru/c-voronezh/fasadnye-raboty",
                    "http://voronezh.2map.su/catalog/category/785/",
                    "https://www.orgpage.ru/voronezh/%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b5_%d1%80%d0%b0%d0%b1%d0%be%d1%82%d1%8b/"
                ]
            ],
            "штукатурка декоративная фасадная" => [
                "sites" => [
                    "https://leroymerlin.ru/catalogue/shtukaturki/dekorativnye-shtukaturki-dlya-fasadov/",
                    "https://www.ozon.ru/category/dekorativnaya-shtukaturka-dlya-fasadov/",
                    "https://market.yandex.ru/search?text=%d0%b4%d0%b5%d0%ba%d0%be%d1%80%d0%b0%d1%82%d0%b8%d0%b2%d0%bd%d0%b0%d1%8f%20%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d0%b0%d1%8f%20%d0%b4%d0%bb%d1%8f%20%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%bd%d1%8b%d1%85%20%d1%80%d0%b0%d0%b1%d0%be%d1%82%20%d1%86%d0%b5%d0%bd%d0%b0",
                    "https://st-par.ru/catalog/dekorativnye-shtukaturki/fasad/",
                    "https://moscow.petrovich.ru/catalog/6654/fasadnaya-dekorativnaya-shtukaturka/",
                    "https://vgtkraska.ru/katalog/dekor?page=3",
                    "https://www.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/fasadnaya/",
                    "https://stroy-podskazka.ru/shtukaturka/fasadnaya-dekorativnaya/",
                    "https://dekormos.ru/35-fasadnye-shtukaturki",
                    "https://m-strana.ru/design/dekorativnaya-shtukaturka-dlya-fasada-doma-vidy-i-osobennosti/",
                    "https://expert-deco.ru/catalog/fasadnaya-shtukaturka/",
                    "https://glavsnab.net/dekorativnaya-fasadnaya-shtukaturka",
                    "https://www.baufasad.ru/catalog/dekorativnaya_shtukaturka_dlya_mokrogo_fasada/",
                    "https://dessa-decor.ru/catalog/fasadnye_shtukaturki/",
                    "https://sanmarco-vernici.ru/dekorativnye-shtukaturki/dekorativnaya-fasadnaya-shtukaturka/",
                    "https://abk-fasad.ru/catalog/uteplenie-fasadov/decorative-shtukaturki",
                    "https://dekoriko.ru/shtukaturka/fasadnaya-dekorativnaya/",
                    "https://msk.blizko.ru/predl/construction/decoration/smesi/shtukaturki_dekorativny/f:33528_dlia-fasadov",
                    "https://www.albia.ru/fasadnye-sistemy-i-komplektuyushchie/finishnye-dekorativnye-pokrytiya/",
                    "https://dom-kraski.ru/catalog/fasadnye-raboty/fasadnye-dekorativnye-shtukaturki/"
                ]
            ],
            "штукатурка короед купить в воронеже" => [
                "sites" => [
                    "https://voronezh.leroymerlin.ru/catalogue/shtukaturki/koroed/",
                    "https://www.avito.ru/voronezh?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                    "https://market.yandex.ru/search?text=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4%20%d0%b2%20%d0%b2%d0%be%d1%80%d0%be%d0%bd%d0%b5%d0%b6%d0%b5%20%d1%86%d0%b5%d0%bd%d1%8b",
                    "https://voronezh.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/koroed/",
                    "https://voronezh.stroyportal.ru/catalog/section-shtukaturka-koroed-7581/",
                    "https://voronezh.regmarkets.ru/shtukaturka-koroed/",
                    "https://kraski36.ru/shtukaturka-koroed-voronezh/",
                    "https://voronezh.pulscen.ru/price/110514-shtukaturka/f:62057_koroied",
                    "http://fasad36.ru/catalog/koroed/",
                    "https://www.ozon.ru/category/shtukaturki-koroed/",
                    "https://voronezh.dommalera.ru/catalog/materialy_dlya_dekora/shtukaturki_dekorativnye_1/koroed_1/",
                    "https://voronezh.blizko.ru/predl/construction/decoration/smesi/shtukaturki_dekorativny/f:166_koroied",
                    "https://voronezh.satom.ru/k/dekorativnye-shtukaturki-koroed/",
                    "http://stroitelnye-materialy-v-voronezhe.ru/shtukaturka-koroed",
                    "https://stroybaza-vrn.ru/katalog/suhie-stroitelmie-smesi/%d0%b4%d0%b5%d0%ba%d0%be%d1%80%d0%b0%d1%82%d0%b8%d0%b2%d0%bd%d0%b0%d1%8f-%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0",
                    "https://www.castorama.ru/catalogsearch/result/?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                    "https://voronezh.compumir.ru/shtukaturka-koroed",
                    "https://voronezh.yavitrina.ru/dekorativnye-shtukaturki-koroed",
                    "https://voronezh.neopod.ru/shtukaturki-fasadnye-koroed-bergauf",
                    "https://voronez.gamma-cveta.ru/shtukaturki-dekorativnye-fakturnye-kraski-main/effekt-koroed/"
                ]
            ],
            "штукатурка короед цена в воронеже" => [
                "sites" => [
                    "https://voronezh.leroymerlin.ru/catalogue/shtukaturki/koroed/",
                    "https://market.yandex.ru/search?text=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4%20%d0%b2%20%d0%b2%d0%be%d1%80%d0%be%d0%bd%d0%b5%d0%b6%d0%b5%20%d1%86%d0%b5%d0%bd%d1%8b",
                    "https://www.avito.ru/voronezh?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                    "https://voronezh.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/koroed/",
                    "https://voronezh.regmarkets.ru/shtukaturka-koroed/",
                    "https://voronezh.stroyportal.ru/catalog/section-shtukaturka-koroed-7581/",
                    "https://voronezh.pulscen.ru/price/110514-shtukaturka/f:62057_koroied",
                    "https://kraski36.ru/shtukaturka-koroed-voronezh/",
                    "https://voronezh.satom.ru/k/dekorativnye-shtukaturki-koroed/",
                    "https://www.ozon.ru/category/shtukaturki-koroed/",
                    "https://voronezh.blizko.ru/predl/construction/decoration/smesi/shtukaturki_dekorativny/f:166_koroied",
                    "https://voronezh.dommalera.ru/catalog/materialy_dlya_dekora/shtukaturki_dekorativnye_1/koroed_1/",
                    "http://stroitelnye-materialy-v-voronezhe.ru/shtukaturka-koroed",
                    "http://fasad36.ru/catalog/koroed/",
                    "https://stroybaza-vrn.ru/katalog/suhie-stroitelmie-smesi/%d0%b4%d0%b5%d0%ba%d0%be%d1%80%d0%b0%d1%82%d0%b8%d0%b2%d0%bd%d0%b0%d1%8f-%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0",
                    "https://www.castorama.ru/catalogsearch/result/?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                    "https://voronez.gamma-cveta.ru/shtukaturki-dekorativnye-fakturnye-kraski-main/effekt-koroed/",
                    "https://voronezh.yavitrina.ru/shtukaturka-koroed",
                    "https://lidecor.ru/category/pokrytiya-koroed/",
                    "https://voronezh.yamart.ru/shtukaturku-koroed-457562509/"
                ]
            ],
            "штукатурка короед цена воронеж" => [
                "sites" => [
                    "https://voronezh.leroymerlin.ru/catalogue/shtukaturki/koroed/",
                    "https://www.avito.ru/voronezh?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                    "https://market.yandex.ru/search?text=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4%20%d0%b2%20%d0%b2%d0%be%d1%80%d0%be%d0%bd%d0%b5%d0%b6%d0%b5%20%d1%86%d0%b5%d0%bd%d1%8b",
                    "https://voronezh.regmarkets.ru/shtukaturka-koroed/",
                    "https://voronezh.stroyportal.ru/catalog/section-shtukaturka-koroed-7581/",
                    "https://voronezh.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/koroed/",
                    "https://kraski36.ru/shtukaturka-koroed-voronezh/",
                    "https://voronezh.pulscen.ru/price/110514-shtukaturka/f:62057_koroied",
                    "https://www.ozon.ru/category/shtukaturki-koroed/",
                    "https://voronezh.blizko.ru/predl/construction/decoration/smesi/shtukaturki_dekorativny/f:166_koroied",
                    "https://voronezh.dommalera.ru/catalog/materialy_dlya_dekora/shtukaturki_dekorativnye_1/koroed_1/",
                    "https://voronezh.satom.ru/k/dekorativnye-shtukaturki-koroed/",
                    "http://fasad36.ru/catalog/koroed/",
                    "http://stroitelnye-materialy-v-voronezhe.ru/shtukaturka-koroed",
                    "https://voronezh.yavitrina.ru/shtukaturka-koroed",
                    "https://www.castorama.ru/catalogsearch/result/?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                    "https://stroybaza-vrn.ru/katalog/suhie-stroitelmie-smesi/%d0%b4%d0%b5%d0%ba%d0%be%d1%80%d0%b0%d1%82%d0%b8%d0%b2%d0%bd%d0%b0%d1%8f-%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0",
                    "https://voronezh.neopod.ru/shtukaturki-fasadnye-koroed-bergauf",
                    "https://voronezh.compumir.ru/shtukaturka-koroed",
                    "https://vdom36.ru/katalog/shtukaturnye-fasady/dekorativnaya-shtukaturka/"
                ]
            ],
            "штукатурка стен фасада" => [
                "sites" => [
                    "https://leroymerlin.ru/catalogue/shtukaturki/dlya-naruzhnyh-rabot/",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
                    "https://market.yandex.ru/search?text=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d0%b0%d1%8f%20%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%b4%d0%bb%d1%8f%20%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%bd%d1%8b%d1%85%20%d1%80%d0%b0%d0%b1%d0%be%d1%82",
                    "https://dom-i-remont.info/posts/fasad-doma/vidy-fasadnyh-shtukaturok-harakteristiki-lidery-i-sekrety-otdelki/",
                    "https://st-par.ru/info/stati-o-sukhikh-smesyakh/shtukaturka-fasada/",
                    "https://moscow.petrovich.ru/catalog/1447/fasadnye-shtukaturki/",
                    "https://m.avito.ru/moskva/predlozheniya_uslug?query=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
                    "https://m-strana.ru/articles/otdelka-fasada-doma-shtukaturkoy/",
                    "https://1pofasady.ru/shtukaturka/oshtukaturivanie-fasada",
                    "https://kronotech.ru/fasadnye-raboty/shtukaturka-fasada",
                    "https://stroy-podskazka.ru/shtukaturka/fasadnaya/",
                    "https://www.youtube.com/playlist?list=pluivzwm_q9lz9x7bf9mxin8zpkdwtn_of",
                    "https://www.prof-fasady.ru/catalog/shtukaturka-fasada/",
                    "https://profi.ru/remont/malyarnye-shtukaturnye-raboty/shtukatury/shtukaturka-sten/shtukaturka-fasada/",
                    "https://fasadblog.ru/shtukaturnyj-fasad/",
                    "https://www.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/fasadnaya/",
                    "https://inistroy.com/facade/",
                    "https://dekoriko.ru/shtukaturka/fasadnaya/",
                    "https://dimax.su/uslugi/shtukaturka-fasada/",
                    "https://prodekorsten.com/vyravnivanie/fasada/shtukaturka-fasada.html"
                ]
            ],
            "штукатурка фасада" => [
                "sites" => [
                    "https://leroymerlin.ru/catalogue/shtukaturki/dlya-naruzhnyh-rabot/",
                    "https://market.yandex.ru/search?text=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d0%b0%d1%8f%20%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%b4%d0%bb%d1%8f%20%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%bd%d1%8b%d1%85%20%d1%80%d0%b0%d0%b1%d0%be%d1%82",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
                    "https://moscow.petrovich.ru/catalog/1447/fasadnye-shtukaturki/",
                    "https://stroy-podskazka.ru/shtukaturka/fasadnaya/",
                    "https://st-par.ru/info/stati-o-sukhikh-smesyakh/shtukaturka-fasada/",
                    "https://www.ozon.ru/category/shtukaturka-dlya-naruzhnyh-rabot/",
                    "https://m-strana.ru/articles/otdelka-fasada-doma-shtukaturkoy/",
                    "https://www.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/fasadnaya/",
                    "https://dom-i-remont.info/posts/fasad-doma/vidy-fasadnyh-shtukaturok-harakteristiki-lidery-i-sekrety-otdelki/",
                    "https://www.avito.ru/moskva/uslugi?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
                    "https://kronotech.ru/fasadnye-raboty/shtukaturka-fasada",
                    "https://dimax.su/uslugi/shtukaturka-fasada/",
                    "https://1pofasady.ru/shtukaturka/oshtukaturivanie-fasada",
                    "https://www.forumhouse.ru/journal/articles/4446-shtukaturka-fasada-doma",
                    "https://expert-deco.ru/catalog/fasadnaya-shtukaturka/",
                    "https://www.prof-fasady.ru/catalog/shtukaturka-fasada/",
                    "https://www.strd.ru/suhie_smesi/shtukaturki/dla_fasada/",
                    "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/kakuyu-vybrat-shtukaturku-dlya-fasada.html",
                    "https://7dach.ru/natashapetrova/shtukaturka-dlya-fasada-praktichno-nadezhno-krasivo-164829.html"
                ]
            ],
            "штукатурка фасада воронеж" => [
                "sites" => [
                    "https://www.avito.ru/voronezh/predlozheniya_uslug?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%be%d0%b2",
                    "https://uslugi.yandex.ru/193-voronezh/category?text=%d0%be%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%b8%d1%82%d1%8c+%d0%b4%d0%be%d0%bc+%d1%81%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%b8",
                    "https://vrn.profi.ru/remont/malyarnye-shtukaturnye-raboty/shtukatury/shtukaturka-sten/shtukaturka-fasada/",
                    "https://voronezh.leroymerlin.ru/catalogue/fasadnye-shtukaturki/",
                    "https://voronezh.myguru.ru/services/fasadnye-raboty/oshtukaturivanie-fasada-zdaniya/",
                    "http://fasad36.ru/services/shtukaturka-fasada/",
                    "https://zoon.ru/voronezh/m/shtukaturivanie_fasada/",
                    "https://kronvest.net/voronezh/plaster-fasad",
                    "https://voronezh.vse-podklyuch.ru/uslugi/shtukaturnye-raboty/shtukaturka-fasada/",
                    "https://www.cmlt.ru/ads--rubric-402-servicetype-30444",
                    "https://novostroy1.ru/fasadnye-raboty",
                    "https://voronezh.trade-services.ru/services/fasadnye-raboty/shtukaturka-fasada/",
                    "http://xn--36-glchqd5adeocin.xn--p1ai/fasadnye-raboty.html",
                    "https://vrn.masterdel.ru/master/shtukaturka-fasada/",
                    "https://voronezh.regmarkets.ru/shtukaturki-fasadnye-33211/",
                    "https://36-fasad.ru/",
                    "http://teplofasad36.ru/decor-shtukat",
                    "https://voronezh.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/fasadnaya/",
                    "https://fasad-com.ru/dekorativnaya-shtukaturka/",
                    "https://market.yandex.ru/search?text=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d0%b0%d1%8f%20%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d1%83%d0%bf%d0%b8%d1%82%d1%8c%20%d0%b2%20%d0%b2%d0%be%d1%80%d0%be%d0%bd%d0%b5%d0%b6%d0%b5"
                ]
            ],
            "штукатурка фасада короед" => [
                "sites" => [
                    "https://leroymerlin.ru/catalogue/shtukaturki/koroed/",
                    "https://market.yandex.ru/search?text=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d0%b0%d1%8f%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                    "https://m-strana.ru/articles/kak-nanosit-koroed-na-fasad/",
                    "https://www.ozon.ru/category/shtukaturki-koroed/",
                    "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/fasad-koroed.html",
                    "https://fasad-exp.ru/vidy-materialov-dlya-otdelki-fasadov/shtukaturka/nanesenie-dekorativnoy-shtukaturki-k.html",
                    "https://moscow.petrovich.ru/catalog/6654/fasadnaya-dekorativnaya-shtukaturka-koroed/",
                    "https://www.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/koroed/",
                    "https://stroy-podskazka.ru/dom/fasad/shtukaturka-koroed/",
                    "https://expert-dacha.pro/stroitelstvo/steny/otdelka-fasada/shtukaturka/koroed.html",
                    "https://dg-home.ru/blog/shtukaturka-koroed-v-dizajne_b655130/",
                    "https://www.mirkrasok.ru/catalog/shtukaturki_dekorativnye_i_fakturnye_kraski-effekt_koroed/work_type-is-naruzhnye_raboty/",
                    "https://dzen.ru/media/goodwillstroi/dekorativnaia-shtukaturka-koroed-vidy-i-sostav-tehnologiia-5f9951dfbaf78e79e76abd64",
                    "https://1beton.info/maloetazhnoe/otdelka/koroed-fasadnaya-dekorativnaya-shtukaturka",
                    "https://oshtukaturke.ru/raznovidnosti/koroed-fasad",
                    "https://st-par.ru/catalog/dekorativnye-shtukaturki/koroed/",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d0%bd%d0%b0%d0%bd%d0%b5%d1%81%d0%b5%d0%bd%d0%b8%d0%b5+%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b8+%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4+%d1%86%d0%b5%d0%bd%d0%b0+%d1%80%d0%b0%d0%b1%d0%be%d1%82%d1%8b+%d0%b7%d0%b0+%d0%bc2",
                    "https://fasad-prosto.ru/fasadnye-sistemy/mokryj-fasad/fasadnaya-shtukaturka/detalnaya-texnologiya-naneseniya-shtukaturki-koroed.html",
                    "https://www.avito.ru/moskva/predlozheniya_uslug?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                    "https://optimfasad.ru/koroed"
                ]
            ],
            "штукатурка фасада стоимость работ" => [
                "sites" => [
                    "https://www.avito.ru/moskva/uslugi?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
                    "https://www.prof-fasady.ru/catalog/shtukaturka-fasada/",
                    "https://uslugi.yandex.ru/213-moscow/category?text=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0+%d1%86%d0%b5%d0%bd%d0%b0+%d0%b7%d0%b0+%d0%bc2+%d0%be%d1%82+150+%d1%80%d1%83%d0%b1",
                    "https://optimumbuilding.ru/fasadnye-raboty",
                    "https://profi.ru/remont/malyarnye-shtukaturnye-raboty/shtukatury/shtukaturka-sten/shtukaturka-fasada/",
                    "https://kronotech.ru/prays-shtukaturnye-raboty",
                    "https://www.stroyremfasad.ru/ceny/fasadnye-raboty/",
                    "https://www.ksu-nordwest.ru/price/price-na-otdelku-fasada.php",
                    "https://zoon.ru/msk/m/shtukaturivanie_fasada/",
                    "https://hidropro.ru/services/fasadnye-raboty/",
                    "https://efee.ru/price/fasadnie-raboty/",
                    "https://lkgstroi.ru/stoimost/shtukaturka-fasada-doma/",
                    "https://msk.vse-podklyuch.ru/uslugi/shtukaturnye-raboty/shtukaturka-fasada/",
                    "https://tehstroy-city.ru/obshhestroitelnyie-rabotyi/fasad/czena-fasadnyix-rabot",
                    "https://aograd.ru/fasadnye-raboty/price/",
                    "https://topstroy-remont.ru/fasadnye-raboty/shtukaturka-fasada",
                    "https://myguru.ru/services/fasadnye-raboty/oshtukaturivanie-fasada-zdaniya/",
                    "https://robo-remont.ru/prices/ceny-na-shtukaturku-sten-i-fasadov/",
                    "https://korsgrup.ru/fasadnye-raboty-prajs-list",
                    "https://dimax.su/uslugi/shtukaturka-fasada/"
                ]
            ]
        ];

        $clusters = [];
        $willClustered = [];
        $minimum = 10;

        foreach ($jayParsedAry as $phrase => $sites) {
            foreach ($jayParsedAry as $phrase2 => $sites2) {
                if (isset($willClustered[$phrase2])) {
                    continue;
                }
                if (isset($clusters[$phrase])) {
                    foreach ($clusters[$phrase] as $item) {
                        if (count(array_intersect($item, $sites2['sites']))) {
                            $clusters[$phrase][$phrase2] = $sites2['sites'];
                            $willClustered[$phrase2] = true;
                            break;
                        }
                    }

                } else {
                    if (count(array_intersect($sites['sites'], $sites2['sites'])) >= $minimum) {
                        $clusters[$phrase][$phrase2] = $sites2['sites'];
                        $willClustered[$phrase2] = true;
                    }
                }
            }
        }

        foreach ($clusters as $phrase => $item) {
            foreach ($item as $itemPhrase => $elems) {
                $clusters[$phrase][$itemPhrase] = ['sites' => $elems];
            }
        }
        dd($clusters);
    });
});
