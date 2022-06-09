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

use App\TelegramBot;
use App\TextAnalyzer;
use Illuminate\Support\Facades\Auth;

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
Route::post('/balance-add/result', 'BalanceAddController@result')->name('balance.add.result');

Route::middleware(['verified'])->group(function () {

    Route::get('test', 'TestController@index')->name('test');

    Route::get('/', 'HomeController@index')->name('home');
    Route::post('project-sortable', 'HomeController@projectSort');
    Route::post('menu-item-sortable', 'HomeController@menuItemSort');
    Route::post('/get-description-projects', 'HomeController@getDescriptionProjects')->name('get.description.projects');

    Route::resource('main-projects', 'DescriptionProjectForAdminController');

    Route::get('users/{id}/login', 'UsersController@login')->name('users.login');
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

    Route::get('/competitor-analysis', 'SearchCompetitorsController@index');
    Route::post('/competitor-analysis', 'SearchCompetitorsController@analyzeSites')->name('analysis.sites');
    Route::post('/analyze-nesting', 'SearchCompetitorsController@analyseNesting')->name('analysis.nesting');
    Route::post('/analyze-positions', 'SearchCompetitorsController@analysePositions')->name('analysis.positions');
    Route::post('/analyze-tags', 'SearchCompetitorsController@analyseTags')->name('analysis.tags');

    Route::get('/test-relevance', 'TestRelevanceController@testView')->name('test.relevance.view');
    Route::post('/test-analyse', 'TestRelevanceController@testAnalyse')->name('test.relevance');
    Route::get('/create-queue-test', 'TestRelevanceController@createQueue')->name('create.queue.testView');
    Route::post('/create-queue-test-post', 'TestRelevanceController@createTaskQueue')->name('create.queue.test.post');
    Route::get('/history-test', 'TestRelevanceController@history')->name('relevance.history.test');

    Route::get('/create-queue', 'RelevanceController@createQueue')->name('create.queue.view');
    Route::post('/create-queue', 'RelevanceController@createTaskQueue')->name('create.queue');
    Route::get('/analyze-relevance', 'RelevanceController@index')->name('relevance-analysis');
    Route::post('/analyze-relevance', 'RelevanceController@analysis')->name('analysis.relevance');
    Route::post('/repeat-analyze-main-page', 'RelevanceController@repeatMainPageAnalysis')->name('repeat.main.page.analysis');
    Route::post('/repeat-analyze-relevance', 'RelevanceController@repeatRelevanceAnalysis')->name('repeat.relevance.analysis');
    Route::post('/configure-children-rows', 'RelevanceController@configureChildrenRows')->name('configure.children.rows');
    Route::get('/show-children-rows/{filePath}', 'RelevanceController@showChildrenRows')->name('show.children.rows');
    Route::post('/change-config', 'RelevanceController@changeConfig')->name('changeConfig');
    Route::get('/history', 'HistoryRelevanceController@index')->name('relevance.history');
    Route::post('/edit-group-name', 'HistoryRelevanceController@editGroupName')->name('edit.group.name');
    Route::post('/edit-history-comment', 'HistoryRelevanceController@editComment')->name('edit.history.comment');
    Route::post('/change-state', 'HistoryRelevanceController@changeCalculateState')->name('change.state');
    Route::get('/show-history/{id}', 'HistoryRelevanceController@show')->name('show.history');
    Route::post('/get-details-history', 'HistoryRelevanceController@getDetailsInfo')->name('get.details.info');
    Route::post('/get-stories', 'HistoryRelevanceController@getStories')->name('get.stories');
    Route::get('/get-history-info/{object}', 'HistoryRelevanceController@getHistoryInfo')->name('get.history.info');
    Route::post('/repeat-scan', 'HistoryRelevanceController@repeatScan')->name('repeat.scan');

    Route::get('/balance', 'BalanceController@index')->name('balance.index');
    Route::resource('balance-add', 'BalanceAddController');

    Route::get('/tariff/{confirm?}/unsubscribe', 'TariffPayController@confirmUnsubscribe')->name('tariff.unsubscribe');
    Route::post('/tariff/total', 'TariffPayController@total')->name('tariff.total');
    Route::resource('tariff', 'TariffPayController');

    Route::resource('monitoring', 'MonitoringController');
    Route::get('/monitoring/projects/get', 'MonitoringController@getProjects')->name('monitoring.projects.get');
    Route::get('/monitoring/{project_id}/keywords/get', 'MonitoringController@getKeywordsByProject')->name('monitoring.keywords.get');

    Route::resource('monitoring/keywords', 'MonitoringKeywordsController');
    Route::resource('monitoring/groups', 'MonitoringGroupsController');
    Route::post('monitoring/keywords/queue', 'MonitoringKeywordsController@addingQueue')->name('keywords.queue');

});

Route::get('/bla', function () {
//    $site = TextAnalyzer::removeStylesAndScripts(TextAnalyzer::curlInit('https://svetlica-vrn.ru/category/rulonnye-shtory/'));
//    dd(\App\TestRelevance::clearHTMLFromLinks($site));
    $s = '<!doctype html>
<html lang=\"ru\">
<head>
    <!-- global site tag (gtag.js) - google analytics -->



    <!-- global site tag (gtag.js) - google analytics -->



    <meta charset=\"utf-8\">
    <title>рулонные шторы купить в москве в интернет-магазине готовых рулонных штор недорого</title>
    <meta name=\"title\" content=\"рулонные шторы купить в москве в интернет-магазине готовых рулонных штор недорого\">
    <meta name=\"description\" content=\"купить готовые рулонные шторы в москве недорого в интернет магазине рулонных штор цена от 450 руб: готовые рулонные шторы от производителя с доставкой по москве: рулонные шторы legrand\">
    <meta name=\"keywords\" content=\"\">
    <meta name=\"robots\" content=\"index, follow\">
                <link rel=\"canonical\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna\"/>



    <meta name=\"yandex-verification\" content=\"2ff1be656f841191\" />
    <meta name=\"google-site-verification\" content=\"wkespn8ce7lmcgqnrfl01xo_q7rmesmgk0gzpzael7w\" />

    <meta name=\"author\" content=\"snapix\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <meta name=\"generator\" content=\"octobercms\">
    <meta name=\"cmsmagazine\" content=\"ba1be6000f5ff0d32d17a0952ac993ee\"/>

    <link rel=\"preconnect\" href=\"//gstatic.com\">
    <link rel=\"preconnect\" href=\"//google.com\">
    <link rel=\"preconnect\" href=\"//cdn.jsdelivr.net\">
    <link rel=\"preconnect\" href=\"//cdnjs.cloudflare.com\">
    <link rel=\"preconnect\" href=\"//s3-us-west-2.amazonaws.com\">
    <link rel=\"preload\" href=\"https://domlegrand.com/themes/legrand/assets/styles/style.css\" as=\"style\">


    <link rel=\"shortcut icon\" href=\"https://domlegrand.com/themes/legrand/assets/images/favicon.svg\">
    <link rel=\"apple-touch-icon\" sizes=\"57x57\" href=\"https://domlegrand.com/themes/legrand/assets/images/icon-57.png\">
    <link rel=\"apple-touch-icon\" sizes=\"120x120\" href=\"https://domlegrand.com/themes/legrand/assets/images/icon-120.png\">
    <link rel=\"apple-touch-icon\" sizes=\"152x152\" href=\"https://domlegrand.com/themes/legrand/assets/images/icon-152.png\">
    <link rel=\"apple-touch-icon\" sizes=\"176x176\" href=\"https://domlegrand.com/themes/legrand/assets/images/icon-176.png\">
    <link rel=\"apple-touch-icon\" sizes=\"180x180\" href=\"https://domlegrand.com/themes/legrand/assets/images/icon-180.png\">

<!--    <link rel=\"stylesheet\" type=\"text/css\" href=\"https://domlegrand.com/combine/1d116078cf5f38884eeb5438ee59beff-1652436203\" />-->
<!--    <link rel=\"stylesheet\" class=\"critical-css\" type=\"text/css\" href=\"https://domlegrand.com/themes/legrand/assets/styles/style-critical.css\" />-->
    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css\" />
    <link rel=\"stylesheet\" type=\"text/css\" href=\"https://domlegrand.com/themes/legrand/assets/styles/style.css\" />

</head>
<body>
<!-- yandex.metrika counter -->

<noscript><div><img src=\"https://mc.yandex.ru/watch/14457736\" style=\"position:absolute; left:-9999px;\" alt=\"\" /></div></noscript>
<!-- /yandex.metrika counter -->
<div class=\"page\">

\t\t\t\t\t\t\t\t
\t<header class=\"page-header \">
\t\t<div class=\"page-header__top\" data-has-tooltip>
\t\t\t<div class=\"page-header__row page-header__content\">

\t\t\t\t<div class=\"page-header__section\">

\t\t\t\t\t<button class=\"header-menu-btn js-toggle-header-menu\">
\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path d=\"m33,15h12v1.8261h21v15z\"/>
\t\t\t<path d=\"m33,21.087h12v1.8261h21v21.087z\"/>
\t\t\t<path d=\"m33,27.1739h12v29h21v27.1739z\"/>
\t\t</svg>
\t

\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t


\t\t\t\t\t\t<span id=\"partial_w-and-c-count\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-tools__counter hidden\">0</span>
\t\t\t\t\t\t</span>

\t\t\t\t\t\t


\t<div class=\"tile-tooltip     \" data-tooltip-index=\"1\">
\t\tменю

\t\t<div class=\"tile-tooltip__corner\">
\t\t\t<svg class=\"icon-raw\" width=\"2.2em\" height=\"1em\" viewbox=\"0 0 22 10\" fill=\"none\">
\t\t\t<path d=\"m11 0s1 4.5 4.344 7.17c18.014 9.304 22 10 22 10h0s3.837-.694 6.574-2.83c10 4.5 11 0 11 0z\" />
\t\t</svg>
\t
</div>
\t</div>

\t\t\t\t\t</button>

\t\t\t\t\t

\t<ul class=\"header-top-list header-top-nav\">
\t\t\t\t\t<li class=\"header-top-list__item phone\">
\t\t\t\t\t\t\t\t\t<a class=\"header-top-list__link\" href=\"tel:+7(495)191-00-26\">
\t\t\t\t\t\t<span class=\"header-top-list__link-icon\">
\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t<path d=\"m30.65 27.313-1.995-1.33l26.88 24.8a.786.786 0 0 0-1.056.171l-1.097 1.412a.782.782 0 0 1-.998.207c-.745-.415-1.626-.805-3.57-2.75-1.943-1.947-2.335-2.825-2.75-3.57a.782.782 0 0 1 .207-.998l1.412-1.097a.786.786 0 0 0 .17-1.056l18.055 15.4l-1.367-2.05a.786.786 0 0 0-1.058-.238l-1.576.946c-.426.25-.74.656-.875 1.131-.43 1.572-.518 5.039 5.038 10.595 5.556 5.556 9.023 5.469 10.595 5.038.475-.136.88-.449 1.131-.875l.946-1.576a.786.786 0 0 0-.238-1.058zm23.241 15.793a5.282 5.282 0 0 1 5.276 5.276.31.31 0 1 0 .62 0 5.903 5.903 0 0 0-5.896-5.897.31.31 0 0 0 0 .621z\" />
\t\t<path d=\"m23.241 17.655a3.418 3.418 0 0 1 3.414 3.414.31.31 0 1 0 .62 0 4.04 4.04 0 0 0-4.034-4.035.31.31 0 0 0 0 .621z\" />
\t\t<path d=\"m23.241 19.517c.857.001 1.55.695 1.552 1.552a.31.31 0 1 0 .62 0 2.175 2.175 0 0 0-2.172-2.173.31.31 0 0 0 0 .621z\" />
\t</svg>
\t
</span>
\t\t\t\t\t\t<span class=\"header-top-list__link-title\">+7 (495) 191-00-26</span>
\t\t\t\t\t</a>
\t\t\t\t\t\t\t</li>
\t\t\t</ul>


\t\t\t\t</div>

\t\t\t\t<a class=\"header-logo\" href=\"https://domlegrand.com\">
\t\t\t\t\t
\t\t\t<svg class=\"icon-raw\" width=\"6.8em\" height=\"1em\" viewbox=\"0 0 136 20\" fill=\"none\">
\t\t\t<path d=\"m121.97 19.555h5.928c6.067 0 8.102-3.562 8.102-9.932 0-6.952-2.316-9.281-7.927-9.281h-6.103v19.213zm-64.597 0h4.068v-7.569h3.016l2.911 7.57h4.597l-3.859-8.837a6.15 6.15 0 001.721-2.104 6.004 6.004 0 00.63-2.622c0-3.219-2.21-5.65-6.384-5.65h-6.698l-.002 19.212zm104.904.342h-5.331l-.141 19.213h3.473l-.141-5.171c-.105-2.911-.316-7.5-.701-10.857.561 1.953 1.262 3.767 2.876 7.808l3.226 8.22h5.367l.14-19.213h110.2l.14 5.171c.071 3.528.246 7.809.632 11.028-.562-1.952-1.158-3.664-2.842-7.98l-3.226-8.22zm80.647 19.555l.982-4.315h5.858l1.087 4.315h4.034l87.802.342h-6.137l-4.77 19.213h3.752zm41.85 11.882h3.368v5.003c-.7.235-1.436.351-2.175.342-3.122 0-4.209-1.918-4.209-7.055 0-5.24.701-7.397 3.541-7.397 1.614 0 2.63.924 2.63 2.465v.993h4.104v5c0-3.219-2.244-5-6.769-5-4.98 0-7.751 2.98-7.751 10.034 0 6.576 1.999 9.966 8.137 9.966 2.14 0 4.735-.581 6.348-1.54v9.042h-7.225l.001 2.839zm-24.958 7.671h10.803v-2.945h20.96v-5.614h6.032v8.048h20.96v-4.76h6.734v.341h16.89v19.211zm0 19.553h10.487v16.37h-6.42v.342h0v19.212zm128.003 3.287c2.876 0 3.753 1.404 3.753 6.336 0 5.137-.632 6.986-4.069 6.986h-1.648v3.288l1.964-.002zm-64.212-.274c1.894 0 2.56 1.78 2.56 3.082 0 1.678-.841 3.22-2.665 3.22h-2.243v3.011h2.348zm20.645 0l2.35 9.35h-4.49l2.14-9.35z\"/>
\t\t</svg>
\t

\t\t\t\t</a>

\t\t\t\t<div class=\"page-header__section\">

\t\t\t\t\t
\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"header-tools \">
\t\t<div class=\"header-tools__item header-tools__search\">
\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile      header-tools__btn js-toggle-header-drop\"
\t\t\thref=\"#header-search\"
\t\t\tdata-js=\"\"
\t\t>
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m20.913,27.826
\t\t\tc3.8179,0,6.913-3.0951,6.913-6.913s24.7309,14,20.913,14s14,17.0951,14,20.913s17.0951,27.826,20.913,27.826z\"/>
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m30.3,30.7759
\t\t\tl-4.758-4.835\"/>
\t\t</svg>
\t
</a>
\t
\t
\t\t</div>

\t\t<div class=\"header-tools__item header-tools__wish\">
\t\t\t
\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile      header-tools__btn\"
\t\t\thref=\"https://domlegrand.com/wish\"
\t\t\tdata-js=\"\"
\t\t>\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t


\t\t\t\t<div id=\"partial_wishcount\">
\t\t\t\t\t\t\t\t\t</div>
\t\t\t</a>
\t
\t

\t\t\t
\t\t\t
\t
\t<div id=\"header-wish\" class=\"header-drop header-wish-menu js-header-drop\">
\t\t<div class=\"header-drop__head\">
\t\t\t<p class=\"header-drop__title h2\">избранное</p>

\t\t\t<button class=\"header-drop__close js-close-header-drop js-toggle-header-drop\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t</div>

\t\t<div class=\"header-drop__body\">
\t\t\t
\t\t\t\t\t\t\t<div id=\"partial_headerwish\" class=\"overlay-header-drop\">
\t\t\t\t\t


\t<div class=\"header-wish\">
\t\t<div class=\"header-wish__body\" data-empty-text=\"lorem ipsum\">

\t\t\t\t\t\t\t<div class=\"header-wish__empty\">нет ни одного избранного товара</div>
\t\t\t
\t\t</div>

\t\t<div class=\"header-wish__foot\">
\t\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog\"
\t\t\tdata-js=\"\"
\t\t>перейти в каталог</a>
\t
\t
\t\t\t\t\t</div>
\t</div>

\t\t\t\t</div>
\t\t\t
\t\t\t
\t\t</div>

\t\t<div class=\"header-drop__corner\">
\t\t\t<svg class=\"icon-raw\" width=\"2.2em\" height=\"1em\" viewbox=\"0 0 22 10\" fill=\"none\">
\t\t\t<path d=\"m11 0s1 4.5 4.344 7.17c18.014 9.304 22 10 22 10h0s3.837-.694 6.574-2.83c10 4.5 11 0 11 0z\" />
\t\t</svg>
\t
</div>

\t\t
\t</div>

\t\t\t


\t<div class=\"tile-tooltip                         tile-tooltip--center
            \" data-tooltip-index=\"2\">
\t\tизбранное

\t\t<div class=\"tile-tooltip__corner\">
\t\t\t<svg class=\"icon-raw\" width=\"2.2em\" height=\"1em\" viewbox=\"0 0 22 10\" fill=\"none\">
\t\t\t<path d=\"m11 0s1 4.5 4.344 7.17c18.014 9.304 22 10 22 10h0s3.837-.694 6.574-2.83c10 4.5 11 0 11 0z\" />
\t\t</svg>
\t
</div>
\t</div>

\t\t</div>

\t\t<div class=\"header-tools__item header-tools__compare\">
\t\t\t
\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile      header-tools__btn\"
\t\t\thref=\"https://domlegrand.com/compare\"
\t\t\tdata-js=\"\"
\t\t>\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t


\t\t\t\t<div id=\"partial_comparecount\">
\t\t\t\t\t\t\t\t\t</div>
\t\t\t</a>
\t
\t

\t\t\t\t\t\t
\t\t\t
\t
\t<div id=\"header-compare\" class=\"header-drop header-compare-menu js-header-drop\">
\t\t<div class=\"header-drop__head\">
\t\t\t<p class=\"header-drop__title h2\">сравнение</p>

\t\t\t<button class=\"header-drop__close js-close-header-drop js-toggle-header-drop\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t</div>

\t\t<div class=\"header-drop__body\">
\t\t\t
\t\t\t\t\t\t\t<div id=\"partial_headercompare\" class=\"overlay-header-drop\">
\t\t\t\t\t



\t<div class=\"header-compare\">
\t\t\t\t\t<div class=\"header-compare__empty\">
\t\t\t\tу вас пока что нет товаров для сравнения

\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog\"
\t\t\tdata-js=\"\"
\t\t>перейти в каталог</a>
\t
\t
\t\t\t</div>
\t\t\t</div>

\t\t\t\t</div>
\t\t\t
\t\t\t
\t\t</div>

\t\t<div class=\"header-drop__corner\">
\t\t\t<svg class=\"icon-raw\" width=\"2.2em\" height=\"1em\" viewbox=\"0 0 22 10\" fill=\"none\">
\t\t\t<path d=\"m11 0s1 4.5 4.344 7.17c18.014 9.304 22 10 22 10h0s3.837-.694 6.574-2.83c10 4.5 11 0 11 0z\" />
\t\t</svg>
\t
</div>

\t\t
\t</div>

\t\t\t


\t<div class=\"tile-tooltip                         tile-tooltip--center
            \" data-tooltip-index=\"3\">
\t\tсравнение

\t\t<div class=\"tile-tooltip__corner\">
\t\t\t<svg class=\"icon-raw\" width=\"2.2em\" height=\"1em\" viewbox=\"0 0 22 10\" fill=\"none\">
\t\t\t<path d=\"m11 0s1 4.5 4.344 7.17c18.014 9.304 22 10 22 10h0s3.837-.694 6.574-2.83c10 4.5 11 0 11 0z\" />
\t\t</svg>
\t
</div>
\t</div>

\t\t</div>

\t\t<div class=\"header-tools__item header-tools__phone\">
\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile      header-tools__btn\"
\t\t\thref=\"tel:+7(495)191-00-26\"
\t\t\tdata-js=\"\"
\t\t>
\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t<path d=\"m30.65 27.313-1.995-1.33l26.88 24.8a.786.786 0 0 0-1.056.171l-1.097 1.412a.782.782 0 0 1-.998.207c-.745-.415-1.626-.805-3.57-2.75-1.943-1.947-2.335-2.825-2.75-3.57a.782.782 0 0 1 .207-.998l1.412-1.097a.786.786 0 0 0 .17-1.056l18.055 15.4l-1.367-2.05a.786.786 0 0 0-1.058-.238l-1.576.946c-.426.25-.74.656-.875 1.131-.43 1.572-.518 5.039 5.038 10.595 5.556 5.556 9.023 5.469 10.595 5.038.475-.136.88-.449 1.131-.875l.946-1.576a.786.786 0 0 0-.238-1.058zm23.241 15.793a5.282 5.282 0 0 1 5.276 5.276.31.31 0 1 0 .62 0 5.903 5.903 0 0 0-5.896-5.897.31.31 0 0 0 0 .621z\" />
\t\t<path d=\"m23.241 17.655a3.418 3.418 0 0 1 3.414 3.414.31.31 0 1 0 .62 0 4.04 4.04 0 0 0-4.034-4.035.31.31 0 0 0 0 .621z\" />
\t\t<path d=\"m23.241 19.517c.857.001 1.55.695 1.552 1.552a.31.31 0 1 0 .62 0 2.175 2.175 0 0 0-2.172-2.173.31.31 0 0 0 0 .621z\" />
\t</svg>
\t
</a>
\t
\t
\t\t</div>

\t\t<div class=\"header-tools__item header-tools__cart\">
\t\t\t
\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile      header-tools__btn\"
\t\t\thref=\"https://domlegrand.com/ordering\"
\t\t\tdata-js=\"\"
\t\t>\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-miterlimit=\"10\" d=\"m16.687,13l14,16.582v29.12c0,0.475,0.1887,0.9306,0.5246,1.2664
\t\t\tc0.1663,0.1663,0.3637,0.2983,0.581,0.3883s0.4502,0.1363,0.6854,0.1363h12.538c0.475,0,0.9306-0.1887,1.2664-0.5246
\t\t\tc29.9313,30.0506,30.12,29.595,30.12,29.12v16.582l27.434,13h16.687z\"/>
\t\t\t<path stroke-width=\"1.5\" stroke-miterlimit=\"10\" d=\"m14.1841,16.323h15.607\"/>
\t\t\t<path stroke-width=\"1.5\" stroke-miterlimit=\"10\" d=\"m25.643,20.165c0,0.9501-0.3774,1.8614-1.0493,2.5332
\t\t\tc-0.6718,0.6719-1.583,1.0493-2.5332,1.0493c-0.9501,0-1.8613-0.3774-2.5332-1.0493c-0.6718-0.6718-1.0493-1.5831-1.0493-2.5332\"/>
\t\t</svg>
\t


\t\t\t\t<div id=\"partial_cartcount\">
\t\t\t\t\t\t\t\t\t</div>
\t\t\t</a>
\t
\t

\t\t\t
\t\t\t
\t
\t<div id=\"header-cart\" class=\"header-drop header-cart-menu js-header-drop\">
\t\t<div class=\"header-drop__head\">
\t\t\t<p class=\"header-drop__title h2\">корзина</p>

\t\t\t<button class=\"header-drop__close js-close-header-drop js-toggle-header-drop\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t</div>

\t\t<div class=\"header-drop__body\">
\t\t\t
\t\t\t\t\t\t\t<div id=\"partial_headercart\" class=\"overlay-header-drop\">
\t\t\t\t\t

\t\t\t
\t<div class=\"header-cart\">
\t\t<div class=\"header-cart__body\" data-empty-text=\"lorem ipsum\">
\t\t\t
\t\t\t\t\t\t\t<div class=\"header-cart__empty\">в корзине пока ничего нет</div>
\t\t\t
\t\t</div>

\t\t<div class=\"header-cart__foot\">
\t\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog\"
\t\t\tdata-js=\"\"
\t\t>перейти в каталог</a>
\t
\t
\t\t\t\t\t</div>
\t</div>

\t\t\t\t</div>
\t\t\t
\t\t\t
\t\t</div>

\t\t<div class=\"header-drop__corner\">
\t\t\t<svg class=\"icon-raw\" width=\"2.2em\" height=\"1em\" viewbox=\"0 0 22 10\" fill=\"none\">
\t\t\t<path d=\"m11 0s1 4.5 4.344 7.17c18.014 9.304 22 10 22 10h0s3.837-.694 6.574-2.83c10 4.5 11 0 11 0z\" />
\t\t</svg>
\t
</div>

\t\t
\t</div>

\t\t\t


\t<div class=\"tile-tooltip                         tile-tooltip--right
            \" data-tooltip-index=\"4\">
\t\tкорзина

\t\t<div class=\"tile-tooltip__corner\">
\t\t\t<svg class=\"icon-raw\" width=\"2.2em\" height=\"1em\" viewbox=\"0 0 22 10\" fill=\"none\">
\t\t\t<path d=\"m11 0s1 4.5 4.344 7.17c18.014 9.304 22 10 22 10h0s3.837-.694 6.574-2.83c10 4.5 11 0 11 0z\" />
\t\t</svg>
\t
</div>
\t</div>

\t\t</div>

\t\t
\t
\t<div id=\"header-search\" class=\"header-drop header-search-menu js-header-drop\">
\t\t<div class=\"header-drop__head\">
\t\t\t<p class=\"header-drop__title h2\">поиск</p>

\t\t\t<button class=\"header-drop__close js-close-header-drop js-toggle-header-drop\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t</div>

\t\t<div class=\"header-drop__body\">
\t\t\t
\t\t\t

<form class=\"header-search\" action=\"https://domlegrand.com/search\">
\t\t<div class=\"header-search__field\">
\t\t\t

\t<div class=\"form-input        \">
\t\t<input class=\"form-input__element \"
\t\t\t   type=\"text\"
\t\t\t   name=\"search\"
\t\t\t   placeholder=\"что вы ищите?\"
\t\t\t   value=\"\"
\t\t\t   \t\t\t   \t\t\t   \t\t\t   \t\t>

\t\t
\t\t\t</div>

\t\t</div>

\t\t<div class=\"header-search__actions\">
\t\t\t<button class=\"header-search__actions-item header-search__submit\" type=\"submit\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m20.913,27.826
\t\t\tc3.8179,0,6.913-3.0951,6.913-6.913s24.7309,14,20.913,14s14,17.0951,14,20.913s17.0951,27.826,20.913,27.826z\"/>
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m30.3,30.7759
\t\t\tl-4.758-4.835\"/>
\t\t</svg>
\t
</button>
\t\t\t<button class=\"header-search__actions-item header-search__reset js-toggle-header-drop\" type=\"reset\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t</div>
\t</form>

\t\t\t
\t\t</div>

\t\t<div class=\"header-drop__corner\">
\t\t\t<svg class=\"icon-raw\" width=\"2.2em\" height=\"1em\" viewbox=\"0 0 22 10\" fill=\"none\">
\t\t\t<path d=\"m11 0s1 4.5 4.344 7.17c18.014 9.304 22 10 22 10h0s3.837-.694 6.574-2.83c10 4.5 11 0 11 0z\" />
\t\t</svg>
\t
</div>

\t\t\t\t\t<div class=\"header-drop__blur js-close-header-drop js-toggle-header-drop\"></div>
\t\t
\t</div>

\t</div>


\t\t\t\t</div>

\t\t\t</div>
\t\t</div>

\t\t\t\t\t<div class=\"page-header__catalog\">
\t\t\t<div class=\"carousel carousel--side-blur page-header__content\">

\t\t\t\t<div class=\"swiper-container carousel__body js-carousel-header-catalog\">
\t\t\t\t\t<div class=\"swiper-wrapper carousel__list js-category-menu\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t
\t

\t<a class=\"header-catalog-tile\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor\" data-catalog-id=\"header-catalog\">
\t\t<div class=\"header-catalog-tile__icon\"><img src=\"https://domlegrand.com/storage/app/uploads/public/618/24f/55a/61824f55aeb0c155156499.svg\" alt=\"\"></div>
<!--\t\t<div class=\"header-catalog-tile__icon\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 32 32\" fill=\"none\">
\t\t  <path d=\"m26.434 5.565h2.782v23.653h9.74a16.49 16.49 0 00-2.28-8.348h17.08a16.49 16.49 0 00-2.279 8.348h6.957v5.565h-2.783zm5.564 6.957h5.566a16.418 16.418 0 01-5.392 12.521h4.174v6.957h1.39zm-1.39 20.87v20.87h1.64a15.118 15.118 0 012.465 6.956h4.174zm3.603-8.349a18.204 18.204 0 004.744-12.521h2.783v12.521h7.777zm8.918-12.521h2.783a18.204 18.204 0 004.744 12.521h-7.527v6.957zm7.025 20.87a15.12 15.12 0 012.466-6.957h1.64v6.956h23.72zm4.106-8.349h26.26a16.418 16.418 0 01-5.392-12.521h6.957v12.521zm29.217 2.783h2.782v1.391h26.435v2.783z\"/>
\t\t</svg>
\t
</div>-->
\t\t<div class=\"header-catalog-tile__title\">комплекты штор</div>
\t</a>

\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t
\t

\t<a class=\"header-catalog-tile\" href=\"https://domlegrand.com/catalog/shtory\" data-catalog-id=\"header-catalog\">
\t\t<div class=\"header-catalog-tile__icon\"><img src=\"https://domlegrand.com/storage/app/uploads/public/617/aa1/74b/617aa174bb8b6791953446.svg\" alt=\"\"></div>
<!--\t\t<div class=\"header-catalog-tile__icon\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 32 32\" fill=\"none\">
\t\t  <path d=\"m26.434 5.565h2.782v23.653h9.74a16.49 16.49 0 00-2.28-8.348h17.08a16.49 16.49 0 00-2.279 8.348h6.957v5.565h-2.783zm5.564 6.957h5.566a16.418 16.418 0 01-5.392 12.521h4.174v6.957h1.39zm-1.39 20.87v20.87h1.64a15.118 15.118 0 012.465 6.956h4.174zm3.603-8.349a18.204 18.204 0 004.744-12.521h2.783v12.521h7.777zm8.918-12.521h2.783a18.204 18.204 0 004.744 12.521h-7.527v6.957zm7.025 20.87a15.12 15.12 0 012.466-6.957h1.64v6.956h23.72zm4.106-8.349h26.26a16.418 16.418 0 01-5.392-12.521h6.957v12.521zm29.217 2.783h2.782v1.391h26.435v2.783z\"/>
\t\t</svg>
\t
</div>-->
\t\t<div class=\"header-catalog-tile__title\">готовые шторы</div>
\t</a>

\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t
\t

\t<a class=\"header-catalog-tile\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna\" data-catalog-id=\"header-catalog\">
\t\t<div class=\"header-catalog-tile__icon\"><img src=\"https://domlegrand.com/storage/app/uploads/public/617/aa2/026/617aa202619df192035586.svg\" alt=\"\"></div>
<!--\t\t<div class=\"header-catalog-tile__icon\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 32 32\" fill=\"none\">
\t\t  <path d=\"m26.434 5.565h2.782v23.653h9.74a16.49 16.49 0 00-2.28-8.348h17.08a16.49 16.49 0 00-2.279 8.348h6.957v5.565h-2.783zm5.564 6.957h5.566a16.418 16.418 0 01-5.392 12.521h4.174v6.957h1.39zm-1.39 20.87v20.87h1.64a15.118 15.118 0 012.465 6.956h4.174zm3.603-8.349a18.204 18.204 0 004.744-12.521h2.783v12.521h7.777zm8.918-12.521h2.783a18.204 18.204 0 004.744 12.521h-7.527v6.957zm7.025 20.87a15.12 15.12 0 012.466-6.957h1.64v6.956h23.72zm4.106-8.349h26.26a16.418 16.418 0 01-5.392-12.521h6.957v12.521zm29.217 2.783h2.782v1.391h26.435v2.783z\"/>
\t\t</svg>
\t
</div>-->
\t\t<div class=\"header-catalog-tile__title\">рулонные шторы</div>
\t</a>

\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t
\t

\t<a class=\"header-catalog-tile\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni\" data-catalog-id=\"header-catalog\">
\t\t<div class=\"header-catalog-tile__icon\"><img src=\"https://domlegrand.com/storage/app/uploads/public/618/93a/782/61893a782d457827290947.svg\" alt=\"\"></div>
<!--\t\t<div class=\"header-catalog-tile__icon\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 32 32\" fill=\"none\">
\t\t  <path d=\"m26.434 5.565h2.782v23.653h9.74a16.49 16.49 0 00-2.28-8.348h17.08a16.49 16.49 0 00-2.279 8.348h6.957v5.565h-2.783zm5.564 6.957h5.566a16.418 16.418 0 01-5.392 12.521h4.174v6.957h1.39zm-1.39 20.87v20.87h1.64a15.118 15.118 0 012.465 6.956h4.174zm3.603-8.349a18.204 18.204 0 004.744-12.521h2.783v12.521h7.777zm8.918-12.521h2.783a18.204 18.204 0 004.744 12.521h-7.527v6.957zm7.025 20.87a15.12 15.12 0 012.466-6.957h1.64v6.956h23.72zm4.106-8.349h26.26a16.418 16.418 0 01-5.392-12.521h6.957v12.521zm29.217 2.783h2.782v1.391h26.435v2.783z\"/>
\t\t</svg>
\t
</div>-->
\t\t<div class=\"header-catalog-tile__title\">текстиль для кухни</div>
\t</a>

\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t
\t

\t<a class=\"header-catalog-tile\" href=\"https://domlegrand.com/catalog/tyul\" data-catalog-id=\"header-catalog\">
\t\t<div class=\"header-catalog-tile__icon\"><img src=\"https://domlegrand.com/storage/app/uploads/public/617/aa2/d01/617aa2d013710891346743.svg\" alt=\"\"></div>
<!--\t\t<div class=\"header-catalog-tile__icon\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 32 32\" fill=\"none\">
\t\t  <path d=\"m26.434 5.565h2.782v23.653h9.74a16.49 16.49 0 00-2.28-8.348h17.08a16.49 16.49 0 00-2.279 8.348h6.957v5.565h-2.783zm5.564 6.957h5.566a16.418 16.418 0 01-5.392 12.521h4.174v6.957h1.39zm-1.39 20.87v20.87h1.64a15.118 15.118 0 012.465 6.956h4.174zm3.603-8.349a18.204 18.204 0 004.744-12.521h2.783v12.521h7.777zm8.918-12.521h2.783a18.204 18.204 0 004.744 12.521h-7.527v6.957zm7.025 20.87a15.12 15.12 0 012.466-6.957h1.64v6.956h23.72zm4.106-8.349h26.26a16.418 16.418 0 01-5.392-12.521h6.957v12.521zm29.217 2.783h2.782v1.391h26.435v2.783z\"/>
\t\t</svg>
\t
</div>-->
\t\t<div class=\"header-catalog-tile__title\">тюль</div>
\t</a>

\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t
\t

\t<a class=\"header-catalog-tile\" href=\"https://domlegrand.com/catalog/karnizy\" data-catalog-id=\"header-catalog\">
\t\t<div class=\"header-catalog-tile__icon\"><img src=\"https://domlegrand.com/storage/app/uploads/public/617/aa4/546/617aa45462707304206647.svg\" alt=\"\"></div>
<!--\t\t<div class=\"header-catalog-tile__icon\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 32 32\" fill=\"none\">
\t\t  <path d=\"m26.434 5.565h2.782v23.653h9.74a16.49 16.49 0 00-2.28-8.348h17.08a16.49 16.49 0 00-2.279 8.348h6.957v5.565h-2.783zm5.564 6.957h5.566a16.418 16.418 0 01-5.392 12.521h4.174v6.957h1.39zm-1.39 20.87v20.87h1.64a15.118 15.118 0 012.465 6.956h4.174zm3.603-8.349a18.204 18.204 0 004.744-12.521h2.783v12.521h7.777zm8.918-12.521h2.783a18.204 18.204 0 004.744 12.521h-7.527v6.957zm7.025 20.87a15.12 15.12 0 012.466-6.957h1.64v6.956h23.72zm4.106-8.349h26.26a16.418 16.418 0 01-5.392-12.521h6.957v12.521zm29.217 2.783h2.782v1.391h26.435v2.783z\"/>
\t\t</svg>
\t
</div>-->
\t\t<div class=\"header-catalog-tile__title\">карнизы</div>
\t</a>

\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t
\t

\t<a class=\"header-catalog-tile\" href=\"https://domlegrand.com/catalog/tekstil\" data-catalog-id=\"header-catalog\">
\t\t<div class=\"header-catalog-tile__icon\"><img src=\"https://domlegrand.com/storage/app/uploads/public/618/93a/97d/61893a97d4c7e401886910.svg\" alt=\"\"></div>
<!--\t\t<div class=\"header-catalog-tile__icon\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 32 32\" fill=\"none\">
\t\t  <path d=\"m26.434 5.565h2.782v23.653h9.74a16.49 16.49 0 00-2.28-8.348h17.08a16.49 16.49 0 00-2.279 8.348h6.957v5.565h-2.783zm5.564 6.957h5.566a16.418 16.418 0 01-5.392 12.521h4.174v6.957h1.39zm-1.39 20.87v20.87h1.64a15.118 15.118 0 012.465 6.956h4.174zm3.603-8.349a18.204 18.204 0 004.744-12.521h2.783v12.521h7.777zm8.918-12.521h2.783a18.204 18.204 0 004.744 12.521h-7.527v6.957zm7.025 20.87a15.12 15.12 0 012.466-6.957h1.64v6.956h23.72zm4.106-8.349h26.26a16.418 16.418 0 01-5.392-12.521h6.957v12.521zm29.217 2.783h2.782v1.391h26.435v2.783z\"/>
\t\t</svg>
\t
</div>-->
\t\t<div class=\"header-catalog-tile__title\">подушки</div>
\t</a>

\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t
\t

\t<a class=\"header-catalog-tile\" href=\"https://domlegrand.com/catalog/komplektuyushchie\" data-catalog-id=\"header-catalog\">
\t\t<div class=\"header-catalog-tile__icon\"><img src=\"https://domlegrand.com/storage/app/uploads/public/618/24f/ac6/61824fac603f3434372501.svg\" alt=\"\"></div>
<!--\t\t<div class=\"header-catalog-tile__icon\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 32 32\" fill=\"none\">
\t\t  <path d=\"m26.434 5.565h2.782v23.653h9.74a16.49 16.49 0 00-2.28-8.348h17.08a16.49 16.49 0 00-2.279 8.348h6.957v5.565h-2.783zm5.564 6.957h5.566a16.418 16.418 0 01-5.392 12.521h4.174v6.957h1.39zm-1.39 20.87v20.87h1.64a15.118 15.118 0 012.465 6.956h4.174zm3.603-8.349a18.204 18.204 0 004.744-12.521h2.783v12.521h7.777zm8.918-12.521h2.783a18.204 18.204 0 004.744 12.521h-7.527v6.957zm7.025 20.87a15.12 15.12 0 012.466-6.957h1.64v6.956h23.72zm4.106-8.349h26.26a16.418 16.418 0 01-5.392-12.521h6.957v12.521zm29.217 2.783h2.782v1.391h26.435v2.783z\"/>
\t\t</svg>
\t
</div>-->
\t\t<div class=\"header-catalog-tile__title\">комплектующие</div>
\t</a>

\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t
\t\t\t\t\t\t<div class=\"swiper-slide carousel__item page-header__catalog-empty-holder\" style=\"width: 12px;\"></div>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile      page-header__catalog-next js-carousel-header-catalog-next\"
\t\t\ttype=\"button\"
\t\t\t
\t\t\t\t\t\t\t\t>
\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m1 14.9629l7.27 7.98089l1 0.99989\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t


\t\t\t\t\t</button>

\t
\t\t\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile      page-header__catalog-prev js-carousel-header-catalog-prev\"
\t\t\ttype=\"button\"
\t\t\t
\t\t\t\t\t\t\t\t>
\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t


\t\t\t\t\t</button>

\t
\t\t\t</div>

\t\t\t\t\t\t\t
\t\t\t\t
\t<section id=\"header-catalog\" class=\"header-catalog      js-header-catalog\">
\t\t<div class=\"header-catalog__content\">

\t\t\t<button class=\"header-catalog__close js-close-header-submenu\">
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t</button>

\t\t\t<div class=\"header-catalog__categories\">

\t\t\t\t<div class=\"header-catalog__nav\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>

<!--\t\t\t\tновинки/распродажа-->
\t\t\t\t<div class=\"header-catalog__nav header-catalog__nav--primary\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/novye-tovary\">новинки</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/super-rasprodazha\">супер распродажа</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>
                \t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             header-catalog__to-all\"
\t\t\thref=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor\"
\t\t\tdata-js=\"\"
\t\t>все комплекты штор</a>
\t
\t

\t\t\t</div>

\t\t\t<div class=\"header-catalog__items\">

\t\t\t\t<div class=\"carousel\">
\t\t\t\t\t<div class=\"swiper-container carousel__body js-carousel-header-item\">
\t\t\t\t\t\t<div class=\"swiper-wrapper carousel__list\">
                            \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"259\" data-category=\"шторы комплекты готовые\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-porter-blekaut\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6ea/c11/622/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"комплекты портьер блэкаут\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"259\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">5 468 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-porter-blekaut\">комплекты портьер блэкаут</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"257\" data-category=\"шторы комплекты готовые\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-porter-barhat-s-podhvatami\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/27f/043/91f/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"комплекты портьер бархат с подхватами\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"257\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">4 920 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-porter-barhat-s-podhvatami\">комплекты портьер бархат с подхватами</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"260\" data-category=\"шторы комплекты готовые\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-porter-melanzh\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/33b/bac/d2b/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"комплекты портьер меланж\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"260\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">5 233 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-porter-melanzh\">комплекты портьер меланж</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"261\" data-category=\"шторы комплекты готовые\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-porter-mramor-soft\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/5ee/39e/053/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"комплекты портьер мрамор софт\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"261\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">5 155 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-porter-mramor-soft\">комплекты портьер мрамор софт</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"273\" data-category=\"шторы комплекты готовые\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekt-shtor-ameli\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/cfc/01f/632/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"комплект штор с тюлем амели\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"273\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">8 450 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekt-shtor-ameli\">комплект штор с тюлем амели</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"274\" data-category=\"шторы комплекты готовые\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekt-shtor-latte\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/275/fd3/eef/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"комплект штор с тюлем латте\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"274\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">7 770 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekt-shtor-latte\">комплект штор с тюлем латте</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\"></div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<button class=\"header-catalog__items-next js-carousel-header-item-next\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m1 14.9629l7.27 7.98089l1 0.99989\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t<button class=\"header-catalog__items-prev js-carousel-header-item-prev\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t</div>

\t\t\t<div class=\"header-catalog__filters\">

\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по помещению</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-shtor-na-kuhnyu\">комплекты для кухни</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-shtor-v-spalnyu\">комплект штор для спальни</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-shtor-dlya-gostinoj\">готовые комплекты штор для гостиной</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-shtor-v-detskuyu\">комплект штор в детскую комнату</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-shtor-v-zal\">готовые комплекты штор для зала</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t
\t\t\t</div>

\t\t</div>
\t</section>


\t\t\t\t\t\t\t
\t\t\t\t
\t<section id=\"header-catalog\" class=\"header-catalog      js-header-catalog\">
\t\t<div class=\"header-catalog__content\">

\t\t\t<button class=\"header-catalog__close js-close-header-submenu\">
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t</button>

\t\t\t<div class=\"header-catalog__categories\">

\t\t\t\t<div class=\"header-catalog__nav\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/shtory/blekaut-shtory\">блэкаут шторы</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/shtory/barhatnye\">бархатные шторы</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/shtory/iz-rogozhki\">шторы из рогожки</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/shtory/odnotonnye-shtory\">однотонные шторы</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/shtory/portery\">портьеры</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>

<!--\t\t\t\tновинки/распродажа-->
\t\t\t\t<div class=\"header-catalog__nav header-catalog__nav--primary\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/shtory/novaya-kollekciya\">новая коллекция</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/shtory/hit-prodazh\">хит продаж</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>
                \t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             header-catalog__to-all\"
\t\t\thref=\"https://domlegrand.com/catalog/shtory\"
\t\t\tdata-js=\"\"
\t\t>все готовые шторы</a>
\t
\t

\t\t\t</div>

\t\t\t<div class=\"header-catalog__items\">

\t\t\t\t<div class=\"carousel\">
\t\t\t\t\t<div class=\"swiper-container carousel__body js-carousel-header-item\">
\t\t\t\t\t\t<div class=\"swiper-wrapper carousel__list\">
                            \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"26\" data-category=\"готовые шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/shtory/shtory-blekaut\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/58a/de0/f3d/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"шторы блэкаут\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"26\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">2 415 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/shtory/shtory-blekaut\">шторы блэкаут</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"29\" data-category=\"готовые шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/shtory/shtory-barhat\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/4f7/f9a/58b/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"шторы бархат\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"29\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">2 401 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/shtory/shtory-barhat\">шторы бархат</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"32\" data-category=\"готовые шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/shtory/shtory-ameliya\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/76d/d39/3ec/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"шторы амелия\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"32\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">2 565 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/shtory/shtory-ameliya\">шторы амелия</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"38\" data-category=\"готовые шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/shtory/shtory-melanzh\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/58d/0c2/9f7/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"шторы меланж\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"38\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">2 212 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/shtory/shtory-melanzh\">шторы меланж</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"39\" data-category=\"готовые шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/shtory/shtory-mramor-soft\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/e26/617/e14/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"шторы мрамор софт\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"39\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 953 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/shtory/shtory-mramor-soft\">шторы мрамор софт</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"48\" data-category=\"готовые шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/shtory/shtory-matritsa\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/481/7b3/5ed/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"шторы матрица\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"48\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 897 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/shtory/shtory-matritsa\">шторы матрица</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\"></div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<button class=\"header-catalog__items-next js-carousel-header-item-next\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m1 14.9629l7.27 7.98089l1 0.99989\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t<button class=\"header-catalog__items-prev js-carousel-header-item-prev\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t</div>

\t\t\t<div class=\"header-catalog__filters\">

\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по виду</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/klassicheskie\">классические шторы</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-sovremennye\">современные шторы</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/fotoshtory\">фотошторы</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-s-podhvatami\">с подхватами</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/korotkie\">короткие</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/nedorogie\">недорогие</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/na-okna\">на окна</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по помещению</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-v-gostinuyu\">шторы в гостиную</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-v-spalnyu\">шторы в спальню</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-v-detskuyu\">в детскую</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-dlya-kuhni\">шторы для кухни</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-dlya-doma\">для дома</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-na-balkon\">на балкон</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/story-v-zal\">шторы для зала</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по дизайну</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-bez-uzora\">без узора</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-geometriya\">геометрия</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-s-uzorom\">шторы с узором</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-abstrakciya\">абстракция</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t
\t\t\t</div>

\t\t</div>
\t</section>


\t\t\t\t\t\t\t
\t\t\t\t
\t<section id=\"header-catalog\" class=\"header-catalog      js-header-catalog\">
\t\t<div class=\"header-catalog__content\">

\t\t\t<button class=\"header-catalog__close js-close-header-submenu\">
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t</button>

\t\t\t<div class=\"header-catalog__categories\">

\t\t\t\t<div class=\"header-catalog__nav\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/den-noch\">рулонные шторы день-ночь</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-blekaut\">рулонные шторы блэкаут</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-s-risunkom\">рулонные шторы с рисунками</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/zhakkard\">жаккардовые рулонные шторы</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/dekorativnye\">декоративные</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/odnotonnye-rulonnye-shtory\">однотонные</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/svetopronicaemye\">светонепроницаемые</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>

<!--\t\t\t\tновинки/распродажа-->
\t\t\t\t<div class=\"header-catalog__nav header-catalog__nav--primary\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/novinki\">новинки</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rasprodazha\">распродажа</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>
                \t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             header-catalog__to-all\"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna\"
\t\t\tdata-js=\"\"
\t\t>все рулонные шторы</a>
\t
\t

\t\t\t</div>

\t\t\t<div class=\"header-catalog__items\">

\t\t\t\t<div class=\"carousel\">
\t\t\t\t\t<div class=\"swiper-container carousel__body js-carousel-header-item\">
\t\t\t\t\t\t<div class=\"swiper-wrapper carousel__list\">
                            \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"270\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-marko\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/2aa/215/a3a/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы марко\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"270\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">765 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-marko\">рулонные шторы марко</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"268\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-favor\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/118/5ce/e7c/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы фавор\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"268\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">960 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-favor\">рулонные шторы фавор</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"272\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-lester\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/ab7/148/b85/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы лестер\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"272\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">585 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-lester\">рулонные шторы лестер</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"271\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-layt\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/cd4/c92/516/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы лайт\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"271\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">530 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-layt\">рулонные шторы лайт</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"57\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-frost\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/be4/ec3/e60/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы фрост\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"57\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">871 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-frost\">рулонные шторы фрост</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"37\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-blackout\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/260/f24/35e/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы блэкаут\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"37\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 045 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-blackout\">рулонные шторы блэкаут</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\"></div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<button class=\"header-catalog__items-next js-carousel-header-item-next\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m1 14.9629l7.27 7.98089l1 0.99989\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t<button class=\"header-catalog__items-prev js-carousel-header-item-prev\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t</div>

\t\t\t<div class=\"header-catalog__filters\">

\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по виду</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/solncezashchitnye\">солнцезащитные</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/na-bolshie-okna\">на большие окна</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-prozrachnye\">прозрачные</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/zebra\">рулонные шторы зебра</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/mini\">рулонные шторы мини</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по помещению</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-dlya-kuhni\">рулонные шторы на кухню</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-dlya-spalni\">рулонные шторы для спальни</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-dlya-gostinoj\">для гостиной</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-v-detskuyu\">в детскую</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-na-balkon\">рулонные шторы на балкон</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-v-zal\">в зал</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-dlya-dachi\">для дачи</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по принципу крепления</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/bez-sverleniya\">без сверления</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/na-plastikovye-okna\">на пластиковые окна</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/s-napravlyayushchimi\">с направляющими</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t
\t\t\t</div>

\t\t</div>
\t</section>


\t\t\t\t\t\t\t
\t\t\t\t
\t<section id=\"header-catalog\" class=\"header-catalog      js-header-catalog\">
\t\t<div class=\"header-catalog__content\">

\t\t\t<button class=\"header-catalog__close js-close-header-submenu\">
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t</button>

\t\t\t<div class=\"header-catalog__categories\">

\t\t\t\t<div class=\"header-catalog__nav\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/shtory-i-tyul-na-kuhnyu\">шторы и тюль на кухню</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/podushki-na-stul\">подушки для стульев на кухню</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/skaterti\">скатерти</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>

<!--\t\t\t\tновинки/распродажа-->
\t\t\t\t<div class=\"header-catalog__nav header-catalog__nav--primary\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/skidka\">скидка</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>
                \t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             header-catalog__to-all\"
\t\t\thref=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni\"
\t\t\tdata-js=\"\"
\t\t>весь текстиль для кухни</a>
\t
\t

\t\t\t</div>

\t\t\t<div class=\"header-catalog__items\">

\t\t\t\t<div class=\"carousel\">
\t\t\t\t\t<div class=\"swiper-container carousel__body js-carousel-header-item\">
\t\t\t\t\t\t<div class=\"swiper-wrapper carousel__list\">
                            \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"145\" data-category=\"текстиль для кухни\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-vual\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/ece/34d/cc7/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль для кухни вуаль\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"145\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">790 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-vual\">тюль для кухни вуаль</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"146\" data-category=\"текстиль для кухни\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-len-belyy-s-zolotom\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/ce9/d72/d00/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль для кухни лен\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"146\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">985 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-len-belyy-s-zolotom\">тюль для кухни лен</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"147\" data-category=\"текстиль для кухни\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-azhur-belyy\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/538/2f3/bd8/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль для кухни ажур\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"147\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 085 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-azhur-belyy\">тюль для кухни ажур</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"148\" data-category=\"текстиль для кухни\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-dolores-belyy-s-zolotom\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/111/6d1/852/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль для кухни долорес\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"148\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">2 415 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-dolores-belyy-s-zolotom\">тюль для кухни долорес</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"149\" data-category=\"текстиль для кухни\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-leya-belyy\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/07b/368/dab/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль для кухни лея\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"149\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">2 390 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-leya-belyy\">тюль для кухни лея</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"150\" data-category=\"текстиль для кухни\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-lira-belyy\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/8b0/344/c13/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль для кухни лира\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"150\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">2 260 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-lira-belyy\">тюль для кухни лира</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\"></div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<button class=\"header-catalog__items-next js-carousel-header-item-next\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m1 14.9629l7.27 7.98089l1 0.99989\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t<button class=\"header-catalog__items-prev js-carousel-header-item-prev\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t</div>

\t\t\t<div class=\"header-catalog__filters\">

\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по виду</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/shtory-na-kuhnyu-v-sovremennom-stile\">шторы на кухню в современном стиле</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/korotkie-shtory-na-kuhnyu\">короткие шторы на кухню</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/shtory-v-interere-kuhni\">шторы в интерьере кухни</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/krasivye-shtory-na-kuhnyu\">красивые шторы на кухню</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/shtory-s-podhvatami-na-kuhnyu\">шторы с подхватами</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t
\t\t\t</div>

\t\t</div>
\t</section>


\t\t\t\t\t\t\t
\t\t\t\t
\t<section id=\"header-catalog\" class=\"header-catalog      js-header-catalog\">
\t\t<div class=\"header-catalog__content\">

\t\t\t<button class=\"header-catalog__close js-close-header-submenu\">
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t</button>

\t\t\t<div class=\"header-catalog__categories\">

\t\t\t\t<div class=\"header-catalog__nav\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tyul/vual\">тюль вуаль</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tyul/setka\">тюль сетка</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tyul/s-vyshevkoj\">тюль с вышивкой</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tyul/kruzhevnoj\">жаккардовая кружевная тюль</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-odnotonnye\">однотонные</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>

<!--\t\t\t\tновинки/распродажа-->
\t\t\t\t<div class=\"header-catalog__nav header-catalog__nav--primary\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tyul/novye-postupleniya\">новые поступления</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-po-akcii\">тюль по акции распродажа</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>
                \t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             header-catalog__to-all\"
\t\t\thref=\"https://domlegrand.com/catalog/tyul\"
\t\t\tdata-js=\"\"
\t\t>все тюли</a>
\t
\t

\t\t\t</div>

\t\t\t<div class=\"header-catalog__items\">

\t\t\t\t<div class=\"carousel\">
\t\t\t\t\t<div class=\"swiper-container carousel__body js-carousel-header-item\">
\t\t\t\t\t\t<div class=\"swiper-wrapper carousel__list\">
                            \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"62\" data-category=\"тюль интернет-магазин\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tyul/tyul-vual\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/2f0/0ba/c97/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль вуаль\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"62\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 090 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tyul/tyul-vual\">тюль вуаль</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"63\" data-category=\"тюль интернет-магазин\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tyul/tyul-vual-shelk\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/742/1ce/73a/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль вуаль шелк\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"63\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 595 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tyul/tyul-vual-shelk\">тюль вуаль шелк</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"64\" data-category=\"тюль интернет-магазин\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tyul/tyul-layn\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/005/bd2/0ee/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль лайн\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"64\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 305 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tyul/tyul-layn\">тюль лайн</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"65\" data-category=\"тюль интернет-магазин\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tyul/tyul-azhur\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/d94/350/f04/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль ажур\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"65\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">2 160 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tyul/tyul-azhur\">тюль ажур</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"67\" data-category=\"тюль интернет-магазин\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tyul/tyul-dozhd\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/f7f/64b/21a/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль дождь\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"67\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 745 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tyul/tyul-dozhd\">тюль дождь</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"68\" data-category=\"тюль интернет-магазин\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tyul/tyul-paola\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6b0/470/c01/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль паола\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"68\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">2 395 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tyul/tyul-paola\">тюль паола</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\"></div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<button class=\"header-catalog__items-next js-carousel-header-item-next\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m1 14.9629l7.27 7.98089l1 0.99989\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t<button class=\"header-catalog__items-prev js-carousel-header-item-prev\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t</div>

\t\t\t<div class=\"header-catalog__filters\">

\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по виду</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-nedorogoj\">недорогой</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-abstrakciya\">современный</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/korotkij\">короткий</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-s-risunkom\">с рисунком</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-na-okna\">тюль для окон</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по помещению</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-dlya-kuhni\">тюль на кухню</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-v-gostinuyu\">тюль в гостиную</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-v-spalnyu\">тюль в спальню</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-v-detskuyu\">тюль для детской комнаты</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-na-balkon\">на балкон</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-dlya-zala\">тюль для зала</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по цвету</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-belyj\">белый</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-molochnyj\">молочный</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-cvetnoj\">цветная тюль с рисунком</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-prozrachnyj\">прозрачный</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t
\t\t\t</div>

\t\t</div>
\t</section>


\t\t\t\t\t\t\t
\t\t\t\t
\t<section id=\"header-catalog\" class=\"header-catalog      js-header-catalog\">
\t\t<div class=\"header-catalog__content\">

\t\t\t<button class=\"header-catalog__close js-close-header-submenu\">
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t</button>

\t\t\t<div class=\"header-catalog__categories\">

\t\t\t\t<div class=\"header-catalog__nav\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/karnizy/bagetnye\">багетные карнизы</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/karnizy/metallicheskie\">карнизы металлические для штор</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/karnizy/plastikovye\">потолочные пластиковые карнизы для штор</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/karnizy/alyuminievye\">алюминиевые потолочные карнизы</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/karnizy/kovanye\">кованые карнизы</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/karnizy/kruglye\">круглые карнизы</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/karnizy/gibkie\">гибкие карнизы для штор</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/karnizy/nakonechniki\">наконечники</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>

<!--\t\t\t\tновинки/распродажа-->
\t\t\t\t<div class=\"header-catalog__nav header-catalog__nav--primary\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/karnizy/populyarnye-tovary\">популярные товары</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>
                \t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             header-catalog__to-all\"
\t\t\thref=\"https://domlegrand.com/catalog/karnizy\"
\t\t\tdata-js=\"\"
\t\t>все карнизы</a>
\t
\t

\t\t\t</div>

\t\t\t<div class=\"header-catalog__items\">

\t\t\t\t<div class=\"carousel\">
\t\t\t\t\t<div class=\"swiper-container carousel__body js-carousel-header-item\">
\t\t\t\t\t\t<div class=\"swiper-wrapper carousel__list\">
                            \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"41\" data-category=\"карнизы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/karnizy/karniz-vivat\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/3e7/2f6/0b5/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"карнизы виват\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"41\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 275 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/karnizy/karniz-vivat\">карнизы виват</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"82\" data-category=\"карнизы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/karnizy/antique\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/a5e/c7b/fde/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"карнизы антик\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"82\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 165 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/karnizy/antique\">карнизы антик</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"83\" data-category=\"карнизы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/karnizy/karniz-edelveys\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/757/7c5/438/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"карнизы эдельвейс\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"83\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 175 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/karnizy/karniz-edelveys\">карнизы эдельвейс</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"93\" data-category=\"карнизы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/karnizy/karnizy-eliza\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/0ac/145/26b/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"карнизы элиза\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"93\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 275 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/karnizy/karnizy-eliza\">карнизы элиза</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"105\" data-category=\"карнизы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/karnizy/karnizy-prima\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/c9c/26f/445/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"карнизы прима\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"105\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 500 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/karnizy/karnizy-prima\">карнизы прима</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"166\" data-category=\"карнизы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/karnizy/karniz-monarkh\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/94e/5a3/cc7/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"карниз монарх\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"166\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">2 005 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/karnizy/karniz-monarkh\">карниз монарх</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\"></div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<button class=\"header-catalog__items-next js-carousel-header-item-next\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m1 14.9629l7.27 7.98089l1 0.99989\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t<button class=\"header-catalog__items-prev js-carousel-header-item-prev\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t</div>

\t\t\t<div class=\"header-catalog__filters\">

\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по виду</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/odnoryadnye\">однорядные</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/dvuhryadnye\">двухрядные</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/trehryadnye\">трехрядные</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/teleskopicheskie\">телескопические</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/erkernye\">карнизы для эркерных окон</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/skrytye\">скрытые карнизы для штор</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по помещению</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/karnizy-v-gostinuyu\">карнизы для гостиной</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/karnizy-v-spalnyu\">в спальню</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/karnizy-v-detskuyu\">карнизы в детскую комнату</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/karnizy-dlya-kuhni\">карнизы на кухню</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/karnizy-dlya-doma\">для дома</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/karnizy-na-balkon\">на балкон</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по принципу крепления</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/potolochnye\">карнизы потолочные для штор</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/nastennye\">настенные карнизы</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/profilnyj\">потолочные профильные карнизы для штор</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t
\t\t\t</div>

\t\t</div>
\t</section>


\t\t\t\t\t\t\t
\t\t\t\t
\t<section id=\"header-catalog\" class=\"header-catalog      js-header-catalog\">
\t\t<div class=\"header-catalog__content\">

\t\t\t<button class=\"header-catalog__close js-close-header-submenu\">
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t</button>

\t\t\t<div class=\"header-catalog__categories\">

\t\t\t\t<div class=\"header-catalog__nav\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tekstil/dekorativnye-podushki\">подушки декоративные</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tekstil/novogodnie-podushki\">новогодние подушки</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>

<!--\t\t\t\tновинки/распродажа-->
\t\t\t\t<div class=\"header-catalog__nav header-catalog__nav--primary\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>
                \t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             header-catalog__to-all\"
\t\t\thref=\"https://domlegrand.com/catalog/tekstil\"
\t\t\tdata-js=\"\"
\t\t>все подушки</a>
\t
\t

\t\t\t</div>

\t\t\t<div class=\"header-catalog__items\">

\t\t\t\t<div class=\"carousel\">
\t\t\t\t\t<div class=\"swiper-container carousel__body js-carousel-header-item\">
\t\t\t\t\t\t<div class=\"swiper-wrapper carousel__list\">
                            \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"192\" data-category=\"подушки\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil/podushki-dekorativnye-barkhat\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/7fb/a45/45d/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"подушки декоративные бархат\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"192\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">581 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil/podushki-dekorativnye-barkhat\">подушки декоративные бархат</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"193\" data-category=\"подушки\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil/podushki-dekorativnye-soft\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6fc/9e8/89d/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"подушки декоративные софт\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"193\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">452 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil/podushki-dekorativnye-soft\">подушки декоративные софт</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"194\" data-category=\"подушки\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil/podushki-dekorativnye-marokko\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/c49/be5/f17/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"подушки декоративные марокко\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"194\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">515 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil/podushki-dekorativnye-marokko\">подушки декоративные марокко</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"138\" data-category=\"текстиль для кухни\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/podushka-na-stul-oliva\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/7c3/88f/45d/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"подушка на стул олива\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"138\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">575 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/podushka-na-stul-oliva\">подушка на стул олива</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"139\" data-category=\"текстиль для кухни\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/podushka-na-stul-rayskiy-sad\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/399/a9f/d78/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"подушка на стул райский сад\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"139\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">575 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/podushka-na-stul-rayskiy-sad\">подушка на стул райский сад</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"140\" data-category=\"текстиль для кухни\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/podushka-na-stul-bona\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/069/f5b/4e9/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"подушка на стул бона\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"140\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">575 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/podushka-na-stul-bona\">подушка на стул бона</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\"></div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<button class=\"header-catalog__items-next js-carousel-header-item-next\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m1 14.9629l7.27 7.98089l1 0.99989\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t<button class=\"header-catalog__items-prev js-carousel-header-item-prev\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t</div>

\t\t\t<div class=\"header-catalog__filters\">

\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по виду</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tekstil/podushki-dlya-divana\">подушки на диван</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tekstil/podushki-na-krovat\">подушки на кровать</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tekstil/krasivye-podushki\">красивые подушки</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tekstil/detskaya-podushka\">детские подушки</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t
\t\t\t</div>

\t\t</div>
\t</section>


\t\t\t\t\t\t\t
\t\t\t\t
\t<section id=\"header-catalog\" class=\"header-catalog      js-header-catalog\">
\t\t<div class=\"header-catalog__content\">

\t\t\t<button class=\"header-catalog__close js-close-header-submenu\">
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t</button>

\t\t\t<div class=\"header-catalog__categories\">

\t\t\t\t<div class=\"header-catalog__nav\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/komplektuyushchie/komplektuyushchie-dlya-rulonnyh-shtor\">комплектующие для рулонных штор</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/komplektuyushchie/komplektuyushchie-dlya-karnizov\">комплектующие для карнизов</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>

<!--\t\t\t\tновинки/распродажа-->
\t\t\t\t<div class=\"header-catalog__nav header-catalog__nav--primary\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/komplektuyushchie/rasprodazha-komplektuyushchih\">распродажа</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>
                \t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             header-catalog__to-all\"
\t\t\thref=\"https://domlegrand.com/catalog/komplektuyushchie\"
\t\t\tdata-js=\"\"
\t\t>все комплектующие</a>
\t
\t

\t\t\t</div>

\t\t\t<div class=\"header-catalog__items\">

\t\t\t\t<div class=\"carousel\">
\t\t\t\t\t<div class=\"swiper-container carousel__body js-carousel-header-item\">
\t\t\t\t\t\t<div class=\"swiper-wrapper carousel__list\">
                            \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"55\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/napravlyayuschie\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/ebf/6d2/806/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"направляющие для рулонных штор\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"55\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">250 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/napravlyayuschie\">направляющие для рулонных штор</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"152\" data-category=\"комплектующие\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/komplektuyushchie/kryuchok-gvozdik\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/30f/7f6/fd9/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"крючок-гвоздик\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"152\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">20 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/komplektuyushchie/kryuchok-gvozdik\">крючок-гвоздик</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"153\" data-category=\"комплектующие\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/komplektuyushchie/kronshteyn-stenovoy\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/4e3/9c9/3bf/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"кронштейн стеновой\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"153\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">130 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/komplektuyushchie/kronshteyn-stenovoy\">кронштейн стеновой</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"154\" data-category=\"комплектующие\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/komplektuyushchie/soedinitel-dlya-profilya-pvkh\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/955/15e/5ae/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"соединитель для профиля пвх\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"154\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">55 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/komplektuyushchie/soedinitel-dlya-profilya-pvkh\">соединитель для профиля пвх</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"155\" data-category=\"комплектующие\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/komplektuyushchie/zaglushka-figurnaya\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/dc6/765/54d/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"заглушка фигурная\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"155\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">40 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/komplektuyushchie/zaglushka-figurnaya\">заглушка фигурная</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"156\" data-category=\"комплектующие\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/komplektuyushchie/zaglushka-pryamaya\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/180/31c/0a8/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"заглушка прямая\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"156\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">30 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/komplektuyushchie/zaglushka-pryamaya\">заглушка прямая</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\"></div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<button class=\"header-catalog__items-next js-carousel-header-item-next\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m1 14.9629l7.27 7.98089l1 0.99989\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t<button class=\"header-catalog__items-prev js-carousel-header-item-prev\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t</div>

\t\t\t<div class=\"header-catalog__filters\">

\t\t\t\t
\t\t\t</div>

\t\t</div>
\t</section>


\t\t\t
\t\t</div>
\t\t
\t\t
\t\t\t\t\t


\t<div class=\"header-menu js-header-menu\">
\t\t<div class=\"header-menu__blur js-toggle-header-menu\"></div>

\t\t<div class=\"header-menu__content\">\t\t\t
\t\t\t<button class=\"header-menu__close js-toggle-header-menu\">
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t</button>

\t\t\t<nav class=\"header-menu__nav\">
\t\t\t\t<ul class=\"header-menu__nav-list\">
\t\t\t\t\t<li class=\"header-menu__nav-item\">
\t\t\t\t\t\t<a class=\"header-menu__nav-link\" href=\"https://domlegrand.com/catalog\">
\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">каталог</span>
\t\t\t\t\t\t\t<button class=\"header-menu__nav-btn js-toggle-header-submenu\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m1 14.9629l7.27 7.98089l1 0.99989\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t\t\t</a>

\t\t\t\t\t\t<div class=\"header-menu__submenu js-header-submenu\">
\t\t\t\t\t\t\t<div class=\"header-menu__submenu-head\">
\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link\" href=\"https://domlegrand.com/catalog\">
\t\t\t\t\t\t\t\t\t<button class=\"header-menu__nav-btn js-toggle-header-submenu\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">каталог</span>
\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"header-menu__submenu-body\">
\t\t\t\t\t\t\t\t<ul class=\"header-menu__catalog\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__catalog-item\">

\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__catalog-link\">
\t\t\t\t\t\t\t\t\t\t\t\t
\t

\t<a class=\"header-catalog-tile\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor\" data-catalog-id=\"header-catalog\">
\t\t<div class=\"header-catalog-tile__icon\"><img src=\"https://domlegrand.com/storage/app/uploads/public/618/24f/55a/61824f55aeb0c155156499.svg\" alt=\"\"></div>
<!--\t\t<div class=\"header-catalog-tile__icon\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 32 32\" fill=\"none\">
\t\t  <path d=\"m26.434 5.565h2.782v23.653h9.74a16.49 16.49 0 00-2.28-8.348h17.08a16.49 16.49 0 00-2.279 8.348h6.957v5.565h-2.783zm5.564 6.957h5.566a16.418 16.418 0 01-5.392 12.521h4.174v6.957h1.39zm-1.39 20.87v20.87h1.64a15.118 15.118 0 012.465 6.956h4.174zm3.603-8.349a18.204 18.204 0 004.744-12.521h2.783v12.521h7.777zm8.918-12.521h2.783a18.204 18.204 0 004.744 12.521h-7.527v6.957zm7.025 20.87a15.12 15.12 0 012.466-6.957h1.64v6.956h23.72zm4.106-8.349h26.26a16.418 16.418 0 01-5.392-12.521h6.957v12.521zm29.217 2.783h2.782v1.391h26.435v2.783z\"/>
\t\t</svg>
\t
</div>-->
\t\t<div class=\"header-catalog-tile__title\">комплекты штор</div>
\t</a>

\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__catalog-open-detail js-toggle-header-submenu\"></div>
\t\t\t\t\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu header-menu__catalog-detail js-header-submenu\">
\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu-head\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link\" href=\"#\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<button class=\"header-menu__nav-btn js-toggle-header-submenu\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">шторы комплекты готовые</span>
\t\t\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu-body\">
\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t
\t<section id=\"\" class=\"header-catalog                         header-catalog--mobile
             js-header-catalog\">
\t\t<div class=\"header-catalog__content\">

\t\t\t<button class=\"header-catalog__close js-close-header-submenu\">
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t</button>

\t\t\t<div class=\"header-catalog__categories\">

\t\t\t\t<div class=\"header-catalog__nav\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>

<!--\t\t\t\tновинки/распродажа-->
\t\t\t\t<div class=\"header-catalog__nav header-catalog__nav--primary\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/novye-tovary\">новинки</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/super-rasprodazha\">супер распродажа</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>
                \t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             header-catalog__to-all\"
\t\t\thref=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor\"
\t\t\tdata-js=\"\"
\t\t>все комплекты штор</a>
\t
\t

\t\t\t</div>

\t\t\t<div class=\"header-catalog__items\">

\t\t\t\t<div class=\"carousel\">
\t\t\t\t\t<div class=\"swiper-container carousel__body js-carousel-header-item\">
\t\t\t\t\t\t<div class=\"swiper-wrapper carousel__list\">
                            \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"259\" data-category=\"шторы комплекты готовые\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-porter-blekaut\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6ea/c11/622/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"комплекты портьер блэкаут\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"259\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">5 468 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-porter-blekaut\">комплекты портьер блэкаут</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"257\" data-category=\"шторы комплекты готовые\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-porter-barhat-s-podhvatami\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/27f/043/91f/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"комплекты портьер бархат с подхватами\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"257\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">4 920 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-porter-barhat-s-podhvatami\">комплекты портьер бархат с подхватами</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"260\" data-category=\"шторы комплекты готовые\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-porter-melanzh\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/33b/bac/d2b/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"комплекты портьер меланж\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"260\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">5 233 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-porter-melanzh\">комплекты портьер меланж</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"261\" data-category=\"шторы комплекты готовые\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-porter-mramor-soft\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/5ee/39e/053/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"комплекты портьер мрамор софт\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"261\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">5 155 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-porter-mramor-soft\">комплекты портьер мрамор софт</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"273\" data-category=\"шторы комплекты готовые\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekt-shtor-ameli\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/cfc/01f/632/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"комплект штор с тюлем амели\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"273\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">8 450 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekt-shtor-ameli\">комплект штор с тюлем амели</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"274\" data-category=\"шторы комплекты готовые\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekt-shtor-latte\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/275/fd3/eef/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"комплект штор с тюлем латте\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"274\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">7 770 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekt-shtor-latte\">комплект штор с тюлем латте</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\"></div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<button class=\"header-catalog__items-next js-carousel-header-item-next\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m1 14.9629l7.27 7.98089l1 0.99989\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t<button class=\"header-catalog__items-prev js-carousel-header-item-prev\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t</div>

\t\t\t<div class=\"header-catalog__filters\">

\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по помещению</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-shtor-na-kuhnyu\">комплекты для кухни</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-shtor-v-spalnyu\">комплект штор для спальни</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-shtor-dlya-gostinoj\">готовые комплекты штор для гостиной</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-shtor-v-detskuyu\">комплект штор в детскую комнату</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor/komplekty-shtor-v-zal\">готовые комплекты штор для зала</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t
\t\t\t</div>

\t\t</div>
\t</section>


\t\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__catalog-item\">

\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__catalog-link\">
\t\t\t\t\t\t\t\t\t\t\t\t
\t

\t<a class=\"header-catalog-tile\" href=\"https://domlegrand.com/catalog/shtory\" data-catalog-id=\"header-catalog\">
\t\t<div class=\"header-catalog-tile__icon\"><img src=\"https://domlegrand.com/storage/app/uploads/public/617/aa1/74b/617aa174bb8b6791953446.svg\" alt=\"\"></div>
<!--\t\t<div class=\"header-catalog-tile__icon\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 32 32\" fill=\"none\">
\t\t  <path d=\"m26.434 5.565h2.782v23.653h9.74a16.49 16.49 0 00-2.28-8.348h17.08a16.49 16.49 0 00-2.279 8.348h6.957v5.565h-2.783zm5.564 6.957h5.566a16.418 16.418 0 01-5.392 12.521h4.174v6.957h1.39zm-1.39 20.87v20.87h1.64a15.118 15.118 0 012.465 6.956h4.174zm3.603-8.349a18.204 18.204 0 004.744-12.521h2.783v12.521h7.777zm8.918-12.521h2.783a18.204 18.204 0 004.744 12.521h-7.527v6.957zm7.025 20.87a15.12 15.12 0 012.466-6.957h1.64v6.956h23.72zm4.106-8.349h26.26a16.418 16.418 0 01-5.392-12.521h6.957v12.521zm29.217 2.783h2.782v1.391h26.435v2.783z\"/>
\t\t</svg>
\t
</div>-->
\t\t<div class=\"header-catalog-tile__title\">готовые шторы</div>
\t</a>

\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__catalog-open-detail js-toggle-header-submenu\"></div>
\t\t\t\t\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu header-menu__catalog-detail js-header-submenu\">
\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu-head\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link\" href=\"#\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<button class=\"header-menu__nav-btn js-toggle-header-submenu\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">готовые шторы</span>
\t\t\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu-body\">
\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t
\t<section id=\"\" class=\"header-catalog                         header-catalog--mobile
             js-header-catalog\">
\t\t<div class=\"header-catalog__content\">

\t\t\t<button class=\"header-catalog__close js-close-header-submenu\">
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t</button>

\t\t\t<div class=\"header-catalog__categories\">

\t\t\t\t<div class=\"header-catalog__nav\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/shtory/blekaut-shtory\">блэкаут шторы</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/shtory/barhatnye\">бархатные шторы</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/shtory/iz-rogozhki\">шторы из рогожки</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/shtory/odnotonnye-shtory\">однотонные шторы</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/shtory/portery\">портьеры</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>

<!--\t\t\t\tновинки/распродажа-->
\t\t\t\t<div class=\"header-catalog__nav header-catalog__nav--primary\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/shtory/novaya-kollekciya\">новая коллекция</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/shtory/hit-prodazh\">хит продаж</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>
                \t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             header-catalog__to-all\"
\t\t\thref=\"https://domlegrand.com/catalog/shtory\"
\t\t\tdata-js=\"\"
\t\t>все готовые шторы</a>
\t
\t

\t\t\t</div>

\t\t\t<div class=\"header-catalog__items\">

\t\t\t\t<div class=\"carousel\">
\t\t\t\t\t<div class=\"swiper-container carousel__body js-carousel-header-item\">
\t\t\t\t\t\t<div class=\"swiper-wrapper carousel__list\">
                            \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"26\" data-category=\"готовые шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/shtory/shtory-blekaut\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/58a/de0/f3d/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"шторы блэкаут\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"26\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">2 415 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/shtory/shtory-blekaut\">шторы блэкаут</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"29\" data-category=\"готовые шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/shtory/shtory-barhat\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/4f7/f9a/58b/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"шторы бархат\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"29\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">2 401 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/shtory/shtory-barhat\">шторы бархат</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"32\" data-category=\"готовые шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/shtory/shtory-ameliya\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/76d/d39/3ec/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"шторы амелия\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"32\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">2 565 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/shtory/shtory-ameliya\">шторы амелия</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"38\" data-category=\"готовые шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/shtory/shtory-melanzh\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/58d/0c2/9f7/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"шторы меланж\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"38\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">2 212 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/shtory/shtory-melanzh\">шторы меланж</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"39\" data-category=\"готовые шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/shtory/shtory-mramor-soft\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/e26/617/e14/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"шторы мрамор софт\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"39\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 953 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/shtory/shtory-mramor-soft\">шторы мрамор софт</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"48\" data-category=\"готовые шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/shtory/shtory-matritsa\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/481/7b3/5ed/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"шторы матрица\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"48\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 897 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/shtory/shtory-matritsa\">шторы матрица</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\"></div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<button class=\"header-catalog__items-next js-carousel-header-item-next\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m1 14.9629l7.27 7.98089l1 0.99989\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t<button class=\"header-catalog__items-prev js-carousel-header-item-prev\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t</div>

\t\t\t<div class=\"header-catalog__filters\">

\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по виду</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/klassicheskie\">классические шторы</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-sovremennye\">современные шторы</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/fotoshtory\">фотошторы</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-s-podhvatami\">с подхватами</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/korotkie\">короткие</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/nedorogie\">недорогие</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/na-okna\">на окна</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по помещению</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-v-gostinuyu\">шторы в гостиную</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-v-spalnyu\">шторы в спальню</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-v-detskuyu\">в детскую</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-dlya-kuhni\">шторы для кухни</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-dlya-doma\">для дома</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-na-balkon\">на балкон</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/story-v-zal\">шторы для зала</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по дизайну</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-bez-uzora\">без узора</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-geometriya\">геометрия</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-s-uzorom\">шторы с узором</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/shtory/shtory-abstrakciya\">абстракция</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t
\t\t\t</div>

\t\t</div>
\t</section>


\t\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__catalog-item\">

\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__catalog-link\">
\t\t\t\t\t\t\t\t\t\t\t\t
\t

\t<a class=\"header-catalog-tile\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna\" data-catalog-id=\"header-catalog\">
\t\t<div class=\"header-catalog-tile__icon\"><img src=\"https://domlegrand.com/storage/app/uploads/public/617/aa2/026/617aa202619df192035586.svg\" alt=\"\"></div>
<!--\t\t<div class=\"header-catalog-tile__icon\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 32 32\" fill=\"none\">
\t\t  <path d=\"m26.434 5.565h2.782v23.653h9.74a16.49 16.49 0 00-2.28-8.348h17.08a16.49 16.49 0 00-2.279 8.348h6.957v5.565h-2.783zm5.564 6.957h5.566a16.418 16.418 0 01-5.392 12.521h4.174v6.957h1.39zm-1.39 20.87v20.87h1.64a15.118 15.118 0 012.465 6.956h4.174zm3.603-8.349a18.204 18.204 0 004.744-12.521h2.783v12.521h7.777zm8.918-12.521h2.783a18.204 18.204 0 004.744 12.521h-7.527v6.957zm7.025 20.87a15.12 15.12 0 012.466-6.957h1.64v6.956h23.72zm4.106-8.349h26.26a16.418 16.418 0 01-5.392-12.521h6.957v12.521zm29.217 2.783h2.782v1.391h26.435v2.783z\"/>
\t\t</svg>
\t
</div>-->
\t\t<div class=\"header-catalog-tile__title\">рулонные шторы</div>
\t</a>

\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__catalog-open-detail js-toggle-header-submenu\"></div>
\t\t\t\t\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu header-menu__catalog-detail js-header-submenu\">
\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu-head\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link\" href=\"#\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<button class=\"header-menu__nav-btn js-toggle-header-submenu\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">рулонные шторы</span>
\t\t\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu-body\">
\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t
\t<section id=\"\" class=\"header-catalog                         header-catalog--mobile
             js-header-catalog\">
\t\t<div class=\"header-catalog__content\">

\t\t\t<button class=\"header-catalog__close js-close-header-submenu\">
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t</button>

\t\t\t<div class=\"header-catalog__categories\">

\t\t\t\t<div class=\"header-catalog__nav\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/den-noch\">рулонные шторы день-ночь</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-blekaut\">рулонные шторы блэкаут</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-s-risunkom\">рулонные шторы с рисунками</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/zhakkard\">жаккардовые рулонные шторы</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/dekorativnye\">декоративные</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/odnotonnye-rulonnye-shtory\">однотонные</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/svetopronicaemye\">светонепроницаемые</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>

<!--\t\t\t\tновинки/распродажа-->
\t\t\t\t<div class=\"header-catalog__nav header-catalog__nav--primary\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/novinki\">новинки</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rasprodazha\">распродажа</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>
                \t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             header-catalog__to-all\"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna\"
\t\t\tdata-js=\"\"
\t\t>все рулонные шторы</a>
\t
\t

\t\t\t</div>

\t\t\t<div class=\"header-catalog__items\">

\t\t\t\t<div class=\"carousel\">
\t\t\t\t\t<div class=\"swiper-container carousel__body js-carousel-header-item\">
\t\t\t\t\t\t<div class=\"swiper-wrapper carousel__list\">
                            \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"270\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-marko\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/2aa/215/a3a/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы марко\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"270\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">765 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-marko\">рулонные шторы марко</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"268\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-favor\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/118/5ce/e7c/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы фавор\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"268\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">960 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-favor\">рулонные шторы фавор</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"272\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-lester\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/ab7/148/b85/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы лестер\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"272\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">585 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-lester\">рулонные шторы лестер</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"271\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-layt\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/cd4/c92/516/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы лайт\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"271\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">530 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-layt\">рулонные шторы лайт</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"57\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-frost\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/be4/ec3/e60/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы фрост\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"57\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">871 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-frost\">рулонные шторы фрост</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"37\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-blackout\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/260/f24/35e/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы блэкаут\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"37\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 045 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-blackout\">рулонные шторы блэкаут</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\"></div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<button class=\"header-catalog__items-next js-carousel-header-item-next\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m1 14.9629l7.27 7.98089l1 0.99989\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t<button class=\"header-catalog__items-prev js-carousel-header-item-prev\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t</div>

\t\t\t<div class=\"header-catalog__filters\">

\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по виду</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/solncezashchitnye\">солнцезащитные</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/na-bolshie-okna\">на большие окна</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-prozrachnye\">прозрачные</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/zebra\">рулонные шторы зебра</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/mini\">рулонные шторы мини</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по помещению</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-dlya-kuhni\">рулонные шторы на кухню</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-dlya-spalni\">рулонные шторы для спальни</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-dlya-gostinoj\">для гостиной</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-v-detskuyu\">в детскую</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-na-balkon\">рулонные шторы на балкон</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-v-zal\">в зал</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-dlya-dachi\">для дачи</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по принципу крепления</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/bez-sverleniya\">без сверления</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/na-plastikovye-okna\">на пластиковые окна</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/s-napravlyayushchimi\">с направляющими</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t
\t\t\t</div>

\t\t</div>
\t</section>


\t\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__catalog-item\">

\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__catalog-link\">
\t\t\t\t\t\t\t\t\t\t\t\t
\t

\t<a class=\"header-catalog-tile\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni\" data-catalog-id=\"header-catalog\">
\t\t<div class=\"header-catalog-tile__icon\"><img src=\"https://domlegrand.com/storage/app/uploads/public/618/93a/782/61893a782d457827290947.svg\" alt=\"\"></div>
<!--\t\t<div class=\"header-catalog-tile__icon\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 32 32\" fill=\"none\">
\t\t  <path d=\"m26.434 5.565h2.782v23.653h9.74a16.49 16.49 0 00-2.28-8.348h17.08a16.49 16.49 0 00-2.279 8.348h6.957v5.565h-2.783zm5.564 6.957h5.566a16.418 16.418 0 01-5.392 12.521h4.174v6.957h1.39zm-1.39 20.87v20.87h1.64a15.118 15.118 0 012.465 6.956h4.174zm3.603-8.349a18.204 18.204 0 004.744-12.521h2.783v12.521h7.777zm8.918-12.521h2.783a18.204 18.204 0 004.744 12.521h-7.527v6.957zm7.025 20.87a15.12 15.12 0 012.466-6.957h1.64v6.956h23.72zm4.106-8.349h26.26a16.418 16.418 0 01-5.392-12.521h6.957v12.521zm29.217 2.783h2.782v1.391h26.435v2.783z\"/>
\t\t</svg>
\t
</div>-->
\t\t<div class=\"header-catalog-tile__title\">текстиль для кухни</div>
\t</a>

\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__catalog-open-detail js-toggle-header-submenu\"></div>
\t\t\t\t\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu header-menu__catalog-detail js-header-submenu\">
\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu-head\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link\" href=\"#\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<button class=\"header-menu__nav-btn js-toggle-header-submenu\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">текстиль для кухни</span>
\t\t\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu-body\">
\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t
\t<section id=\"\" class=\"header-catalog                         header-catalog--mobile
             js-header-catalog\">
\t\t<div class=\"header-catalog__content\">

\t\t\t<button class=\"header-catalog__close js-close-header-submenu\">
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t</button>

\t\t\t<div class=\"header-catalog__categories\">

\t\t\t\t<div class=\"header-catalog__nav\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/shtory-i-tyul-na-kuhnyu\">шторы и тюль на кухню</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/podushki-na-stul\">подушки для стульев на кухню</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/skaterti\">скатерти</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>

<!--\t\t\t\tновинки/распродажа-->
\t\t\t\t<div class=\"header-catalog__nav header-catalog__nav--primary\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/skidka\">скидка</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>
                \t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             header-catalog__to-all\"
\t\t\thref=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni\"
\t\t\tdata-js=\"\"
\t\t>весь текстиль для кухни</a>
\t
\t

\t\t\t</div>

\t\t\t<div class=\"header-catalog__items\">

\t\t\t\t<div class=\"carousel\">
\t\t\t\t\t<div class=\"swiper-container carousel__body js-carousel-header-item\">
\t\t\t\t\t\t<div class=\"swiper-wrapper carousel__list\">
                            \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"145\" data-category=\"текстиль для кухни\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-vual\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/ece/34d/cc7/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль для кухни вуаль\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"145\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">790 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-vual\">тюль для кухни вуаль</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"146\" data-category=\"текстиль для кухни\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-len-belyy-s-zolotom\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/ce9/d72/d00/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль для кухни лен\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"146\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">985 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-len-belyy-s-zolotom\">тюль для кухни лен</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"147\" data-category=\"текстиль для кухни\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-azhur-belyy\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/538/2f3/bd8/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль для кухни ажур\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"147\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 085 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-azhur-belyy\">тюль для кухни ажур</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"148\" data-category=\"текстиль для кухни\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-dolores-belyy-s-zolotom\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/111/6d1/852/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль для кухни долорес\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"148\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">2 415 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-dolores-belyy-s-zolotom\">тюль для кухни долорес</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"149\" data-category=\"текстиль для кухни\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-leya-belyy\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/07b/368/dab/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль для кухни лея\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"149\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">2 390 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-leya-belyy\">тюль для кухни лея</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"150\" data-category=\"текстиль для кухни\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-lira-belyy\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/8b0/344/c13/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль для кухни лира\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"150\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">2 260 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/tyul-dlya-kukhni-lira-belyy\">тюль для кухни лира</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\"></div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<button class=\"header-catalog__items-next js-carousel-header-item-next\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m1 14.9629l7.27 7.98089l1 0.99989\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t<button class=\"header-catalog__items-prev js-carousel-header-item-prev\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t</div>

\t\t\t<div class=\"header-catalog__filters\">

\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по виду</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/shtory-na-kuhnyu-v-sovremennom-stile\">шторы на кухню в современном стиле</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/korotkie-shtory-na-kuhnyu\">короткие шторы на кухню</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/shtory-v-interere-kuhni\">шторы в интерьере кухни</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/krasivye-shtory-na-kuhnyu\">красивые шторы на кухню</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/shtory-s-podhvatami-na-kuhnyu\">шторы с подхватами</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t
\t\t\t</div>

\t\t</div>
\t</section>


\t\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__catalog-item\">

\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__catalog-link\">
\t\t\t\t\t\t\t\t\t\t\t\t
\t

\t<a class=\"header-catalog-tile\" href=\"https://domlegrand.com/catalog/tyul\" data-catalog-id=\"header-catalog\">
\t\t<div class=\"header-catalog-tile__icon\"><img src=\"https://domlegrand.com/storage/app/uploads/public/617/aa2/d01/617aa2d013710891346743.svg\" alt=\"\"></div>
<!--\t\t<div class=\"header-catalog-tile__icon\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 32 32\" fill=\"none\">
\t\t  <path d=\"m26.434 5.565h2.782v23.653h9.74a16.49 16.49 0 00-2.28-8.348h17.08a16.49 16.49 0 00-2.279 8.348h6.957v5.565h-2.783zm5.564 6.957h5.566a16.418 16.418 0 01-5.392 12.521h4.174v6.957h1.39zm-1.39 20.87v20.87h1.64a15.118 15.118 0 012.465 6.956h4.174zm3.603-8.349a18.204 18.204 0 004.744-12.521h2.783v12.521h7.777zm8.918-12.521h2.783a18.204 18.204 0 004.744 12.521h-7.527v6.957zm7.025 20.87a15.12 15.12 0 012.466-6.957h1.64v6.956h23.72zm4.106-8.349h26.26a16.418 16.418 0 01-5.392-12.521h6.957v12.521zm29.217 2.783h2.782v1.391h26.435v2.783z\"/>
\t\t</svg>
\t
</div>-->
\t\t<div class=\"header-catalog-tile__title\">тюль</div>
\t</a>

\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__catalog-open-detail js-toggle-header-submenu\"></div>
\t\t\t\t\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu header-menu__catalog-detail js-header-submenu\">
\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu-head\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link\" href=\"#\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<button class=\"header-menu__nav-btn js-toggle-header-submenu\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">тюль интернет-магазин</span>
\t\t\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu-body\">
\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t
\t<section id=\"\" class=\"header-catalog                         header-catalog--mobile
             js-header-catalog\">
\t\t<div class=\"header-catalog__content\">

\t\t\t<button class=\"header-catalog__close js-close-header-submenu\">
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t</button>

\t\t\t<div class=\"header-catalog__categories\">

\t\t\t\t<div class=\"header-catalog__nav\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tyul/vual\">тюль вуаль</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tyul/setka\">тюль сетка</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tyul/s-vyshevkoj\">тюль с вышивкой</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tyul/kruzhevnoj\">жаккардовая кружевная тюль</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-odnotonnye\">однотонные</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>

<!--\t\t\t\tновинки/распродажа-->
\t\t\t\t<div class=\"header-catalog__nav header-catalog__nav--primary\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tyul/novye-postupleniya\">новые поступления</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-po-akcii\">тюль по акции распродажа</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>
                \t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             header-catalog__to-all\"
\t\t\thref=\"https://domlegrand.com/catalog/tyul\"
\t\t\tdata-js=\"\"
\t\t>все тюли</a>
\t
\t

\t\t\t</div>

\t\t\t<div class=\"header-catalog__items\">

\t\t\t\t<div class=\"carousel\">
\t\t\t\t\t<div class=\"swiper-container carousel__body js-carousel-header-item\">
\t\t\t\t\t\t<div class=\"swiper-wrapper carousel__list\">
                            \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"62\" data-category=\"тюль интернет-магазин\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tyul/tyul-vual\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/2f0/0ba/c97/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль вуаль\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"62\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 090 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tyul/tyul-vual\">тюль вуаль</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"63\" data-category=\"тюль интернет-магазин\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tyul/tyul-vual-shelk\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/742/1ce/73a/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль вуаль шелк\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"63\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 595 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tyul/tyul-vual-shelk\">тюль вуаль шелк</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"64\" data-category=\"тюль интернет-магазин\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tyul/tyul-layn\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/005/bd2/0ee/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль лайн\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"64\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 305 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tyul/tyul-layn\">тюль лайн</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"65\" data-category=\"тюль интернет-магазин\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tyul/tyul-azhur\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/d94/350/f04/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль ажур\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"65\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">2 160 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tyul/tyul-azhur\">тюль ажур</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"67\" data-category=\"тюль интернет-магазин\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tyul/tyul-dozhd\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/f7f/64b/21a/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль дождь\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"67\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 745 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tyul/tyul-dozhd\">тюль дождь</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"68\" data-category=\"тюль интернет-магазин\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tyul/tyul-paola\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6b0/470/c01/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"тюль паола\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"68\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">2 395 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tyul/tyul-paola\">тюль паола</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\"></div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<button class=\"header-catalog__items-next js-carousel-header-item-next\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m1 14.9629l7.27 7.98089l1 0.99989\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t<button class=\"header-catalog__items-prev js-carousel-header-item-prev\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t</div>

\t\t\t<div class=\"header-catalog__filters\">

\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по виду</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-nedorogoj\">недорогой</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-abstrakciya\">современный</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/korotkij\">короткий</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-s-risunkom\">с рисунком</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-na-okna\">тюль для окон</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по помещению</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-dlya-kuhni\">тюль на кухню</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-v-gostinuyu\">тюль в гостиную</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-v-spalnyu\">тюль в спальню</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-v-detskuyu\">тюль для детской комнаты</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-na-balkon\">на балкон</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-dlya-zala\">тюль для зала</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по цвету</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-belyj\">белый</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-molochnyj\">молочный</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-cvetnoj\">цветная тюль с рисунком</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tyul/tyul-prozrachnyj\">прозрачный</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t
\t\t\t</div>

\t\t</div>
\t</section>


\t\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__catalog-item\">

\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__catalog-link\">
\t\t\t\t\t\t\t\t\t\t\t\t
\t

\t<a class=\"header-catalog-tile\" href=\"https://domlegrand.com/catalog/karnizy\" data-catalog-id=\"header-catalog\">
\t\t<div class=\"header-catalog-tile__icon\"><img src=\"https://domlegrand.com/storage/app/uploads/public/617/aa4/546/617aa45462707304206647.svg\" alt=\"\"></div>
<!--\t\t<div class=\"header-catalog-tile__icon\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 32 32\" fill=\"none\">
\t\t  <path d=\"m26.434 5.565h2.782v23.653h9.74a16.49 16.49 0 00-2.28-8.348h17.08a16.49 16.49 0 00-2.279 8.348h6.957v5.565h-2.783zm5.564 6.957h5.566a16.418 16.418 0 01-5.392 12.521h4.174v6.957h1.39zm-1.39 20.87v20.87h1.64a15.118 15.118 0 012.465 6.956h4.174zm3.603-8.349a18.204 18.204 0 004.744-12.521h2.783v12.521h7.777zm8.918-12.521h2.783a18.204 18.204 0 004.744 12.521h-7.527v6.957zm7.025 20.87a15.12 15.12 0 012.466-6.957h1.64v6.956h23.72zm4.106-8.349h26.26a16.418 16.418 0 01-5.392-12.521h6.957v12.521zm29.217 2.783h2.782v1.391h26.435v2.783z\"/>
\t\t</svg>
\t
</div>-->
\t\t<div class=\"header-catalog-tile__title\">карнизы</div>
\t</a>

\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__catalog-open-detail js-toggle-header-submenu\"></div>
\t\t\t\t\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu header-menu__catalog-detail js-header-submenu\">
\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu-head\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link\" href=\"#\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<button class=\"header-menu__nav-btn js-toggle-header-submenu\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">карнизы</span>
\t\t\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu-body\">
\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t
\t<section id=\"\" class=\"header-catalog                         header-catalog--mobile
             js-header-catalog\">
\t\t<div class=\"header-catalog__content\">

\t\t\t<button class=\"header-catalog__close js-close-header-submenu\">
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t</button>

\t\t\t<div class=\"header-catalog__categories\">

\t\t\t\t<div class=\"header-catalog__nav\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/karnizy/bagetnye\">багетные карнизы</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/karnizy/metallicheskie\">карнизы металлические для штор</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/karnizy/plastikovye\">потолочные пластиковые карнизы для штор</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/karnizy/alyuminievye\">алюминиевые потолочные карнизы</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/karnizy/kovanye\">кованые карнизы</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/karnizy/kruglye\">круглые карнизы</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/karnizy/gibkie\">гибкие карнизы для штор</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/karnizy/nakonechniki\">наконечники</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>

<!--\t\t\t\tновинки/распродажа-->
\t\t\t\t<div class=\"header-catalog__nav header-catalog__nav--primary\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/karnizy/populyarnye-tovary\">популярные товары</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>
                \t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             header-catalog__to-all\"
\t\t\thref=\"https://domlegrand.com/catalog/karnizy\"
\t\t\tdata-js=\"\"
\t\t>все карнизы</a>
\t
\t

\t\t\t</div>

\t\t\t<div class=\"header-catalog__items\">

\t\t\t\t<div class=\"carousel\">
\t\t\t\t\t<div class=\"swiper-container carousel__body js-carousel-header-item\">
\t\t\t\t\t\t<div class=\"swiper-wrapper carousel__list\">
                            \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"41\" data-category=\"карнизы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/karnizy/karniz-vivat\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/3e7/2f6/0b5/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"карнизы виват\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"41\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 275 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/karnizy/karniz-vivat\">карнизы виват</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"82\" data-category=\"карнизы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/karnizy/antique\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/a5e/c7b/fde/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"карнизы антик\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"82\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 165 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/karnizy/antique\">карнизы антик</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"83\" data-category=\"карнизы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/karnizy/karniz-edelveys\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/757/7c5/438/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"карнизы эдельвейс\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"83\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 175 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/karnizy/karniz-edelveys\">карнизы эдельвейс</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"93\" data-category=\"карнизы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/karnizy/karnizy-eliza\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/0ac/145/26b/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"карнизы элиза\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"93\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 275 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/karnizy/karnizy-eliza\">карнизы элиза</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"105\" data-category=\"карнизы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/karnizy/karnizy-prima\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/c9c/26f/445/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"карнизы прима\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"105\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 500 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/karnizy/karnizy-prima\">карнизы прима</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"166\" data-category=\"карнизы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/karnizy/karniz-monarkh\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/94e/5a3/cc7/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"карниз монарх\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"166\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">2 005 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/karnizy/karniz-monarkh\">карниз монарх</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\"></div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<button class=\"header-catalog__items-next js-carousel-header-item-next\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m1 14.9629l7.27 7.98089l1 0.99989\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t<button class=\"header-catalog__items-prev js-carousel-header-item-prev\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t</div>

\t\t\t<div class=\"header-catalog__filters\">

\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по виду</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/odnoryadnye\">однорядные</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/dvuhryadnye\">двухрядные</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/trehryadnye\">трехрядные</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/teleskopicheskie\">телескопические</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/erkernye\">карнизы для эркерных окон</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/skrytye\">скрытые карнизы для штор</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по помещению</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/karnizy-v-gostinuyu\">карнизы для гостиной</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/karnizy-v-spalnyu\">в спальню</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/karnizy-v-detskuyu\">карнизы в детскую комнату</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/karnizy-dlya-kuhni\">карнизы на кухню</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/karnizy-dlya-doma\">для дома</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/karnizy-na-balkon\">на балкон</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по принципу крепления</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/potolochnye\">карнизы потолочные для штор</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/nastennye\">настенные карнизы</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/karnizy/profilnyj\">потолочные профильные карнизы для штор</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t
\t\t\t</div>

\t\t</div>
\t</section>


\t\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__catalog-item\">

\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__catalog-link\">
\t\t\t\t\t\t\t\t\t\t\t\t
\t

\t<a class=\"header-catalog-tile\" href=\"https://domlegrand.com/catalog/tekstil\" data-catalog-id=\"header-catalog\">
\t\t<div class=\"header-catalog-tile__icon\"><img src=\"https://domlegrand.com/storage/app/uploads/public/618/93a/97d/61893a97d4c7e401886910.svg\" alt=\"\"></div>
<!--\t\t<div class=\"header-catalog-tile__icon\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 32 32\" fill=\"none\">
\t\t  <path d=\"m26.434 5.565h2.782v23.653h9.74a16.49 16.49 0 00-2.28-8.348h17.08a16.49 16.49 0 00-2.279 8.348h6.957v5.565h-2.783zm5.564 6.957h5.566a16.418 16.418 0 01-5.392 12.521h4.174v6.957h1.39zm-1.39 20.87v20.87h1.64a15.118 15.118 0 012.465 6.956h4.174zm3.603-8.349a18.204 18.204 0 004.744-12.521h2.783v12.521h7.777zm8.918-12.521h2.783a18.204 18.204 0 004.744 12.521h-7.527v6.957zm7.025 20.87a15.12 15.12 0 012.466-6.957h1.64v6.956h23.72zm4.106-8.349h26.26a16.418 16.418 0 01-5.392-12.521h6.957v12.521zm29.217 2.783h2.782v1.391h26.435v2.783z\"/>
\t\t</svg>
\t
</div>-->
\t\t<div class=\"header-catalog-tile__title\">подушки</div>
\t</a>

\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__catalog-open-detail js-toggle-header-submenu\"></div>
\t\t\t\t\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu header-menu__catalog-detail js-header-submenu\">
\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu-head\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link\" href=\"#\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<button class=\"header-menu__nav-btn js-toggle-header-submenu\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">подушки</span>
\t\t\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu-body\">
\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t
\t<section id=\"\" class=\"header-catalog                         header-catalog--mobile
             js-header-catalog\">
\t\t<div class=\"header-catalog__content\">

\t\t\t<button class=\"header-catalog__close js-close-header-submenu\">
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t</button>

\t\t\t<div class=\"header-catalog__categories\">

\t\t\t\t<div class=\"header-catalog__nav\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tekstil/dekorativnye-podushki\">подушки декоративные</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/tekstil/novogodnie-podushki\">новогодние подушки</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>

<!--\t\t\t\tновинки/распродажа-->
\t\t\t\t<div class=\"header-catalog__nav header-catalog__nav--primary\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>
                \t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             header-catalog__to-all\"
\t\t\thref=\"https://domlegrand.com/catalog/tekstil\"
\t\t\tdata-js=\"\"
\t\t>все подушки</a>
\t
\t

\t\t\t</div>

\t\t\t<div class=\"header-catalog__items\">

\t\t\t\t<div class=\"carousel\">
\t\t\t\t\t<div class=\"swiper-container carousel__body js-carousel-header-item\">
\t\t\t\t\t\t<div class=\"swiper-wrapper carousel__list\">
                            \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"192\" data-category=\"подушки\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil/podushki-dekorativnye-barkhat\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/7fb/a45/45d/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"подушки декоративные бархат\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"192\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">581 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil/podushki-dekorativnye-barkhat\">подушки декоративные бархат</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"193\" data-category=\"подушки\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil/podushki-dekorativnye-soft\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6fc/9e8/89d/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"подушки декоративные софт\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"193\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">452 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil/podushki-dekorativnye-soft\">подушки декоративные софт</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"194\" data-category=\"подушки\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil/podushki-dekorativnye-marokko\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/c49/be5/f17/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"подушки декоративные марокко\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"194\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">515 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil/podushki-dekorativnye-marokko\">подушки декоративные марокко</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"138\" data-category=\"текстиль для кухни\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/podushka-na-stul-oliva\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/7c3/88f/45d/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"подушка на стул олива\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"138\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">575 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/podushka-na-stul-oliva\">подушка на стул олива</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"139\" data-category=\"текстиль для кухни\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/podushka-na-stul-rayskiy-sad\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/399/a9f/d78/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"подушка на стул райский сад\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"139\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">575 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/podushka-na-stul-rayskiy-sad\">подушка на стул райский сад</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"140\" data-category=\"текстиль для кухни\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/podushka-na-stul-bona\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/069/f5b/4e9/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"подушка на стул бона\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"140\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">575 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni/podushka-na-stul-bona\">подушка на стул бона</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\"></div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<button class=\"header-catalog__items-next js-carousel-header-item-next\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m1 14.9629l7.27 7.98089l1 0.99989\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t<button class=\"header-catalog__items-prev js-carousel-header-item-prev\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t</div>

\t\t\t<div class=\"header-catalog__filters\">

\t\t\t\t\t\t\t\t\t<div class=\"header-catalog__filter\">
\t\t\t\t\t\t<p class=\"header-catalog__filter-title h3\">по виду</p>

\t\t\t\t\t\t<ul class=\"header-catalog__filter-list\">
                            \t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tekstil/podushki-dlya-divana\">подушки на диван</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tekstil/podushki-na-krovat\">подушки на кровать</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tekstil/krasivye-podushki\">красивые подушки</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__filter-item\">
\t\t\t\t\t\t\t\t\t<a class=\"header-catalog__filter-link\" href=\"https://domlegrand.com/catalog/tekstil/detskaya-podushka\">детские подушки</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
\t\t\t\t
\t\t\t</div>

\t\t</div>
\t</section>


\t\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__catalog-item\">

\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__catalog-link\">
\t\t\t\t\t\t\t\t\t\t\t\t
\t

\t<a class=\"header-catalog-tile\" href=\"https://domlegrand.com/catalog/komplektuyushchie\" data-catalog-id=\"header-catalog\">
\t\t<div class=\"header-catalog-tile__icon\"><img src=\"https://domlegrand.com/storage/app/uploads/public/618/24f/ac6/61824fac603f3434372501.svg\" alt=\"\"></div>
<!--\t\t<div class=\"header-catalog-tile__icon\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 32 32\" fill=\"none\">
\t\t  <path d=\"m26.434 5.565h2.782v23.653h9.74a16.49 16.49 0 00-2.28-8.348h17.08a16.49 16.49 0 00-2.279 8.348h6.957v5.565h-2.783zm5.564 6.957h5.566a16.418 16.418 0 01-5.392 12.521h4.174v6.957h1.39zm-1.39 20.87v20.87h1.64a15.118 15.118 0 012.465 6.956h4.174zm3.603-8.349a18.204 18.204 0 004.744-12.521h2.783v12.521h7.777zm8.918-12.521h2.783a18.204 18.204 0 004.744 12.521h-7.527v6.957zm7.025 20.87a15.12 15.12 0 012.466-6.957h1.64v6.956h23.72zm4.106-8.349h26.26a16.418 16.418 0 01-5.392-12.521h6.957v12.521zm29.217 2.783h2.782v1.391h26.435v2.783z\"/>
\t\t</svg>
\t
</div>-->
\t\t<div class=\"header-catalog-tile__title\">комплектующие</div>
\t</a>

\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__catalog-open-detail js-toggle-header-submenu\"></div>
\t\t\t\t\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu header-menu__catalog-detail js-header-submenu\">
\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu-head\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link\" href=\"#\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<button class=\"header-menu__nav-btn js-toggle-header-submenu\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">комплектующие</span>
\t\t\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu-body\">
\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t
\t<section id=\"\" class=\"header-catalog                         header-catalog--mobile
             js-header-catalog\">
\t\t<div class=\"header-catalog__content\">

\t\t\t<button class=\"header-catalog__close js-close-header-submenu\">
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t</button>

\t\t\t<div class=\"header-catalog__categories\">

\t\t\t\t<div class=\"header-catalog__nav\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/komplektuyushchie/komplektuyushchie-dlya-rulonnyh-shtor\">комплектующие для рулонных штор</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/komplektuyushchie/komplektuyushchie-dlya-karnizov\">комплектующие для карнизов</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>

<!--\t\t\t\tновинки/распродажа-->
\t\t\t\t<div class=\"header-catalog__nav header-catalog__nav--primary\">
\t\t\t\t\t<ul class=\"header-catalog__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-catalog__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-catalog__nav-link\" href=\"https://domlegrand.com/catalog/komplektuyushchie/rasprodazha-komplektuyushchih\">распродажа</a>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>
                \t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             header-catalog__to-all\"
\t\t\thref=\"https://domlegrand.com/catalog/komplektuyushchie\"
\t\t\tdata-js=\"\"
\t\t>все комплектующие</a>
\t
\t

\t\t\t</div>

\t\t\t<div class=\"header-catalog__items\">

\t\t\t\t<div class=\"carousel\">
\t\t\t\t\t<div class=\"swiper-container carousel__body js-carousel-header-item\">
\t\t\t\t\t\t<div class=\"swiper-wrapper carousel__list\">
                            \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"55\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/napravlyayuschie\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/ebf/6d2/806/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"направляющие для рулонных штор\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"55\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">250 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/napravlyayuschie\">направляющие для рулонных штор</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"152\" data-category=\"комплектующие\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/komplektuyushchie/kryuchok-gvozdik\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/30f/7f6/fd9/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"крючок-гвоздик\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"152\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">20 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/komplektuyushchie/kryuchok-gvozdik\">крючок-гвоздик</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"153\" data-category=\"комплектующие\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/komplektuyushchie/kronshteyn-stenovoy\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/4e3/9c9/3bf/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"кронштейн стеновой\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"153\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">130 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/komplektuyushchie/kronshteyn-stenovoy\">кронштейн стеновой</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"154\" data-category=\"комплектующие\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/komplektuyushchie/soedinitel-dlya-profilya-pvkh\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/955/15e/5ae/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"соединитель для профиля пвх\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"154\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">55 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/komplektuyushchie/soedinitel-dlya-profilya-pvkh\">соединитель для профиля пвх</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"155\" data-category=\"комплектующие\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/komplektuyushchie/zaglushka-figurnaya\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/dc6/765/54d/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"заглушка фигурная\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"155\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">40 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/komplektuyushchie/zaglushka-figurnaya\">заглушка фигурная</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item                         tile-item--no-hover
                    tile-item--compact
              \"
\t\t data-id=\"156\" data-category=\"комплектующие\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/komplektuyushchie/zaglushka-pryamaya\">
            \t\t\t<img class=\"tile-item__image lazy-custom\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/180/31c/0a8/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"заглушка прямая\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"156\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">30 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/komplektuyushchie/zaglushka-pryamaya\">заглушка прямая</a>

\t\t\t\t\t</div>

\t</div>


\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item\"></div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<button class=\"header-catalog__items-next js-carousel-header-item-next\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m1 14.9629l7.27 7.98089l1 0.99989\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t<button class=\"header-catalog__items-prev js-carousel-header-item-prev\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t</div>

\t\t\t<div class=\"header-catalog__filters\">

\t\t\t\t
\t\t\t</div>

\t\t</div>
\t</section>


\t\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t</div>
\t\t\t\t\t</li>

\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-menu__nav-link   \"
\t\t\t\t\t\t\t   href=\"https://domlegrand.com/partners\"
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t>
\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">для бизнеса</span>

\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</a>

\t\t\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-menu__nav-link   \"
\t\t\t\t\t\t\t   href=\"https://domlegrand.com/delivery\"
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t>
\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">для покупателей</span>

\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<button class=\"header-menu__nav-btn js-toggle-header-submenu\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m1 14.9629l7.27 7.98089l1 0.99989\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</a>

\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu js-header-submenu\">
\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu-head\">
\t\t\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link\" href=\"https://domlegrand.com/delivery\">
\t\t\t\t\t\t\t\t\t\t\t<button class=\"header-menu__nav-btn js-toggle-header-submenu\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">для покупателей</span>
\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu-body\">
\t\t\t\t\t\t\t\t\t\t<ul class=\"header-menu__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__nav-item\">
\t\t\t\t\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link   \"
\t\t\t\t\t\t\t\t\t\t\t\t   href=\"https://domlegrand.com/delivery\"
\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t>
\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">доставка</span>
\t\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__nav-item\">
\t\t\t\t\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link   \"
\t\t\t\t\t\t\t\t\t\t\t\t   href=\"https://domlegrand.com/payment\"
\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t>
\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">оплата</span>
\t\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__nav-item\">
\t\t\t\t\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link   \"
\t\t\t\t\t\t\t\t\t\t\t\t   href=\"https://domlegrand.com/guarantees\"
\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t>
\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">гарантии и возврат</span>
\t\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__nav-item\">
\t\t\t\t\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link   \"
\t\t\t\t\t\t\t\t\t\t\t\t   href=\"https://domlegrand.com/shops\"
\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t>
\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">адреса торговых точек</span>
\t\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-menu__nav-link   \"
\t\t\t\t\t\t\t   href=\"https://domlegrand.com/manufacture\"
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t>
\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">о компании</span>

\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<button class=\"header-menu__nav-btn js-toggle-header-submenu\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m1 14.9629l7.27 7.98089l1 0.99989\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</a>

\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu js-header-submenu\">
\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu-head\">
\t\t\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link\" href=\"https://domlegrand.com/manufacture\">
\t\t\t\t\t\t\t\t\t\t\t<button class=\"header-menu__nav-btn js-toggle-header-submenu\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">о компании</span>
\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu-body\">
\t\t\t\t\t\t\t\t\t\t<ul class=\"header-menu__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__nav-item\">
\t\t\t\t\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link   \"
\t\t\t\t\t\t\t\t\t\t\t\t   href=\"https://domlegrand.com/manufacture\"
\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t>
\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">производство</span>
\t\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__nav-item\">
\t\t\t\t\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link   \"
\t\t\t\t\t\t\t\t\t\t\t\t   href=\"https://domlegrand.com/history\"
\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t>
\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">история компании</span>
\t\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__nav-item\">
\t\t\t\t\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link   \"
\t\t\t\t\t\t\t\t\t\t\t\t   href=\"https://domlegrand.com/news\"
\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t>
\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">новости и акции</span>
\t\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__nav-item\">
\t\t\t\t\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link   \"
\t\t\t\t\t\t\t\t\t\t\t\t   href=\"https://domlegrand.com/school\"
\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t>
\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">студия дизайна</span>
\t\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__nav-item\">
\t\t\t\t\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link   \"
\t\t\t\t\t\t\t\t\t\t\t\t   href=\"https://domlegrand.com/vacancies\"
\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t>
\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">вакансии</span>
\t\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__nav-item\">
\t\t\t\t\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link   \"
\t\t\t\t\t\t\t\t\t\t\t\t   href=\"https://domlegrand.com/requisites\"
\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t>
\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">реквизиты</span>
\t\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-menu__nav-link   \"
\t\t\t\t\t\t\t   href=\"https://domlegrand.com/cooperation-with-organizers\"
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t>
\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">сотрудничество</span>

\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<button class=\"header-menu__nav-btn js-toggle-header-submenu\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m1 14.9629l7.27 7.98089l1 0.99989\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</a>

\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu js-header-submenu\">
\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu-head\">
\t\t\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link\" href=\"https://domlegrand.com/cooperation-with-organizers\">
\t\t\t\t\t\t\t\t\t\t\t<button class=\"header-menu__nav-btn js-toggle-header-submenu\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">сотрудничество</span>
\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t\t\t<div class=\"header-menu__submenu-body\">
\t\t\t\t\t\t\t\t\t\t<ul class=\"header-menu__nav-list\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__nav-item\">
\t\t\t\t\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link   \"
\t\t\t\t\t\t\t\t\t\t\t\t   href=\"https://domlegrand.com/cooperation-with-organizers\"
\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t>
\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">совместные покупки</span>
\t\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__nav-item\">
\t\t\t\t\t\t\t\t\t\t\t\t<a class=\"header-menu__nav-link   \"
\t\t\t\t\t\t\t\t\t\t\t\t   href=\"https://domlegrand.com/wholesale\"
\t\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t>
\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">продажи оптом</span>
\t\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"header-menu__nav-item\">
\t\t\t\t\t\t\t<a class=\"header-menu__nav-link   \"
\t\t\t\t\t\t\t   href=\"https://domlegrand.com/contacts\"
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t>
\t\t\t\t\t\t\t\t<span class=\"header-menu__nav-title\">контакты</span>

\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</a>

\t\t\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t</nav>

\t\t\t<div class=\"header-menu__contacts\">
\t\t\t\t<a class=\"header-menu__contacts-phone\"
\t\t\t\t   href=\"tel:+7(495)191-00-26\">
\t\t\t\t\t+7 (495) 191-00-26
\t\t\t\t</a>
\t\t\t\t<p class=\"header-menu__contacts-time\">c 9:00 до 18:00</p>
\t\t\t</div>

\t\t\t<div class=\"header-menu__tools\">
\t\t\t\t
\t\t\t\t
\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile      header-menu__tools-item js-toggle-header-drop\"
\t\t\thref=\"#header-wish\"
\t\t\tdata-js=\"\"
\t\t>\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t


\t\t\t\t<div id=\"partial_wishcount--headermenu\">
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t</a>
\t
\t
\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile      header-menu__tools-item js-toggle-header-drop\"
\t\t\thref=\"#header-compare\"
\t\t\tdata-js=\"\"
\t\t>\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t


\t\t\t\t<div id=\"partial_comparecount--headermenu\">
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t</a>
\t
\t
\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile      header-menu__tools-item hidden\"
\t\t\thref=\"https://domlegrand.com/profile\"
\t\t\tdata-js=\"\"
\t\t>
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<circle stroke-width=\"1.5\" cx=\"21.767\" cy=\"16.84\" r=\"3.75\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m21.812 21.402c4.084 0 6.633 2.504 8.2 5.138a18.222 18.222 0 0 1 1.627 3.631c.09.274.163.523.224.738l-10.051.001h-9.674c.053-.221.12-.478.202-.763.286-.995.756-2.314 1.488-3.626 1.464-2.625 3.907-5.12 7.984-5.119z\"/>
\t\t</svg>
\t
</a>
\t
\t
\t\t\t</div>

\t\t\t<div class=\"header-menu__actions\">
\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--upper
                    btn-tile--outline
                    btn-tile--primary
             header-menu__actions-item js-toggle-overlay\"
\t\t\thref=\"#popup-callback\"
\t\t\tdata-js=\"\"
\t\t>перезвоните мне</a>
\t
\t
\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             header-menu__actions-item js-toggle-overlay\"
\t\t\thref=\"#popup-partner\"
\t\t\tdata-js=\"\"
\t\t>стать партнером</a>
\t
\t
\t\t\t</div>

\t\t\t
\t\t\t<div class=\"header-menu__profile hidden\">
\t\t\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--upper
                    btn-tile--outline
                    btn-tile--primary
             header-menu__login\"
\t\t\ttype=\"button\"
\t\t\t
\t\t\t\t\t\t\t\t>
\t\t\tвход в личный кабинет

\t\t\t\t\t</button>

\t
\t\t\t</div>
\t\t</div>


\t</div>

\t</header>



    <main class=\"page-main\">



<div class=\"page-content page-catalog-content\">




\t\t\t
\t<div class=\"content-head     \">
\t\t<div class=\"content-head__content\">
\t\t\t
\t\t\t\t\t\t\t\t<nav class=\"page-crumbs\">
\t\t<a class=\"page-crumbs__link\" href=\"https://domlegrand.com\">главная</a>

\t\t\t\t\t<span class=\"page-crumbs__split\"> / </span>
\t\t\t\t\t\t\t<a class=\"page-crumbs__link\" href=\"https://domlegrand.com/catalog\">каталог</a>
\t\t\t\t\t\t\t\t<span class=\"page-crumbs__split\"> / </span>
\t\t\t\t\t\t\t<span class=\"page-crumbs__link--current\">рулонные шторы</span>
\t\t\t\t\t\t</nav>

\t\t\t
\t\t\t


\t\t\t\t\t\t\t<h1 class=\"content-head__title\">рулонные шторы в москве</h1>
\t\t\t
\t\t\t\t\t\t
\t\t</div>
\t</div>




\t<section class=\"catalog-grid\">
\t\t<div class=\"catalog-grid__content\">

\t\t\t\t\t\t<div class=\"catalog-grid__head\">

\t\t\t\t
\t\t\t
\t<div class=\"catalog-sort\">
\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             catalog-sort__filter-btn js-filter-open\"
\t\t\ttype=\"button\"
\t\t\t
\t\t\t\t\t\t\t\t>
\t\t\tвсе фильтры

\t\t\t\t\t\t\t<div class=\"btn-tile__icon\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.833333em\" height=\"1em\" viewbox=\"0 0 20 24\" fill=\"none\">
\t\t\t<path stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m1 4h7m12 4h7m1 12h9m14 12h5m1 20h5m10 20h9m8 1v6m14 9v6m6 17v6\"/>
\t\t</svg>
\t
</div>
\t\t\t\t\t</button>

\t

\t\t<div class=\"form-select       js-form-select\">
\t\t\t<div class=\"form-select__content\">
\t\t\t\t<div class=\"form-select__head js-form-select-head\">
\t\t\t\t\t<div class=\"form-select__value js-form-select-value\"></div>
\t\t\t\t\t<div class=\"form-select__head-icon\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"2.076923em\" height=\"1em\" viewbox=\"0 0 27 13\" fill=\"none\">
\t\t\t<path d=\"m1 1l13 11.781l25.007 1\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</div>
\t\t\t\t</div>

\t\t\t\t<div class=\"form-select__body\">
\t\t\t\t\t<ul class=\"form-select__list\">
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"form-select__list-item js-form-select-item\" data-value=\"price asc\">
\t\t\t\t\t\t\tсначала самые дешевые
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"form-select__list-item js-form-select-item\" data-value=\"price desc\">
\t\t\t\t\t\t\tсначала самые дорогие
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"form-select__list-item js-form-select-item\" data-value=\"sort asc\">
\t\t\t\t\t\t\tпо популярности
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t</div>
\t\t\t</div>

\t\t\t\t\t\t<select class=\"form-select__element\"
\t\t\t\t\tname=\"filter[sort]\"
\t\t\t\t\tid=\"sort\"
\t\t\t>

\t\t\t\t\t\t\t\t<option
\t\t\t\t\t\t\t\t\tvalue=\"price asc\"
\t\t\t\t
\t\t\t\tclass=\"\"
\t\t\t\tdata-url=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna?filter%5binterier%5d=1&filter%5bsort%5d=price+asc\"
\t\t\t\t>сначала самые дешевые</option>
\t\t\t\t\t\t\t\t<option
\t\t\t\t\t\t\t\t\tvalue=\"price desc\"
\t\t\t\t
\t\t\t\tclass=\"\"
\t\t\t\tdata-url=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna?filter%5binterier%5d=1&filter%5bsort%5d=price+desc\"
\t\t\t\t>сначала самые дорогие</option>
\t\t\t\t\t\t\t\t<option
\t\t\t\t\t\t\t\t\tvalue=\"sort asc\"
\t\t\t\tselected
\t\t\t\tclass=\"\"
\t\t\t\tdata-url=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna?filter%5binterier%5d=1&filter%5bsort%5d=sort+asc\"
\t\t\t\t>по популярности</option>
\t\t\t\t\t\t\t</select>
\t\t</div>

\t\t<div class=\"catalog-sort__category\">
\t\t\t\t\t\t\t<a class=\"catalog-sort__category-item \" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna?filter%5binterier%5d=0\" data-filter=\"1\">
\t\t\t\t\tтовары
\t\t\t\t</a>
\t\t\t\t\t\t\t<a class=\"catalog-sort__category-item catalog-sort__category-item--active\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna?filter%5binterier%5d=1\" data-filter=\"0\">
\t\t\t\t\tв интерьере
\t\t\t\t</a>
\t\t\t\t\t</div>
\t</div>


\t\t\t\t
\t\t
\t\t\t\t\t\t
\t<form id=\"filter-form\" class=\"catalog-filter js-filter\">
\t\t<div class=\"catalog-filter__blur js-filter-close\"></div>

\t\t<div class=\"catalog-filter__content\">
\t\t\t<div class=\"catalog-filter__head\">
\t\t\t\t<button class=\"catalog-filter__close js-filter-close\" type=\"button\">
\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t</button>
\t\t\t</div>

\t\t\t<div class=\"catalog-filter__body\">

\t\t\t\t


\t\t
\t<div class=\"spoiler-box      js-spoiler\" data-open>
\t\t<div class=\"spoiler-box__head js-spoiler-head\">
\t\t\t
\t\t\t<div class=\"spoiler-box__title\">цвет</div>

\t\t\t
\t\t\t<div class=\"spoiler-box__head-icon\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"2.076923em\" height=\"1em\" viewbox=\"0 0 27 13\" fill=\"none\">
\t\t\t<path d=\"m1 1l13 11.781l25.007 1\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</div>

\t\t</div>

\t\t<div class=\"spoiler-box__body js-spoiler-body\">
\t\t\t<div class=\"spoiler-box__content js-spoiler-content\">
\t\t\t\t\t<div class=\"catalog-colors\">
\t\t <!--|slice(0,5)-->
\t\t\t<label class=\"catalog-colors__item\">
\t\t\t\t<input
                    class=\"catalog-colors__element\"
                    type=\"checkbox\"
                    name=\"filter[colors][]\"
                    value=\"1\"

                >
\t\t\t\t<div class=\"catalog-colors__value\" style=\"background-color: #ce2020;\"></div>
\t\t\t</label>
\t\t <!--|slice(0,5)-->
\t\t\t<label class=\"catalog-colors__item\">
\t\t\t\t<input
                    class=\"catalog-colors__element\"
                    type=\"checkbox\"
                    name=\"filter[colors][]\"
                    value=\"6\"

                >
\t\t\t\t<div class=\"catalog-colors__value\" style=\"background-color: #1abc9c;\"></div>
\t\t\t</label>
\t\t <!--|slice(0,5)-->
\t\t\t<label class=\"catalog-colors__item\">
\t\t\t\t<input
                    class=\"catalog-colors__element\"
                    type=\"checkbox\"
                    name=\"filter[colors][]\"
                    value=\"7\"

                >
\t\t\t\t<div class=\"catalog-colors__value\" style=\"background-color: #dd8f24;\"></div>
\t\t\t</label>
\t\t <!--|slice(0,5)-->
\t\t\t<label class=\"catalog-colors__item\">
\t\t\t\t<input
                    class=\"catalog-colors__element\"
                    type=\"checkbox\"
                    name=\"filter[colors][]\"
                    value=\"8\"

                >
\t\t\t\t<div class=\"catalog-colors__value\" style=\"background-color: #ce2323;\"></div>
\t\t\t</label>
\t\t <!--|slice(0,5)-->
\t\t\t<label class=\"catalog-colors__item\">
\t\t\t\t<input
                    class=\"catalog-colors__element\"
                    type=\"checkbox\"
                    name=\"filter[colors][]\"
                    value=\"9\"

                >
\t\t\t\t<div class=\"catalog-colors__value\" style=\"background-color: #dd8e34;\"></div>
\t\t\t</label>
\t\t <!--|slice(0,5)-->
\t\t\t<label class=\"catalog-colors__item\">
\t\t\t\t<input
                    class=\"catalog-colors__element\"
                    type=\"checkbox\"
                    name=\"filter[colors][]\"
                    value=\"10\"

                >
\t\t\t\t<div class=\"catalog-colors__value\" style=\"background-color: #2ecc71;\"></div>
\t\t\t</label>
\t\t <!--|slice(0,5)-->
\t\t\t<label class=\"catalog-colors__item\">
\t\t\t\t<input
                    class=\"catalog-colors__element\"
                    type=\"checkbox\"
                    name=\"filter[colors][]\"
                    value=\"11\"

                >
\t\t\t\t<div class=\"catalog-colors__value\" style=\"background-color: #2980b9;\"></div>
\t\t\t</label>
\t\t <!--|slice(0,5)-->
\t\t\t<label class=\"catalog-colors__item\">
\t\t\t\t<input
                    class=\"catalog-colors__element\"
                    type=\"checkbox\"
                    name=\"filter[colors][]\"
                    value=\"12\"

                >
\t\t\t\t<div class=\"catalog-colors__value\" style=\"background-color: #27ae60;\"></div>
\t\t\t</label>
\t\t <!--|slice(0,5)-->
\t\t\t<label class=\"catalog-colors__item\">
\t\t\t\t<input
                    class=\"catalog-colors__element\"
                    type=\"checkbox\"
                    name=\"filter[colors][]\"
                    value=\"13\"

                >
\t\t\t\t<div class=\"catalog-colors__value\" style=\"background-color: #f1c40f;\"></div>
\t\t\t</label>
\t\t <!--|slice(0,5)-->
\t\t\t<label class=\"catalog-colors__item\">
\t\t\t\t<input
                    class=\"catalog-colors__element\"
                    type=\"checkbox\"
                    name=\"filter[colors][]\"
                    value=\"14\"

                >
\t\t\t\t<div class=\"catalog-colors__value\" style=\"background-color: #ecf0f1;\"></div>
\t\t\t</label>
\t\t <!--|slice(0,5)-->
\t\t\t<label class=\"catalog-colors__item\">
\t\t\t\t<input
                    class=\"catalog-colors__element\"
                    type=\"checkbox\"
                    name=\"filter[colors][]\"
                    value=\"15\"

                >
\t\t\t\t<div class=\"catalog-colors__value\" style=\"background-color: #050505;\"></div>
\t\t\t</label>
\t\t <!--|slice(0,5)-->
\t\t\t<label class=\"catalog-colors__item\">
\t\t\t\t<input
                    class=\"catalog-colors__element\"
                    type=\"checkbox\"
                    name=\"filter[colors][]\"
                    value=\"16\"

                >
\t\t\t\t<div class=\"catalog-colors__value\" style=\"background-color: #eece8f;\"></div>
\t\t\t</label>
\t\t <!--|slice(0,5)-->
\t\t\t<label class=\"catalog-colors__item\">
\t\t\t\t<input
                    class=\"catalog-colors__element\"
                    type=\"checkbox\"
                    name=\"filter[colors][]\"
                    value=\"17\"

                >
\t\t\t\t<div class=\"catalog-colors__value\" style=\"background-color: #7f8c8d;\"></div>
\t\t\t</label>
\t\t <!--|slice(0,5)-->
\t\t\t<label class=\"catalog-colors__item\">
\t\t\t\t<input
                    class=\"catalog-colors__element\"
                    type=\"checkbox\"
                    name=\"filter[colors][]\"
                    value=\"18\"

                >
\t\t\t\t<div class=\"catalog-colors__value\" style=\"background-color: #663300;\"></div>
\t\t\t</label>
\t\t <!--|slice(0,5)-->
\t\t\t<label class=\"catalog-colors__item\">
\t\t\t\t<input
                    class=\"catalog-colors__element\"
                    type=\"checkbox\"
                    name=\"filter[colors][]\"
                    value=\"19\"

                >
\t\t\t\t<div class=\"catalog-colors__value\" style=\"background-color: #ffd700;\"></div>
\t\t\t</label>
\t\t <!--|slice(0,5)-->
\t\t\t<label class=\"catalog-colors__item\">
\t\t\t\t<input
                    class=\"catalog-colors__element\"
                    type=\"checkbox\"
                    name=\"filter[colors][]\"
                    value=\"20\"

                >
\t\t\t\t<div class=\"catalog-colors__value\" style=\"background-color: #fff6d4;\"></div>
\t\t\t</label>
\t\t <!--|slice(0,5)-->
\t\t\t<label class=\"catalog-colors__item\">
\t\t\t\t<input
                    class=\"catalog-colors__element\"
                    type=\"checkbox\"
                    name=\"filter[colors][]\"
                    value=\"21\"

                >
\t\t\t\t<div class=\"catalog-colors__value\" style=\"background-color: #ffc0cb;\"></div>
\t\t\t</label>
\t\t <!--|slice(0,5)-->
\t\t\t<label class=\"catalog-colors__item\">
\t\t\t\t<input
                    class=\"catalog-colors__element\"
                    type=\"checkbox\"
                    name=\"filter[colors][]\"
                    value=\"22\"

                >
\t\t\t\t<div class=\"catalog-colors__value\" style=\"background-color: #98ffff;\"></div>
\t\t\t</label>
\t\t <!--|slice(0,5)-->
\t\t\t<label class=\"catalog-colors__item\">
\t\t\t\t<input
                    class=\"catalog-colors__element\"
                    type=\"checkbox\"
                    name=\"filter[colors][]\"
                    value=\"23\"

                >
\t\t\t\t<div class=\"catalog-colors__value\" style=\"background-color: #9fcdf7;\"></div>
\t\t\t</label>
\t\t <!--|slice(0,5)-->
\t\t\t<label class=\"catalog-colors__item\">
\t\t\t\t<input
                    class=\"catalog-colors__element\"
                    type=\"checkbox\"
                    name=\"filter[colors][]\"
                    value=\"24\"

                >
\t\t\t\t<div class=\"catalog-colors__value\" style=\"background-color: #800080;\"></div>
\t\t\t</label>
\t\t <!--|slice(0,5)-->
\t\t\t<label class=\"catalog-colors__item\">
\t\t\t\t<input
                    class=\"catalog-colors__element\"
                    type=\"checkbox\"
                    name=\"filter[colors][]\"
                    value=\"25\"

                >
\t\t\t\t<div class=\"catalog-colors__value\" style=\"background-color: #e67e22;\"></div>
\t\t\t</label>
\t\t
\t\t
<!--\t\ttodo-->
\t\t\t</div>

\t\t\t</div>
\t\t</div>
\t</div>



\t\t\t\t

\t\t\t\t
                <input type=\"hidden\" name=\"category\" value=\"6\" class=\"hidden\">

\t\t\t</div>

\t\t\t<div class=\"catalog-filter__foot\">

\t\t\t\t
\t\t\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\ttype=\"submit\"
\t\t\t
\t\t\t\t\t\t\t\t>
\t\t\t\t\t\t\t\tсмотреть&nbsp;<span id=\"ajaxcounter\"></span>
\t\t\t\t

\t\t\t\t\t</button>

\t
\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--outline
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna\"
\t\t\tdata-js=\"\"
\t\t>сбросить фильтры</a>
\t
\t

\t\t\t</div>
\t\t</div>
\t</form>

\t\t\t</div>
\t\t\t
\t\t\t<ul class=\"catalog-grid__list\" data-list_name=\"каталог\">
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"270\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-marko\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/2aa/215/a3a/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы марко\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/2aa/215/a3a/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы марко2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/104/e78/bc3/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы марко1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/c06/0f1/d1a/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы марко3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/37b/f87/419/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы марко4\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы марко5\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"270\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">765 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-marko\">рулонные шторы марко</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">765 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"4530\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_270_mini\"
                       value=\"трюфель\"
                       data-count_size=\"17\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/746/22e/494/thumb__36_16_0_0_crop.jpg\" alt=\"трюфель\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/2aa/215/a3a/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/104/e78/bc3/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/c06/0f1/d1a/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/37b/f87/419/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"4528\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_270_mini\"
                       value=\"темно-серый\"
                       data-count_size=\"17\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/531/5a7/cf8/thumb__36_16_0_0_crop.jpg\" alt=\"темно-серый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/c45/6a8/098/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/206/f38/111/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/419/f23/875/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"4529\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_270_mini\"
                       value=\"светло-серый\"
                       data-count_size=\"17\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/c95/4d1/2b7/thumb__36_16_0_0_crop.jpg\" alt=\"светло-серый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/47f/315/d27/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/1e9/75e/592/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/e04/2f8/16a/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"4531\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_270_mini\"
                       value=\"миндаль\"
                       data-count_size=\"17\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/f51/e17/147/thumb__36_16_0_0_crop.jpg\" alt=\"миндаль\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/d0b/294/948/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/abd/ce9/c91/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/414/8fa/ce7/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>


            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>17</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"270\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-marko\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"270\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"268\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-favor\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/118/5ce/e7c/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы фавор\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/118/5ce/e7c/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы фавор2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/1b2/249/6d3/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы фавор1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/edd/a8d/318/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы фавор3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/418/b3d/d44/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы фавор4\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы фавор5\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"268\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">960 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-favor\">рулонные шторы фавор</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">960 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"4514\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_268_mini\"
                       value=\"темно-серый\"
                       data-count_size=\"17\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/531/5a7/cf8/thumb__36_16_0_0_crop.jpg\" alt=\"темно-серый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/118/5ce/e7c/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/1b2/249/6d3/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/edd/a8d/318/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/418/b3d/d44/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"4515\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_268_mini\"
                       value=\"миндаль\"
                       data-count_size=\"16\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/dee/a71/18e/thumb__36_16_0_0_crop.jpg\" alt=\"миндаль\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/936/028/2e1/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/e42/f38/508/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/865/7cc/465/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>


            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>17</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"268\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-favor\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"268\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"272\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-lester\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/ab7/148/b85/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы лестер\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/ab7/148/b85/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы лестер2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/dcf/16c/e6f/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы лестер1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/a1b/bf2/391/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы лестер3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы лестер4\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"272\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">585 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-lester\">рулонные шторы лестер</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">585 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"4542\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_272_mini\"
                       value=\"светло-серый\"
                       data-count_size=\"17\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/fa2/8eb/32a/thumb__36_16_0_0_crop.jpg\" alt=\"светло-серый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/ab7/148/b85/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/dcf/16c/e6f/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/a1b/bf2/391/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"4543\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_272_mini\"
                       value=\"серый\"
                       data-count_size=\"17\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/b60/38b/023/thumb__36_16_0_0_crop.jpg\" alt=\"серый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/157/dca/316/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/874/944/b10/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/1be/6ca/0e8/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"4544\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_272_mini\"
                       value=\"шампань\"
                       data-count_size=\"17\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/ac2/0a4/e5b/thumb__36_16_0_0_crop.jpg\" alt=\"шампань\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/243/264/0ef/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/b47/485/4fe/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/804/3f4/369/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"4545\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_272_mini\"
                       value=\"белый\"
                       data-count_size=\"17\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/ffd/713/d10/thumb__36_16_0_0_crop.jpg\" alt=\"белый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/1bc/97f/5d5/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/0a8/a5a/71b/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/a08/13c/c49/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>

                    <div class=\"catalog-colors__more-counter\">+4</div>

            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>17</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"272\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-lester\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"272\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"271\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-layt\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/cd4/c92/516/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы лайт\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/cd4/c92/516/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы лайт2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/a84/55e/b54/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы лайт1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы лайт3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"271\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">530 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-layt\">рулонные шторы лайт</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">530 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"4535\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_271_mini\"
                       value=\"белый\"
                       data-count_size=\"17\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/e80/0f6/087/thumb__36_16_0_0_crop.jpg\" alt=\"белый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/cd4/c92/516/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/a84/55e/b54/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"4536\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_271_mini\"
                       value=\"миндаль\"
                       data-count_size=\"17\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/329/8ba/a08/thumb__36_16_0_0_crop.jpg\" alt=\"миндаль\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/97d/6d4/e76/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/048/a58/080/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"4537\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_271_mini\"
                       value=\"пудра\"
                       data-count_size=\"17\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/25c/427/052/thumb__36_16_0_0_crop.jpg\" alt=\"пудра\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/b17/26b/ab9/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/d9b/ddc/c28/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"4538\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_271_mini\"
                       value=\"светло-серый\"
                       data-count_size=\"17\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/ead/3fe/6be/thumb__36_16_0_0_crop.jpg\" alt=\"светло-серый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/b52/59f/37f/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/a69/8c2/482/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>

                    <div class=\"catalog-colors__more-counter\">+2</div>

            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>17</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"271\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-layt\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"271\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"57\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-frost\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/be4/ec3/e60/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы фрост\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/be4/ec3/e60/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы фрост2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/33b/c36/857/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы фрост1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/edd/2ee/f2d/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы фрост3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/612/c17/e21/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы фрост4\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/16f/90f/cad/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы фрост5\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/677/180/f15/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы фрост6\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"57\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">871 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-frost\">рулонные шторы фрост</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">871 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3392\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_57_mini\"
                       value=\"темно-синий\"
                       data-count_size=\"15\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/c44/58a/6e6/thumb__36_16_0_0_crop.jpg\" alt=\"темно-синий\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/be4/ec3/e60/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/33b/c36/857/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/edd/2ee/f2d/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/612/c17/e21/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/16f/90f/cad/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/677/180/f15/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3393\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_57_mini\"
                       value=\"бело-серый\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/688/b61/42d/thumb__36_16_0_0_crop.jpg\" alt=\"бело-серый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/495/47e/984/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/d05/48a/595/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/a32/078/fcc/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/617/9d6/205/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3394\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_57_mini\"
                       value=\"светло-серый\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/bce/e21/668/thumb__36_16_0_0_crop.jpg\" alt=\"светло-серый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/b03/042/878/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/7c6/b03/a49/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6ce/72c/6f5/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3395\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_57_mini\"
                       value=\"бежевый\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/f60/b8d/523/thumb__36_16_0_0_crop.jpg\" alt=\"бежевый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/8f1/06b/833/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/3d9/6c8/b50/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/432/7bd/509/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>

                    <div class=\"catalog-colors__more-counter\">+1</div>

            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>15</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"57\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-frost\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"57\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"37\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-blackout\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/260/f24/35e/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы блэкаут\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/260/f24/35e/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы блэкаут2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/236/7bf/ffd/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы блэкаут1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/854/1e6/39f/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы блэкаут3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы блэкаут4\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"37\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 045 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-blackout\">рулонные шторы блэкаут</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">1 045 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3675\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_37_mini\"
                       value=\"коралл\"
                       data-count_size=\"15\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/fec/b8f/587/thumb__36_16_0_0_crop.jpg\" alt=\"коралл\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/260/f24/35e/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/236/7bf/ffd/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/854/1e6/39f/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3676\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_37_mini\"
                       value=\"пурпур\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/5f4/a1f/69d/thumb__36_16_0_0_crop.jpg\" alt=\"пурпур\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/559/a7a/921/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/222/fc0/00f/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/d40/4e1/4df/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3677\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_37_mini\"
                       value=\"экрю\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/458/a38/5fd/thumb__36_16_0_0_crop.jpg\" alt=\"экрю\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/030/76d/d13/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/34b/1ad/3d7/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/a74/44c/c31/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3678\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_37_mini\"
                       value=\"латте\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/46c/911/d67/thumb__36_16_0_0_crop.jpg\" alt=\"латте\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/c3f/fe6/9c3/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/c0b/8d0/500/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/a1f/dd3/a8c/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>

                    <div class=\"catalog-colors__more-counter\">+4</div>

            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>15</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"37\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-blackout\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"37\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"45\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-blekaut-kristall\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/4fa/8e2/578/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы кристалл блэкаут\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/4fa/8e2/578/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы кристалл блэкаут2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/079/716/4b1/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы кристалл блэкаут1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/10e/caa/d16/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы кристалл блэкаут3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы кристалл блэкаут4\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"45\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 125 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-blekaut-kristall\">рулонные шторы кристалл блэкаут</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">1 125 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3421\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_45_mini\"
                       value=\"голубой\"
                       data-count_size=\"15\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/9cd/ee2/28e/thumb__36_16_0_0_crop.jpg\" alt=\"голубой\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/4fa/8e2/578/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/079/716/4b1/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/10e/caa/d16/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3422\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_45_mini\"
                       value=\"мятный\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/5c7/cf6/25e/thumb__36_16_0_0_crop.jpg\" alt=\"мятный\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/8eb/20d/863/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/932/b5c/759/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/d4b/697/051/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3423\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_45_mini\"
                       value=\"белый\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/688/b61/42d/thumb__36_16_0_0_crop.jpg\" alt=\"белый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/f98/4f0/878/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/b23/512/dc2/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/b55/617/62a/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3424\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_45_mini\"
                       value=\"крем\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/f60/b8d/523/thumb__36_16_0_0_crop.jpg\" alt=\"крем\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/6c0/780/80a/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/761/eca/d25/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/57b/bab/4c2/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>

                    <div class=\"catalog-colors__more-counter\">+1</div>

            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>15</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"45\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-blekaut-kristall\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"45\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"46\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-blekaut-silver\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/959/3e9/c09/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы сильвер блэкаут\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/959/3e9/c09/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы сильвер блэкаут2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/c49/727/044/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы сильвер блэкаут1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/895/760/574/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы сильвер блэкаут3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/3e6/97d/ae4/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы сильвер блэкаут4\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы сильвер блэкаут5\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"46\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">840 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-blekaut-silver\">рулонные шторы сильвер блэкаут</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">840 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3408\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_46_mini\"
                       value=\"зеленый\"
                       data-count_size=\"15\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/3c3/388/580/thumb__36_16_0_0_crop.jpg\" alt=\"зеленый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/959/3e9/c09/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/c49/727/044/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/895/760/574/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/3e6/97d/ae4/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3409\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_46_mini\"
                       value=\"лиловый\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/823/18a/a24/thumb__36_16_0_0_crop.jpg\" alt=\"лиловый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/0b9/a5e/a38/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/e2e/add/3b7/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/325/75e/9ce/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/073/fc0/b77/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3410\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_46_mini\"
                       value=\"ваниль\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/1d6/9d2/4f1/thumb__36_16_0_0_crop.jpg\" alt=\"ваниль\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/ccc/40c/20f/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/952/d3b/f6c/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/757/2a1/75e/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/685/21c/cab/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3411\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_46_mini\"
                       value=\"бежевый\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/f60/b8d/523/thumb__36_16_0_0_crop.jpg\" alt=\"бежевый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/efc/a91/6df/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/ade/552/ce5/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/4f7/9a1/6ac/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/992/b90/784/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>

                    <div class=\"catalog-colors__more-counter\">+4</div>

            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>15</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"46\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-blekaut-silver\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"46\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"74\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/seviliya\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/998/27d/a16/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы севилия\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/998/27d/a16/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы севилия2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/096/0f0/a64/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы севилия1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/785/02c/b26/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы севилия3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/957/fbd/2d3/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы севилия4\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/e96/d75/b14/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы севилия5\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы севилия6\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"74\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">703 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/seviliya\">рулонные шторы севилия</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">703 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3693\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_74_mini\"
                       value=\"трюфель\"
                       data-count_size=\"15\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/8f5/3d0/b58/thumb__36_16_0_0_crop.jpg\" alt=\"трюфель\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/998/27d/a16/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/096/0f0/a64/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/785/02c/b26/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/957/fbd/2d3/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/e96/d75/b14/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3694\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_74_mini\"
                       value=\"какао\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/24e/6c3/09b/thumb__36_16_0_0_crop.jpg\" alt=\"какао\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/3c8/6cd/b84/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/844/295/da7/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/5e4/b4b/1b1/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/3b8/85c/b79/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/b9b/97d/576/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3695\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_74_mini\"
                       value=\"серебро\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/1b5/5fb/30e/thumb__36_16_0_0_crop.jpg\" alt=\"серебро\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/52e/25d/7fc/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/55e/4d9/152/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/f2f/365/86b/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/98e/d1f/f8f/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/a75/895/51d/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3696\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_74_mini\"
                       value=\"золото\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/89e/161/115/thumb__36_16_0_0_crop.jpg\" alt=\"золото\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/a47/c6d/abc/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/0e7/178/835/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/dc5/2f8/cf6/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/8b5/c9e/969/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/ee5/064/bc1/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>

                    <div class=\"catalog-colors__more-counter\">+2</div>

            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>15</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"74\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/seviliya\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"74\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"53\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-briz\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/846/baa/679/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы бриз\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/846/baa/679/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы бриз2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/1d7/2eb/a5e/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы бриз1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/3af/544/5e3/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы бриз3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы бриз4\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"53\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">794 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-briz\">рулонные шторы бриз</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">794 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3683\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_53_mini\"
                       value=\"пудра\"
                       data-count_size=\"15\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/fe9/398/602/thumb__36_16_0_0_crop.jpg\" alt=\"пудра\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/846/baa/679/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/1d7/2eb/a5e/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/3af/544/5e3/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3684\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_53_mini\"
                       value=\"снежно-белый\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/07e/852/b44/thumb__36_16_0_0_crop.jpg\" alt=\"снежно-белый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/95b/04a/77b/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/64f/70e/5d4/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/9e9/31d/668/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3685\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_53_mini\"
                       value=\"салатовый\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/d8d/bc2/20e/thumb__36_16_0_0_crop.jpg\" alt=\"салатовый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/b33/06b/d6e/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/504/4d4/2cd/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/8b6/35e/079/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3686\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_53_mini\"
                       value=\"голубой\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/9cd/ee2/28e/thumb__36_16_0_0_crop.jpg\" alt=\"голубой\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/f7d/eab/92c/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/b16/0c8/dd3/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/8d9/a76/9e6/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>

                    <div class=\"catalog-colors__more-counter\">+2</div>

            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>15</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"53\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-briz\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"53\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"51\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-den-noch\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/a66/829/f64/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные жалюзи день-ночь\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/a66/829/f64/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные жалюзи день-ночь2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/339/286/98b/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные жалюзи день-ночь1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/3eb/846/184/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные жалюзи день-ночь3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/378/e28/412/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные жалюзи день-ночь4\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/a62/133/558/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные жалюзи день-ночь5\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"51\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 540 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-den-noch\">рулонные жалюзи день-ночь</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">1 540 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3618\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_51_mini\"
                       value=\"снежно-белый\"
                       data-count_size=\"15\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/688/b61/42d/thumb__36_16_0_0_crop.jpg\" alt=\"снежно-белый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/a66/829/f64/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/339/286/98b/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/3eb/846/184/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/378/e28/412/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/a62/133/558/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3617\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_51_mini\"
                       value=\"абрикос\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/46c/767/db4/thumb__36_16_0_0_crop.jpg\" alt=\"абрикос\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/085/c41/4b1/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/967/253/6a1/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/4dc/17c/b12/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/bc5/09a/4dd/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/a62/133/558/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3619\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_51_mini\"
                       value=\"меланж белый\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/688/b61/42d/thumb__36_16_0_0_crop.jpg\" alt=\"меланж белый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/111/79d/ab5/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/63f/876/314/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/bf1/5b2/a77/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/30e/24f/1dd/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/a62/133/558/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3620\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_51_mini\"
                       value=\"молочный\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/5f3/84c/7a7/thumb__36_16_0_0_crop.jpg\" alt=\"молочный\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/64f/676/f0c/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/2dd/c47/dbc/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/d68/346/96e/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/c9f/795/6a0/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/a62/133/558/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>

                    <div class=\"catalog-colors__more-counter\">+2</div>

            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>15</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"51\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-den-noch\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"51\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"54\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-mozaika\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/820/485/057/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы мозаика\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/820/485/057/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы мозаика2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/fdf/7e8/965/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы мозаика1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/16f/687/211/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы мозаика3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/403/900/49f/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы мозаика4\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы мозаика5\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"54\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 005 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-mozaika\">рулонные шторы мозаика</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">1 005 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3365\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_54_mini\"
                       value=\"темно-серый\"
                       data-count_size=\"15\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/531/5a7/cf8/thumb__36_16_0_0_crop.jpg\" alt=\"темно-серый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/820/485/057/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/fdf/7e8/965/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/16f/687/211/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/403/900/49f/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3366\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_54_mini\"
                       value=\"венге\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/423/e5f/5b0/thumb__36_16_0_0_crop.jpg\" alt=\"венге\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/425/e75/3da/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/91f/3c9/c2a/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/c80/015/523/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3367\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_54_mini\"
                       value=\"коричневый\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/79e/b27/6fb/thumb__36_16_0_0_crop.jpg\" alt=\"коричневый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/839/eb0/697/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/3c0/329/797/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6cb/c2f/8d5/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3368\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_54_mini\"
                       value=\"белый\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/688/b61/42d/thumb__36_16_0_0_crop.jpg\" alt=\"белый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/78b/fa6/44f/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/30a/64f/91a/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/e3b/a7c/07d/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>

                    <div class=\"catalog-colors__more-counter\">+3</div>

            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>15</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"54\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-mozaika\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"54\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"122\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-dekor\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/d11/6f8/e3b/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы декор\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/d11/6f8/e3b/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы декор2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/0b7/907/20d/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы декор1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/92a/a81/65b/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы декор3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/b31/d07/d3b/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы декор4\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы декор5\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"122\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">851 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-dekor\">рулонные шторы декор</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">851 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3352\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_122_mini\"
                       value=\"пепел розы\"
                       data-count_size=\"13\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/823/18a/a24/thumb__36_16_0_0_crop.jpg\" alt=\"пепел розы\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/d11/6f8/e3b/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/0b7/907/20d/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/92a/a81/65b/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/b31/d07/d3b/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3350\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_122_mini\"
                       value=\"пудра\"
                       data-count_size=\"12\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/fe9/398/602/thumb__36_16_0_0_crop.jpg\" alt=\"пудра\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/d77/1fc/e77/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/2b2/2c8/587/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/524/86b/2b4/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3347\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_122_mini\"
                       value=\"мятный\"
                       data-count_size=\"12\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/5c7/cf6/25e/thumb__36_16_0_0_crop.jpg\" alt=\"мятный\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/39a/7c1/aed/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/210/925/928/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/eef/443/bcd/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3348\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_122_mini\"
                       value=\"серый\"
                       data-count_size=\"12\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/531/5a7/cf8/thumb__36_16_0_0_crop.jpg\" alt=\"серый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/c99/e25/a14/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/341/6ca/b80/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/fc9/645/2b2/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>

                    <div class=\"catalog-colors__more-counter\">+7</div>

            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>13</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"122\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-dekor\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"122\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"199\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-den-noch-korichnevyy\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/ca4/fc2/24f/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы день-ночь коричневый\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/ca4/fc2/24f/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы день-ночь коричневый2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/2c8/80a/2b0/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы день-ночь коричневый1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/a2a/dc2/b62/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы день-ночь коричневый3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/682/a76/31f/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы день-ночь коричневый4\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/a62/133/558/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы день-ночь коричневый5\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"199\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 745 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-den-noch-korichnevyy\">рулонные шторы день-ночь коричневый</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">1 745 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"4246\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_199_mini\"
                       value=\"коричневый\"
                       data-count_size=\"15\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/79e/b27/6fb/thumb__36_16_0_0_crop.jpg\" alt=\"коричневый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/ca4/fc2/24f/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/2c8/80a/2b0/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/a2a/dc2/b62/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/682/a76/31f/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/a62/133/558/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>


            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>15</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"199\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-den-noch-korichnevyy\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"199\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"59\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-sharm\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/076/41a/43b/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы шарм\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/076/41a/43b/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы шарм2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/44f/316/968/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы шарм1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/f31/ff1/42c/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы шарм3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/56c/4e4/bdf/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы шарм4\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы шарм5\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"59\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">878 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-sharm\">рулонные шторы шарм</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">878 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3397\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_59_mini\"
                       value=\"серый\"
                       data-count_size=\"15\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/531/5a7/cf8/thumb__36_16_0_0_crop.jpg\" alt=\"серый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/076/41a/43b/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/44f/316/968/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/f31/ff1/42c/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/56c/4e4/bdf/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3398\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_59_mini\"
                       value=\"бежевый\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/f60/b8d/523/thumb__36_16_0_0_crop.jpg\" alt=\"бежевый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/ad2/246/033/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/a42/7fd/5a6/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/92c/c2e/06c/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3399\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_59_mini\"
                       value=\"лиловый\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/823/18a/a24/thumb__36_16_0_0_crop.jpg\" alt=\"лиловый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/da6/0e9/a4e/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/cbf/7d1/e57/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/d17/4af/e29/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>


            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>15</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"59\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-sharm\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"59\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"200\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-den-noch-trikolor\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/af2/48c/88b/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы день-ночь триколор\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/af2/48c/88b/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы день-ночь триколор2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/78f/585/17c/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы день-ночь триколор1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/470/856/fe4/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы день-ночь триколор3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/ea8/476/e72/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы день-ночь триколор4\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/a62/133/558/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы день-ночь триколор5\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"200\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 745 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-den-noch-trikolor\">рулонные шторы день-ночь триколор</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">1 745 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"4254\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_200_mini\"
                       value=\"серо-белый\"
                       data-count_size=\"15\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/531/5a7/cf8/thumb__36_16_0_0_crop.jpg\" alt=\"серо-белый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/af2/48c/88b/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/78f/585/17c/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/470/856/fe4/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/ea8/476/e72/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/a62/133/558/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"4256\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_200_mini\"
                       value=\"бежево-коричневый\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/a61/477/576/thumb__36_16_0_0_crop.jpg\" alt=\"бежево-коричневый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/c07/549/7ff/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/ff4/453/de5/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/0a7/ba6/8f0/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/edd/215/35f/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/a62/133/558/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>


            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>15</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"200\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnye-shtory-den-noch-trikolor\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"200\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"60\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-persiya\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/f46/f17/e47/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы персия\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/f46/f17/e47/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы персия2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/1b3/018/487/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы персия1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/09c/c70/5db/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы персия3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы персия4\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"60\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">851 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-persiya\">рулонные шторы персия</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">851 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3643\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_60_mini\"
                       value=\"латте\"
                       data-count_size=\"15\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/46c/911/d67/thumb__36_16_0_0_crop.jpg\" alt=\"латте\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/f46/f17/e47/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/1b3/018/487/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/09c/c70/5db/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3644\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_60_mini\"
                       value=\"трюфель\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/8f5/3d0/b58/thumb__36_16_0_0_crop.jpg\" alt=\"трюфель\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/e1b/27d/775/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/83e/417/8b3/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/e4a/888/afa/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3645\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_60_mini\"
                       value=\"белый\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/688/b61/42d/thumb__36_16_0_0_crop.jpg\" alt=\"белый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/b37/daa/517/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/7e6/e46/bb3/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/2e5/094/d2b/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>


            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>15</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"60\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-persiya\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"60\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"34\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-akcent\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/161/a4c/db2/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы акцент\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/161/a4c/db2/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы акцент2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/ed0/f26/ef6/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы акцент1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/aba/a9f/462/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы акцент3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/d1e/5f9/4d0/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы акцент4\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы акцент5\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"34\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">885 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-akcent\">рулонные шторы акцент</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">885 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3673\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_34_mini\"
                       value=\"беж\"
                       data-count_size=\"15\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/f60/b8d/523/thumb__36_16_0_0_crop.jpg\" alt=\"беж\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/161/a4c/db2/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/ed0/f26/ef6/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/aba/a9f/462/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/d1e/5f9/4d0/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3674\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_34_mini\"
                       value=\"сталь\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/e8a/2b4/068/thumb__36_16_0_0_crop.jpg\" alt=\"сталь\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/db3/54b/a80/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/448/950/217/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/901/30e/4be/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"4513\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_34_mini\"
                       value=\"миндаль\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/f59/e98/771/thumb__36_16_0_0_crop.jpg\" alt=\"миндаль\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/da9/f90/221/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/bf9/610/098/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/923/71a/427/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>


            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>15</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"34\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-akcent\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"34\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"66\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-damask\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/1cc/529/0be/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы дамаск\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/1cc/529/0be/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы дамаск2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/818/96a/20d/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы дамаск1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/e37/ade/242/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы дамаск3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/d0f/b2d/5e4/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы дамаск4\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы дамаск5\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"66\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">858 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-damask\">рулонные шторы дамаск</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">858 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3345\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_66_mini\"
                       value=\"ваниль\"
                       data-count_size=\"15\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/1d6/9d2/4f1/thumb__36_16_0_0_crop.jpg\" alt=\"ваниль\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/1cc/529/0be/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/818/96a/20d/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/e37/ade/242/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/d0f/b2d/5e4/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3344\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_66_mini\"
                       value=\"экрю\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/458/a38/5fd/thumb__36_16_0_0_crop.jpg\" alt=\"экрю\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/e77/e28/17e/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/7e0/cc1/409/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/661/90a/f09/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3346\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_66_mini\"
                       value=\"серебро\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/eed/829/85e/thumb__36_16_0_0_crop.jpg\" alt=\"серебро\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/8c3/788/89a/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/18a/ad4/6d9/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/612/ab7/bb8/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>


            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>15</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"66\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-damask\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"66\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"69\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-melanzh\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/220/dd0/b72/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы меланж\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/220/dd0/b72/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы меланж2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/397/e64/d2a/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы меланж1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/bb9/f5f/2a1/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы меланж3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы меланж4\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"69\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 046 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-melanzh\">рулонные шторы меланж</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">1 046 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3363\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_69_mini\"
                       value=\"бежевый\"
                       data-count_size=\"15\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/f60/b8d/523/thumb__36_16_0_0_crop.jpg\" alt=\"бежевый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/220/dd0/b72/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/397/e64/d2a/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/bb9/f5f/2a1/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3364\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_69_mini\"
                       value=\"синий\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/d9b/edf/933/thumb__36_16_0_0_crop.jpg\" alt=\"синий\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/333/474/98a/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/7e7/7bd/0ae/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/02f/281/113/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>


            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>15</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"69\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-melanzh\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"69\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"77\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-galaxy\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/e58/78a/9d9/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы гелакси\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/e58/78a/9d9/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы гелакси2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/f44/c3a/abb/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы гелакси1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/4ef/672/af4/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы гелакси3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/2fd/074/1ba/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы гелакси4\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы гелакси5\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"77\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">1 026 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-galaxy\">рулонные шторы гелакси</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">1 026 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3334\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_77_mini\"
                       value=\"пудра\"
                       data-count_size=\"15\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/fe9/398/602/thumb__36_16_0_0_crop.jpg\" alt=\"пудра\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/e58/78a/9d9/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/f44/c3a/abb/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/4ef/672/af4/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/2fd/074/1ba/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3335\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_77_mini\"
                       value=\"какао\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/24e/6c3/09b/thumb__36_16_0_0_crop.jpg\" alt=\"какао\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/8c1/55f/6cf/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/fe5/7a5/681/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/635/b43/d84/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/a15/387/59d/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3336\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_77_mini\"
                       value=\"платина\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/117/8a4/112/thumb__36_16_0_0_crop.jpg\" alt=\"платина\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/be1/e1a/f14/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/e7d/b75/1f9/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/5e3/ee7/6d3/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/15c/961/4a2/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3337\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_77_mini\"
                       value=\"серебро\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/1b5/5fb/30e/thumb__36_16_0_0_crop.jpg\" alt=\"серебро\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/a47/20d/011/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/63a/6ca/e1c/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/3d4/8a9/ce4/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/8ad/48f/e29/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>

                    <div class=\"catalog-colors__more-counter\">+2</div>

            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>15</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"77\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-galaxy\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"77\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"25\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/kruzhevo\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/1e9/11b/1f7/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы кружево\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/1e9/11b/1f7/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы кружево2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/503/b20/37c/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы кружево1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/e7c/de7/e80/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы кружево3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы кружево4\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"25\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">871 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/kruzhevo\">рулонные шторы кружево</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">871 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3992\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_25_mini\"
                       value=\"белый\"
                       data-count_size=\"15\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/b15/d7d/9cd/thumb__36_16_0_0_crop.jpg\" alt=\"белый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/1e9/11b/1f7/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/503/b20/37c/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/e7c/de7/e80/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>


            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>15</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"25\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/kruzhevo\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"25\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"50\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-dozhd\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/15f/f0d/e30/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы дождь\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/15f/f0d/e30/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы дождь2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/ee3/396/74e/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы дождь1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/7d9/465/e6a/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы дождь3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/bd4/a4f/1e1/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы дождь4\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/cb5/927/c34/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы дождь5\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы дождь6\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"50\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">742 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-dozhd\">рулонные шторы дождь</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">742 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3360\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_50_mini\"
                       value=\"серый\"
                       data-count_size=\"10\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/531/5a7/cf8/thumb__36_16_0_0_crop.jpg\" alt=\"серый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/15f/f0d/e30/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/ee3/396/74e/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/7d9/465/e6a/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/bd4/a4f/1e1/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/cb5/927/c34/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3361\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_50_mini\"
                       value=\"белый\"
                       data-count_size=\"10\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/688/b61/42d/thumb__36_16_0_0_crop.jpg\" alt=\"белый\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/929/a49/72a/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/fb6/8bf/ac7/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/d4b/cf6/0ca/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>


            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>10</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"50\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/rulonnaya-shtora-dozhd\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"50\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t

\t\t\t\t\t
\t\t\t\t\t<li class=\"catalog-grid__item\">
\t\t\t\t\t\t
\t\t\t\t\t

\t<div class=\"tile-item       js-ga\"
\t\t data-id=\"56\" data-category=\"рулонные шторы\"
\t>
\t\t<a class=\"tile-item__head\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/gorizont\">
            \t\t\t<img class=\"tile-item__image lazyload\"
\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/9fb/f68/e85/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t alt=\"рулонные шторы горизонт\"
\t\t\t\t loading=\"lazy\"
\t\t\t>

\t\t\t\t\t\t\t<ul class=\"tile-item__gallery\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/9fb/f68/e85/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы горизонт2\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/97a/22f/e07/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы горизонт1\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/90d/c8d/a94/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы горизонт3\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/311/e63/ca7/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы горизонт4\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"tile-item__gallery-item\">
\t\t\t\t\t\t\t<img class=\"tile-item__gallery-image lazyload\"
\t\t\t\t\t\t\t\t src=\"https://domlegrand.com/storage/app/uploads/public/583/531/4ce/thumb__314_365_0_0_crop.png\"
\t\t\t\t\t\t\t\t data-src=\"https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg\"
\t\t\t\t\t\t\t\t alt=\"рулонные шторы горизонт5\"
\t\t\t\t\t\t\t\t loading=\"lazy\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t
\t\t\t<button
\t\t\t\tclass=\"tile-item__in-wish trigger-favorites \"
\t\t\t\ttype=\"button\"
\t\t\t\tdata-id=\"56\"
\t\t\t>
\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t</button>
\t\t</a>

\t\t<div class=\"tile-item__body\">
\t\t\t<div class=\"tile-item__price\">
\t\t\t\t<strong class=\"tile-item__price-current\">799 ₽</strong>

\t\t\t\t\t\t\t</div>

\t\t\t<a class=\"tile-item__title\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/gorizont\">рулонные шторы горизонт</a>

\t\t\t<!--\t\t\tналичие опций-->
\t\t\t\t<div class=\"tile-item__cut\">
\t\t\t\t\t<div class=\"tile-item__body tile-item__cut-body\">

\t\t\t\t\t\t<div class=\"tile-item__cut-row\">
\t\t\t\t\t\t\t<div class=\"tile-item__cut-info\">
\t\t\t\t\t\t\t\t<div class=\"tile-item__price\">
\t\t\t\t\t\t\t\t\t<strong class=\"tile-item__price-current\">799 ₽</strong>
<!--todo discount-->
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>


\t\t\t\t\t\t\t\t    <div class=\"catalog-colors \">

                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3689\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_56_mini\"
                       value=\"трюфель\"
                       data-count_size=\"15\"
                       checked                >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/bce/e21/668/thumb__36_16_0_0_crop.jpg\" alt=\"трюфель\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/9fb/f68/e85/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/97a/22f/e07/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/90d/c8d/a94/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/311/e63/ca7/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3690\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_56_mini\"
                       value=\"ваниль\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/1d6/9d2/4f1/thumb__36_16_0_0_crop.jpg\" alt=\"ваниль\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/2a0/af8/1e0/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/f8b/787/a2e/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/d77/07e/04d/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3691\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_56_mini\"
                       value=\"мятный\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/5c7/cf6/25e/thumb__36_16_0_0_crop.jpg\" alt=\"мятный\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/ef0/f5c/ddc/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/34e/ecd/9a0/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/7ad/6c3/036/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>
                    <label class=\"catalog-colors__item js_trigger-color_tile \"
                   data-option=\"3692\"
                   data-out_stock=\"0\"
            >
                <input class=\"catalog-colors__element\"
                       type=\"radio\"
                       name=\"color_56_mini\"
                       value=\"миндаль\"
                       data-count_size=\"15\"
                                       >
                <div class=\"catalog-colors__value\">
                    <img src=\"https://domlegrand.com/storage/app/uploads/public/45b/625/e27/thumb__36_16_0_0_crop.jpg\" alt=\"миндаль\">
                </div>


                <div class=\"js_img-for-color hidden\">

                                                    https://domlegrand.com/storage/app/uploads/public/10a/830/63f/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/91d/b7c/17d/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/886/9f9/b83/thumb__314_365_0_0_crop.jpg

                        ~


                                                    https://domlegrand.com/storage/app/uploads/public/6db/838/7cf/thumb__314_365_0_0_crop.jpg



                                    </div>

                            </label>


            </div>


\t\t\t\t\t\t\t\t<div class=\"tile-item__sizes\">доступно размеров: <span>15</span></div>
\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"tile-item__tools\">
\t\t\t\t\t\t\t\t<button class=\"tile-item__tool trigger-compare \" data-id=\"56\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna/gorizont\"
\t\t\tdata-js=\"\"
\t\t>подробнее</a>
\t
\t

\t\t\t\t\t\t<a class=\"tile-item__view preview_product\" href=\"#\" data-id=\"56\">
\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1.33333em\" height=\"1em\" viewbox=\"0 0 20 15\" fill=\"none\">
\t\t\t<path d=\"m1 7.578s4.289 1 10.045 1s9.045 6.578 9.045 6.578-3.289 6.578-9.045 6.578s1 7.578 1 7.578z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t\t<path d=\"m12.512 7.58a2.468 2.468 0 11-4.935 0 2.468 2.468 0 014.935 0v0z\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t<span class=\"tile-item__view-title\">быстрый просмотр</span>
\t\t\t\t\t\t</a>

\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t\t</div>

\t</div>

\t\t\t\t\t</li>
\t\t\t\t\t\t\t</ul>

\t\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--center
                    btn-tile--upper
                    btn-tile--outline
                    btn-tile--primary
             catalog-grid__load hidden\"
\t\t\ttype=\"button\"
\t\t\t
\t\t\t\t\t\t\t\t>
\t\t\tдалее

\t\t\t\t\t</button>

\t


            <ul class=\"pagination\">



                                    <li> <a class=\"link active\" href=\"http://domlegrand.com/catalog/rulonnye-shtory-na-okna?filter%5binterier%5d=1&amp;page=1\" >1</a></li>

                                                            <li> <a class=\"link \" href=\"http://domlegrand.com/catalog/rulonnye-shtory-na-okna?filter%5binterier%5d=1&amp;page=2\" >2</a></li>
                                            <li> <a class=\"link \" href=\"http://domlegrand.com/catalog/rulonnye-shtory-na-okna?filter%5binterier%5d=1&amp;page=3\" >3</a></li>


                            <li><a href=\"http://domlegrand.com/catalog/rulonnye-shtory-na-okna?filter%5binterier%5d=1&amp;page=2\" class=\"link\"> &gt; </a></li>
                <li><a href=\"http://domlegrand.com/catalog/rulonnye-shtory-na-okna?filter%5binterier%5d=1&amp;page=3\" class=\"link\"> &gt;&gt; </a></li>
                    </ul>

        <div class=\"pagination_mobile\">
            <p class=\"title\">страница №</p>
            <div class=\"form-select js-form-select\">
                <div class=\"form-select__content\">
                    <div class=\"form-select__head js-form-select-head\">
                        <div class=\"form-select__value js-form-select-value\"></div>
                        <div class=\"form-select__head-icon\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"2.076923em\" height=\"1em\" viewbox=\"0 0 27 13\" fill=\"none\">
\t\t\t<path d=\"m1 1l13 11.781l25.007 1\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</div>
                    </div>

                    <div class=\"form-select__body\">
                        <ul class=\"form-select__list\">
                                                        <li class=\"form-select__list-item js-form-select-item\" data-value=\"1\">
                                1
                            </li>
                                                        <li class=\"form-select__list-item js-form-select-item\" data-value=\"2\">
                                2
                            </li>
                                                        <li class=\"form-select__list-item js-form-select-item\" data-value=\"3\">
                                3
                            </li>
                                                    </ul>
                    </div>
                </div>

                        selected
                    class=\"selected\"
                    data-url=\"http://domlegrand.com/catalog/rulonnye-shtory-na-okna?filter%5binterier%5d=1&amp;page=1\"
                    >
                    1
                    </option>
                                        <option

                    class=\"\"
                    data-url=\"http://domlegrand.com/catalog/rulonnye-shtory-na-okna?filter%5binterier%5d=1&amp;page=2\"
                    >
                    2
                    </option>
                                        <option

                    class=\"\"
                    data-url=\"http://domlegrand.com/catalog/rulonnye-shtory-na-okna?filter%5binterier%5d=1&amp;page=3\"
                    >
                    3
                    </option>
                                    </select>
            </div>
        </div>

\t\t</div>
\t</section>







\t<section class=\"section-text\">
\t\t<div class=\"section-text__content\">
\t\t\t
\t\t\t<div class=\"section-text__head\">
\t\t\t\t<h2 class=\"section-text__title\">рулонные шторы</h2>
\t\t\t</div>

\t\t\t<div class=\"section-text__body\">
\t\t\t\t<p>интернет-магазин рулонных штор в москве: купить рулонные шторы на пластиковые окна в москве недорого в интернет-магазине производителя рулонных штор: готовые рулонные шторы крепятся на окна без сверления. рулонные шторы в москве - каталог рулонных штор с ценами и фото.&nbsp;</p>

<p>осуществляем доставку рулонных штор по москве и россии.</p>
\t\t\t</div>

\t\t</div>
\t</section>





\t<div id=\"item-view-overlay\" class=\"page-overlay      js-overlay\">
\t\t<div class=\"page-overlay__content\">
\t\t\t
\t\t\t<div
\t\t\t\tclass=\"page-overlay__body\"
\t\t\t\tstyle=\"
\t\t\t\t\t--overlay-width: 1000px;\t\t\t\t\t\t\t\t\t\t\t\t\t\t\"
\t\t\t>
\t\t\t\t\t\t\t\t\t<button class=\"page-overlay__close js-toggle-overlay \" type=\"button\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t
\t\t\t\t        <div id=\"partial_productpreview\">



\t\t\t\t
\t\t\t\t\t\t
\t

\t
\t
\t

\t\t\t
\t\t
\t\t\t\t\t\t\t\t
\t\t
\t\t
\t\t
\t
\t

\t
\t
\t<section class=\"item-card                         item-card--inner
             item-card__info\">
\t\t<div class=\"item-card__body\">
\t\t\t<div class=\"item-card__content\">

\t\t\t\t<div class=\"grid-top\">
\t\t\t\t\t
\t\t\t\t\t\t\t
\t\t\t\t<p class=\"item-card__title h2 first-fix\"></p>
\t\t
\t\t<div class=\"item-card__id item-card__article \" data-id=\"\" data-category=\"\"></div>

\t\t<div class=\"item-card__rating \">
\t\t\t<div class=\"item-card__rating-icon\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 17 16\" fill=\"none\">
\t\t\t<path d=\"m8.5 0l2.627 5.266 5.873.85-4.25 4.096l13.753 16 8.5 13.266 3.247 16l1.003-5.788l0 6.116l5.873-.85l8.5 0z\" />
\t\t</svg>
\t
</div>

\t\t\t<div class=\"item-card__rating-value\">0</div>
\t\t\t<a class=\"item-card__rating-reviews \" href=\"#item-review\">0 отзывов</a>
\t\t</div>
\t
\t\t\t\t</div>



\t\t\t\t<div class=\"item-card__gallery grid-left\">

\t\t\t\t\t<div class=\"carousel big\">
\t\t\t\t\t\t<div class=\"swiper-container carousel__body js-carousel-item-card\">
\t\t\t\t\t\t\t<div class=\"swiper-wrapper carousel__list\">
\t\t\t\t\t\t\t\t                                \t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<button class=\"carousel__prev swiper-button-prev js-carousel-item-card-prev\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t\t\t\t<button class=\"carousel__next swiper-button-next js-carousel-item-card-next\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m1 14.9629l7.27 7.98089l1 0.99989\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>

\t\t\t\t\t\t\t<div class=\"carousel__pagination swiper-pagination js-carousel-item-card-pagination\"></div>

\t\t\t\t\t\t\t<div class=\"item-card__tools \">
\t\t\t\t\t\t\t\t<button class=\"item-card__tools-item  trigger-favorites\" data-id=\"\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.6623,15.4759
\t\t\tc-0.4676-0.4679-1.0228-0.8391-1.6339-1.0923c27.4173,14.1303,26.7623,14,26.1008,14c-0.6615,0-1.3165,0.1303-1.9276,0.3836
\t\t\tc-0.6111,0.2532-1.1663,0.6244-1.6339,1.0923l-0.97,0.97l-0.97-0.97c-0.4679-0.4677-1.0234-0.8386-1.6346-1.0917
\t\t\tc-0.6113-0.253-1.2664-0.3832-1.928-0.383c-1.3361,0.0002-2.6173,0.5313-3.5619,1.4762c12.5302,16.4224,11.9997,17.7039,12,19.04
\t\t\tc0.0003,1.3361,0.5313,2.6174,1.4763,3.5619l0.97,0.97l7.123,7.123l7.123-7.123l0.97-0.97
\t\t\tc0.4679-0.4676,0.8391-1.0228,1.0923-1.6339c0.2533-0.6111,0.3836-1.2661,0.3836-1.9276c0-0.6615-0.1303-1.3165-0.3836-1.9276
\t\t\tc-0.2532-0.6111-0.6244-1.1663-1.0923-1.6339v15.4759z\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>

<!--\t\t\t\t\t\t\t\t<button class=\"item-card__tools-item share-trigger\" data-value=\"\" type=\"button\">-->
<!--\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m29.892 15.415a2.915 2.915 0 11-5.83 0 2.915 2.915 0 015.83 0h0zm18.23 22.218a2.915 2.915 0 11-5.83 0 2.915 2.915 0 015.83 0h0zm29.892 29.02a2.916 2.916 0 11-5.831 0 2.916 2.916 0 015.831 0h0zm17.832 23.686l6.638 3.868m24.46 16.883l-6.628 3.868\"/>
\t\t</svg>
\t
-->
<!--\t\t\t\t\t\t\t\t</button>-->

\t\t\t\t\t\t\t\t<button class=\"item-card__tools-item  trigger-compare\" data-id=\"\" type=\"button\">
\t\t\t\t\t\t\t\t\t
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" d=\"m14,16.7439h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,21.3649h11.9921\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m14,25.9869h7.0811\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m23.3921,25.9869h10.398\"/>
\t\t\t<path stroke-width=\"1.5\" d=\"m28.5913,31.1859v-10.398\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>

\t\t\t\t\t<div class=\"item-card__gallery-foot\">
\t\t\t\t\t\t<div class=\"carousel small\">
\t\t\t\t\t\t\t<div class=\"swiper-container carousel__body js-carousel-item-card-thumbnail\">
\t\t\t\t\t\t\t\t<div class=\"swiper-wrapper carousel__list\">

\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t<button class=\"carousel__prev swiper-button-prev js-carousel-item-card-thumbnail-prev\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t\t\t\t\t<button class=\"carousel__next swiper-button-next js-carousel-item-card-thumbnail-next\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m1 14.9629l7.27 7.98089l1 0.99989\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>

\t\t\t\t</div>

\t\t\t\t<div class=\"grid-right\">
\t\t\t\t\t\t\t<div class=\"item-card__price\">
\t\t\t<strong class=\"item-card__price-current\">
\t\t\t\t<span class=\"price_from \">от </span>
\t\t\t\t<span class=\"product_price\"> 0</span> ₽
\t\t\t</strong>

\t\t\t\t\t</div>
\t

\t\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t<div class=\"item-card__status item-card__status--in-stock js-out-stock \" data-type=\"is-stock\">
\t\t\t\t\t\t\t<div class=\"item-card__status-icon\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 15 16\" fill=\"none\">
\t\t\t<path d=\"m14.526 8a7.263 7.263 0 11-.001 8 7.263 7.263 0 0114.526 8zm-8.1 3.845l5.389-5.389a.469.469 0 000-.663l-.663-.663a.469.469 0 00-.663 0l6.092 9.526 4.038 7.474a.469.469 0 00-.663 0l-.663.663a.469.469 0 000 .663l3.046 3.046a.469.469 0 00.663 0h.005z\" />
\t\t</svg>
\t
</div>
\t\t\t\t\t\t\t<div class=\"item-card__status-title\">есть в наличии</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t<div class=\"item-card__status item-card__status--extended-time js-out-stock hidden\" data-type=\"out-stock\">
\t\t\t\t\t\t\t<div class=\"item-card__status-icon\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 14 15\" fill=\"none\">
\t\t\t<path fill-rule=\"evenodd\" clip-rule=\"evenodd\" d=\"m14 7.5a7 7 0 1 1-14 0 7 7 0 0 1 14 0zm-6-5v7h6v-7h2zm0 10v-2h6v2h2z\" />
\t\t</svg>
\t
</div>
\t\t\t\t\t\t\t<div class=\"item-card__status-title\">нет в наличии</div>
\t\t\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t
\t\t\t\t\t
\t\t\t\t\t\t\t\t\t\t\t<div class=\"item-card__options\">
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t<div class=\"item-card__options-item\">
\t\t\t\t\t\t\t\t
\t
\t<div class=\"form-number                         form-number--count
             js-form-number\">
\t\t\t\t\t<div class=\"form-number__lock\"></div>
\t\t
\t\t<button class=\"form-number__minus js-form-number-minus\" type=\"button\">
\t\t\t<div class=\"form-number__icon\"></div>
\t\t</button>

\t\t<div class=\"form-number__text\">
\t\t\t<span class=\"form-number__value js-form-number-value hidden\">
\t\t\t\t
\t\t\t</span>
\t\t\t<input class=\"js-form-number-input form-number__value js-form-number-value form-number--input \"
\t\t\t\t   type=\"text\"
\t\t\t\t   min=\"1\"
\t\t\t\t   value=\"1\"
\t\t\t\t   onkeypress=\"return (event.charcode >= 48 && event.charcode <= 57 && /^\\d{0,4}$/.test(this.value));\"
\t\t\t>
\t\t\t<span class=\"form-number__unit\">шт</span>
\t\t</div>

\t\t<button class=\"form-number__plus js-form-number-plus\" type=\"button\">
\t\t\t<div class=\"form-number__icon\"></div>
\t\t</button>
\t</div>

\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<div class=\"item-card__options-item\">
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>
\t\t\t\t\t
\t\t\t\t\t
\t\t\t\t\t<div class=\"item-card__actions\">

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--upper
                    btn-tile--outline
                    btn-tile--primary
             item-card__actions-call js-toggle-overlay js-buy-one-click\"
\t\t\thref=\"#buy-one-click\"
\t\t\tdata-js=\"\"
\t\t>быстрый заказ</a>
\t
\t

\t\t\t\t\t\t

\t\t\t\t\t\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             item-card__actions-add product\"
\t\t\ttype=\"button\"
\t\t\tdata-id=
\t\t\t\t\t\t\t\t>
\t\t\tдобавить в корзину

\t\t\t\t\t\t\t<div class=\"btn-tile__icon\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-miterlimit=\"10\" d=\"m16.687,13l14,16.582v29.12c0,0.475,0.1887,0.9306,0.5246,1.2664
\t\t\tc0.1663,0.1663,0.3637,0.2983,0.581,0.3883s0.4502,0.1363,0.6854,0.1363h12.538c0.475,0,0.9306-0.1887,1.2664-0.5246
\t\t\tc29.9313,30.0506,30.12,29.595,30.12,29.12v16.582l27.434,13h16.687z\"/>
\t\t\t<path stroke-width=\"1.5\" stroke-miterlimit=\"10\" d=\"m14.1841,16.323h15.607\"/>
\t\t\t<path stroke-width=\"1.5\" stroke-miterlimit=\"10\" d=\"m25.643,20.165c0,0.9501-0.3774,1.8614-1.0493,2.5332
\t\t\tc-0.6718,0.6719-1.583,1.0493-2.5332,1.0493c-0.9501,0-1.8613-0.3774-2.5332-1.0493c-0.6718-0.6718-1.0493-1.5831-1.0493-2.5332\"/>
\t\t</svg>
\t
</div>
\t\t\t\t\t</button>

\t
\t\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--upper
                    btn-tile--outline
                    btn-tile--primary
             item-card__actions-fast js-toggle-overlay js-buy-one-click\"
\t\t\thref=\"#buy-one-click\"
\t\t\tdata-js=\"\"
\t\t>купить в один клик</a>
\t
\t
\t\t\t\t\t\t\t\t\t\t\t</div>

\t\t\t\t\t<div class=\"item-card__mobile-actions\">
\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--solid
                    btn-tile--primary-light
             js-toggle-overlay\"
\t\t\thref=\"#item-card-delivery\"
\t\t\tdata-js=\"\"
\t\t>доставка</a>
\t
\t

\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--solid
                    btn-tile--primary-light
             js-toggle-overlay\"
\t\t\thref=\"#item-card-pay\"
\t\t\tdata-js=\"\"
\t\t>оплата</a>
\t
\t
\t\t\t\t\t</div>

\t\t\t\t\t
\t\t\t\t\t

\t\t\t\t\t\t\t\t\t</div>
\t\t\t</div>
\t\t</div>

\t\t\t\t\t<div class=\"item-card__foot\">
\t\t\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             item-card__link\"
\t\t\ttype=\"button\"
\t\t\t
\t\t\t\t\t\t\t\t>
\t\t\tподробнее о товаре

\t\t\t\t\t</button>

\t
\t\t\t</div>
\t\t\t</section>

\t
\t
\t

\t

\t<div id=\"item-card-delivery\" class=\"page-overlay      js-overlay\">
\t\t<div class=\"page-overlay__content\">
\t\t\t
\t\t\t<div
\t\t\t\tclass=\"page-overlay__body\"
\t\t\t\tstyle=\"
\t\t\t\t\t--overlay-width: 640px;\t\t\t\t\t\t\t\t\t\t\t\t\t\t\"
\t\t\t>
\t\t\t\t\t\t\t\t\t<button class=\"page-overlay__close js-toggle-overlay \" type=\"button\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t
\t\t\t\t\t<div class=\"item-order-info\">
\t\t\t\t<p class=\"h2\">доставка</p>
\t\t<p><b>доставка осуществляется от 2 до 5 дней:</b></p>
\t\t<ul>
\t\t\t<li>москва и московская область <b>от 120 рублей</b></li>
\t\t\t<li>санкт-петербург и ленинградская область <b>от 120 рублей</b></li>
\t\t\t<li>по всей россии <b>от 120 рублей</b></li>
\t\t</ul>
\t\t<p><b>способы доставки:</b></p>
\t\t<ul>
\t\t\t<li>курьером до двери</li>
\t\t\t<li>в пункт выдачи. доставка осуществляется в течение 2-5 дней с момента заказа.</li>
\t\t</ul>
\t\t<small>
\t\t\t<p>стоимость доставки рассчитывается в корзине при оформлении заказа и указана из расчета веса до 5 кг.
\t\t\t\tесли вес вашего заказа превышает 5 кг (более 6-ти предметов) или длину 1,6 метра, то стоимость доставки,
\t\t\t\tуказанная в корзине, является ориентировочной.</p>
\t\t\t<p>внимательно осматривайте посылку перед получением доставки в пункте выдачи или у курьера!</p>
\t\t</small>
\t\t<p>для точного расчета стоимости и сроков доставки свяжитесь с нами по телефону:
\t\t\t<b><a href=\"tel:\"></a></b>
\t\t</p>
\t
\t</div>

\t\t\t</div>

\t\t\t<div class=\"page-overlay__blur js-toggle-overlay \"></div>
\t\t</div>
\t</div>


\t

\t<div id=\"item-card-pay\" class=\"page-overlay      js-overlay\">
\t\t<div class=\"page-overlay__content\">
\t\t\t
\t\t\t<div
\t\t\t\tclass=\"page-overlay__body\"
\t\t\t\tstyle=\"
\t\t\t\t\t--overlay-width: 640px;\t\t\t\t\t\t\t\t\t\t\t\t\t\t\"
\t\t\t>
\t\t\t\t\t\t\t\t\t<button class=\"page-overlay__close js-toggle-overlay \" type=\"button\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t
\t\t\t\t\t<div class=\"item-order-info\">
\t\t\t\t<p class=\"h2\">оплата</p>
\t\t<p>оплатить заказ можно несколькими способами:</p>
\t\t<p><b>курьеру</b> – наличными или банковской картой</p>
\t\t<p><b>на сайте</b> – с помощью электронного кошелька или банковской карты (используется сервис «яндекс деньги»)</p>
\t\t<p><b>безналичный расчет</b> – оплата по выставленному счету на расчетный счет магазина.</p>
\t\t<small>
\t\t\t<p>все персональные данные на сайте защищаются шифрованием и не передаются третьим лицам.
\t\t\t\tпри совершении онлайн-покупки данные банковских карт и платежных систем не сохраняются и не передаются!</p>
\t\t</small>
\t
\t</div>

\t\t\t</div>

\t\t\t<div class=\"page-overlay__blur js-toggle-overlay \"></div>
\t\t</div>
\t</div>


\t

\t<div id=\"item-card-size-help\" class=\"page-overlay      js-overlay\">
\t\t<div class=\"page-overlay__content\">
\t\t\t
\t\t\t<div
\t\t\t\tclass=\"page-overlay__body\"
\t\t\t\tstyle=\"
\t\t\t\t\t--overlay-width: 750px;\t\t\t\t\t\t\t\t\t\t\t\t\t\t\"
\t\t\t>
\t\t\t\t\t\t\t\t\t<button class=\"page-overlay__close js-toggle-overlay \" type=\"button\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t
\t\t\t\t\t<div class=\"item-order-info\">
\t\t\t\t<p class=\"title\">как выбрать размер</p>
\t\t<div class=\"overlay-size-help\">
\t\t\t\t\t</div>
\t
\t</div>

\t\t\t</div>

\t\t\t<div class=\"page-overlay__blur js-toggle-overlay \"></div>
\t\t</div>
\t</div>


        </div>

\t\t\t</div>

\t\t\t<div class=\"page-overlay__blur js-toggle-overlay \"></div>
\t\t</div>
\t</div>


</div>

\t<div id=\"login\" class=\"page-overlay      js-overlay\">
\t\t<div class=\"page-overlay__content\">
\t\t\t
\t\t\t<div
\t\t\t\tclass=\"page-overlay__body\"
\t\t\t\tstyle=\"
\t\t\t\t\t--overlay-width: 560px;\t\t\t\t\t\t\t\t\t\t\t\t\t\t\"
\t\t\t>
\t\t\t\t\t\t\t\t\t<button class=\"page-overlay__close js-toggle-overlay \" type=\"button\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t
\t\t\t\t



\t<div class=\"auth-form js-form\">
\t\t<form class=\"auth-form__page auth-form__page--login js-form__page\" data-id=\"login\" action=\"/login\" data-method=\"phone\">
\t\t\t<p class=\"auth-form__title h3 first-fix\">войти</p>

\t\t\t<div class=\"auth-form__body\">
\t\t\t\t

\t<div class=\"form-phone js-login-phone js-toggle\">
\t\t
\t\t\t
\t<div class=\"form-select       js-form-select\">
\t\t<div class=\"form-select__content\">
\t\t\t<div class=\"form-select__head js-form-select-head\">
\t\t\t\t\t\t\t\t<div class=\"form-select__value js-form-select-value\"></div>
\t\t\t\t<div class=\"form-select__head-icon\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"2.076923em\" height=\"1em\" viewbox=\"0 0 27 13\" fill=\"none\">
\t\t\t<path d=\"m1 1l13 11.781l25.007 1\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</div>
\t\t\t</div>

\t\t\t<div class=\"form-select__body\">
\t\t\t\t<ul class=\"form-select__list\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"form-select__list-item js-form-select-item \"
\t\t\t\t\t\t\tdata-value=\"+7\"
\t\t\t\t\t\t\tdata-alias=\"\"
\t\t\t\t\t\t>
\t\t\t\t\t\t\t+7
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"form-select__list-item js-form-select-item \"
\t\t\t\t\t\t\tdata-value=\"+8\"
\t\t\t\t\t\t\tdata-alias=\"\"
\t\t\t\t\t\t>
\t\t\t\t\t\t\t+8
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"form-select__list-item js-form-select-item \"
\t\t\t\t\t\t\tdata-value=\"+9\"
\t\t\t\t\t\t\tdata-alias=\"\"
\t\t\t\t\t\t>
\t\t\t\t\t\t\t+9
\t\t\t\t\t\t</li>
\t\t\t\t\t
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t</div>
\t\t</div>

\t\t<select class=\"form-select__element\" name=\"phone-code\">
\t\t\t\t\t\t\t<option value=\"+7\" class=\"\">
\t\t\t\t\t+7
\t\t\t\t</option>
\t\t\t\t\t\t\t<option value=\"+8\" class=\"\">
\t\t\t\t\t+8
\t\t\t\t</option>
\t\t\t\t\t\t\t<option value=\"+9\" class=\"\">
\t\t\t\t\t+9
\t\t\t\t</option>
\t\t\t
\t\t\t\t\t</select>

\t\t\t</div>

\t\t

\t<div class=\"form-input        \">
\t\t<input class=\"form-input__element \"
\t\t\t   type=\"\"
\t\t\t   name=\"phone\"
\t\t\t   placeholder=\"\"
\t\t\t   value=\"\"
\t\t\t   \t\t\t   required\t\t\t   data-input-mask=\"(000)000-00-00\"\t\t\t   pattern=\"^[(][0-9]{3}[)][0-9]{3}[\\-][0-9]{2}[\\-][0-9]{2}$\"\t\t>

\t\t
\t\t\t\t\t\t<strong class=\"form-error hidden\">обязательное поле</strong>

\t\t\t</div>

\t</div>

\t\t\t\t

\t<div class=\"form-input js-login-email js-toggle hidden       \">
\t\t<input class=\"form-input__element \"
\t\t\t   type=\"email\"
\t\t\t   name=\"email\"
\t\t\t   placeholder=\"ваш e-mail\"
\t\t\t   value=\"\"
\t\t\t   \t\t\t   required\t\t\t   \t\t\t   \t\t>

\t\t
\t\t\t\t\t\t<strong class=\"form-error hidden\">обязательное поле</strong>

\t\t\t</div>


\t\t\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             js-form__submit\"
\t\t\ttype=\"button\"
\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-href=\"login-check-code\"
\t\t\t\t\t\t\t\t\t>
\t\t\tполучить код

\t\t\t\t\t</button>

\t

\t\t\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary-opp
             auth_form__btn-secondary js-login-toggle\"
\t\t\ttype=\"button\"
\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-toggle=\"email\"
\t\t\t\t\t\t\t\t\t>
\t\t\tвойти с помощью e-mail

\t\t\t\t\t</button>

\t


\t\t\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary-opp
             js-form__link\"
\t\t\ttype=\"button\"
\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-href=\"register-phone\"
\t\t\t\t\t\t\t\t\t>
\t\t\tзарегистрироваться

\t\t\t\t\t</button>

\t
\t\t\t</div>
\t\t</form>

\t\t<form class=\"auth-form__page auth-form__page--phone-code js-form__page\" data-id=\"login-check-code\" action=\"/login-check-code\">
\t\t\t<p class=\"auth-form__title h3 first-fix\">введите код</p>
\t\t\t<a class=\"auth-form__back js-form__link\" data-href=\"login\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</a>

\t\t\t<div class=\"auth-form__body\">
\t\t\t\t

\t<div class=\"form-input        \">
\t\t<input class=\"form-input__element \"
\t\t\t   type=\"\"
\t\t\t   name=\"code\"
\t\t\t   placeholder=\"\"
\t\t\t   value=\"\"
\t\t\t   \t\t\t   \t\t\t   \t\t\t   \t\t>

\t\t
\t\t\t</div>

\t\t\t\t<p class=\"auth-form__subtext js-timer-block\">отправить код повторно можно через <span class=\"auth-form__timer\">10</span> сек.</p>
\t\t\t\t<a class=\"auth-form__sublink auth-form__subtext auth-form__no-code hidden\">не приходит код</a>

\t\t\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             js-form__submit\"
\t\t\ttype=\"button\"
\t\t\t
\t\t\t\t\t\t\t\t>
\t\t\tвойти

\t\t\t\t\t</button>

\t
\t\t\t\t
\t\t
\t<label class=\"form-checkbox                         form-checkbox--size-sm
            \">
\t\t<input class=\"form-checkbox__element\"
\t\t\t   type=\"checkbox\"
\t\t\t   name=\"policy\"
\t\t\t   checked=\"checked\"\t\t\t   required\t\t>

\t\t<div class=\"form-checkbox__row\">
\t\t\t<span class=\"form-checkbox__icon\"></span>
\t\t\t<span class=\"form-checkbox__title\">согласен с условиями правил пользования торговой площадкой и правилами возврата</span>
\t\t</div>

\t\t\t</label>



\t\t\t</div>
\t\t</form>

\t\t<div class=\"auth-form__page auth-form__page--register js-form__page\" data-id=\"register-phone\" action=\"/register-phone\">
\t\t\t<p class=\"auth-form__title h3 first-fix\">регистрация</p>

\t\t\t<div class=\"auth-form__body\">
\t\t\t\t<div class=\"tabs-box js-tab-root\">
\t\t\t\t\t<div class=\"auth-form__navbar\">
\t\t\t\t\t\t
\t
\t<nav class=\"section-nav                         section-nav--center
            \">
\t\t
\t\t<div class=\"carousel section-nav__content js-tab-nav\">
\t\t\t<div class=\"swiper-container carousel__body section-nav__row js-carousel-section-nav\">
\t\t\t\t<div class=\"swiper-wrapper carousel__list\">
\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item \">
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t<a class=\"section-nav__link \" href=\"#auth-form__physic\">физлица</a>

\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t<div class=\"swiper-slide carousel__item \">
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t<a class=\"section-nav__link \" href=\"#auth-form__company\">юрлица</a>

\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t</div>
\t\t\t</div>
\t\t</div>

\t</nav>

\t\t\t\t\t</div>

\t\t\t\t\t<form id=\"auth-form__physic\" class=\"tabs-box__content js-tab-content\" data-uridical=\"false\">
\t\t\t\t\t\t<div class=\"auth-form__body\">
\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"uridical\" class=\"hidden\">
\t\t\t\t\t\t\t
\t\t\t\t\t\t\t

\t<div class=\"form-input        \">
\t\t<input class=\"form-input__element \"
\t\t\t   type=\"\"
\t\t\t   name=\"name\"
\t\t\t   placeholder=\"ваше имя*\"
\t\t\t   value=\"\"
\t\t\t   \t\t\t   required\t\t\t   \t\t\t   \t\t>

\t\t
\t\t\t\t\t\t<strong class=\"form-error hidden\">обязательное поле</strong>

\t\t\t</div>

\t\t\t\t\t\t\t

\t<div class=\"form-phone \">
\t\t
\t\t\t
\t<div class=\"form-select       js-form-select\">
\t\t<div class=\"form-select__content\">
\t\t\t<div class=\"form-select__head js-form-select-head\">
\t\t\t\t\t\t\t\t<div class=\"form-select__value js-form-select-value\"></div>
\t\t\t\t<div class=\"form-select__head-icon\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"2.076923em\" height=\"1em\" viewbox=\"0 0 27 13\" fill=\"none\">
\t\t\t<path d=\"m1 1l13 11.781l25.007 1\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</div>
\t\t\t</div>

\t\t\t<div class=\"form-select__body\">
\t\t\t\t<ul class=\"form-select__list\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"form-select__list-item js-form-select-item \"
\t\t\t\t\t\t\tdata-value=\"+7\"
\t\t\t\t\t\t\tdata-alias=\"\"
\t\t\t\t\t\t>
\t\t\t\t\t\t\t+7
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"form-select__list-item js-form-select-item \"
\t\t\t\t\t\t\tdata-value=\"+8\"
\t\t\t\t\t\t\tdata-alias=\"\"
\t\t\t\t\t\t>
\t\t\t\t\t\t\t+8
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"form-select__list-item js-form-select-item \"
\t\t\t\t\t\t\tdata-value=\"+9\"
\t\t\t\t\t\t\tdata-alias=\"\"
\t\t\t\t\t\t>
\t\t\t\t\t\t\t+9
\t\t\t\t\t\t</li>
\t\t\t\t\t
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t</div>
\t\t</div>

\t\t<select class=\"form-select__element\" name=\"phone-code\">
\t\t\t\t\t\t\t<option value=\"+7\" class=\"\">
\t\t\t\t\t+7
\t\t\t\t</option>
\t\t\t\t\t\t\t<option value=\"+8\" class=\"\">
\t\t\t\t\t+8
\t\t\t\t</option>
\t\t\t\t\t\t\t<option value=\"+9\" class=\"\">
\t\t\t\t\t+9
\t\t\t\t</option>
\t\t\t
\t\t\t\t\t</select>

\t\t\t</div>

\t\t

\t<div class=\"form-input        \">
\t\t<input class=\"form-input__element \"
\t\t\t   type=\"\"
\t\t\t   name=\"phone\"
\t\t\t   placeholder=\"\"
\t\t\t   value=\"\"
\t\t\t   \t\t\t   required\t\t\t   data-input-mask=\"(000)000-00-00\"\t\t\t   pattern=\"^[(][0-9]{3}[)][0-9]{3}[\\-][0-9]{2}[\\-][0-9]{2}$\"\t\t>

\t\t
\t\t\t\t\t\t<strong class=\"form-error hidden\">обязательное поле</strong>

\t\t\t</div>

\t</div>

\t\t\t\t\t\t\t

\t<div class=\"form-input        \">
\t\t<input class=\"form-input__element \"
\t\t\t   type=\"email\"
\t\t\t   name=\"email\"
\t\t\t   placeholder=\"ваш e-mail*\"
\t\t\t   value=\"\"
\t\t\t   \t\t\t   required\t\t\t   \t\t\t   \t\t>

\t\t
\t\t\t\t\t\t<strong class=\"form-error hidden\">обязательное поле</strong>

\t\t\t</div>


\t\t\t\t\t\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             js-form__submit\"
\t\t\ttype=\"button\"
\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-href=\"register-phone-code\"
\t\t\t\t\t\t\t\t\t>
\t\t\tполучить sms

\t\t\t\t\t</button>

\t

\t\t\t\t\t\t\t
\t\t
\t<label class=\"form-checkbox                         form-checkbox--size-sm
            \">
\t\t<input class=\"form-checkbox__element\"
\t\t\t   type=\"checkbox\"
\t\t\t   name=\"confidential\"
\t\t\t   checked=\"checked\"\t\t\t   required\t\t>

\t\t<div class=\"form-checkbox__row\">
\t\t\t<span class=\"form-checkbox__icon\"></span>
\t\t\t<span class=\"form-checkbox__title\">нажимая кнопку «получить sms» вы соглашаетесь с политикой конфиденциальности</span>
\t\t</div>

\t\t\t</label>

\t\t\t\t\t\t\t
\t\t
\t<label class=\"form-checkbox                         form-checkbox--size-sm
            \">
\t\t<input class=\"form-checkbox__element\"
\t\t\t   type=\"checkbox\"
\t\t\t   name=\"not_remember\"
\t\t\t   \t\t\t   \t\t>

\t\t<div class=\"form-checkbox__row\">
\t\t\t<span class=\"form-checkbox__icon\"></span>
\t\t\t<span class=\"form-checkbox__title\">чужой компьютер</span>
\t\t</div>

\t\t\t</label>

\t\t\t\t\t\t\t
\t\t
\t<label class=\"form-checkbox                         form-checkbox--size-sm
            \">
\t\t<input class=\"form-checkbox__element\"
\t\t\t   type=\"checkbox\"
\t\t\t   name=\"mailing\"
\t\t\t   \t\t\t   \t\t>

\t\t<div class=\"form-checkbox__row\">
\t\t\t<span class=\"form-checkbox__icon\"></span>
\t\t\t<span class=\"form-checkbox__title\">получать эксклюзивные скидки в sms-рассылке</span>
\t\t</div>

\t\t\t</label>


\t\t\t\t\t\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary-opp
             js-form__link\"
\t\t\ttype=\"button\"
\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-href=\"login\"
\t\t\t\t\t\t\t\t\t>
\t\t\tвойти

\t\t\t\t\t</button>

\t
\t\t\t\t\t\t</div>
\t\t\t\t\t</form>

\t\t\t\t\t<form id=\"auth-form__company\" class=\"tabs-box__content js-tab-content\" data-uridical=\"true\">
\t\t\t\t\t\t<div class=\"auth-form__body\">
\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"uridical\" class=\"hidden\" checked>

\t\t\t\t\t\t\t

\t<div class=\"form-input        \">
\t\t<input class=\"form-input__element \"
\t\t\t   type=\"\"
\t\t\t   name=\"name\"
\t\t\t   placeholder=\"ваше имя*\"
\t\t\t   value=\"\"
\t\t\t   \t\t\t   required\t\t\t   \t\t\t   \t\t>

\t\t
\t\t\t\t\t\t<strong class=\"form-error hidden\">обязательное поле</strong>

\t\t\t</div>

\t\t\t\t\t\t\t

\t<div class=\"form-input        \">
\t\t<input class=\"form-input__element \"
\t\t\t   type=\"\"
\t\t\t   name=\"surname\"
\t\t\t   placeholder=\"ваша фамилия*\"
\t\t\t   value=\"\"
\t\t\t   \t\t\t   required\t\t\t   \t\t\t   \t\t>

\t\t
\t\t\t\t\t\t<strong class=\"form-error hidden\">обязательное поле</strong>

\t\t\t</div>

\t\t\t\t\t\t\t

\t<div class=\"form-phone \">
\t\t
\t\t\t
\t<div class=\"form-select       js-form-select\">
\t\t<div class=\"form-select__content\">
\t\t\t<div class=\"form-select__head js-form-select-head\">
\t\t\t\t\t\t\t\t<div class=\"form-select__value js-form-select-value\"></div>
\t\t\t\t<div class=\"form-select__head-icon\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"2.076923em\" height=\"1em\" viewbox=\"0 0 27 13\" fill=\"none\">
\t\t\t<path d=\"m1 1l13 11.781l25.007 1\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</div>
\t\t\t</div>

\t\t\t<div class=\"form-select__body\">
\t\t\t\t<ul class=\"form-select__list\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"form-select__list-item js-form-select-item \"
\t\t\t\t\t\t\tdata-value=\"+7\"
\t\t\t\t\t\t\tdata-alias=\"\"
\t\t\t\t\t\t>
\t\t\t\t\t\t\t+7
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"form-select__list-item js-form-select-item \"
\t\t\t\t\t\t\tdata-value=\"+8\"
\t\t\t\t\t\t\tdata-alias=\"\"
\t\t\t\t\t\t>
\t\t\t\t\t\t\t+8
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"form-select__list-item js-form-select-item \"
\t\t\t\t\t\t\tdata-value=\"+9\"
\t\t\t\t\t\t\tdata-alias=\"\"
\t\t\t\t\t\t>
\t\t\t\t\t\t\t+9
\t\t\t\t\t\t</li>
\t\t\t\t\t
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t</div>
\t\t</div>

\t\t<select class=\"form-select__element\" name=\"phone-code\">
\t\t\t\t\t\t\t<option value=\"+7\" class=\"\">
\t\t\t\t\t+7
\t\t\t\t</option>
\t\t\t\t\t\t\t<option value=\"+8\" class=\"\">
\t\t\t\t\t+8
\t\t\t\t</option>
\t\t\t\t\t\t\t<option value=\"+9\" class=\"\">
\t\t\t\t\t+9
\t\t\t\t</option>
\t\t\t
\t\t\t\t\t</select>

\t\t\t</div>

\t\t

\t<div class=\"form-input        \">
\t\t<input class=\"form-input__element \"
\t\t\t   type=\"\"
\t\t\t   name=\"phone\"
\t\t\t   placeholder=\"\"
\t\t\t   value=\"\"
\t\t\t   \t\t\t   required\t\t\t   data-input-mask=\"(000)000-00-00\"\t\t\t   pattern=\"^[(][0-9]{3}[)][0-9]{3}[\\-][0-9]{2}[\\-][0-9]{2}$\"\t\t>

\t\t
\t\t\t\t\t\t<strong class=\"form-error hidden\">обязательное поле</strong>

\t\t\t</div>

\t</div>

\t\t\t\t\t\t\t

\t<div class=\"form-input        \">
\t\t<input class=\"form-input__element \"
\t\t\t   type=\"email\"
\t\t\t   name=\"email\"
\t\t\t   placeholder=\"ваш e-mail*\"
\t\t\t   value=\"\"
\t\t\t   \t\t\t   required\t\t\t   \t\t\t   \t\t>

\t\t
\t\t\t\t\t\t<strong class=\"form-error hidden\">обязательное поле</strong>

\t\t\t</div>

\t\t\t\t\t\t\t

\t<div class=\"form-input        \">
\t\t<input class=\"form-input__element js-dadata-inn\"
\t\t\t   type=\"\"
\t\t\t   name=\"inn\"
\t\t\t   placeholder=\"инн вашей компании*\"
\t\t\t   value=\"\"
\t\t\t   \t\t\t   required\t\t\t   \t\t\t   \t\t>

\t\t
\t\t\t\t\t\t<strong class=\"form-error hidden\">обязательное поле</strong>

\t\t\t</div>


\t\t\t\t\t\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             js-form__submit\"
\t\t\ttype=\"button\"
\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-href=\"register-phone-code\"
\t\t\t\t\t\t\t\t\t>
\t\t\tполучить sms

\t\t\t\t\t</button>

\t

\t\t\t\t\t\t\t
\t\t
\t<label class=\"form-checkbox                         form-checkbox--size-sm
            \">
\t\t<input class=\"form-checkbox__element\"
\t\t\t   type=\"checkbox\"
\t\t\t   name=\"confidential\"
\t\t\t   checked=\"checked\"\t\t\t   required\t\t>

\t\t<div class=\"form-checkbox__row\">
\t\t\t<span class=\"form-checkbox__icon\"></span>
\t\t\t<span class=\"form-checkbox__title\">нажимая кнопку «получить sms» вы соглашаетесь с политикой конфиденциальности</span>
\t\t</div>

\t\t\t</label>

\t\t\t\t\t\t\t
\t\t
\t<label class=\"form-checkbox                         form-checkbox--size-sm
            \">
\t\t<input class=\"form-checkbox__element\"
\t\t\t   type=\"checkbox\"
\t\t\t   name=\"mailing\"
\t\t\t   \t\t\t   \t\t>

\t\t<div class=\"form-checkbox__row\">
\t\t\t<span class=\"form-checkbox__icon\"></span>
\t\t\t<span class=\"form-checkbox__title\">получать эксклюзивные скидки в sms-рассылке</span>
\t\t</div>

\t\t\t</label>


\t\t\t\t\t\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary-opp
             js-form__link\"
\t\t\ttype=\"button\"
\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-href=\"login\"
\t\t\t\t\t\t\t\t\t>
\t\t\tвойти

\t\t\t\t\t</button>

\t
\t\t\t\t\t\t</div>
\t\t\t\t\t</form>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>

\t\t<form class=\"auth-form__page auth-form__page--phone-code js-form__page\" data-id=\"register-phone-code\" action=\"/register-phone-code\">
\t\t\t<p class=\"auth-form__title h3 first-fix\">введите код</p>
\t\t\t<a class=\"auth-form__back js-form__link\" data-href=\"register-phone\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"0.5625em\" height=\"1em\" viewbox=\"0 0 9 16\" fill=\"none\">
\t\t\t<path d=\"m7.87 1.1l1.6 8.082l6.27 6.981\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</a>

\t\t\t<div class=\"auth-form__body\">
\t\t\t\t

\t<div class=\"form-input        \">
\t\t<input class=\"form-input__element \"
\t\t\t   type=\"\"
\t\t\t   name=\"code\"
\t\t\t   placeholder=\"\"
\t\t\t   value=\"\"
\t\t\t   \t\t\t   \t\t\t   \t\t\t   \t\t>

\t\t
\t\t\t</div>

\t\t\t\t<p class=\"auth-form__subtext js-timer-block\">отправить код повторно можно через <span class=\"auth-form__timer\">10</span> сек.</p>
\t\t\t\t<a class=\"auth-form__sublink auth-form__subtext auth-form__no-code hidden\">не приходит код</a>

\t\t\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             js-form__submit\"
\t\t\ttype=\"button\"
\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-href=\"register-complete\"
\t\t\t\t\t\t\t\t\t>
\t\t\tзарегистироваться

\t\t\t\t\t</button>

\t

\t\t\t\t
\t\t
\t<label class=\"form-checkbox                         form-checkbox--size-sm
            \">
\t\t<input class=\"form-checkbox__element\"
\t\t\t   type=\"checkbox\"
\t\t\t   name=\"policy\"
\t\t\t   checked=\"checked\"\t\t\t   required\t\t>

\t\t<div class=\"form-checkbox__row\">
\t\t\t<span class=\"form-checkbox__icon\"></span>
\t\t\t<span class=\"form-checkbox__title\">согласен с условиями правил пользования торговой площадкой и правилами возврата</span>
\t\t</div>

\t\t\t</label>


\t\t\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary-opp
             auth_form__btn-secondary js-form__link js-reg-method-toggle\"
\t\t\ttype=\"button\"
\t\t\t
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tdata-toggle=\"email\"
\t\t\t\t\t\t\t\t\t>
\t\t\tполучить код с помощью email

\t\t\t\t\t</button>

\t
\t\t\t</div>
\t\t</form>

\t\t\t\t<div class=\"auth-form__page auth-form__page--complete js-form__page\" data-id=\"register-complete\">
\t\t\t<p class=\"auth-form__head-title h3 first-fix\">регистрация<br>прошла успешно</p>

\t\t\t<div class=\"auth-form__body\">
\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--wide
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             \"
\t\t\thref=\"https://domlegrand.com/profile\"
\t\t\tdata-js=\"\"
\t\t>перейти в личный кабинет</a>
\t
\t
\t\t\t</div>
\t\t</div>
\t</div>

\t\t\t</div>

\t\t\t<div class=\"page-overlay__blur js-toggle-overlay \"></div>
\t\t</div>
\t</div>



\t<div id=\"popup-callback\" class=\"page-overlay      js-overlay\">
\t\t<div class=\"page-overlay__content\">
\t\t\t
\t\t\t<div
\t\t\t\tclass=\"page-overlay__body\"
\t\t\t\tstyle=\"
\t\t\t\t\t--overlay-width: 560px;\t\t\t\t\t\t\t\t\t\t\t\t\t\t\"
\t\t\t>
\t\t\t\t\t\t\t\t\t<button class=\"page-overlay__close js-toggle-overlay \" type=\"button\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t
\t\t\t\t

\t<form class=\"form-buy-one-click\" data-request=\"callbackform::onformsubmit\" data-request-loading=\".loading-form\">
\t\t<div class=\"loading-form\">
    <p class=\"text\">подождите, идет отправка...</p>
</div>\t\t<input name=\"_token\" type=\"hidden\" value=\"k6uec2p9al13luwdo7ikuo8xzvtzsd1mi5isnepd\">
\t\t<div id=\"callbackform_forms_flash\"></div>

\t\t<div class=\"form-buy-one-click__head\">
\t\t\t<p class=\"form-buy-one-click__title h3 first-fix\">оставить заявку на обратный звонок</p>
\t\t</div>

\t\t<div class=\"form-buy-one-click__body\">
\t\t\t<div class=\"form-buy-one-click__field\">
\t\t\t\t

\t<div class=\"form-input        \">
\t\t<input class=\"form-input__element \"
\t\t\t   type=\"\"
\t\t\t   name=\"name\"
\t\t\t   placeholder=\"ваше имя\"
\t\t\t   value=\"\"
\t\t\t   \t\t\t   \t\t\t   \t\t\t   \t\t>

\t\t
\t\t\t</div>

\t\t\t</div>

\t\t\t<div class=\"form-buy-one-click__field\">
\t\t\t\t

\t<div class=\"form-phone \">
\t\t
\t\t\t
\t<div class=\"form-select       js-form-select\">
\t\t<div class=\"form-select__content\">
\t\t\t<div class=\"form-select__head js-form-select-head\">
\t\t\t\t\t\t\t\t<div class=\"form-select__value js-form-select-value\"></div>
\t\t\t\t<div class=\"form-select__head-icon\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"2.076923em\" height=\"1em\" viewbox=\"0 0 27 13\" fill=\"none\">
\t\t\t<path d=\"m1 1l13 11.781l25.007 1\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</div>
\t\t\t</div>

\t\t\t<div class=\"form-select__body\">
\t\t\t\t<ul class=\"form-select__list\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"form-select__list-item js-form-select-item \"
\t\t\t\t\t\t\tdata-value=\"+7\"
\t\t\t\t\t\t\tdata-alias=\"\"
\t\t\t\t\t\t>
\t\t\t\t\t\t\t+7
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"form-select__list-item js-form-select-item \"
\t\t\t\t\t\t\tdata-value=\"+8\"
\t\t\t\t\t\t\tdata-alias=\"\"
\t\t\t\t\t\t>
\t\t\t\t\t\t\t+8
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"form-select__list-item js-form-select-item \"
\t\t\t\t\t\t\tdata-value=\"+9\"
\t\t\t\t\t\t\tdata-alias=\"\"
\t\t\t\t\t\t>
\t\t\t\t\t\t\t+9
\t\t\t\t\t\t</li>
\t\t\t\t\t
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t</div>
\t\t</div>

\t\t<select class=\"form-select__element\" name=\"phone-code\">
\t\t\t\t\t\t\t<option value=\"+7\" class=\"\">
\t\t\t\t\t+7
\t\t\t\t</option>
\t\t\t\t\t\t\t<option value=\"+8\" class=\"\">
\t\t\t\t\t+8
\t\t\t\t</option>
\t\t\t\t\t\t\t<option value=\"+9\" class=\"\">
\t\t\t\t\t+9
\t\t\t\t</option>
\t\t\t
\t\t\t\t\t</select>

\t\t\t</div>

\t\t

\t<div class=\"form-input        \">
\t\t<input class=\"form-input__element \"
\t\t\t   type=\"\"
\t\t\t   name=\"phone\"
\t\t\t   placeholder=\"\"
\t\t\t   value=\"\"
\t\t\t   \t\t\t   required\t\t\t   data-input-mask=\"(000)000-00-00\"\t\t\t   pattern=\"^[(][0-9]{3}[)][0-9]{3}[\\-][0-9]{2}[\\-][0-9]{2}$\"\t\t>

\t\t
\t\t\t\t\t\t<strong class=\"form-error hidden\">обязательное поле</strong>

\t\t\t</div>

\t</div>

\t\t\t</div>

\t\t</div>

\t\t<div class=\"form-buy-one-click__foot\">
\t\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             form-buy-one-click__submit\"
\t\t\ttype=\"submit\"
\t\t\t
\t\t\t\t\t\t\t\t>
\t\t\tотправить

\t\t\t\t\t</button>

\t
\t\t</div>

\t\t

\t<div class=\"form-message js-form-message\">
\t\t<div class=\"form-message__content\">

\t\t\t<div class=\"form-message__body\">
\t\t\t\t<div class=\"form-message__icon\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 15 16\" fill=\"none\">
\t\t\t<path d=\"m14.526 8a7.263 7.263 0 11-.001 8 7.263 7.263 0 0114.526 8zm-8.1 3.845l5.389-5.389a.469.469 0 000-.663l-.663-.663a.469.469 0 00-.663 0l6.092 9.526 4.038 7.474a.469.469 0 00-.663 0l-.663.663a.469.469 0 000 .663l3.046 3.046a.469.469 0 00.663 0h.005z\" />
\t\t</svg>
\t
</div>

\t\t\t\t\t\t\t\t\t<p class=\"form-message__title h2 first-fix\">благодарим <br> за ваш отзыв!</p>
\t\t\t\t\t<p class=\"form-message__caption\">ваше мнение помогает нам улучшать качество товаров и услуг!</p>
\t\t\t\t
\t\t\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--current
                    btn-tile--upper
                    btn-tile--outline
                    btn-tile--center
             form-message__close js-close-form-message js-toggle-overlay hidden\"
\t\t\ttype=\"button\"
\t\t\t
\t\t\t\t\t\t\t\t>
\t\t\tок

\t\t\t\t\t</button>

\t
\t\t\t</div>

\t\t</div>
\t</div>

\t\t
    <div id=\"callbackform\" class=\"g-recaptcha\"
         data-sitekey=\"6ldkyaueaaaaahupiiqcf2t0vw_bxk-0dmekov-w\"
         data-theme=\"light\"
         data-type=\"image\"
         data-size=\"invisible\">

    </div>
\t</form>

\t\t\t</div>

\t\t\t<div class=\"page-overlay__blur js-toggle-overlay \"></div>
\t\t</div>
\t</div>



\t<div id=\"popup-partner\" class=\"page-overlay      js-overlay\">
\t\t<div class=\"page-overlay__content\">
\t\t\t
\t\t\t<div
\t\t\t\tclass=\"page-overlay__body\"
\t\t\t\tstyle=\"
\t\t\t\t\t--overlay-width: 560px;\t\t\t\t\t\t\t\t\t\t\t\t\t\t\"
\t\t\t>
\t\t\t\t\t\t\t\t\t<button class=\"page-overlay__close js-toggle-overlay \" type=\"button\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t
\t\t\t\t

\t<form class=\"form-buy-one-click\" data-request=\"partnerform::onformsubmit\" data-request-loading=\".loading-form\">
\t\t<div class=\"loading-form\">
    <p class=\"text\">подождите, идет отправка...</p>
</div>\t\t<input name=\"_token\" type=\"hidden\" value=\"k6uec2p9al13luwdo7ikuo8xzvtzsd1mi5isnepd\">
\t\t<div id=\"partnerform_forms_flash\"></div>

\t\t<div class=\"form-buy-one-click__head\">
\t\t\t<p class=\"form-buy-one-click__title h3 first-fix\">стань партнером</p>
\t\t</div>

\t\t<div class=\"form-buy-one-click__body\">
\t\t\t<div class=\"form-buy-one-click__field\">
\t\t\t\t

\t<div class=\"form-input        \">
\t\t<input class=\"form-input__element \"
\t\t\t   type=\"\"
\t\t\t   name=\"name\"
\t\t\t   placeholder=\"ваше имя\"
\t\t\t   value=\"\"
\t\t\t   \t\t\t   \t\t\t   \t\t\t   \t\t>

\t\t
\t\t\t</div>

\t\t\t</div>

\t\t\t<div class=\"form-buy-one-click__field\">
\t\t\t\t

\t<div class=\"form-phone \">
\t\t
\t\t\t
\t<div class=\"form-select       js-form-select\">
\t\t<div class=\"form-select__content\">
\t\t\t<div class=\"form-select__head js-form-select-head\">
\t\t\t\t\t\t\t\t<div class=\"form-select__value js-form-select-value\"></div>
\t\t\t\t<div class=\"form-select__head-icon\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"2.076923em\" height=\"1em\" viewbox=\"0 0 27 13\" fill=\"none\">
\t\t\t<path d=\"m1 1l13 11.781l25.007 1\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</div>
\t\t\t</div>

\t\t\t<div class=\"form-select__body\">
\t\t\t\t<ul class=\"form-select__list\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"form-select__list-item js-form-select-item \"
\t\t\t\t\t\t\tdata-value=\"+7\"
\t\t\t\t\t\t\tdata-alias=\"\"
\t\t\t\t\t\t>
\t\t\t\t\t\t\t+7
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"form-select__list-item js-form-select-item \"
\t\t\t\t\t\t\tdata-value=\"+8\"
\t\t\t\t\t\t\tdata-alias=\"\"
\t\t\t\t\t\t>
\t\t\t\t\t\t\t+8
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"form-select__list-item js-form-select-item \"
\t\t\t\t\t\t\tdata-value=\"+9\"
\t\t\t\t\t\t\tdata-alias=\"\"
\t\t\t\t\t\t>
\t\t\t\t\t\t\t+9
\t\t\t\t\t\t</li>
\t\t\t\t\t
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t</div>
\t\t</div>

\t\t<select class=\"form-select__element\" name=\"phone-code\">
\t\t\t\t\t\t\t<option value=\"+7\" class=\"\">
\t\t\t\t\t+7
\t\t\t\t</option>
\t\t\t\t\t\t\t<option value=\"+8\" class=\"\">
\t\t\t\t\t+8
\t\t\t\t</option>
\t\t\t\t\t\t\t<option value=\"+9\" class=\"\">
\t\t\t\t\t+9
\t\t\t\t</option>
\t\t\t
\t\t\t\t\t</select>

\t\t\t</div>

\t\t

\t<div class=\"form-input        \">
\t\t<input class=\"form-input__element \"
\t\t\t   type=\"\"
\t\t\t   name=\"phone\"
\t\t\t   placeholder=\"\"
\t\t\t   value=\"\"
\t\t\t   \t\t\t   required\t\t\t   data-input-mask=\"(000)000-00-00\"\t\t\t   pattern=\"^[(][0-9]{3}[)][0-9]{3}[\\-][0-9]{2}[\\-][0-9]{2}$\"\t\t>

\t\t
\t\t\t\t\t\t<strong class=\"form-error hidden\">обязательное поле</strong>

\t\t\t</div>

\t</div>

\t\t\t</div>

\t\t</div>

\t\t<div class=\"form-buy-one-click__foot\">
\t\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             form-buy-one-click__submit\"
\t\t\ttype=\"submit\"
\t\t\t
\t\t\t\t\t\t\t\t>
\t\t\tотправить заявку

\t\t\t\t\t</button>

\t
\t\t</div>

\t\t

\t<div class=\"form-message js-form-message\">
\t\t<div class=\"form-message__content\">

\t\t\t<div class=\"form-message__body\">
\t\t\t\t<div class=\"form-message__icon\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 15 16\" fill=\"none\">
\t\t\t<path d=\"m14.526 8a7.263 7.263 0 11-.001 8 7.263 7.263 0 0114.526 8zm-8.1 3.845l5.389-5.389a.469.469 0 000-.663l-.663-.663a.469.469 0 00-.663 0l6.092 9.526 4.038 7.474a.469.469 0 00-.663 0l-.663.663a.469.469 0 000 .663l3.046 3.046a.469.469 0 00.663 0h.005z\" />
\t\t</svg>
\t
</div>

\t\t\t\t\t\t\t\t\t<p class=\"form-message__title h2 first-fix\">благодарим <br> за ваш отзыв!</p>
\t\t\t\t\t<p class=\"form-message__caption\">ваше мнение помогает нам улучшать качество товаров и услуг!</p>
\t\t\t\t
\t\t\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--current
                    btn-tile--upper
                    btn-tile--outline
                    btn-tile--center
             form-message__close js-close-form-message js-toggle-overlay hidden\"
\t\t\ttype=\"button\"
\t\t\t
\t\t\t\t\t\t\t\t>
\t\t\tок

\t\t\t\t\t</button>

\t
\t\t\t</div>

\t\t</div>
\t</div>

\t</form>

\t\t\t</div>

\t\t\t<div class=\"page-overlay__blur js-toggle-overlay \"></div>
\t\t</div>
\t</div>



\t<div id=\"buy-one-click\" class=\"page-overlay      js-overlay\">
\t\t<div class=\"page-overlay__content\">
\t\t\t
\t\t\t<div
\t\t\t\tclass=\"page-overlay__body\"
\t\t\t\tstyle=\"
\t\t\t\t\t--overlay-width: 560px;\t\t\t\t\t\t\t\t\t\t\t\t\t\t\"
\t\t\t>
\t\t\t\t\t\t\t\t\t<button class=\"page-overlay__close js-toggle-overlay \" type=\"button\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 20 20\" fill=\"none\">
\t\t\t<path d=\"m18.414 1.414l-17 17m1.414 1.414l17 17\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</button>
\t\t\t\t
\t\t\t\t

\t<form class=\"form-buy-one-click\" data-request=\"buyoneclickform::onformsubmit\" data-request-loading=\".loading-form\">
\t\t<div class=\"loading-form\">
    <p class=\"text\">подождите, идет отправка...</p>
</div>\t\t<input name=\"_token\" type=\"hidden\" value=\"k6uec2p9al13luwdo7ikuo8xzvtzsd1mi5isnepd\">
\t\t<div id=\"buyoneclickform_forms_flash\"></div>

\t\t<div class=\"form-buy-one-click__head\">
\t\t\t<p class=\"form-buy-one-click__title h3 first-fix\">купить в один клик</p>
\t\t</div>

\t\t<div class=\"form-buy-one-click__body\">
\t\t\t<div class=\"form-buy-one-click__field\">
\t\t\t\t

\t<div class=\"form-input        \">
\t\t<input class=\"form-input__element \"
\t\t\t   type=\"\"
\t\t\t   name=\"name\"
\t\t\t   placeholder=\"ваше имя\"
\t\t\t   value=\"\"
\t\t\t   \t\t\t   \t\t\t   \t\t\t   \t\t>

\t\t
\t\t\t</div>

\t\t\t</div>

\t\t\t<div class=\"form-buy-one-click__field\">
\t\t\t\t

\t<div class=\"form-phone \">
\t\t
\t\t\t
\t<div class=\"form-select       js-form-select\">
\t\t<div class=\"form-select__content\">
\t\t\t<div class=\"form-select__head js-form-select-head\">
\t\t\t\t\t\t\t\t<div class=\"form-select__value js-form-select-value\"></div>
\t\t\t\t<div class=\"form-select__head-icon\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"2.076923em\" height=\"1em\" viewbox=\"0 0 27 13\" fill=\"none\">
\t\t\t<path d=\"m1 1l13 11.781l25.007 1\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</div>
\t\t\t</div>

\t\t\t<div class=\"form-select__body\">
\t\t\t\t<ul class=\"form-select__list\">
\t\t\t\t\t\t\t\t\t\t\t<li class=\"form-select__list-item js-form-select-item \"
\t\t\t\t\t\t\tdata-value=\"+7\"
\t\t\t\t\t\t\tdata-alias=\"\"
\t\t\t\t\t\t>
\t\t\t\t\t\t\t+7
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"form-select__list-item js-form-select-item \"
\t\t\t\t\t\t\tdata-value=\"+8\"
\t\t\t\t\t\t\tdata-alias=\"\"
\t\t\t\t\t\t>
\t\t\t\t\t\t\t+8
\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t<li class=\"form-select__list-item js-form-select-item \"
\t\t\t\t\t\t\tdata-value=\"+9\"
\t\t\t\t\t\t\tdata-alias=\"\"
\t\t\t\t\t\t>
\t\t\t\t\t\t\t+9
\t\t\t\t\t\t</li>
\t\t\t\t\t
\t\t\t\t\t\t\t\t\t</ul>
\t\t\t</div>
\t\t</div>

\t\t<select class=\"form-select__element\" name=\"phone-code\">
\t\t\t\t\t\t\t<option value=\"+7\" class=\"\">
\t\t\t\t\t+7
\t\t\t\t</option>
\t\t\t\t\t\t\t<option value=\"+8\" class=\"\">
\t\t\t\t\t+8
\t\t\t\t</option>
\t\t\t\t\t\t\t<option value=\"+9\" class=\"\">
\t\t\t\t\t+9
\t\t\t\t</option>
\t\t\t
\t\t\t\t\t</select>

\t\t\t</div>

\t\t

\t<div class=\"form-input        \">
\t\t<input class=\"form-input__element \"
\t\t\t   type=\"\"
\t\t\t   name=\"phone\"
\t\t\t   placeholder=\"\"
\t\t\t   value=\"\"
\t\t\t   \t\t\t   required\t\t\t   data-input-mask=\"(000)000-00-00\"\t\t\t   pattern=\"^[(][0-9]{3}[)][0-9]{3}[\\-][0-9]{2}[\\-][0-9]{2}$\"\t\t>

\t\t
\t\t\t\t\t\t<strong class=\"form-error hidden\">обязательное поле</strong>

\t\t\t</div>

\t</div>

\t\t\t</div>
\t\t\t
\t\t\t<div class=\"form-buy-one-click__field\">
\t\t\t\t

\t<div class=\"form-input        \">
\t\t<input class=\"form-input__element \"
\t\t\t   type=\"email\"
\t\t\t   name=\"email\"
\t\t\t   placeholder=\"email\"
\t\t\t   value=\"\"
\t\t\t   \t\t\t   \t\t\t   \t\t\t   \t\t>

\t\t
\t\t\t</div>

\t\t\t</div>
\t\t\t<input type=\"hidden\" name=\"product\" class=\"buy-one-click__product\" value=\"\">
\t\t</div>

\t\t<div class=\"form-buy-one-click__foot\">
\t\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary
             form-buy-one-click__submit\"
\t\t\ttype=\"submit\"
\t\t\t
\t\t\t\t\t\t\t\t>
\t\t\tотправить

\t\t\t\t\t</button>

\t
\t\t</div>

\t\t

\t<div class=\"form-message js-form-message\">
\t\t<div class=\"form-message__content\">

\t\t\t<div class=\"form-message__body\">
\t\t\t\t<div class=\"form-message__icon\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 15 16\" fill=\"none\">
\t\t\t<path d=\"m14.526 8a7.263 7.263 0 11-.001 8 7.263 7.263 0 0114.526 8zm-8.1 3.845l5.389-5.389a.469.469 0 000-.663l-.663-.663a.469.469 0 00-.663 0l6.092 9.526 4.038 7.474a.469.469 0 00-.663 0l-.663.663a.469.469 0 000 .663l3.046 3.046a.469.469 0 00.663 0h.005z\" />
\t\t</svg>
\t
</div>

\t\t\t\t\t\t\t\t\t<p class=\"form-message__title h2 first-fix\">благодарим <br> за ваш отзыв!</p>
\t\t\t\t\t<p class=\"form-message__caption\">ваше мнение помогает нам улучшать качество товаров и услуг!</p>
\t\t\t\t
\t\t\t\t
\t\t
\t
\t\t<button
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--current
                    btn-tile--upper
                    btn-tile--outline
                    btn-tile--center
             form-message__close js-close-form-message js-toggle-overlay hidden\"
\t\t\ttype=\"button\"
\t\t\t
\t\t\t\t\t\t\t\t>
\t\t\tок

\t\t\t\t\t</button>

\t
\t\t\t</div>

\t\t</div>
\t</div>

\t\t
    <div id=\"buyoneclickform\" class=\"g-recaptcha\"
         data-sitekey=\"6ldkyaueaaaaahupiiqcf2t0vw_bxk-0dmekov-w\"
         data-theme=\"light\"
         data-type=\"image\"
         data-size=\"invisible\">

    </div>
\t</form>

\t\t\t</div>

\t\t\t<div class=\"page-overlay__blur js-toggle-overlay \"></div>
\t\t</div>
\t</div>

        <div class=\"page-helpers-wrapper\">
  <div data-target=\"page-helpers\" class=\"page-helpers\">
    <div class=\"page-helpers__contacts\">
      <div data-target=\"page-helpers-inner\" class=\"page-helpers-inner\">
        <a href=\"tel:+7(495)191-00-26\" class=\"page-helpers-button-contact\">
          <div class=\"page-helpers-button-contact__content\">
            <div class=\"icon_b\">
              <svg
                  xmlns=\"http://www.w3.org/2000/svg\"
                  width=\"35\"
                  height=\"35\"
                  fill=\"none\"
                  viewbox=\"0 0 35 35\"
              >
                <g clip-path=\"url(#clip0_98:35075)\">
                  <path
                      fill=\"#fff\"
                      d=\"m17.4995 34.077c9.3886 0 16.9995-7.6113 16.9995-17.0005c34.499 7.6875 26.8881.076 17.4995.076s.5 7.6874.5 17.0765c0 9.3892 7.611 17.0005 16.9995 17.0005z\"
                  />
                </g>

              </svg>

\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 44 44\" fill=\"none\">
\t\t<path d=\"m30.65 27.313-1.995-1.33l26.88 24.8a.786.786 0 0 0-1.056.171l-1.097 1.412a.782.782 0 0 1-.998.207c-.745-.415-1.626-.805-3.57-2.75-1.943-1.947-2.335-2.825-2.75-3.57a.782.782 0 0 1 .207-.998l1.412-1.097a.786.786 0 0 0 .17-1.056l18.055 15.4l-1.367-2.05a.786.786 0 0 0-1.058-.238l-1.576.946c-.426.25-.74.656-.875 1.131-.43 1.572-.518 5.039 5.038 10.595 5.556 5.556 9.023 5.469 10.595 5.038.475-.136.88-.449 1.131-.875l.946-1.576a.786.786 0 0 0-.238-1.058zm23.241 15.793a5.282 5.282 0 0 1 5.276 5.276.31.31 0 1 0 .62 0 5.903 5.903 0 0 0-5.896-5.897.31.31 0 0 0 0 .621z\" />
\t\t<path d=\"m23.241 17.655a3.418 3.418 0 0 1 3.414 3.414.31.31 0 1 0 .62 0 4.04 4.04 0 0 0-4.034-4.035.31.31 0 0 0 0 .621z\" />
\t\t<path d=\"m23.241 19.517c.857.001 1.55.695 1.552 1.552a.31.31 0 1 0 .62 0 2.175 2.175 0 0 0-2.172-2.173.31.31 0 0 0 0 .621z\" />
\t</svg>
\t

            </div>

            <span class=\"page-helpers-button-contact__caption\">вызов</span>
          </div>
        </a>
        <a href=\"https://t.me/legrand_design\" target=\"_blank\" class=\"page-helpers-button-contact\">
          <div class=\"page-helpers-button-contact__content\">
            <svg
                xmlns=\"http://www.w3.org/2000/svg\"
                width=\"33\"
                height=\"33\"
                fill=\"none\"
                viewbox=\"0 0 33 33\"
            >
              <g clip-path=\"url(#clip0_98:35198)\">
                <path
                    fill=\"#fff\"
                    d=\"m16.5002.0762a16.424 16.424 0 1 0 16.424 16.424 16.4216 16.4216 0 0 0-4.8096-11.6144a16.4207 16.4207 0 0 0 16.5002.0762zm8.066 11.251-2.7 12.7c-.2.9-.735 1.119-1.483.7l-4.106-3.027-1.98 1.907a1.0365 1.0365 0 0 1-.828.4l.291-4.179 7.616-6.871c.331-.291-.073-.457-.51-.166l-9.4 5.921-4.059-1.265c-.881-.278-.9-.881.185-1.3l15.835-6.106c.735-.265 1.378.179 1.139 1.291v-.005z\"
                />
              </g>
              <defs>
                <clippath id=\"clip0_98:35198\">
                  <path
                      fill=\"#fff\"
                      d=\"m0 0h32.848v32.848h0z\"
                      transform=\"translate(.0762 .0762)\"
                  />
                </clippath>
              </defs>
            </svg>
            <span class=\"page-helpers-button-contact__caption\">telegram</span>
          </div>
        </a>
        <a href=\"https://wa.me/79104047843\" target=\"_blank\" class=\"page-helpers-button-contact\">
          <div class=\"page-helpers-button-contact__content\">
            <svg
              xmlns=\"http://www.w3.org/2000/svg\"
              width=\"33\"
              height=\"34\"
              fill=\"none\"
              viewbox=\"0 0 33 34\"
            >
              <g clip-path=\"url(#clip0_98:35146)\">
                <path
                  fill=\"#fff\"
                  d=\"m16.7994.076a16.1387 16.1387 0 0 0 .5988 16.1518a15.91 15.91 0 0 0 2.325 8.2968l0 33.076l8.9695-2.8498a16.228 16.228 0 0 0 21.8444-6.0922a16.2284 16.2284 0 0 0 33 16.1507a16.1371 16.1371 0 0 0-4.7711-11.3926a16.138 16.138 0 0 0 16.7994.076zm8.0558 22.1803a4.1843 4.1843 0 0 1-2.8649 1.8473c-.7597.04-.7817.5888-4.923-1.2105-4.1414-1.7993-6.6334-6.1766-6.8293-6.4575a7.9447 7.9447 0 0 1-1.5284-4.3063 4.5898 4.5898 0 0 1 1.5763-3.3687 1.5872 1.5872 0 0 1 1.1216-.4728c.3259-.005.5378-.01.7787 0s.5998-.05.9166.7837c.3169.8337 1.0636 2.8839 1.1596 3.0928a.751.751 0 0 1 .008.7197 2.814 2.814 0 0 1-.4388.6687c-.216.232-.4549.5188-.6478.6998-.2149.1999-.4398.4108-.2139.8296a12.3965 12.3965 0 0 0 2.1931 2.9269 11.3045 11.3045 0 0 0 3.2487 2.1591c.4059.2209.6488.2.8997-.0679s1.0786-1.1596 1.3695-1.5594c.2908-.3999.5647-.3219.9386-.172.3738.15 2.369 1.2196 2.7749 1.4405.4058.2209.6767.3338.7737.5078a3.399 3.399 0 0 1-.3129 1.9392z\"
                />
              </g>
              <defs>
                <clippath id=\"clip0_98:35146\">
                  <path
                    fill=\"#fff\"
                    d=\"m0 0h33v33h0z\"
                    transform=\"translate(0 .076)\"
                  />
                </clippath>
              </defs>
            </svg>
            <span class=\"page-helpers-button-contact__caption\">whatsapp</span>
          </div>
        </a>
      </div>
      <button data-target=\"button-chat\" class=\"page-helpers-button page-helpers-button--chat\">
        <svg
          class=\"page-helpers-button__icon page-helpers-button__icon--ask\"
          xmlns=\"http://www.w3.org/2000/svg\"
          width=\"40\"
          height=\"40\"
          fill=\"none\"
          viewbox=\"0 0 40 40\"
        >
          <g clip-path=\"url(#clip0_3:1044)\">
            <path
              fill=\"#fff\"
              d=\"m20.0657 0a19.454 19.454 0 0 0 .6337 19.432a19.213 19.213 0 0 0 3.91 11.683 15.3568 15.3568 0 0 1-4.213 6.86.945.945 0 0 0-.162 1.257c.1.138.333.468 1.74.468a19.3976 19.3976 0 0 0 2.916-.28 21.825 21.825 0 0 0 7.109-2.338 19.2356 19.2356 0 0 0 8.133 1.782 19.4309 19.4309 0 0 0 13.7405-5.6915 19.4316 19.4316 0 0 0 0-27.481a19.4317 19.4317 0 0 0 20.0667 0h-.001z\"
            />
            <g clip-path=\"url(#clip1_3:1044)\">
              <path
                fill=\"#aa0755\"
                d=\"m19.813 11c3.369 0 5.687 1.869 5.687 4.555a4.394 4.394 0 0 1-2.525 3.989c-1.563.907-2.093 1.572-2.093 2.722v.71h-3.118l-.027-.773a3.7403 3.7403 0 0 1 2.12-3.953c1.518-.907 2.156-1.482 2.156-2.6a2.181 2.181 0 0 0-2.413-1.928 2.2736 2.2736 0 0 0-1.6727.5943 2.272 2.272 0 0 0-.7353 1.6157h14c14.063 13.039 16.2 11 19.813 11zm17.45 26.4a1.929 1.929 0 1 1 1.923 1.85 1.883 1.883 0 0 1-1.3464-.5256 1.882 1.882 0 0 1-.5766-1.3254v.001z\"
              />
            </g>
          </g>
          <defs>
            <clippath id=\"clip0_3:1044\">
              <path fill=\"#fff\" d=\"m0 0h39.499v39.7h0z\" />
            </clippath>
            <clippath id=\"clip1_3:1044\">
              <path
                fill=\"#fff\"
                d=\"m0 0h11.5v17.25h0z\"
                transform=\"translate(14 11)\"
              />
            </clippath>
          </defs>
        </svg>
        <svg
          class=\"page-helpers-button__icon page-helpers-button__icon--close\"
          xmlns=\"http://www.w3.org/2000/svg\"
          width=\"39\"
          height=\"39\"
          fill=\"none\"
          viewbox=\"0 0 39 39\"
        >
          <circle cx=\"19\" cy=\"19\" r=\"19\" fill=\"#fff\" />
          <g
            stroke=\"#aa0755\"
            stroke-linecap=\"round\"
            stroke-linejoin=\"round\"
            stroke-width=\"2\"
            clip-path=\"url(#clip0_642:223748)\"
          >
            <path d=\"m27.4141 10.4139-17 17\" />
            <path d=\"m10.4141 10.4139 17 17\" />
          </g>
          <defs>
            <clippath id=\"clip0_642:223748\">
              <path
                fill=\"#fff\"
                d=\"m0 0h19.828v19.829h0z\"
                transform=\"translate(9 9)\"
              />
            </clippath>
          </defs>
        </svg>
      </button>
    </div>
    <button
      data-target=\"scroll-to-top\"
      class=\"page-helpers-button page-helpers-button--to-top\"
    >
      <svg
        xmlns=\"http://www.w3.org/2000/svg\"
        width=\"38\"
        height=\"38\"
        fill=\"none\"
        viewbox=\"0 0 38 38\"
      >
        <circle cx=\"19\" cy=\"19\" r=\"19\" fill=\"#f1f1f1\" />
        <path
          stroke=\"#000\"
          stroke-linecap=\"round\"
          stroke-linejoin=\"round\"
          stroke-opacity=\".4\"
          stroke-width=\"2\"
          d=\"m13.0625 21.375 5.9358-4.75 5.9392 4.75\"
        />
      </svg>
    </button>
  </div>
</div>


\t\t\t\t\t\t




\t<footer class=\"page-footer\">
\t\t<div class=\"page-footer__content\">

\t\t\t<div class=\"page-footer__grid\">

\t\t\t\t<div class=\"page-footer__item page-footer__contacts\">

\t\t\t\t\t<div class=\"footer-contacts\">
\t\t\t\t\t\t<div class=\"footer-contacts__item\">
\t\t\t\t\t\t\t<a class=\"footer-contacts__phone\"
\t\t\t\t\t\t\t   href=\"tel:+7(495)191-00-26\"
\t\t\t\t\t\t\t>
\t\t\t\t\t\t\t\t+7 (495) 191-00-26
\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t<div class=\"footer-contacts__item\">
\t\t\t\t\t\t\t<a class=\"footer-contacts__mail\" href=\"mailto:info@karnizov.net\">
\t\t\t\t\t\t\t\t<div class=\"footer-contacts__mail-title\">info@karnizov.net</div>
\t\t\t\t\t\t\t\t<div class=\"footer-contacts__mail-icon\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"1em\" height=\"1em\" viewbox=\"0 0 16 16\" fill=\"none\">
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m2.2 2h11.6c.385 0 .753.158 1.025.44.272.28.425.662.425 1.06v9c0 .398-.153.78-.425 1.06-.272.282-.64.44-1.025.44h2.2c-.385 0-.753-.158-1.025-.44a1.527 1.527 0 01.75 12.5v-9c0-.398.153-.78.425-1.06s1.815 2 2.2 2h0z\"/>
\t\t\t<path stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"m15.25 3.5l8 8.75.75 3.5\"/>
\t\t</svg>
\t
</div>
\t\t\t\t\t\t\t</a>

\t\t\t\t\t\t\t<div class=\"footer-contacts__time\">c 9:00 до 18:00</div>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t<div class=\"footer-contacts__item\">
\t\t\t\t\t\t\t
\t\t
\t\t\t
\t\t<a
\t\t\tclass=\"btn-tile                         btn-tile--size-md
                    btn-tile--upper
                    btn-tile--solid
                    btn-tile--primary-opp
             footer-contacts__call js-toggle-overlay\"
\t\t\thref=\"#popup-callback\"
\t\t\tdata-js=\"\"
\t\t>заказать звонок</a>
\t
\t
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>

\t\t\t\t</div>

\t\t\t\t<div class=\"page-footer__item page-footer__location\">

\t\t\t\t\t<div class=\"foooter-location\">
\t\t\t\t\t\t<p class=\"footer-location__address\">московская область, г. электросталь, строительный пер., д.10</p>

\t\t\t\t\t\t<ul class=\"footer-location__list\">
\t\t\t\t\t\t\t<li class=\"footer-location__list-item\">
\t\t\t<svg class=\"icon-raw\" width=\"0.75em\" height=\"1em\" viewbox=\"0 0 12 16\" fill=\"none\">
\t\t\t<path d=\"m10.243 2.757a6 6 0 000 7c0 4.667 6 8.667 6 8.667s6-4 6-8.667a6 6 0 00-1.757-4.243zm-1.749 5.91a3 3 0 11-4.988-3.333 3 3 0 014.988 3.333z\"/>
\t\t</svg>
\t
</li>

\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"footer-location__list-item\">
\t\t\t\t\t\t\t\t\t<a rel=\"nofollow\" class=\"footer-location__list-btn\" href=\"https://yandex.ru/maps/org/legrand/1110491757/?from=tabbar&amp;ll=38.486773%2c55.795413&amp;source=serp_navig&amp;z=17\" target=\"_blank\">яндекс. карты</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"footer-location__list-item\">
\t\t\t\t\t\t\t\t\t<a rel=\"nofollow\" class=\"footer-location__list-btn\" href=\"https://goo.gl/maps/1ubicworv5ctnvcq6\" target=\"_blank\">google maps</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>

\t\t\t\t</div>

\t\t\t\t<div class=\"page-footer__item page-footer__navbar\">
\t\t\t\t\t<div class=\"page-footer__spoilers\">
\t\t\t\t\t\t
\t\t
\t<div class=\"spoiler-box                         spoiler-box--light
                    spoiler-box--low
             js-spoiler\" >
\t\t<div class=\"spoiler-box__head js-spoiler-head\">
\t\t\t
\t\t\t<div class=\"spoiler-box__title\">для покупателей</div>

\t\t\t
\t\t\t<div class=\"spoiler-box__head-icon\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"2.076923em\" height=\"1em\" viewbox=\"0 0 27 13\" fill=\"none\">
\t\t\t<path d=\"m1 1l13 11.781l25.007 1\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</div>

\t\t</div>

\t\t<div class=\"spoiler-box__body js-spoiler-body\">
\t\t\t<div class=\"spoiler-box__content js-spoiler-content\">
\t\t\t\t\t<section class=\"footer-list-box \">
\t\t
\t\t<ul class=\"footer-list-box__list\">
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/delivery\">доставка</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/payment\">оплата</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/guarantees\">гарантии и возврат</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/shops\">адреса торговых точек</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t</ul>
\t</section>

\t\t\t</div>
\t\t</div>
\t</div>

\t\t\t\t\t\t
\t\t
\t<div class=\"spoiler-box                         spoiler-box--light
                    spoiler-box--low
             js-spoiler\" >
\t\t<div class=\"spoiler-box__head js-spoiler-head\">
\t\t\t
\t\t\t<div class=\"spoiler-box__title\">каталог</div>

\t\t\t
\t\t\t<div class=\"spoiler-box__head-icon\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"2.076923em\" height=\"1em\" viewbox=\"0 0 27 13\" fill=\"none\">
\t\t\t<path d=\"m1 1l13 11.781l25.007 1\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</div>

\t\t</div>

\t\t<div class=\"spoiler-box__body js-spoiler-body\">
\t\t\t<div class=\"spoiler-box__content js-spoiler-content\">
\t\t\t\t\t<section class=\"footer-list-box \">
\t\t
\t\t<ul class=\"footer-list-box__list\">
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor\">комплекты штор</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/catalog/shtory\">готовые шторы</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna\">рулонные шторы</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni\">текстиль для кухни</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/catalog/tyul\">тюль</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/catalog/karnizy\">карнизы</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/catalog/tekstil\">подушки</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/catalog/komplektuyushchie\">комплектующие</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t</ul>
\t</section>

\t\t\t</div>
\t\t</div>
\t</div>

\t\t\t\t\t\t
\t\t
\t<div class=\"spoiler-box                         spoiler-box--light
                    spoiler-box--low
             js-spoiler\" >
\t\t<div class=\"spoiler-box__head js-spoiler-head\">
\t\t\t
\t\t\t<div class=\"spoiler-box__title\">о нас</div>

\t\t\t
\t\t\t<div class=\"spoiler-box__head-icon\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"2.076923em\" height=\"1em\" viewbox=\"0 0 27 13\" fill=\"none\">
\t\t\t<path d=\"m1 1l13 11.781l25.007 1\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</div>

\t\t</div>

\t\t<div class=\"spoiler-box__body js-spoiler-body\">
\t\t\t<div class=\"spoiler-box__content js-spoiler-content\">
\t\t\t\t\t<section class=\"footer-list-box \">
\t\t
\t\t<ul class=\"footer-list-box__list\">
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/manufacture\">производство</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/history\">история</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/vacancies\">вакансии</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/team\">команда</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/requisites\">реквизиты</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/documentation\">документация</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/company-life\">жизнь компании</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t</ul>
\t</section>

\t\t\t</div>
\t\t</div>
\t</div>

\t\t\t\t\t\t
\t\t
\t<div class=\"spoiler-box                         spoiler-box--light
                    spoiler-box--low
             js-spoiler\" >
\t\t<div class=\"spoiler-box__head js-spoiler-head\">
\t\t\t
\t\t\t<div class=\"spoiler-box__title\">для бизнеса</div>

\t\t\t
\t\t\t<div class=\"spoiler-box__head-icon\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"2.076923em\" height=\"1em\" viewbox=\"0 0 27 13\" fill=\"none\">
\t\t\t<path d=\"m1 1l13 11.781l25.007 1\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</div>

\t\t</div>

\t\t<div class=\"spoiler-box__body js-spoiler-body\">
\t\t\t<div class=\"spoiler-box__content js-spoiler-content\">
\t\t\t\t\t<section class=\"footer-list-box \">
\t\t
\t\t<ul class=\"footer-list-box__list\">
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t <b>партнерам</b> \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t</ul>
\t</section>

\t\t\t</div>
\t\t</div>
\t</div>

\t\t\t\t\t\t
\t\t
\t<div class=\"spoiler-box                         spoiler-box--light
                    spoiler-box--low
             js-spoiler\" >
\t\t<div class=\"spoiler-box__head js-spoiler-head\">
\t\t\t
\t\t\t<div class=\"spoiler-box__title\">режим работы</div>

\t\t\t
\t\t\t<div class=\"spoiler-box__head-icon\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"2.076923em\" height=\"1em\" viewbox=\"0 0 27 13\" fill=\"none\">
\t\t\t<path d=\"m1 1l13 11.781l25.007 1\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</div>

\t\t</div>

\t\t<div class=\"spoiler-box__body js-spoiler-body\">
\t\t\t<div class=\"spoiler-box__content js-spoiler-content\">
\t\t\t\t\t<section class=\"footer-list-box \">
\t\t
\t\t<ul class=\"footer-list-box__list\">
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t <b>call-центр</b> \t\t\t\t\t\t <span>ежедневно с 9:00 до 18:00</span> \t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t <b>интернет-магазин</b> \t\t\t\t\t\t <span>ежедневно с 9:00 до 18:00</span> \t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t <b>доставка заказов</b> \t\t\t\t\t\t <span>ежедневно с 9:00 до 20:00</span> \t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t</ul>
\t</section>

\t\t\t</div>
\t\t</div>
\t</div>

\t\t\t\t\t\t
\t\t
\t<div class=\"spoiler-box                         spoiler-box--light
                    spoiler-box--low
             js-spoiler\" >
\t\t<div class=\"spoiler-box__head js-spoiler-head\">
\t\t\t
\t\t\t<div class=\"spoiler-box__title\">филиалы</div>

\t\t\t
\t\t\t<div class=\"spoiler-box__head-icon\">
\t\t\t<svg class=\"icon-raw icon-raw--stroke\" width=\"2.076923em\" height=\"1em\" viewbox=\"0 0 27 13\" fill=\"none\">
\t\t\t<path d=\"m1 1l13 11.781l25.007 1\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>
\t\t</svg>
\t
</div>

\t\t</div>

\t\t<div class=\"spoiler-box__body js-spoiler-body\">
\t\t\t<div class=\"spoiler-box__content js-spoiler-content\">
\t\t\t\t\t<section class=\"footer-list-box \">
\t\t
\t\t<ul class=\"footer-list-box__list\">
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t <b>санкт-петербург ул. салова, 46</b> \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t <b>новосибирск толмачёвское шоссе, д. 27 к2, оф. 17</b> \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t <b>казань ул. магистральная, 77 офис 3.1</b> \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t</ul>
\t</section>

\t\t\t</div>
\t\t</div>
\t</div>

\t\t\t\t\t</div>

\t\t\t\t\t<div class=\"page-footer__nav\">
\t\t\t\t\t\t\t<section class=\"footer-list-box \">
\t\t\t\t\t<p class=\"footer-list-box__title h3 first-fix\">информация</p>
\t\t
\t\t<ul class=\"footer-list-box__list\">
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/manufacture\">о нас</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/school\">студия дизайна</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/delivery\">доставка</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/payment\">оплата</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/guarantees\">гарантии</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/quality-department\">отдел качества</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t <b>раздел для бизнеса</b> \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/sitemap\">карта сайта</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t</ul>
\t</section>

\t\t\t\t\t</div>

\t\t\t\t\t<div class=\"page-footer__nav\">
\t\t\t\t\t\t\t<section class=\"footer-list-box \">
\t\t\t\t\t<p class=\"footer-list-box__title h3 first-fix\">продукция</p>
\t\t
\t\t<ul class=\"footer-list-box__list\">
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/catalog/gotovye-komplekty-shtor\">комплекты штор</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/catalog/shtory\">готовые шторы</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/catalog/rulonnye-shtory-na-okna\">рулонные шторы</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/catalog/tekstil-dlya-kukhni\">текстиль для кухни</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/catalog/tyul\">тюль</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/catalog/karnizy\">карнизы</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/catalog/tekstil\">подушки</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t<a class=\"footer-list-box__link\" href=\"https://domlegrand.com/catalog/komplektuyushchie\">комплектующие</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t</ul>
\t</section>

\t\t\t\t\t</div>

\t\t\t\t\t<div class=\"page-footer__nav\">
\t\t\t\t\t\t\t<section class=\"footer-list-box \">
\t\t\t\t\t<p class=\"footer-list-box__title h3 first-fix\">режим работы</p>
\t\t
\t\t<ul class=\"footer-list-box__list\">
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t <b>call-центр</b> \t\t\t\t\t\t <span>ежедневно с 9:00 до 18:00</span> \t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t <b>интернет-магазин</b> \t\t\t\t\t\t <span>ежедневно с 9:00 до 18:00</span> \t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t <b>доставка заказов</b> \t\t\t\t\t\t <span>ежедневно с 9:00 до 20:00</span> \t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t</ul>
\t</section>

\t\t\t\t\t\t\t<section class=\"footer-list-box branch\">
\t\t\t\t\t<p class=\"footer-list-box__title h3 first-fix\">филиалы:</p>
\t\t
\t\t<ul class=\"footer-list-box__list\">
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t <b>санкт-петербург ул. салова, 46</b> \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t <b>новосибирск толмачёвское шоссе, д. 27 к2, оф. 17</b> \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-list-box__item\">
\t\t\t\t\t\t\t\t\t\t\t <b>казань ул. магистральная, 77 офис 3.1</b> \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t</ul>
\t</section>

\t\t\t\t\t</div>

\t\t\t\t\t<div class=\"page-footer__app\">

\t\t\t\t\t\t<div class=\"footer-rating\">
\t\t\t\t\t\t\t<div class=\"footer-rating__value\">0.0</div>

\t\t\t\t\t\t\t<div class=\"footer-rating__line\">
\t\t\t\t\t\t\t\t
\t\t
\t<div
\t\tclass=\"rating-stars                         rating-stars--primary
             \"
\t\tstyle=\"--rating-value: 0.0\"
\t>
\t\t
\t\t
\t\t<div class=\"rating-stars__line\"></div>
\t\t
\t\t<div class=\"rating-stars__icons\">
\t\t\t\t\t\t\t
\t\t\t<svg width=\"1em\" height=\"1em\" viewbox=\"0 0 22 22\">
\t\t\t<path fill=\"#fff\" d=\"m0 0v22h22v0h0zm16.253 19l11 16.266 5.747 19l1.003-5.788l2.5 9.116l5.873-.85l11 3l2.627 5.266 5.873.85-4.25 4.096l16.253 19z\"/>
\t\t\t<path fill=\"#aa0755\" d=\"m11 6.168l9.644 8.887c-.206.413-.605.7-1.066.766l-3.034.44 2.194 2.115c.335.322.487.786.408 1.241l-.517 2.988 2.712-1.412a1.428 1.428 0 011.318 0l2.712 1.412-.517-2.988c-.08-.455.073-.919.408-1.241l2.194-2.116-3.034-.439a1.416 1.416 0 01-1.066-.766l11 6.167zm11 3l2.627 5.266 5.873.85-4.25 4.096l16.253 19 11 16.266 5.747 19l1.003-5.788l2.5 9.116l5.873-.85l11 3z\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t
\t\t\t<svg width=\"1em\" height=\"1em\" viewbox=\"0 0 22 22\">
\t\t\t<path fill=\"#fff\" d=\"m0 0v22h22v0h0zm16.253 19l11 16.266 5.747 19l1.003-5.788l2.5 9.116l5.873-.85l11 3l2.627 5.266 5.873.85-4.25 4.096l16.253 19z\"/>
\t\t\t<path fill=\"#aa0755\" d=\"m11 6.168l9.644 8.887c-.206.413-.605.7-1.066.766l-3.034.44 2.194 2.115c.335.322.487.786.408 1.241l-.517 2.988 2.712-1.412a1.428 1.428 0 011.318 0l2.712 1.412-.517-2.988c-.08-.455.073-.919.408-1.241l2.194-2.116-3.034-.439a1.416 1.416 0 01-1.066-.766l11 6.167zm11 3l2.627 5.266 5.873.85-4.25 4.096l16.253 19 11 16.266 5.747 19l1.003-5.788l2.5 9.116l5.873-.85l11 3z\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t
\t\t\t<svg width=\"1em\" height=\"1em\" viewbox=\"0 0 22 22\">
\t\t\t<path fill=\"#fff\" d=\"m0 0v22h22v0h0zm16.253 19l11 16.266 5.747 19l1.003-5.788l2.5 9.116l5.873-.85l11 3l2.627 5.266 5.873.85-4.25 4.096l16.253 19z\"/>
\t\t\t<path fill=\"#aa0755\" d=\"m11 6.168l9.644 8.887c-.206.413-.605.7-1.066.766l-3.034.44 2.194 2.115c.335.322.487.786.408 1.241l-.517 2.988 2.712-1.412a1.428 1.428 0 011.318 0l2.712 1.412-.517-2.988c-.08-.455.073-.919.408-1.241l2.194-2.116-3.034-.439a1.416 1.416 0 01-1.066-.766l11 6.167zm11 3l2.627 5.266 5.873.85-4.25 4.096l16.253 19 11 16.266 5.747 19l1.003-5.788l2.5 9.116l5.873-.85l11 3z\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t
\t\t\t<svg width=\"1em\" height=\"1em\" viewbox=\"0 0 22 22\">
\t\t\t<path fill=\"#fff\" d=\"m0 0v22h22v0h0zm16.253 19l11 16.266 5.747 19l1.003-5.788l2.5 9.116l5.873-.85l11 3l2.627 5.266 5.873.85-4.25 4.096l16.253 19z\"/>
\t\t\t<path fill=\"#aa0755\" d=\"m11 6.168l9.644 8.887c-.206.413-.605.7-1.066.766l-3.034.44 2.194 2.115c.335.322.487.786.408 1.241l-.517 2.988 2.712-1.412a1.428 1.428 0 011.318 0l2.712 1.412-.517-2.988c-.08-.455.073-.919.408-1.241l2.194-2.116-3.034-.439a1.416 1.416 0 01-1.066-.766l11 6.167zm11 3l2.627 5.266 5.873.85-4.25 4.096l16.253 19 11 16.266 5.747 19l1.003-5.788l2.5 9.116l5.873-.85l11 3z\"/>
\t\t</svg>
\t

\t\t\t\t\t\t\t
\t\t\t<svg width=\"1em\" height=\"1em\" viewbox=\"0 0 22 22\">
\t\t\t<path fill=\"#fff\" d=\"m0 0v22h22v0h0zm16.253 19l11 16.266 5.747 19l1.003-5.788l2.5 9.116l5.873-.85l11 3l2.627 5.266 5.873.85-4.25 4.096l16.253 19z\"/>
\t\t\t<path fill=\"#aa0755\" d=\"m11 6.168l9.644 8.887c-.206.413-.605.7-1.066.766l-3.034.44 2.194 2.115c.335.322.487.786.408 1.241l-.517 2.988 2.712-1.412a1.428 1.428 0 011.318 0l2.712 1.412-.517-2.988c-.08-.455.073-.919.408-1.241l2.194-2.116-3.034-.439a1.416 1.416 0 01-1.066-.766l11 6.167zm11 3l2.627 5.266 5.873.85-4.25 4.096l16.253 19 11 16.266 5.747 19l1.003-5.788l2.5 9.116l5.873-.85l11 3z\"/>
\t\t</svg>
\t

\t\t\t\t\t</div>
\t</div>

\t\t\t\t\t\t\t</div>

\t\t\t\t\t\t\t<p class=\"footer-review footer-rating__review\">
\t\t\t\t\t\t\t\tчитайте отзывы:
\t\t\t\t\t\t\t\t<a rel=\"nofollow\" href=\"https://market.yandex.ru/brands--legrand/12859688\" target=\"_blank\">яндекс маркет</a>
\t\t\t\t\t\t\t\tи <a href=\"https://domlegrand.com/review-company\">раздел на нашем сайте</a>
\t\t\t\t\t\t\t</p>
\t\t\t\t\t\t</div>

\t\t\t\t\t\t<ul class=\"footer-apps hidden\">
\t\t\t\t\t\t\t<li class=\"footer-apps__item\">
\t\t\t\t\t\t\t\t<a class=\"footer-apps__link\" href=\"#\" target=\"_blank\">
\t\t\t\t\t\t\t\t\t<img class=\"footer-apps__image lazyload\" src=\"https://domlegrand.com/themes/legrand/assets/images/app-store.svg\" loading=\"lazy\" alt=\"\">
\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-apps__item\">
\t\t\t\t\t\t\t\t<a class=\"footer-apps__link\" href=\"#\" target=\"_blank\">
\t\t\t\t\t\t\t\t\t<img class=\"footer-apps__image lazyload\" src=\"https://domlegrand.com/themes/legrand/assets/images/google-play.svg\" loading=\"lazy\" alt=\"\">
\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t</ul>

\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<div class=\"page-footer__item page-footer__social\">
\t\t\t\t\t
\t
\t<section class=\"footer-social\">
\t\t<p class=\"footer-social__title\">ищите нас в соцсетях:</p>

\t\t<ul class=\"footer-social__list\">
\t\t\t\t\t\t\t<li class=\"footer-social__item\">
\t\t\t\t\t<a rel=\"nofollow\" class=\"footer-social__link\" href=\"https://vk.com/legrand.official\" target=\"_blank\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 48 48\" fill=\"none\">
\t\t\t<path d=\"m36.872 17.111c.189-.638 0-1.108-.909-1.108h-3.007a1.292 1.292 0 00-1.307.853 25.19 25.19 0 01-3.7 6.151c-.7.7-1.021.924-1.4.924-.189 0-.48-.225-.48-.863v-5.957c0-.766-.214-1.108-.847-1.108h-4.73a.726.726 0 00-.766.69c0 .724 1.082.892 1.195 2.934v4.431c0 .97-.174 1.15-.556 1.15-1.021 0-3.5-3.748-4.972-8.036-.3-.832-.587-1.169-1.358-1.169h-3.004c-.858 0-1.031.4-1.031.853 0 .8 1.021 4.753 4.753 9.98 2.486 3.568 5.988 5.5 9.173 5.5 1.914 0 2.149-.429 2.149-1.169 0-3.41-.174-3.732.786-3.732.444 0 1.21.225 3 1.945 2.042 2.042 2.379 2.956 3.522 2.956h3.007c.858 0 1.292-.429 1.041-1.276-.572-1.782-4.436-5.447-4.61-5.692-.444-.572-.316-.827 0-1.337.005-.005 3.675-5.17 4.053-6.922l-.002.002z\" fill=\"#fff\"/>
\t\t</svg>
\t
</a>
\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-social__item\">
\t\t\t\t\t<a rel=\"nofollow\" class=\"footer-social__link\" href=\"https://t.me/domlegrand_com\" target=\"_blank\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 48 48\" fill=\"none\">
\t\t\t<path xmlns=\"http://www.w3.org/2000/svg\" d=\"m20.885336508946903,27.83686803522896 l-0.4788014392852792,6.734577423095702 c0.6850358123779315,0 0.9817238578796391,-0.2942759475708012 1.3375083026885985,-0.6476482944488545 l3.2117083950042726,-3.0693946170806883 l6.654978191375732,4.873643869400025 c1.2205215530395508,0.6802116165161154 2.080434465408325,0.3220150737762458 2.4096858329772948,-1.1228315868377685 l4.368309352874756,-20.469063041687015 l0.0012060489654541027,-0.0012060489654541027 c0.38714171791076735,-1.804249252319336 -0.652472490310669,-2.509787897109985 -1.8416367702484133,-2.06716792678833 l-25.67678247451782,9.830505117416381 c-1.7523891468048096,0.6802116165161154 -1.7258560695648195,1.6571112785339368 -0.2978940944671633,2.099731248855591 l6.5645245189666745,2.041840898513794 l15.248077070236207,-9.541053365707397 c0.7175991344451921,-0.47518329238891677 1.3700716247558595,-0.21226461791992204 0.8333798351287864,0.2629186744689943 z\" fill=\"#fff\" data-original=\"#000000\" />
\t\t</svg>
\t
</a>
\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-social__item\">
\t\t\t\t\t<a rel=\"nofollow\" class=\"footer-social__link\" href=\"https://ok.ru/legrand.official\" target=\"_blank\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 48 48\" fill=\"none\">
\t\t\t<path d=\"m14.28 25.319c-.777 1.526.105 2.256 2.117 3.503 1.71 1.056 4.073 1.443 5.59 1.597l-5.98 5.753c-1.741 1.668 1.061 4.345 2.8 2.712l4.71-4.542c1.802 1.735 3.53 3.397 4.708 4.548 1.741 1.64 4.542-1.014 2.82-2.712-.129-.123-6.38-6.12-6-5.753 1.537-.154 3.864-.563 5.554-1.597v-.001c2.011-1.253 2.893-1.982 2.128-3.508-.463-.867-1.71-1.591-3.369-.339 0 0-2.241 1.717-5.855 1.717-3.616 0-5.856-1.717-5.856-1.717-1.658-1.259-2.91-.528-3.367.339z\" />
\t\t\t<path d=\"m23.5 24.382c4.405 0 8.003-3.443 8.003-7.682 0-4.257-3.598-7.7-8.004-7.7-4.407 0-8.005 3.443-8.005 7.7 0 4.239 3.598 7.682 8.005 7.682zm0-11.481c2.164 0 3.93 1.699 3.93 3.799 0 2.083-1.766 3.781-3.93 3.781-2.166 0-3.932-1.698-3.932-3.781 0-2.102 1.765-3.8 3.931-3.8z\" />
\t\t</svg>
\t
</a>
\t\t\t\t</li>
\t\t\t\t\t\t\t<li class=\"footer-social__item\">
\t\t\t\t\t<a rel=\"nofollow\" class=\"footer-social__link\" href=\"https://www.youtube.com/channel/uc9rhaaeotq6t9-ptw3o-pvg/featured\" target=\"_blank\">
\t\t\t<svg class=\"icon-raw\" width=\"1em\" height=\"1em\" viewbox=\"0 0 48 48\" fill=\"none\">
\t\t\t<path d=\"m37.298 17.067a3.5 3.5 0 00-2.465-2.481c32.659 14 23.94 14 23.94 14s-8.718 0-10.89.586a3.5 3.5 0 00-2.467 2.481a36.752 36.752 0 0010 23.821a36.752 36.752 0 00.583 6.754 3.452 3.452 0 002.467 2.441c2.174.586 10.892.586 10.892.586s8.718 0 10.892-.586a3.45 3.45 0 002.465-2.441c.402-2.229.597-4.49.583-6.754a36.749 36.749 0 00-.583-6.754h-.002zm-16.208 10.9v-8.291l7.287 4.145-7.287 4.145v.001z\" fill=\"#fff\"/>
\t\t</svg>
\t
</a>
\t\t\t\t</li>
\t\t\t\t\t</ul>
\t</section>

\t\t\t\t</div>

\t\t\t\t<div class=\"page-footer__item page-footer__profile\">
\t\t\t\t\t\t\t\t\t</div>
\t\t\t</div>

\t\t\t<div class=\"footer-note\">

\t\t\t\t<small class=\"footer-note__copyright\">
\t\t\t\t\t© 2000 - 2022 © legrand <span class=\"footer-note__copyright-text\">все материалы сайта www.domlegrand.com, включая текстовую, графическую, фото и видео информацию, структуру и дизайн страниц, доменное имя являются объектами авторского права и прав на интеллектуальную собственность, защищены российсиким законодательством. запрещено любое воспроизведение, в том числе использование, копирование, включение содержания и объектов в другие сайты без предварительного согласия правообладателя.</span>
\t\t\t\t</small>

\t\t\t\t<ul class=\"footer-note__nav\">
\t\t\t\t\t\t\t\t\t\t<li class=\"footer-note__nav-item\">
\t\t\t\t\t\t<a class=\"footer-note__nav-link\" href=\"https://domlegrand.com/storage/app/uploads/public/621/385/aee/621385aeec082413985586.pdf\" target=\"_blank\">политика конфиденциальности</a>
\t\t\t\t\t</li>
\t\t\t\t</ul>

\t\t\t\t<a class=\"footer-note__dev\" href=\"#\" target=\"_blank\">
\t\t\t\t\t\t\t\t\t</a>

\t\t\t</div>

\t\t</div>
\t</footer>

    </main>



    <div id=\"alert-layout\" class=\"hidden\">
        <div class=\"content\"></div>
    </div>
</div>

<!---->









<!---->





























<!---->
<!---->
<!---->
<!---->
<!---->

<!---->
<!---->
<!---->
<!---->
<!---->
<!---->
<!---->
<!---->
<!---->
<!---->
<!---->
<!---->

<!---->








<link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css\" />
<link href=\"https://cdn.jsdelivr.net/npm/suggestions-jquery@21.12.0/dist/css/suggestions.min.css\" rel=\"stylesheet\" />


<link rel=\"stylesheet\" property=\"stylesheet\" href=\"/modules/system/assets/css/framework.extras-min.css\">








</body>
</html>"}
';
    dd(strip_tags($s));
});
