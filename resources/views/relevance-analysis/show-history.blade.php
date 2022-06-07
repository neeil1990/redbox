@component('component.card', ['title' =>  __('Show details') ])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/jqcloud/css/jqcloud.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <style>
            .ui_tooltip_content {
                font-weight: normal;
            }

            .bg-warning-elem {
                background-color: #f5e2aa !important;
            }

            #unigramTBody > tr > td:nth-child(1) {
                text-align: center;
            }

            #app > div > div > div.card-body > div.d-flex.flex-column > div > button.btn.btn-secondary.col-2 > span > i {
                color: #fffdfd !important;
            }

            th {
                background: white;
                position: sticky;
                top: 0;
            }

            .fa.fa-question-circle {
                color: white;
            }

            #unigramTBody > tr > td:nth-child(8),
            #unigramTBody > tr > td:nth-child(10),
            #unigramTBody > tr > td:nth-child(12),
            #phrasesTBody > tr > td:nth-child(7),
            #phrasesTBody > tr > td:nth-child(9),
            #phrasesTBody > tr > td:nth-child(11),
            #recommendationsTBody > tr > td:nth-child(5) {
                background: #ebf0f5;
            }

            .ui_tooltip.__left, .ui_tooltip.__right {
                width: auto;
            }

            .pb-3.unigramd thead th {
                position: sticky;
                top: 0;
                z-index: 1;
            }

            .pb-3.unigramd tbody th {
                position: sticky;
                left: 0;
            }

            .dataTables_paginate.paging_simple_numbers {
                padding-bottom: 50px;
            }

            .dt-buttons {
                margin-left: 20px;
                float: left;
            }

            .bg-my-site {
                background: #4eb767c4;
            }
        </style>
    @endslot
    <div id="toast-container" class="toast-top-right error-message empty" style="display:none;">
        <div class="toast toast-error" aria-live="polite">
            <div class="toast-message error-message" id="toast-message"></div>
        </div>
    </div>

    <div id="toast-container" class="toast-top-right success-message lock-word" style="display:none;">
        <div class="toast toast-success" aria-live="polite">
            <div class="toast-message" id="lock-word"></div>
        </div>
    </div>

    <div class="text-center" id="preloaderBlock">
        <img src="/img/1485.gif" alt="preloader_gif">
        <p>Загрузка..</p>
    </div>

    <div class="tab-pane active" id="tab_1">
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
                    Облака текста сайтов из топа и посадочной страницы
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
                            Вы можете удалить слово из таблицы, если оно было проработано
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
                        successRequest(response.history, response.config)
                    },
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
