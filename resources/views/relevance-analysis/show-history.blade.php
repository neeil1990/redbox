@component('component.card', ['title' =>  __('Show details') ])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/jqcloud/css/jqcloud.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/relevance-analysis/css/style.css') }}"/>
    @endslot

    <div id="toast-container" class="toast-top-right error-message" style="display:none;">
        <div class="toast toast-error" aria-live="polite">
            <div class="toast-message" id="message-error-info"></div>
        </div>
    </div>

    <div id="toast-container" class="toast-top-right success-message" style="display:none;">
        <div class="toast toast-success" aria-live="polite">
            <div class="toast-message">
                {{ __('The reanalysis task has been successfully added to the queue') }}
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex p-0">
            <ul class="nav nav-pills p-2">
                <li class="nav-item">
                    <a class="nav-link active" href="#tab_1" data-toggle="tab"
                       id="firstTab">{{ __('Show details') }}</a>
                </li>
                <li class="nav-item" id="repeat-analyse-item">
                    @if($object->state == 1)
                        <a class="nav-link" href="#tab_2" data-toggle="tab">
                            {{ __('Repeat the analysis') }}
                        </a>
                    @else
                        <div class="three col d-flex align-items-center">
                            {{ __('Standing in line for reanalysis') }}
                            <div class="loader d-flex justify-content-center align-items-center" id="loader-1"
                                 style="height: 35px; width: 35px">
                            </div>
                        </div>
                    @endif
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">

                    <div class="text-center" id="preloaderBlock">
                        <img src="/img/1485.gif" alt="preloader_gif">
                        <p>{{ __("Load..") }}</p>
                    </div>

                    <div class="pb-3 pt-3 text" style="display:none">
                        <h3>{{ __('Comparing the amount of text') }}</h3>
                        <table class="table table-bordered table-striped dataTable dtr-inline">
                            <thead>
                            <tr>
                                <th class="col-3"></th>
                                <th>{{ __('Average values of competitors') }}</th>
                                <th>{{ __('Landing Page Values') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <b>{{ __('Number of words') }}</b>
                                </td>
                                <td id="avgCountWords"></td>
                                <td id="mainPageCountWords"></td>
                            </tr>
                            <tr>
                                <td><b>{{ __('Number of characters') }}</b></td>
                                <td id="avgCountSymbols"></td>
                                <td id="mainPageCountSymbols"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="pb-3 clouds" style="display:none;">
                        <h3>{{ __('The clouds') }}</h3>
                        <div class="d-flex flex-column pb-3">
                            <button id="tf-idf-clouds" class="btn btn-secondary col-lg-3 col-md-5 mb-3"
                                    style="cursor: pointer">
                                Облака tf-idf сайтов из топа и посадочной страницы
                            </button>
                            <div class="tf-idf-clouds" style="display: none">
                                <div class="d-lg-flex mt-4 justify-content-around">

                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('Average tf-idf values of links and competitor text') }}</span>
                                        <div style="height: 350px" id="competitorsTfCloud"
                                             class="generated-cloud"></div>
                                    </div>

                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('TF-idf values of links and landing page text') }}</span>
                                        <div style="height: 350px" id="mainPageTfCloud" class="generated-cloud"></div>
                                    </div>

                                </div>
                                <div class="d-lg-flex mt-4 justify-content-around">

                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('Average tf-idf values of competitors text') }}</span>
                                        <div style="height: 350px" id="competitorsTextTfCloud"
                                             class="generated-cloud"></div>
                                    </div>

                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('TF-idf values of the landing page text') }}</span>
                                        <div style="height: 350px" id="mainPageTextTfCloud"
                                             class="generated-cloud"></div>
                                    </div>

                                </div>
                                <div class="d-lg-flex mt-4 justify-content-around">

                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('Average tf-idf values of competitor links') }}</span>
                                        <div style="height: 350px" id="competitorsLinksTfCloud"
                                             class="generated-cloud"></div>
                                    </div>

                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('TF-idf values of landing page links') }}</span>
                                        <div style="height: 350px" id="mainPageLinksTfCloud"
                                             class="generated-cloud"></div>
                                    </div>

                                </div>
                            </div>
                            <button id="text-clouds" class="btn btn-secondary col-lg-3 col-md-5"
                                    style="cursor: pointer;">
                                {{ __("Clouds of site text from the top and landing page") }}
                            </button>
                            <div class="text-clouds" style=" display: none">
                                <div class="d-lg-flex mt-4 justify-content-around">
                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('Competitors Link Zone') }}</span>
                                        <div style="height: 350px" id="competitorsLinksCloud"
                                             class="generated-cloud"></div>
                                    </div>
                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('The link zone of your page') }}</span>
                                        <div style="height: 350px" id="mainPageLinksCloud"
                                             class="generated-cloud"></div>
                                    </div>
                                </div>
                                <div class="d-lg-flex mt-4 justify-content-around">
                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('Competitors text area') }}</span>
                                        <div style="height: 350px" id="competitorsTextCloud"
                                             class="generated-cloud"></div>
                                    </div>
                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('The text area of your page') }}</span>
                                        <div style="height: 350px" id="mainPageTextCloud" class="generated-cloud"></div>
                                    </div>
                                </div>
                                <div class="d-lg-flex mt-4 justify-content-around">
                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('Competitors Link and Text area') }}</span>
                                        <div style="height: 350px" id="competitorsTextAndLinksCloud"
                                             class="generated-cloud"></div>
                                    </div>
                                    <div class="col-lg-5 col-md-10">
                                        <span>{{ __('The zone of links and text of your page') }}</span>
                                        <div style="height: 350px" id="mainPageTextWithLinksCloud"
                                             class="generated-cloud"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="rec" style="display: none" class="mb-3">
                        <h2>Рекомендации TLP и проверка на спам</h2>
                        <button class="btn btn-secondary" id="recButton">Показать</button>
                    </div>

                    <div class="pb-3 recommendations" style="display:none;">
                        <table id="recommendations" class="table table-bordered table-hover dataTable dtr-inline"
                               style="width: 100% !important;">
                            <thead>
                            <tr style="position: relative; z-index: 100">
                                <th class="сol-1">
                                <span class="text-muted" style="font-weight: 400">
                                    {{ __("You can delete a word from the table if it has been worked out") }}
                                </span>
                                </th>
                                <th>Слово</th>
                                <th>Tf</th>
                                <th>Среднее кол-во повторений у конкурентов</th>
                                <th>Количество у вас на странице</th>
                                <th>Рекомендуемый диапозон</th>
                                <th>Уровень спама</th>
                                <th>Добавить</th>
                                <th>Удалить</th>
                            </tr>
                            </thead>
                            <tbody id="recommendationsTBody">
                            </tbody>
                        </table>
                    </div>

                    <div class="pb-3 unigram" style="display: none; margin-top: 50px">
                        <h2>Топ лист фраз (TLP)</h2>
                        <table id="unigram" class="table table-bordered table-hover dataTable dtr-inline"
                               style="width: 100% !important;">
                            <thead>
                            <tr>
                                <th></th>
                                <th class="font-weight-normal text-muted">{{ __('Ranges for filtering the table') }}</th>
                                <th>
                                    <div style="width: 90px">
                                        <input class="w-100" type="number" name="minTF" id="minTF" placeholder="min">
                                        <input class="w-100" type="number" name="maxTF" id="maxTF" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div style="width: 90px">
                                        <input class="w-100" type="number" name="minIdf" id="minIdf" placeholder="min">
                                        <input class="w-100" type="number" name="maxIdf" id="maxIdf" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" name="minInter" id="minInter"
                                               placeholder="min">
                                        <input class="w-100" type="number" name="maxInter" id="maxInter"
                                               placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" name="minReSpam" id="minReSpam"
                                               placeholder="min">
                                        <input class="w-100" type="number" name="maxReSpam" id="maxReSpam"
                                               placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" name="minAVG" id="minAVG" placeholder="min">
                                        <input class="w-100" type="number" name="maxAVG" id="maxAVG" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" name="minAVGText" id="minAVGText"
                                               placeholder="min">
                                        <input class="w-100" type="number" name="maxAVGText" id="maxAVGText"
                                               placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" name="minInYourPage" id="minInYourPage"
                                               placeholder="min">
                                        <input class="w-100" type="number" name="maxInYourPage" id="maxInYourPage"
                                               placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" name="minTextIYP" id="minTextIYP"
                                               placeholder="min">
                                        <input class="w-100" type="number" name="maxTextIYP" id="maxTextIYP"
                                               placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" name="minAVGLink" id="minAVGLink"
                                               placeholder="min">
                                        <input class="w-100" type="number" name="maxAVGLink" id="maxAVGLink"
                                               placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" name="minLinkIYP" id="minLinkIYP"
                                               placeholder="min">
                                        <input class="w-100" type="number" name="maxLinkIYP" id="maxLinkIYP"
                                               placeholder="max">
                                    </div>
                                </th>
                            </tr>
                            <tr style="position: relative; z-index: 100">
                                <th></th>
                                <th>
                                    {{ __('Words') }}<span class="__helper-link ui_tooltip_w">
                        <i class="fa fa-question-circle"></i>
                        <span class="ui_tooltip __right">
                            <span class="ui_tooltip_content" style="text-align: right">{{ __('Words and their word forms that are present on competitors websites.') }}
                            </span>
                        </span>
                    </span>
                                </th>
                                <th>Tf<span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The weight of the phrase relative to others.') }}
                                </span>
                            </span>
                        </span>
                                </th>
                                <th>Idf<span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The weight of the phrase relative to others.') }}
                                </span>
                            </span>
                        </span>
                                </th>
                                <th>
                                    {{ __('Intersection') }}<span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The number of sites in which the word is present.') }}
                                </span>
                            </span>
                        </span>
                                </th>
                                <th>{{ __('Re - spam') }}<span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The maximum number of repetitions found on the competitors website.') }}
                                </span>
                            </span>
                        </span>
                                </th>
                                <th>{{ __('Average number of repetitions in the text and links') }}<span
                                        class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The average value of the number of repetitions in the text and links of your competitors.') }}
                                </span>
                            </span>
                        </span>
                                </th>
                                <th>{{ __('The total number of repetitions in the text and links') }}<span
                                        class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The total number of repetitions on your page in links and text.') }}
                                </span>
                            </span>
                        </span>
                                </th>
                                <th>{{ __('Average number of repetitions in the text') }}<span
                                        class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The average value of the number of repetitions in the text of your competitors.') }}
                                </span>
                            </span>
                        </span>
                                </th>
                                <th>{{ __('Number of repetitions in text') }}<span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The number of repetitions in the text on your page.') }}
                                </span>
                            </span>
                        </span>
                                </th>
                                <th>{{ __('Average number of repetitions in links') }}<span
                                        class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The average value of the number of repetitions in the links of your competitors.') }}
                                </span>
                            </span>
                        </span>
                                </th>
                                <th>{{ __('Number of repetitions in links') }}<span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __left">
                                <span class="ui_tooltip_content">{{ __('The number of repetitions in the links on your page.') }}
                                </span>
                            </span>
                        </span>
                                </th>
                            </tr>
                            </thead>
                            <tbody id="unigramTBody">
                            </tbody>
                        </table>
                    </div>

                    <div class="phrases" style="display:none;">
                        <h3>Топ лист словосочетаний (TLPs)</h3>
                        <table id="phrases" class="table table-bordered table-hover dataTable dtr-inline w-100">
                            <thead>
                            <tr>
                                <th class="font-weight-normal text-muted">{{ __('Ranges for filtering the table') }}</th>
                                <th>
                                    <div style="width: 90px">
                                        <input class="w-100" type="number" id="phrasesMinTF" placeholder="min">
                                        <input class="w-100" type="number" id="phrasesMaxTF" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div style="width: 90px">
                                        <input class="w-100" type="number" id="phrasesMinIdf" placeholder="min">
                                        <input class="w-100" type="number" id="phrasesMaxIdf" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" id="phrasesMinInter" placeholder="min">
                                        <input class="w-100" type="number" id="phrasesMaxInter" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" id="phrasesMinReSpam" placeholder="min">
                                        <input class="w-100" type="number" id="phrasesMaxReSpam" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" id="phrasesMinAVG" placeholder="min">
                                        <input class="w-100" type="number" id="phrasesMaxAVG" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" id="phrasesMinAVGText" placeholder="min">
                                        <input class="w-100" type="number" id="phrasesMaxAVGText" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" id="phrasesMinInYourPage"
                                               placeholder="min">
                                        <input class="w-100" type="number" id="phrasesMaxInYourPage"
                                               placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" id="phrasesMinTextIYP" placeholder="min">
                                        <input class="w-100" type="number" id="phrasesMaxTextIYP" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" id="phrasesMinAVGLink" placeholder="min">
                                        <input class="w-100" type="number" id="phrasesMaxAVGLink" placeholder="max">
                                    </div>
                                </th>
                                <th>
                                    <div>
                                        <input class="w-100" type="number" id="phrasesMinLinkIYP" placeholder="min">
                                        <input class="w-100" type="number" id="phrasesMaxLinkIYP" placeholder="max">
                                    </div>
                                </th>
                            </tr>
                            <tr style="position: relative; z-index: 100;">
                                <th>Фраза</th>
                                <th>tf</th>
                                <th>idf</th>
                                <th>Пересечение</th>
                                <th>Переспам</th>
                                <th>Среднее количество повторений в тексте и ссылках</th>
                                <th>Общее колиество повторений в тексте и ссылках</th>
                                <th>Среднее количество повторений в тексте</th>
                                <th>Количество повторений в тексте</th>
                                <th>Среднее количество поторений в ссылках</th>
                                <th>Количество поторений в ссылках</th>
                            </tr>
                            </thead>
                            <tbody id="phrasesTBody">
                            </tbody>
                        </table>
                    </div>

                    <div class="sites" style="display:none; margin-top:50px;">
                        <h3>{{ __('Analyzed sites') }}</h3>
                        <table id="scaned-sites" class="table table-bordered table-hover dataTable dtr-inline">
                            <thead>
                            <tr role="row" style="position: relative; z-index: 100">
                                <th>{{ __('Position in the top') }}</th>
                                <th>{{ __('Domain') }}</th>
                                <th>
                                    Общий балл
                                    @if($admin)
                                        <span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __bottom">
                                <span class="ui_tooltip_content" style="width: 300px">
                                    Общий балл рассчитывается следующим образом: охват по важным словам + охват по tf + плотность<br>
                                    Полученная сумма сначала делится на 3, затем умножается на 2<br>
                                    - <br>
                                    Если полученное кол-во баллов больше 100, то мы приравниваем его к 100.<br>
                                </span>
                            </span>
                        </span>
                                    @endif
                                </th>
                                <th>{{ __('coverage for all important words') }}
                                    @if($admin)
                                        <span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __bottom">
                                <span class="ui_tooltip_content" style="width: 300px">
                                    Из таблицы униграм берутся все слова (далее эти слова именуются "важные слова") <br>
                                    Для каждого отдельно взятого сайта происходит проверка наличия в нём слов, которые считаются важными <br>
                                    Если важное слово присутсвует в проверяемом сайте, то он получает за него 1 балл<br>
                                    Полученый процент равен сумме полученых баллов делённой на 600
                                </span>
                            </span>
                        </span>
                                    @endif
                                </th>
                                <th>{{ __('Coverage by tf') }}
                                    @if($admin)
                                        <span class="__helper-link ui_tooltip_w">
                        <i class="fa fa-question-circle"></i>
                        <span class="ui_tooltip __bottom">
                            <span class="ui_tooltip_content" style="width: 300px">
                                Из таблицы униграм берутся все слова и их значения tf(далее эти слова именуются "важные слова") <br>
                                Для каждого отдельно взятого сайта происходит проверка наличия в нём слов, которые считаются важными <br>
                                Если важное слово присутсвует в проверяемом сайте, то он получает за него балл равный tf из таблицы униграм <br>
                                Общая сумма баллов каждого конкретного сайта делиться на общую сумму tf из таблицы униграм, таким образом мы получаем % охвата
                            </span>
                        </span>
                    </span>
                                    @endif
                                </th>
                                <th>{{ __('Width') }}
                                    @if($admin)
                                        <span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __bottom">
                                <span class="ui_tooltip_content" style="width: 300px">
                                    Для вычисления  ширины, беруться первые 10 не игнорируемых сайтов (позиция в топе) <br>
                                    Их охват по всем словам(%) плюсуется и делиться на 10, для того чтобы выявить 100% ширину <br>
                                    В соответствии с этими 100% для каждого сайта ширина просчитывается  отдельно
                                </span>
                            </span>
                        </span>
                                    @endif
                                </th>
                                <th>
                                    {{ __('Density') }}
                                    @if($admin)
                                        <span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-question-circle"></i>
                            <span class="ui_tooltip __bottom">
                                <span class="ui_tooltip_content" style="width: 300px">
                                    Плотность высчитывается от значения средней по ТОПу для КАЖДОЙ ОСНОВНОЙ ФРАЗЫ. <br>
                                    Если в средней 20, а у нас 5, то это 25 баллов. <br>
                                    Дальше все баллы для всех фраз складываются и делятся на общее количество слов. <br>
                                    - <br>
                                    Если мы переспамили, то пока в этом варианте мы никак не учитываем этот момент, фраза просто получает 100 баллов по плотности. <br>
                                </span>
                            </span>
                        </span>
                                    @endif
                                </th>
                                <th>Количество символов</th>
                                <th>{{ __('Result') }}</th>
                            </tr>
                            </thead>
                            <tbody id="scanned-sites-tbody">
                            </tbody>
                        </table>
                    </div>

                    <div class="pb-3 pt-3" id="competitorsTfClouds" style="display: none !important;">
                        <div class="align-items-end clouds-div">
                            <button class="btn btn-secondary col-lg-3 col-md-5" id="coverage-clouds-button">
                                {{ __('Clouds of the first 200 important (tf-idf) words from competitors') }}
                            </button>
                        </div>
                        <div style="display: none" id="coverage-clouds" class="pt-2">
                            <div class='d-flex w-100'>
                                <div class='__helper-link ui_tooltip_w'>
                                    <div
                                        class='custom-control custom-switch custom-switch-off-danger custom-switch-on-success'>
                                        <input type='checkbox'
                                               class='custom-control-input'
                                               id='showOrHideIgnoredClouds'>
                                        <label class='custom-control-label' for='showOrHideIgnoredClouds'></label>
                                    </div>
                                </div>
                                <p>{{ __('hide ignored domains') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab_2">
                    @if(isset($access) && $access->access > 1 || !isset($access))
                        <div class="col-5">

                            <input type="hidden" name="hiddenId" id="hiddenId" value="{{ $object->id }}">
                            <input type="hidden" name="type" id="type" value="{{ $object['request']['type'] }}">

                            <div class="form-group required">
                                <label>{{ __('Your landing page') }}</label>
                                {!! Form::text("link", $object['request']['link'] ,["class" => "form-control link", "required"]) !!}
                            </div>


                            <div id="key-phrase"
                                 @if($object['request']['type'] != 'phrase') style="display: none"@endif>
                                <div class="form-group required">
                                    <label>{{ __('Keyword') }}</label>
                                    {!! Form::text("phrase", $object['request']['phrase'] ,["class" => "form-control phrase", "required"]) !!}
                                </div>

                                <div class="form-group required">
                                    <label>{{ __('Top 10/20') }}</label>
                                    {!! Form::select('count', array_unique([
                                            $object['request']['count'] => $object['request']['count'],
                                            '10' => 10,
                                            '20' => 20,
                                            ]), null, ['class' => 'custom-select rounded-0 count']) !!}
                                </div>

                                <div class="form-group required">
                                    <label>{{ __('Region') }}</label>
                                    {!! Form::select('region', array_unique([
                                           $object['request']['region'] => $object['request']['region'],
                                           '1' => __('Moscow'),
                                           '20' => __('Arkhangelsk'),
                                           '37' => __('Astrakhan'),
                                           '197' => __('Barnaul'),
                                           '4' => __('Belgorod'),
                                           '77' => __('Blagoveshchensk'),
                                           '191' => __('Bryansk'),
                                           '24' => __('Veliky Novgorod'),
                                           '75' => __('Vladivostok'),
                                           '33' => __('Vladikavkaz'),
                                           '192' => __('Vladimir'),
                                           '38' => __('Volgograd'),
                                           '21' => __('Vologda'),
                                           '193' => __('Voronezh'),
                                           '1106' => __('Grozny'),
                                           '54' => __('Ekaterinburg'),
                                           '5' => __('Ivanovo'),
                                           '63' => __('Irkutsk'),
                                           '41' => __('Yoshkar-ola'),
                                           '43' => __('Kazan'),
                                           '22' => __('Kaliningrad'),
                                           '64' => __('Kemerovo'),
                                           '7' => __('Kostroma'),
                                           '35' => __('Krasnodar'),
                                           '62' => __('Krasnoyarsk'),
                                           '53' => __('Kurgan'),
                                           '8' => __('Kursk'),
                                           '9' => __('Lipetsk'),
                                           '28' => __('Makhachkala'),
                                           '213' => __('Moscow'),
                                           '23' => __('Murmansk'),
                                           '1092' => __('Nazran'),
                                           '30' => __('Nalchik'),
                                           '47' => __('Nizhniy Novgorod'),
                                           '65' => __('Novosibirsk'),
                                           '66' => __('Omsk'),
                                           '10' => __('Eagle'),
                                           '48' => __('Orenburg'),
                                           '49' => __('Penza'),
                                           '50' => __('Perm'),
                                           '25' => __('Pskov'),
                                           '39' => __('Rostov-on-Don'),
                                           '11' => __('Ryazan'),
                                           '51' => __('Samara'),
                                           '42' => __('Saransk'),
                                           '2' => __('Saint-Petersburg'),
                                           '12' => __('Smolensk'),
                                           '239' => __('Sochi'),
                                           '36' => __('Stavropol'),
                                           '973' => __('Surgut'),
                                           '13' => __('Tambov'),
                                           '14' => __('Tver'),
                                           '67' => __('Tomsk'),
                                           '15' => __('Tula'),
                                           '195' => __('Ulyanovsk'),
                                           '172' => __('Ufa'),
                                           '76' => __('Khabarovsk'),
                                           '45' => __('Cheboksary'),
                                           '56' => __('Chelyabinsk'),
                                           '1104' => __('Cherkessk'),
                                           '16' => __('Yaroslavl'),
                                           ]), null, ['class' => 'custom-select rounded-0 region']) !!}
                                </div>

                                <div class="form-group required" id="ignoredDomainsBlock">
                                    <label id="ignoredDomains">{{ __('Ignored domains') }}</label>
                                    {!! Form::textarea("ignoredDomains", $object['request']['ignoredDomains'],["class" => "form-control ignoredDomains"] ) !!}
                                </div>
                            </div>
                            <div id="site-list" @if($object['request']['type'] != 'list') style="display: none"@endif>
                                <div class="form-group required">
                                    <label>{{ __('List of sites') }}</label>
                                    {!! Form::textarea("siteList", $object['request']['siteList'] ?? null ,["class" => "form-control", 'id'=>'siteList'] ) !!}
                                </div>
                            </div>

                            <div class="form-group required d-flex align-items-center">
                                <span>{{ __('Cut the words shorter') }}</span>
                                <input type="number" class="form form-control col-2 ml-1 mr-1" name="separator"
                                       id="separator"
                                       value="{{ $object['request']['separator'] }}">
                                <span>{{ __('symbols') }}</span>
                            </div>

                            <div class="switch mt-3 mb-3">
                                <div class="d-flex">
                                    <div class="__helper-link ui_tooltip_w">
                                        <div
                                            class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   id="switchNoindex"
                                                   name="noIndex"
                                                   @if($object['request']['noIndex'] == 'true') checked @endif>
                                            <label class="custom-control-label" for="switchNoindex"></label>
                                        </div>
                                    </div>
                                    <p>{{ __('Track the text in the noindex tag') }}</p>
                                </div>

                                <div class="d-flex">
                                    <div class="__helper-link ui_tooltip_w">
                                        <div
                                            class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   id="switchAltAndTitle"
                                                   name="hiddenText"
                                                   @if($object['request']['hiddenText'] == 'true') checked @endif>
                                            <label class="custom-control-label" for="switchAltAndTitle"></label>
                                        </div>
                                    </div>
                                    <p>{{ __('Track words in the alt, title, and data-text attributes') }}</p>
                                </div>

                                <div class="d-flex">
                                    <div class="__helper-link ui_tooltip_w">
                                        <div
                                            class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   id="switchConjunctionsPrepositionsPronouns"
                                                   name="conjunctionsPrepositionsPronouns"
                                                   @if($object['request']['conjunctionsPrepositionsPronouns'] == 'true') checked @endif>
                                            <label class="custom-control-label"
                                                   for="switchConjunctionsPrepositionsPronouns"></label>
                                        </div>
                                    </div>
                                    <p>{{ __('Track conjunctions, prepositions, pronouns') }}</p>
                                </div>

                                <div class="d-flex">
                                    <div class="__helper-link ui_tooltip_w">
                                        <div
                                            class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   id="switchMyListWords"
                                                   name="switchMyListWords"
                                                   @if($object['request']['switchMyListWords'] == 'true') checked @endif>
                                            <label class="custom-control-label" for="switchMyListWords"></label>
                                        </div>
                                    </div>
                                    <span>{{ __('Exclude') }}
                                    <span class="text-muted">{{ __('(your own list of words)') }}</span>
                                </span>
                                </div>

                                <div class="form-group required list-words mt-1"
                                     @if($object['request']['switchMyListWords'] == 'false') style="display:none;" @endif >
                                    {!! Form::textarea('listWords', $object['request']['listWords'],['class' => 'form-control listWords', 'cols' => 8, 'rows' => 5]) !!}
                                </div>
                            </div>

                            <button type="button" class="btn btn-secondary" id="relevance-repeat-scan">
                                {{ __('Repeat the analysis') }}
                            </button>
                        </div>
                    @else
                        <h3>{{ __("You don't have access to this object") }}</h3>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @slot('js')
        <script src="{{ asset('plugins/canvasjs/js/canvasjs.js') }}"></script>
        <script src="{{ asset('plugins/jqcloud/js/jqcloud-1.0.4.min.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/test-scripts/renderClouds.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/test-scripts/renderUnigramTable.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/test-scripts/renderScannedSitesList.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/test-scripts/renderTextTable.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/test-scripts/renderPhrasesTable.js') }}"></script>
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/scriptsV6/renderRecommendationsTable.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/history/common.js') }}"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
        <script>
            $('#recButton').click(function () {
                if ($('.pb-3.recommendations').is(':visible')) {
                    $('.pb-3.recommendations').hide()
                    $(this).html('Показать')
                } else {
                    $('.pb-3.recommendations').show()
                    $(this).html('Скрыть')
                }
            });

            $('#check-type').on('change', function () {
                if ($(this).val() === 'list') {
                    $('#key-phrase').hide()
                    $('#site-list').show(300)
                } else {
                    $('#site-list').hide()
                    $('#key-phrase').show(300)
                }
            });

            $('input#switchMyListWords').click(function () {
                if ($(this).is(':checked')) {
                    $('.form-group.required.list-words.mt-1').show(300)
                } else {
                    $('.form-group.required.list-words.mt-1').hide(300)
                }
            })

        </script>
        <script>
            var generatedTfIdf = false
            var generatedText = false
            var generatedCompetitorCoverage = false

            $(document).ready(function () {
                $('#main_history_table').DataTable({
                    "order": [[1, "desc"]],
                    "pageLength": 10,
                    "searching": true,
                });

                $('#history_table').DataTable({
                    "order": [[1, "desc"]],
                    "pageLength": 10,
                    "searching": true,
                });

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('get.details.info') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: {{ $id }}
                    },
                    success: function (response) {
                        if (response.code === 200) {
                            successRequest(response.history, response.config)
                        } else if (response.code === 415) {
                            $('.toast-top-right.error-message').show(300)
                            $('#message-error-info').html(response.message)
                            $('#preloaderBlock').html(response.message)
                            setTimeout(() => {
                                $('.toast-top-right.error-message').hide(300)
                            }, 10000)
                        }
                    },
                });

                $('#relevance-repeat-scan').click(function () {
                    let id = $('#hiddenId').val()
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "/repeat-scan",
                        data: {
                            id: id,
                            type: $('#type').val(),
                            siteList: $('#siteList').val(),
                            link: $('.form-control.link').val(),
                            phrase: $('.form-control.phrase').val(),
                            count: $(".custom-select.rounded-0.count").val(),
                            region: $(".custom-select.rounded-0.region").val(),
                            ignoredDomains: $(".form-control.ignoredDomains").html(),
                            separator: $("#separator").val(),
                            noIndex: $('#switchNoindex').is(':checked'),
                            hiddenText: $('#switchAltAndTitle').is(':checked'),
                            conjunctionsPrepositionsPronouns: $('#switchConjunctionsPrepositionsPronouns').is(':checked'),
                            switchMyListWords: $('#switchMyListWords').is(':checked'),
                            listWords: $('.form-control.listWords').val(),
                        },
                        success: function () {
                            $('.toast-top-right.success-message').show(300)
                            setInterval(function () {
                                $('.toast-top-right.success-message').hide(300)
                            }, 3500)


                            $('#firstTab').trigger('click')
                            $('html, body').animate({
                                scrollTop: $('#header-nav-bar').offset().top
                            }, {
                                duration: 370,
                                easing: "linear"
                            });

                            $('#repeat-analyse-item').html(
                                '<div class="three col d-flex align-items-center">{{ __('Standing in line for reanalysis') }}' +
                                '   <div class="loader d-flex justify-content-center align-items-center" ' +
                                '               id="loader-1" style="height: 35px; width: 35px">' +
                                '   </div> ' +
                                '</div>'
                            )
                        },

                        error: function () {
                            $('#toast-container').show(300)
                            setInterval(function () {
                                $('#toast-container').hide(300)
                            }, 3500)
                        }
                    });
                });

            });

            function successRequest(history, config) {
                renderTextTable(history.avg, history.main_page)
                renderRecommendationsTable(history.recommendations, config.recommendations_count)
                renderUnigramTable(history.unigram_table, config.ltp_count, true);
                renderPhrasesTable(history.phrases, config.ltps_count)
                renderScannedSitesList(history.sites, history.avg_coverage_percent, config.scanned_sites_count, false, config.boostPercent, true);
                renderClouds(history.clouds_competitors, history.clouds_main_page, history.tf_comp_clouds, false, true);
                setTimeout(function () {
                    $('#preloaderBlock').hide(300)
                }, 1500)
            }
        </script>
    @endslot
@endcomponent
