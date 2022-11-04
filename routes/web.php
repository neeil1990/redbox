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
});

Route::get('/test', function () {
    $jayParsedAry = [
        "декоративная отделка фасадов" => [
            "декоративная отделка фасадов" => [
                "https://m-strana.ru/articles/chem-nedorogo-otdelat-fasad-doma/",
                "https://stroy-podskazka.ru/dom/otdelka-fasada/",
                "https://www.forumhouse.ru/journal/themes/66-varianty-otdelki-fasada-populyarnye-vidy-konstruktiv-osobennosti",
                "https://market.yandex.ru/search?text=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b9%20%d0%b4%d0%b5%d0%ba%d0%be%d1%80%20%d0%b4%d0%bb%d1%8f%20%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%bd%d0%be%d0%b9%20%d0%be%d1%82%d0%b4%d0%b5%d0%bb%d0%ba%d0%b8%20%d0%b4%d0%be%d0%bc%d0%b0",
                "https://realty.rbc.ru/news/61a7952a9a7947039b719b08",
                "https://www.avito.ru/moskva_i_mo?q=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b9+%d0%b4%d0%b5%d0%ba%d0%be%d1%80",
                "https://www.ivd.ru/dizajn-i-dekor/zagorodnyj-dom/kak-ukrasit-fasad-60-realnyx-variantov-22971",
                "https://remstroiblog.ru/natalia/2017/03/07/10-materialov-dlya-otdelki-fasada-chastnogo-doma/",
                "https://domstrousam.ru/sovremennye-materialy-dlya-fasada-doma-foto/",
                "http://remoo.ru/fasad/fasady-domov",
                "https://www.grandline.ru/informaciya/fasad-chastnogo-doma-otdelka/",
                "https://dizlandshafta.ru/dizajn/doma/varianty-otdelki-fasada/",
                "https://uslugi.yandex.ru/213-moscow/category?text=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b9+%d0%b4%d0%b5%d0%ba%d0%be%d1%80",
                "https://strbani.ru/fasad-doma/",
                "https://www.houzz.ru/foto/krasivye-doma-foto-fasadov-phbr0-bp~t_13935",
                "https://vekroof.ru/articles/luchshie-materialy-dlya-otdelki-fasada/",
                "https://srbu.ru/otdelochnye-materialy/1950-varianty-otdelki-fasada-chastnogo-doma.html",
                "https://artfasad.com/fasad/",
                "https://planken.guru/otdelka-i-montazh-fasadov/dekorativnaya-otdelka-fasadov-raznoobrazie-otdelochnyh-materialov.html",
                "https://kronotech.ru/fasadnye-raboty/otdelka-fasada"
            ]
        ],
        "мокрый фасад воронеж" => [
            "мокрый фасад воронеж" => [
                "https://www.avito.ru/voronezh/predlozheniya_uslug?q=%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9+%d1%84%d0%b0%d1%81%d0%b0%d0%b4",
                "https://dekor36.com/mokriy-fasad.html",
                "https://36-fasad.ru/nashi-uslugi/mokryj-fasad",
                "http://fasad36.ru/services/mokryy-fasad/",
                "https://uslugi.yandex.ru/193-voronezh/category?text=%d1%81%d0%b4%d0%b5%d0%bb%d0%b0%d1%82%d1%8c+%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9+%d1%84%d0%b0%d1%81%d0%b0%d0%b4",
                "https://kronvest.net/voronezh/wet-fasad",
                "https://vrn.profi.ru/remont/montazh-mokrogo-fasada/",
                "http://xn--36-glchqd5adeocin.xn--p1ai/mokryi-fasad.html",
                "https://voronezh.vse-podklyuch.ru/stroitelstvo/oblitsovka-fasadov/mokryy-fasad/",
                "https://uslugio.com/voronezh/1/9/mokryy-fasad",
                "https://fasad-rem.ru/services/%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9-%d1%84%d0%b0%d1%81%d0%b0%d0%b4/",
                "http://teplofasad36.ru/morriy-fasad",
                "http://index-fs.ru/otdelka-mokrym-fasadom",
                "https://rskpanorama.com/uslugi/otdelochnye-raboty/montazh-mokrogo-fasada/",
                "https://sezrem.ru/mokryj-fasad/",
                "https://voronezh.stroyportal.ru/firms/section-mokrye-fasady-2933/",
                "https://voronezh.leroymerlin.ru/catalogue/shtukaturki/mokryy-fasad-s-utepleniem/",
                "https://www.remontnik.ru/voronezh/uteplenie_fasadov_mokryi_fasad/",
                "https://voronezh.urfomarket.ru/montazh_mokrogo_fasada_pod_klyuch.php",
                "https://visota-36.ru/uslugi/fasadnye-raboty/uteplenie-fasadov/"
            ]
        ],
        "штукатурка короед купить в воронеже" => [
            "штукатурка короед купить в воронеже" => [
                "https://voronezh.leroymerlin.ru/catalogue/shtukaturki/koroed/",
                "https://www.avito.ru/voronezh?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                "https://market.yandex.ru/search?text=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4%20%d0%b2%20%d0%b2%d0%be%d1%80%d0%be%d0%bd%d0%b5%d0%b6%d0%b5%20%d1%86%d0%b5%d0%bd%d1%8b",
                "https://voronezh.regmarkets.ru/shtukaturka-koroed/",
                "https://voronezh.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/koroed/",
                "https://voronezh.stroyportal.ru/catalog/section-shtukaturka-koroed-7581/",
                "https://kraski36.ru/shtukaturka-koroed-voronezh/",
                "https://voronezh.pulscen.ru/price/110514-shtukaturka/f:62057_koroied",
                "http://fasad36.ru/catalog/koroed/",
                "https://www.ozon.ru/category/shtukaturki-koroed/",
                "https://voronezh.dommalera.ru/catalog/materialy_dlya_dekora/shtukaturki_dekorativnye_1/koroed_1/",
                "https://voronezh.blizko.ru/predl/construction/decoration/smesi/shtukaturki_dekorativny/f:166_koroied",
                "https://voronezh.satom.ru/k/dekorativnye-shtukaturki-koroed/",
                "https://www.castorama.ru/catalogsearch/result/?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                "http://stroitelnye-materialy-v-voronezhe.ru/shtukaturka-koroed",
                "https://stroybaza-vrn.ru/katalog/suhie-stroitelmie-smesi/%d0%b4%d0%b5%d0%ba%d0%be%d1%80%d0%b0%d1%82%d0%b8%d0%b2%d0%bd%d0%b0%d1%8f-%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0",
                "https://lidecor.ru/category/pokrytiya-koroed/",
                "https://voronezh.yavitrina.ru/dekorativnye-shtukaturki-koroed",
                "https://voronezh.compumir.ru/fasadnaja-shtukaturka-koroed",
                "https://voronezh.neopod.ru/shtukaturki-fasadnye-koroed-bergauf"
            ],
            "штукатурка короед цена в воронеже" => [
                "https://voronezh.leroymerlin.ru/catalogue/shtukaturki/koroed/",
                "https://www.avito.ru/voronezh?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                "https://market.yandex.ru/search?text=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4%20%d0%b2%20%d0%b2%d0%be%d1%80%d0%be%d0%bd%d0%b5%d0%b6%d0%b5%20%d1%86%d0%b5%d0%bd%d1%8b",
                "https://voronezh.regmarkets.ru/shtukaturka-koroed/",
                "https://voronezh.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/koroed/",
                "https://voronezh.stroyportal.ru/catalog/section-shtukaturka-koroed-7581/",
                "https://voronezh.pulscen.ru/price/110514-shtukaturka/f:62057_koroied",
                "https://www.ozon.ru/category/shtukaturki-koroed/",
                "https://kraski36.ru/shtukaturka-koroed-voronezh/",
                "https://voronezh.blizko.ru/predl/construction/decoration/smesi/shtukaturki_dekorativny/f:166_koroied",
                "https://voronezh.satom.ru/k/dekorativnye-shtukaturki-koroed/",
                "http://fasad36.ru/catalog/koroed/",
                "https://www.castorama.ru/catalogsearch/result/?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                "https://voronezh.dommalera.ru/catalog/materialy_dlya_dekora/shtukaturki_dekorativnye_1/koroed_1/",
                "http://stroitelnye-materialy-v-voronezhe.ru/shtukaturka-koroed",
                "https://stroybaza-vrn.ru/katalog/suhie-stroitelmie-smesi/%d0%b4%d0%b5%d0%ba%d0%be%d1%80%d0%b0%d1%82%d0%b8%d0%b2%d0%bd%d0%b0%d1%8f-%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0",
                "https://voronezh.yavitrina.ru/shtukaturka-koroed",
                "https://lidecor.ru/category/dekorativnaya-shtukaturka-koroed/",
                "https://voronez.gamma-cveta.ru/shtukaturki-dekorativnye-fakturnye-kraski-main/effekt-koroed/",
                "https://voronezh.yamart.ru/shtukaturku-koroed-457562509/"
            ],
            "штукатурка короед цена воронеж" => [
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
                "https://lidecor.ru/category/dekorativnaya-shtukaturka-koroed/"
            ]
        ]
    ];

    dd($jayParsedAry);

    $array = [
        "декоративная отделка фасадов" => [
            "sites" => [
                "https://stroy-podskazka.ru/dom/otdelka-fasada/",
                "https://www.forumhouse.ru/journal/themes/66-varianty-otdelki-fasada-populyarnye-vidy-konstruktiv-osobennosti",
                "https://m-strana.ru/articles/chem-nedorogo-otdelat-fasad-doma/",
                "https://www.avito.ru/moskva_i_mo?q=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b9+%d0%b4%d0%b5%d0%ba%d0%be%d1%80",
                "https://realty.rbc.ru/news/61a7952a9a7947039b719b08",
                "https://market.yandex.ru/search?text=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b9%20%d0%b4%d0%b5%d0%ba%d0%be%d1%80%20%d0%b4%d0%bb%d1%8f%20%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%bd%d0%be%d0%b9%20%d0%be%d1%82%d0%b4%d0%b5%d0%bb%d0%ba%d0%b8%20%d0%b4%d0%be%d0%bc%d0%b0",
                "https://srbu.ru/otdelochnye-materialy/1950-varianty-otdelki-fasada-chastnogo-doma.html",
                "https://strbani.ru/fasad-doma/",
                "https://domstrousam.ru/sovremennye-materialy-dlya-fasada-doma-foto/",
                "https://dizlandshafta.ru/dizajn/doma/varianty-otdelki-fasada/",
                "https://uslugi.yandex.ru/213-moscow/category?text=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b9+%d0%b4%d0%b5%d0%ba%d0%be%d1%80",
                "https://remstroiblog.ru/natalia/2017/03/07/10-materialov-dlya-otdelki-fasada-chastnogo-doma/",
                "https://planken.guru/otdelka-i-montazh-fasadov/dekorativnaya-otdelka-fasadov-raznoobrazie-otdelochnyh-materialov.html",
                "https://www.ivd.ru/dizajn-i-dekor/zagorodnyj-dom/kak-ukrasit-fasad-60-realnyx-variantov-22971",
                "https://www.grandline.ru/informaciya/fasad-chastnogo-doma-otdelka/",
                "https://www.houzz.ru/foto/krasivye-doma-foto-fasadov-phbr0-bp~t_13935",
                "https://vekroof.ru/articles/luchshie-materialy-dlya-otdelki-fasada/",
                "http://remoo.ru/fasad/fasady-domov",
                "https://artfasad.com/fasad/",
                "https://dekor-fasada.ru/katalog-fasadnogo-dekora"
            ]
        ],
        "декоративная штукатурка короед цена" => [
            "sites" => [
                "https://leroymerlin.ru/catalogue/shtukaturki/koroed/",
                "https://market.yandex.ru/search?text=%d0%b4%d0%b5%d0%ba%d0%be%d1%80%d0%b0%d1%82%d0%b8%d0%b2%d0%bd%d0%b0%d1%8f%20%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4%20%d1%86%d0%b5%d0%bd%d0%b0",
                "https://www.ozon.ru/category/shtukaturka-koroed/",
                "https://www.avito.ru/moskva/dlya_doma_i_dachi?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                "https://www.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/koroed/",
                "https://moscow.petrovich.ru/catalog/6654/dekorativnaya-shtukaturka-koroed/",
                "https://moskva.regmarkets.ru/shtukaturka-koroed/",
                "https://st-par.ru/catalog/dekorativnye-shtukaturki/koroed/",
                "https://www.stroyportal.ru/catalog/section-shtukaturka-koroed-7581/",
                "https://www.mirkrasok.ru/catalog/shtukaturki_dekorativnye_i_fakturnye_kraski-effekt_koroed/work_type-is-naruzhnye_raboty/",
                "https://msk.pulscen.ru/price/110514-shtukaturka/f:62057_koroied",
                "https://www.baufasad.ru/catalog/dekorativnaya_shtukaturka_dlya_mokrogo_fasada/filter/texture-is-koroed/",
                "https://msk.blizko.ru/predl/construction/decoration/smesi/shtukaturki_dekorativny/f:34399_koroied&68946_dlia-naruzhnykh-rabot",
                "https://www.strd.ru/suhie_smesi/dekorativnie_stukaturki/koroed/",
                "https://glavsnab.net/shtukaturka-koroed",
                "https://www.gipsoplita.ru/otdelochnye-materialy/dekorativnaja-shtukaturka/shtukaturka-koroed/",
                "https://www.sdvor.com/moscow/s/shtukaturka-dekorativnaja-koroed21",
                "https://kraskitorg.ru/collection/fakturnaya-shtukaturka-koroed",
                "https://frontmaster.su/catalog/otdelochnye-materialy/dekorativnaya-shtukaturka/dekorativnaya-shtukaturka-koroed/",
                "https://arhitektor.ru/s-shtukaturka-koroed/"
            ]
        ],
        "короед воронеж" => [
            "sites" => [
                "https://voronezh.leroymerlin.ru/catalogue/shtukaturki/koroed/",
                "https://www.avito.ru/voronezh?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                "https://market.yandex.ru/search?text=%d0%ba%d1%83%d0%bf%d0%b8%d1%82%d1%8c%20%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d1%83%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4%20%d0%b2%20%d0%b2%d0%be%d1%80%d0%be%d0%bd%d0%b5%d0%b6%d0%b5",
                "https://voronezh.regmarkets.ru/shtukaturka-koroed/",
                "https://kraski36.ru/shtukaturka-koroed-voronezh/",
                "https://voronezh.stroyportal.ru/catalog/section-shtukaturka-koroed-7581/",
                "https://uslugi.yandex.ru/193-voronezh/category?text=%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                "https://voronezh.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/koroed/",
                "https://voronezh.pulscen.ru/price/110514-shtukaturka/f:62057_koroied",
                "http://fasad36.ru/catalog/koroed/",
                "https://36-fasad.ru/nashi-uslugi/otdelka-dekorativnoj-shtukaturkoj",
                "https://stroidom36.ru/shtukaturka-koroed/",
                "https://lidecor.ru/category/pokrytiya-koroed/",
                "https://voronezh.blizko.ru/predl/construction/decoration/smesi/shtukaturki_dekorativny/f:166_koroied",
                "https://voronezh.satom.ru/k/dekorativnye-shtukaturki-koroed/",
                "https://voronezh.dommalera.ru/catalog/materialy_dlya_dekora/shtukaturki_dekorativnye_1/koroed_1/",
                "https://www.ozon.ru/category/shtukaturki-koroed/",
                "http://stroitelnye-materialy-v-voronezhe.ru/shtukaturka-koroed",
                "http://xn--b1adccftyeadasf.xn--p1ai/shtukaturki-i-gruntovki-76/shtukaturka/",
                "https://www.castorama.ru/catalogsearch/result/?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4"
            ]
        ],
        "короед цена" => [
            "sites" => [
                "https://leroymerlin.ru/catalogue/shtukaturki/koroed/",
                "https://market.yandex.ru/search?text=%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%bd%d0%b0%d1%8f%20%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4%20%d1%81%d1%82%d0%be%d0%b8%d0%bc%d0%be%d1%81%d1%82%d1%8c",
                "https://www.ozon.ru/category/shtukaturka-koroed/",
                "https://www.avito.ru/moskva/dlya_doma_i_dachi?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                "https://moscow.petrovich.ru/catalog/6654/dekorativnaya-shtukaturka-koroed/",
                "https://www.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/koroed/",
                "https://www.stroyportal.ru/catalog/section-shtukaturka-koroed-7581/",
                "https://moskva.regmarkets.ru/shtukaturka-koroed/",
                "https://www.mirkrasok.ru/catalog/shtukaturki_dekorativnye_i_fakturnye_kraski-effekt_koroed/",
                "https://st-par.ru/catalog/dekorativnye-shtukaturki/koroed/",
                "https://msk.pulscen.ru/price/110514-shtukaturka/f:62057_koroied",
                "https://glavsnab.net/shtukaturka-koroed",
                "https://msk.blizko.ru/predl/construction/decoration/smesi/shtukaturki_dekorativny/f:34399_koroied&68946_dlia-naruzhnykh-rabot",
                "https://moskva.satom.ru/k/dekorativnye-shtukaturki-koroed/",
                "https://www.sdvor.com/moscow/s/shtukaturka-dekorativnaja-koroed21",
                "https://www.strd.ru/suhie_smesi/dekorativnie_stukaturki/koroed/",
                "https://moskeram.ru/catalog/sukhie_smesi/shtukaturka_fasadnaya/filter/fasadnaya_shtukaturka_koroyed/",
                "https://bau-store.ru/stroitelnyye-materialy/shtukaturka-koroed/",
                "https://www.gipsoplita.ru/otdelochnye-materialy/dekorativnaja-shtukaturka/shtukaturka-koroed/",
                "https://kraskitorg.ru/collection/fakturnaya-shtukaturka-koroed"
            ]
        ],
        "короед штукатурка воронеж" => [
            "sites" => [
                "https://voronezh.leroymerlin.ru/catalogue/shtukaturki/koroed/",
                "https://www.avito.ru/voronezh?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                "https://market.yandex.ru/search?text=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4%20%d0%b2%20%d0%b2%d0%be%d1%80%d0%be%d0%bd%d0%b5%d0%b6%d0%b5%20%d1%86%d0%b5%d0%bd%d1%8b",
                "https://voronezh.regmarkets.ru/shtukaturka-koroed/",
                "https://voronezh.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/koroed/",
                "https://voronezh.stroyportal.ru/catalog/section-shtukaturka-koroed-7581/",
                "https://kraski36.ru/shtukaturka-koroed-voronezh/",
                "https://www.ozon.ru/category/shtukaturki-koroed/",
                "https://voronezh.pulscen.ru/price/110514-shtukaturka/f:62057_koroied",
                "http://fasad36.ru/catalog/koroed/",
                "https://www.castorama.ru/catalogsearch/result/?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                "https://voronezh.blizko.ru/predl/construction/decoration/smesi/shtukaturki_dekorativny/f:166_koroied",
                "https://voronezh.dommalera.ru/catalog/materialy_dlya_dekora/shtukaturki_dekorativnye_1/koroed_1/",
                "https://voronezh.satom.ru/k/dekorativnye-shtukaturki-koroed/",
                "http://stroitelnye-materialy-v-voronezhe.ru/shtukaturka-koroed",
                "https://lidecor.ru/category/pokrytiya-koroed/",
                "https://stroybaza-vrn.ru/katalog/suhie-stroitelmie-smesi/%d0%b4%d0%b5%d0%ba%d0%be%d1%80%d0%b0%d1%82%d0%b8%d0%b2%d0%bd%d0%b0%d1%8f-%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0",
                "https://voronezh.yavitrina.ru/shtukaturka-koroed",
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
                "https://www.mirkrasok.ru/catalog/shtukaturki_dekorativnye_i_fakturnye_kraski-effekt_koroed/work_type-is-naruzhnye_raboty/",
                "https://st-par.ru/catalog/dekorativnye-shtukaturki/koroed/",
                "https://msk.pulscen.ru/price/110514-shtukaturka/f:62057_koroied",
                "https://www.strd.ru/suhie_smesi/dekorativnie_stukaturki/koroed/",
                "https://msk.blizko.ru/predl/construction/decoration/smesi/shtukaturki_dekorativny/f:34399_koroied&68946_dlia-naruzhnykh-rabot",
                "https://www.gipsoplita.ru/otdelochnye-materialy/dekorativnaja-shtukaturka/shtukaturka-koroed/",
                "https://glavsnab.net/shtukaturka-koroed",
                "https://kraskitorg.ru/collection/fakturnaya-shtukaturka-koroed",
                "https://www.dommalera.ru/catalog/materialy_dlya_dekora/shtukaturki_dekorativnye_1/s_effektom_koroeda/",
                "https://moskva.satom.ru/k/dekorativnye-shtukaturki-koroed/",
                "https://www.sdvor.com/moscow/s/shtukaturka-dekorativnaja-koroed21",
                "https://bau-store.ru/stroitelnyye-materialy/shtukaturka-koroed/"
            ]
        ],
        "материалы для отделки фасада" => [
            "sites" => [
                "https://m-strana.ru/articles/chem-nedorogo-otdelat-fasad-doma/",
                "https://remstroiblog.ru/natalia/2017/03/07/10-materialov-dlya-otdelki-fasada-chastnogo-doma/",
                "https://www.forumhouse.ru/journal/themes/66-varianty-otdelki-fasada-populyarnye-vidy-konstruktiv-osobennosti",
                "https://stroy-podskazka.ru/materialy/fasadnye-luchshaya-oblicovka/",
                "https://domstrousam.ru/sovremennye-materialy-dlya-fasada-doma-foto/",
                "https://market.yandex.ru/search?text=%d0%bc%d0%b0%d1%82%d0%b5%d1%80%d0%b8%d0%b0%d0%bb%d1%8b%20%d0%b4%d0%bb%d1%8f%20%d0%be%d1%82%d0%b4%d0%b5%d0%bb%d0%ba%d0%b8%20%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
                "https://realty.rbc.ru/news/61a7952a9a7947039b719b08",
                "https://www.alta-profil.ru/client-center/articles/sravnenie-novinok-na-rynke-fasadnyh-materialov/",
                "https://vekroof.ru/articles/luchshie-materialy-dlya-otdelki-fasada/",
                "https://www.grandline.ru/informaciya/fasad-chastnogo-doma-otdelka/",
                "http://remoo.ru/fasad/fasady-domov",
                "https://dizlandshafta.ru/dizajn/doma/varianty-otdelki-fasada/",
                "https://leroymerlin.ru/catalogue/fasadnye-paneli/",
                "https://domof.ru/articles/kakoy-material-vybrat-dlya-otdelki-fasada-zdaniya/",
                "https://markakachestva.ru/rating-of/2247-luchshie-materialy-dlja-oblicovki-fasada.html",
                "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/otdelochnye-materialy-dlya-fasadov-chastnyx-domov.html",
                "https://srbu.ru/otdelochnye-materialy/1950-varianty-otdelki-fasada-chastnogo-doma.html",
                "https://alfakrov.com/blog/sovety_pokupatelyam/chem_obshit_dom_snaruzhi_deshevo_i_krasivo_foto_tseny_kharakteristiki_i_top_7_luchshikh_materialov/",
                "https://dzen.ru/media/tablichnik/luchshie-materialy-dlia-otdelki-fasada-chastnogo-doma-5f0ffd317e2b585adad67632",
                "https://www.ozon.ru/category/otdelochnye-materialy-dlya-fasada/"
            ]
        ],
        "материалы для отделки фасада дома" => [
            "sites" => [
                "https://m-strana.ru/articles/chem-nedorogo-otdelat-fasad-doma/",
                "https://remstroiblog.ru/natalia/2017/03/07/10-materialov-dlya-otdelki-fasada-chastnogo-doma/",
                "https://www.forumhouse.ru/journal/themes/66-varianty-otdelki-fasada-populyarnye-vidy-konstruktiv-osobennosti",
                "https://stroy-podskazka.ru/materialy/fasadnye-luchshaya-oblicovka/",
                "https://domstrousam.ru/sovremennye-materialy-dlya-fasada-doma-foto/",
                "https://markakachestva.ru/rating-of/2247-luchshie-materialy-dlja-oblicovki-fasada.html",
                "https://market.yandex.ru/search?text=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b5%20%d0%bc%d0%b0%d1%82%d0%b5%d1%80%d0%b8%d0%b0%d0%bb%d1%8b%20%d0%b4%d0%bb%d1%8f%20%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%bd%d0%be%d0%b9%20%d0%be%d0%b1%d0%bb%d0%b8%d1%86%d0%be%d0%b2%d0%ba%d0%b8%20%d0%b4%d0%be%d0%bc%d0%b0",
                "https://www.alta-profil.ru/client-center/articles/sravnenie-novinok-na-rynke-fasadnyh-materialov/",
                "https://www.grandline.ru/informaciya/fasad-chastnogo-doma-otdelka/",
                "https://realty.rbc.ru/news/61a7952a9a7947039b719b08",
                "https://vekroof.ru/articles/luchshie-materialy-dlya-otdelki-fasada/",
                "https://srbu.ru/otdelochnye-materialy/1950-varianty-otdelki-fasada-chastnogo-doma.html",
                "https://cvet-dom.ru/dachnyy-dom/top-materialov-dlya-otdelki-fasada-dom",
                "https://dzen.ru/media/tablichnik/luchshie-materialy-dlia-otdelki-fasada-chastnogo-doma-5f0ffd317e2b585adad67632",
                "https://dizlandshafta.ru/dizajn/doma/varianty-otdelki-fasada/",
                "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/kakoj-material-deshevle-i-luchshe-dlya-oblicovki-fasada-doma-obzor-top-9-populyarnyx-materialov.html",
                "https://design-homes.ru/stroitelstvo-i-remont/nedorogo-fasad-doma",
                "http://remoo.ru/fasad/fasady-domov",
                "https://alfakrov.com/blog/sovety_pokupatelyam/chem_obshit_dom_snaruzhi_deshevo_i_krasivo_foto_tseny_kharakteristiki_i_top_7_luchshikh_materialov/",
                "https://domof.ru/articles/kakoy-material-vybrat-dlya-otdelki-fasada-zdaniya/"
            ]
        ],
        "материалы для отделки фасадов частных домов" => [
            "sites" => [
                "https://m-strana.ru/articles/chem-nedorogo-otdelat-fasad-doma/",
                "https://stroy-podskazka.ru/materialy/fasadnye-luchshaya-oblicovka/",
                "https://remstroiblog.ru/natalia/2017/03/07/10-materialov-dlya-otdelki-fasada-chastnogo-doma/",
                "https://www.forumhouse.ru/journal/themes/66-varianty-otdelki-fasada-populyarnye-vidy-konstruktiv-osobennosti",
                "https://domstrousam.ru/sovremennye-materialy-dlya-fasada-doma-foto/",
                "https://srbu.ru/otdelochnye-materialy/1950-varianty-otdelki-fasada-chastnogo-doma.html",
                "https://www.alta-profil.ru/client-center/articles/sravnenie-novinok-na-rynke-fasadnyh-materialov/",
                "https://market.yandex.ru/search?text=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b5%20%d0%bc%d0%b0%d1%82%d0%b5%d1%80%d0%b8%d0%b0%d0%bb%d1%8b%20%d0%b4%d0%bb%d1%8f%20%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%bd%d0%be%d0%b9%20%d0%be%d0%b1%d0%bb%d0%b8%d1%86%d0%be%d0%b2%d0%ba%d0%b8%20%d0%b4%d0%be%d0%bc%d0%b0",
                "https://dzen.ru/media/tablichnik/luchshie-materialy-dlia-otdelki-fasada-chastnogo-doma-5f0ffd317e2b585adad67632",
                "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/otdelochnye-materialy-dlya-fasadov-chastnyx-domov.html",
                "https://www.grandline.ru/informaciya/fasad-chastnogo-doma-otdelka/",
                "https://dizlandshafta.ru/dizajn/doma/varianty-otdelki-fasada/",
                "https://vekroof.ru/articles/luchshie-materialy-dlya-otdelki-fasada/",
                "https://markakachestva.ru/rating-of/2247-luchshie-materialy-dlja-oblicovki-fasada.html",
                "https://www.tn.ru/journal/chem-otdelat-fasad-chastnogo-doma-podrobnyy-gayd-po-populyarnym-materialam/",
                "https://realty.rbc.ru/news/61a7952a9a7947039b719b08",
                "http://remoo.ru/fasad/fasady-domov",
                "https://alfakrov.com/blog/sovety_pokupatelyam/chem_obshit_dom_snaruzhi_deshevo_i_krasivo_foto_tseny_kharakteristiki_i_top_7_luchshikh_materialov/",
                "https://geostart.ru/post/5550",
                "https://kronotech.ru/publications/otdelka-fasada-chastnogo-doma"
            ]
        ],
        "материалы для фасадной отделки дома" => [
            "sites" => [
                "https://m-strana.ru/articles/chem-nedorogo-otdelat-fasad-doma/",
                "https://www.forumhouse.ru/journal/themes/66-varianty-otdelki-fasada-populyarnye-vidy-konstruktiv-osobennosti",
                "https://stroy-podskazka.ru/materialy/fasadnye-luchshaya-oblicovka/",
                "https://remstroiblog.ru/natalia/2017/03/07/10-materialov-dlya-otdelki-fasada-chastnogo-doma/",
                "https://domstrousam.ru/sovremennye-materialy-dlya-fasada-doma-foto/",
                "https://market.yandex.ru/search?text=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b5%20%d0%bc%d0%b0%d1%82%d0%b5%d1%80%d0%b8%d0%b0%d0%bb%d1%8b%20%d0%b4%d0%bb%d1%8f%20%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%bd%d0%be%d0%b9%20%d0%be%d0%b1%d0%bb%d0%b8%d1%86%d0%be%d0%b2%d0%ba%d0%b8%20%d0%b4%d0%be%d0%bc%d0%b0",
                "https://markakachestva.ru/rating-of/2247-luchshie-materialy-dlja-oblicovki-fasada.html",
                "https://www.alta-profil.ru/client-center/articles/sravnenie-novinok-na-rynke-fasadnyh-materialov/",
                "https://vekroof.ru/articles/luchshie-materialy-dlya-otdelki-fasada/",
                "https://www.grandline.ru/informaciya/fasad-chastnogo-doma-otdelka/",
                "https://realty.rbc.ru/news/61a7952a9a7947039b719b08",
                "https://srbu.ru/otdelochnye-materialy/1950-varianty-otdelki-fasada-chastnogo-doma.html",
                "https://dizlandshafta.ru/dizajn/doma/varianty-otdelki-fasada/",
                "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/otdelochnye-materialy-dlya-fasadov-chastnyx-domov.html",
                "https://dzen.ru/media/tablichnik/luchshie-materialy-dlia-otdelki-fasada-chastnogo-doma-5f0ffd317e2b585adad67632",
                "https://domof.ru/articles/kakoy-material-vybrat-dlya-otdelki-fasada-zdaniya/",
                "http://remoo.ru/fasad/fasady-domov",
                "https://alfakrov.com/blog/sovety_pokupatelyam/chem_obshit_dom_snaruzhi_deshevo_i_krasivo_foto_tseny_kharakteristiki_i_top_7_luchshikh_materialov/",
                "https://design-homes.ru/stroitelstvo-i-remont/nedorogo-fasad-doma",
                "https://www.bazaznaniyst.ru/varianty-krasivoj-i-deshevoj-obshivki-doma-snaruzhi/"
            ]
        ],
        "материалы для фасадных работ" => [
            "sites" => [
                "https://m-strana.ru/articles/chem-nedorogo-otdelat-fasad-doma/",
                "https://realty.rbc.ru/news/61a7952a9a7947039b719b08",
                "https://market.yandex.ru/search?text=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b5%20%d0%bc%d0%b0%d1%82%d0%b5%d1%80%d0%b8%d0%b0%d0%bb%d1%8b%20%d0%b4%d0%bb%d1%8f%20%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%bd%d0%be%d0%b9%20%d0%be%d0%b1%d0%bb%d0%b8%d1%86%d0%be%d0%b2%d0%ba%d0%b8%20%d0%b4%d0%be%d0%bc%d0%b0",
                "https://remstroiblog.ru/natalia/2017/03/07/10-materialov-dlya-otdelki-fasada-chastnogo-doma/",
                "https://stroy-podskazka.ru/materialy/fasadnye-luchshaya-oblicovka/",
                "https://www.forumhouse.ru/journal/themes/66-varianty-otdelki-fasada-populyarnye-vidy-konstruktiv-osobennosti",
                "https://domstrousam.ru/sovremennye-materialy-dlya-fasada-doma-foto/",
                "https://leroymerlin.ru/catalogue/fasadnye-paneli/",
                "https://vekroof.ru/articles/luchshie-materialy-dlya-otdelki-fasada/",
                "https://psk-holding.ru/catalog/fasad/",
                "https://krishafasad.ru/shop/fasadnye-materialy/",
                "https://www.grandline.ru/informaciya/fasad-chastnogo-doma-otdelka/",
                "http://remoo.ru/fasad/fasady-domov",
                "https://www.ozon.ru/category/otdelochnye-materialy-dlya-fasada/",
                "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/naruzhnaya-otdelka-doma-varianty.html",
                "https://geostart.ru/post/18183",
                "https://srbu.ru/otdelochnye-materialy/1950-varianty-otdelki-fasada-chastnogo-doma.html",
                "https://domof.ru/articles/kakoy-material-vybrat-dlya-otdelki-fasada-zdaniya/",
                "https://zod07.ru/statji/kak-vybrat-fasadnye-materialy-dlya-otdelki-doma-snaruzhi",
                "https://mk4s.ru/fasadnye-materialy/"
            ]
        ],
        "мокрый фасад воронеж" => [
            "sites" => [
                "https://www.avito.ru/voronezh/predlozheniya_uslug?q=%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9+%d1%84%d0%b0%d1%81%d0%b0%d0%b4",
                "https://dekor36.com/mokriy-fasad.html",
                "https://36-fasad.ru/nashi-uslugi/mokryj-fasad",
                "http://fasad36.ru/services/mokryy-fasad/",
                "https://uslugi.yandex.ru/193-voronezh/category?text=%d1%81%d0%b4%d0%b5%d0%bb%d0%b0%d1%82%d1%8c+%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9+%d1%84%d0%b0%d1%81%d0%b0%d0%b4",
                "https://kronvest.net/voronezh/wet-fasad",
                "https://vrn.profi.ru/remont/montazh-mokrogo-fasada/",
                "http://xn--36-glchqd5adeocin.xn--p1ai/mokryi-fasad.html",
                "https://voronezh.vse-podklyuch.ru/stroitelstvo/oblitsovka-fasadov/mokryy-fasad/",
                "https://uslugio.com/voronezh/1/9/mokryy-fasad",
                "https://fasad-rem.ru/services/%d0%bc%d0%be%d0%ba%d1%80%d1%8b%d0%b9-%d1%84%d0%b0%d1%81%d0%b0%d0%b4/",
                "http://teplofasad36.ru/morriy-fasad",
                "http://index-fs.ru/otdelka-mokrym-fasadom",
                "https://rskpanorama.com/uslugi/otdelochnye-raboty/montazh-mokrogo-fasada/",
                "https://sezrem.ru/mokryj-fasad/",
                "https://voronezh.stroyportal.ru/firms/section-mokrye-fasady-2933/",
                "https://voronezh.leroymerlin.ru/catalogue/shtukaturki/mokryy-fasad-s-utepleniem/",
                "https://www.remontnik.ru/voronezh/uteplenie_fasadov_mokryi_fasad/",
                "https://voronezh.urfomarket.ru/montazh_mokrogo_fasada_pod_klyuch.php",
                "https://visota-36.ru/uslugi/fasadnye-raboty/uteplenie-fasadov/"
            ]
        ],
        "облицовка фасада" => [
            "sites" => [
                "https://m-strana.ru/articles/chem-nedorogo-otdelat-fasad-doma/",
                "https://www.avito.ru/moskva/predlozheniya_uslug?q=%d0%be%d0%b1%d0%bb%d0%b8%d1%86%d0%be%d0%b2%d0%ba%d0%b0+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
                "https://stroy-podskazka.ru/dom/otdelka-fasada/",
                "https://zoon.ru/msk/m/oblitsovka_fasada/",
                "https://spk-fasad.ru/oblicovka-fasadov.html",
                "https://uslugi.yandex.ru/213-moscow/category/remont-i-stroitelstvo/fasadnyie-rabotyi--1981",
                "https://remont-book.com/oblicovka-fasada-zdaniya-kakoj-material-luchshe/",
                "https://fasadblog.ru/otdelka-fasadov/",
                "https://www.strd.ru/fasadi/",
                "https://www.prof-fasady.ru/catalog/fasad-doma/otdelka/chastnogo/",
                "https://www.timeout.ru/msk/uslugi/s_oblitsovka_fasada",
                "https://www.project-home.ru/info/clauses/obliczovka-fasada-doma-vidyi-i-kakoj-material-i-instrumentyi-luchshe",
                "https://mojdominfo.ru/oblicovka-fasada/",
                "https://sovet-ingenera.com/obustroystvo/drugoe-obustroystvo/oblicovka-fasada-doma.html",
                "https://www.tn.ru/journal/chem-otdelat-fasad-chastnogo-doma-podrobnyy-gayd-po-populyarnym-materialam/",
                "https://proremdom.ru/services/otdelochnye-raboty/otdelochnye-raboty-fasada/",
                "https://stroyhelper.ru/fasad-doma/",
                "https://design-homes.ru/stroitelstvo-i-remont/nedorogo-fasad-doma",
                "https://markakachestva.ru/rating-of/2247-luchshie-materialy-dlja-oblicovki-fasada.html",
                "https://dzen.ru/media/tablichnik/luchshie-materialy-dlia-otdelki-fasada-chastnogo-doma-5f0ffd317e2b585adad67632"
            ]
        ],
        "отделка фасада" => [
            "sites" => [
                "https://www.forumhouse.ru/journal/themes/66-varianty-otdelki-fasada-populyarnye-vidy-konstruktiv-osobennosti",
                "https://m-strana.ru/articles/chem-nedorogo-otdelat-fasad-doma/",
                "https://www.avito.ru/moskva/predlozheniya_uslug?q=%d0%be%d1%82%d0%b4%d0%b5%d0%bb%d0%ba%d0%b0+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%be%d0%b2",
                "https://stroy-podskazka.ru/dom/otdelka-fasada/",
                "https://uslugi.yandex.ru/213-moscow/category/remont-i-stroitelstvo/fasadnyie-rabotyi--1981",
                "https://www.grandline.ru/informaciya/fasad-chastnogo-doma-otdelka/",
                "https://realty.rbc.ru/news/61a7952a9a7947039b719b08",
                "https://www.alta-profil.ru/client-center/articles/otdelka-fasada-doma/",
                "http://remoo.ru/fasad/fasady-domov",
                "https://remstroiblog.ru/natalia/2017/03/07/10-materialov-dlya-otdelki-fasada-chastnogo-doma/",
                "https://domstrousam.ru/sovremennye-materialy-dlya-fasada-doma-foto/",
                "https://vekroof.ru/articles/luchshie-materialy-dlya-otdelki-fasada/",
                "https://dizlandshafta.ru/dizajn/doma/varianty-otdelki-fasada/",
                "https://kronotech.ru/fasadnye-raboty/otdelka-fasada",
                "https://market.yandex.ru/search?text=%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%bd%d1%8b%d0%b5%20%d0%bc%d0%b0%d1%82%d0%b5%d1%80%d0%b8%d0%b0%d0%bb%d1%8b%20%d0%b4%d0%bb%d1%8f%20%d0%bd%d0%b0%d1%80%d1%83%d0%b6%d0%bd%d0%be%d0%b9%20%d0%be%d0%b1%d0%bb%d0%b8%d1%86%d0%be%d0%b2%d0%ba%d0%b8%20%d0%b4%d0%be%d0%bc%d0%b0",
                "https://profi.ru/remont/fasadnye-raboty/remont-fasadov/oblicovka-fasadov/",
                "https://www.prof-fasady.ru/catalog/fasad-doma/otdelka/chastnogo/",
                "https://domof.ru/articles/kakoy-material-vybrat-dlya-otdelki-fasada-zdaniya/",
                "https://pikabu.ru/story/kakoy_material_luchshe_dlya_otdelki_fasada_doma_6671254",
                "https://srbu.ru/otdelochnye-materialy/1950-varianty-otdelki-fasada-chastnogo-doma.html"
            ]
        ],
        "отделка фасада дома" => [
            "sites" => [
                "https://m-strana.ru/articles/chem-nedorogo-otdelat-fasad-doma/",
                "https://www.forumhouse.ru/journal/themes/66-varianty-otdelki-fasada-populyarnye-vidy-konstruktiv-osobennosti",
                "https://stroy-podskazka.ru/dom/otdelka-fasada/",
                "https://uslugi.yandex.ru/213-moscow/category/remont-i-stroitelstvo/fasadnyie-rabotyi--1981",
                "https://www.alta-profil.ru/client-center/articles/otdelka-fasada-doma/",
                "https://remstroiblog.ru/natalia/2017/03/07/10-materialov-dlya-otdelki-fasada-chastnogo-doma/",
                "https://dizlandshafta.ru/dizajn/doma/varianty-otdelki-fasada/",
                "https://domstrousam.ru/sovremennye-materialy-dlya-fasada-doma-foto/",
                "https://realty.rbc.ru/news/61a7952a9a7947039b719b08",
                "https://www.grandline.ru/informaciya/fasad-chastnogo-doma-otdelka/",
                "https://strbani.ru/fasad-doma/",
                "https://www.avito.ru/moskva/predlozheniya_uslug?q=%d0%be%d0%b1%d0%bb%d0%b8%d1%86%d0%be%d0%b2%d0%ba%d0%b0+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0",
                "https://srbu.ru/otdelochnye-materialy/1950-varianty-otdelki-fasada-chastnogo-doma.html",
                "https://www.houzz.ru/foto/krasivye-doma-foto-fasadov-phbr0-bp~t_13935",
                "https://www.tn.ru/journal/chem-otdelat-fasad-chastnogo-doma-podrobnyy-gayd-po-populyarnym-materialam/",
                "https://www.hata.by/articles/otdelka_fasada_doma-9122/",
                "https://stroyka-gid.ru/fasad/otdelka-fasada-doma-kakoy-material-luchshe.html",
                "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/naruzhnaya-otdelka-doma-varianty.html",
                "https://pikabu.ru/story/kakoy_material_luchshe_dlya_otdelki_fasada_doma_6671254",
                "https://vekroof.ru/articles/luchshie-materialy-dlya-otdelki-fasada/"
            ]
        ],
        "отделка фасада частного дома" => [
            "sites" => [
                "https://m-strana.ru/articles/chem-nedorogo-otdelat-fasad-doma/",
                "https://stroy-podskazka.ru/dom/otdelka-fasada/",
                "https://www.forumhouse.ru/journal/themes/66-varianty-otdelki-fasada-populyarnye-vidy-konstruktiv-osobennosti",
                "https://remstroiblog.ru/natalia/2017/03/07/10-materialov-dlya-otdelki-fasada-chastnogo-doma/",
                "https://dizlandshafta.ru/dizajn/doma/varianty-otdelki-fasada/",
                "https://domstrousam.ru/sovremennye-materialy-dlya-fasada-doma-foto/",
                "https://www.grandline.ru/informaciya/fasad-chastnogo-doma-otdelka/",
                "https://www.alta-profil.ru/client-center/articles/otdelka-fasada-doma/",
                "https://srbu.ru/otdelochnye-materialy/1950-varianty-otdelki-fasada-chastnogo-doma.html",
                "https://uslugi.yandex.ru/213-moscow/category/remont-i-stroitelstvo/fasadnyie-rabotyi--1981",
                "https://strbani.ru/fasad-doma/",
                "https://stroyday.ru/stroitelstvo-doma/fasadnye-raboty/naruzhnaya-otdelka-doma-varianty.html",
                "http://remoo.ru/fasad/fasady-domov",
                "https://pix-feed.com/krasivye-fasady-chastnyh-domov/",
                "https://www.ksu-nordwest.ru/services/otdelka-fasada/",
                "https://design-homes.ru/stroitelstvo-i-remont/nedorogo-fasad-doma",
                "https://www.houzz.ru/foto/krasivye-doma-foto-fasadov-phbr0-bp~t_13935",
                "https://realty.rbc.ru/news/61a7952a9a7947039b719b08",
                "https://dzen.ru/media/tablichnik/luchshie-materialy-dlia-otdelki-fasada-chastnogo-doma-5f0ffd317e2b585adad67632",
                "https://www.avito.ru/moskva/predlozheniya_uslug?q=%d0%be%d0%b1%d0%bb%d0%b8%d1%86%d0%be%d0%b2%d0%ba%d0%b0+%d1%84%d0%b0%d1%81%d0%b0%d0%b4%d0%b0"
            ]
        ],
        "штукатурка короед купить в воронеже" => [
            "sites" => [
                "https://voronezh.leroymerlin.ru/catalogue/shtukaturki/koroed/",
                "https://www.avito.ru/voronezh?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0+%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                "https://market.yandex.ru/search?text=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4%20%d0%b2%20%d0%b2%d0%be%d1%80%d0%be%d0%bd%d0%b5%d0%b6%d0%b5%20%d1%86%d0%b5%d0%bd%d1%8b",
                "https://voronezh.regmarkets.ru/shtukaturka-koroed/",
                "https://voronezh.vseinstrumenti.ru/stroitelnye-materialy/otdelochnye-materialy/shtukaturki/koroed/",
                "https://voronezh.stroyportal.ru/catalog/section-shtukaturka-koroed-7581/",
                "https://kraski36.ru/shtukaturka-koroed-voronezh/",
                "https://voronezh.pulscen.ru/price/110514-shtukaturka/f:62057_koroied",
                "http://fasad36.ru/catalog/koroed/",
                "https://www.ozon.ru/category/shtukaturki-koroed/",
                "https://voronezh.dommalera.ru/catalog/materialy_dlya_dekora/shtukaturki_dekorativnye_1/koroed_1/",
                "https://voronezh.blizko.ru/predl/construction/decoration/smesi/shtukaturki_dekorativny/f:166_koroied",
                "https://voronezh.satom.ru/k/dekorativnye-shtukaturki-koroed/",
                "https://www.castorama.ru/catalogsearch/result/?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                "http://stroitelnye-materialy-v-voronezhe.ru/shtukaturka-koroed",
                "https://stroybaza-vrn.ru/katalog/suhie-stroitelmie-smesi/%d0%b4%d0%b5%d0%ba%d0%be%d1%80%d0%b0%d1%82%d0%b8%d0%b2%d0%bd%d0%b0%d1%8f-%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0",
                "https://lidecor.ru/category/pokrytiya-koroed/",
                "https://voronezh.yavitrina.ru/dekorativnye-shtukaturki-koroed",
                "https://voronezh.compumir.ru/fasadnaja-shtukaturka-koroed",
                "https://voronezh.neopod.ru/shtukaturki-fasadnye-koroed-bergauf"
            ]
        ],
        "штукатурка короед цена в воронеже" => [
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
                "https://voronezh.satom.ru/k/dekorativnye-shtukaturki-koroed/",
                "https://voronezh.dommalera.ru/catalog/materialy_dlya_dekora/shtukaturki_dekorativnye_1/koroed_1/",
                "http://fasad36.ru/catalog/koroed/",
                "http://stroitelnye-materialy-v-voronezhe.ru/shtukaturka-koroed",
                "https://www.castorama.ru/catalogsearch/result/?q=%d1%88%d1%82%d1%83%d0%ba%d0%b0%d1%82%d1%83%d1%80%d0%ba%d0%b0%20%d0%ba%d0%be%d1%80%d0%be%d0%b5%d0%b4",
                "https://voronezh.yavitrina.ru/shtukaturka-koroed",
                "https://voronezh.neopod.ru/shtukaturki-fasadnye-koroed-bergauf",
                "https://voronezh.compumir.ru/shtukaturka-koroed",
                "https://lidecor.ru/category/dekorativnaya-shtukaturka-koroed/",
                "https://voronez.gamma-cveta.ru/shtukaturki-dekorativnye-fakturnye-kraski-main/effekt-koroed/"
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
                "https://www.ozon.ru/category/shtukaturki-koroed/",
                "https://kraski36.ru/shtukaturka-koroed-voronezh/",
                "https://voronezh.pulscen.ru/price/110514-shtukaturka/f:62057_koroied",
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
                "https://lidecor.ru/category/dekorativnaya-shtukaturka-koroed/"
            ]
        ]
    ];
    $minimum = 8;

    $willClustered = [];
    $clusters = [];

    foreach ($array as $phrase => $sites) {
        foreach ($array as $phrase2 => $sites2) {
            if (isset($willClustered[$phrase2])) {
                continue;
            }
            if (isset($clusters[$phrase2])) {
                foreach ($clusters[$phrase2] as $elems) {
                    foreach ($elems as $elem) {
                        if (count(array_intersect($elem, $sites2['sites'])) >= $minimum) {
                            $clusters[$phrase][$phrase2] = $sites2['sites'];
                            $willClustered[$phrase2] = true;
                            break 2;
                        }
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

    dd($clusters);
    foreach ($clusters as $phrase => $item) {
        foreach ($item as $itemPhrase => $elems) {
            $this->clusters[$phrase][$itemPhrase]['sites'] = $elems[0];
        }
    }

    foreach ($clusters as $mainPhrase => $items) {
        if (count($items) > 1) {
            continue;
        }
        foreach ($clusters as $mainPhrase2 => $items2) {
            if ($mainPhrase === $mainPhrase2) {
                continue;
            }
            foreach ($items2 as $item) {
                if (count(array_intersect($items[array_key_first($items)][0], $item[0])) >= $minimum) {
                    $this->clusters[$mainPhrase2][$mainPhrase] = $items[array_key_first($items)];
                    unset($this->clusters[$mainPhrase]);
                    break 2;
                }
            }
        }
    }
});
