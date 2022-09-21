@component('component.card', ['title' => __('Competitor analysis')])
    @slot('css')
        <link rel="stylesheet"
              type="text/css"
              href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <link rel="stylesheet"
              type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet"
              type="text/css"
              href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" href="{{ asset('plugins/bootstrap4-duallistbox/bootstrap-duallistbox.css') }}">

        <style>
            .row-width {
                min-width: 250px;
                max-width: 250px
            }

            .CompetitorAnalysisPhrases {
                background: oldlace;
            }

            .danger {
                background: rgb(255, 193, 7);
            }

            .word-wrap {
                word-wrap: break-word;
                max-width: 300px;
            }

            .select-text {
                color: white;
                text-shadow: 1px 1px 3px black;
            }

            #sites-tables > div:nth-child(1) > div.card-body.p-0 > table > tbody > tr {
                max-width: 70px;
            }

            .admin-link {
                color: #146dcb !important;
            }
        </style>
    @endslot
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="card">
        <div class="card-header d-flex p-0">
            <ul class="nav nav-pills p-2">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('relevance-analysis') }}">{{ __('Analyzer') }}</a>
                </li>
                @if($admin)
                    <li class="nav-item">
                        <a class="nav-link admin-link"
                           href="{{ route('competitor.config') }}">{{ __('Module administration') }}</a>
                    </li>
                @endif
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="col-md-6">
                    <div class="form-group required">
                        <label>{{ __('List of phrases') }}</label>
                        {!! Form::textarea("phrases", null ,["class"=>"form-control phrases","required"=>"required"]) !!}
                    </div>
                    <div class="form-group required">
                        <label>{{ __('Top 10/20') }}</label>
                        {!! Form::select('count', [
                                '10' => 10,
                                '20' => 20,
                                ], null, ['class' => 'custom-select rounded-0 count']) !!}
                    </div>
                    <div class="form-group required">
                        <label>{{ __('Region') }}</label>
                        {!! Form::select('region', [
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
                                ], $region ?? null, ['class' => 'custom-select rounded-0 region']) !!}
                    </div>
                    <div class="well well-sm clearfix">
                        <button class="btn btn-secondary pull-left" type="button">{{ __('Analyze') }}</button>
                    </div>
                </div>

                <div id="toast-container" class="toast-top-right broken-script-message" style="display: none">
                    <div class="toast toast-error" aria-live="assertive">
                        <div class="toast-message"></div>
                    </div>
                </div>

                <div id="progress-bar" style="display: none">
                    <div class="progress-bar mt-5" role="progressbar"></div>
                    <div id="stage" class="text-muted"></div>
                </div>

                <div class="mt-4" id="render-bar" style="display: none">
                    <img src="{{ asset('/img/1485.gif') }}" alt="preloader_gif">
                    <p>Отрисовка данных..</p>
                </div>

                <div
                    id="sites-block"
                    class="mt-5"
                    style="display:none;">
                    <h2>{{ __('Top sites based on your keywords') }}</h2>
                    <div class="site-block-buttons">
                        <button class="btn btn-secondary colored-button" id="coloredEloquentDomains">
                            Подсветить одинаковые домены
                        </button>

                        <button class="btn btn-default colored-button" id="coloredMainPages">
                            Подсветить все главные страницы
                        </button>

                        <button class="btn btn-default colored-button" id="coloredEloquentUrls">
                            Подсветить одинаковые url
                        </button>

                        <button type="button" class="btn btn-default" data-toggle="modal"
                                data-target="#coloredEloquentMyTextModal">
                            Подсветить своё
                        </button>

                        <div class="modal fade" id="coloredEloquentMyTextModal" tabindex="-1"
                             aria-labelledby="coloredEloquentMyTextModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="coloredEloquentMyTextModalLabel">
                                            {{ __('Highlighting the domains you need') }}
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <label for="search">{{ __('Your line') }}</label>
                                        <textarea name="search" id="search-textarea" cols="30" rows="10"
                                                  class="form form-control"
                                                  placeholder="{{ __('The substring is searched in the string') }}"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-default colored-button"
                                                id="coloredEloquentMyText"
                                                data-dismiss="modal">
                                            {{ __('Highlight your') }}
                                        </button>
                                        <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">{{ __('Close') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-default" data-toggle="modal"
                                data-target="#coloredAgrigators">
                            {{ __('Highlight site aggregators') }}
                        </button>

                        <div class="modal fade" id="coloredAgrigators" tabindex="-1"
                             aria-labelledby="coloredAgrigatorsLabel"
                             aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"
                                            id="coloredAgrigatorsLabel">{{ __('Highlighting aggregators') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <label for="search">{{ __('List of aggregator sites') }}</label>
                                        <textarea disabled name="search" id="search-agrigators" cols="30" rows="10"
                                                  class="form form-control">{{ $config->agrigators }}</textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-default colored-button"
                                                id="coloredAgrigatorsButton"
                                                data-dismiss="modal">
                                            {{ __('Highlight aggregators') }}
                                        </button>
                                        <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">{{ __('Close') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="sites-tables" class="d-flex" style="width: 100%; overflow-x: auto">
                    </div>
                </div>

                <div class="top-sites mt-5" style="display: none">
                    <h2>{{ __('Top sites based on your keywords') }} {{ __('(headers and meta tags)') }}</h2>
                    <table class="table table-bordered table-striped dataTable dtr-inline top-sites-table"
                           style="display: block; overflow-x: auto;">
                        <thead>
                        <tr>
                            <th class="row-width">{{ __('Phrase') }}</th>
                            <th class="row-width">{{ __('First place') }}</th>
                            <th class="row-width">{{ __('Second place') }}</th>
                            <th class="row-width">{{ __('Third place') }}</th>
                            <th class="row-width">{{ __('Fourth place') }}</th>
                            <th class="row-width">{{ __('Fifth place') }}</th>
                            <th class="row-width">{{ __('Sixth place') }}</th>
                            <th class="row-width">{{ __('Seventh place') }}</th>
                            <th class="row-width">{{ __('Eighth place') }}</th>
                            <th class="row-width">{{ __('Ninth place') }}</th>
                            <th class="row-width">{{ __('Tenth place') }}</th>
                            <th class="extra-th row-width">{{ __('Eleventh place') }}</th>
                            <th class="extra-th row-width">{{ __('Twelfth place') }}</th>
                            <th class="extra-th row-width">{{ __('Thirteenth place') }}</th>
                            <th class="extra-th row-width">{{ __('Fourteenth place') }}</th>
                            <th class="extra-th row-width">{{ __('Fifteenth place') }}</th>
                            <th class="extra-th row-width">{{ __('Sixteenth place') }}</th>
                            <th class="extra-th row-width">{{ __('Seventeenth place') }}</th>
                            <th class="extra-th row-width">{{ __('Eighteenth place') }}</th>
                            <th class="extra-th row-width">{{ __('Nineteenth place') }}</th>
                            <th class="extra-th row-width">{{ __('Twentieth place') }}</th>
                        </tr>
                        </thead>
                        <tbody id="top-sites-body">
                        </tbody>
                    </table>
                </div>

                <div class="nested mt-5" style="display:none;">
                    <h2>{{ __('Analysis of page nesting') }}</h2>
                    <table class="table table-bordered table-striped dataTable dtr-inline">
                        <thead>
                        <tr>
                            <th>{{ __('Page') }}</th>
                            <th>{{ __('Count') }}</th>
                            <th>{{ __('Ratio') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="dtr-control sorting_1">{{ __('Main') }}</td>
                            <td class="mainPageCounter"></td>
                            <td class="mainPagePercent"></td>
                        </tr>
                        <tr>
                            <td class="dtr-control sorting_1">{{ __('Nested') }}</td>
                            <td class="nestedPageCounter"></td>
                            <td class="nestedPagePercent"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="urls mt-5" style="display: none">
                    <h2>{{ __('Landing Page analysis') }}</h2>
                    <table class="table table-bordered table-striped dataTable dtr-inline" id="urls-table">
                        <thead>
                        <tr>
                            <th style='max-width: 600px !important; min-width: 450px !important;'>{{ __('Links') }}</th>
                            <th style='max-width: 350px !important; min-width: 250px !important;'>{{ __('The phrase in which the link occurs') }}</th>
                            <th>{{ __('Number of repetitions') }}</th>
                        </thead>
                        <tbody id="urls-tbody">
                        </tbody>
                    </table>
                </div>

                <div class="positions mt-5" style="display: none">
                    <h2>{{ __('Analysis by the percentage of getting into the top and middle positions') }}</h2>
                    <table class="table table-bordered table-striped dataTable dtr-inline" id="positions">
                        <thead>
                        <tr>
                            <th>{{ __('Domain') }}</th>
                            <th>{{ __('Percentage of getting into the top') }}</th>
                            <th>{{ __('Middle position') }}</th>
                        </tr>
                        </thead>
                        <tbody id="positions-tbody">

                        </tbody>
                    </table>
                </div>

                <div class="tag-analysis mt-5" style="display: none">
                    <div class="d-flex flex-row pb-2">
                        <h2>{{ __('Tag Analysis') }}</h2>
                        <button type="button" class="btn btn-secondary ml-2" data-toggle="modal"
                                data-target="#recommendationModal">
                            Получить рекомендации
                        </button>
                    </div>
                    <table class="table table-bordered table-striped dataTable dtr-inline" id="tag-analysis"
                           style="display: block; overflow-x: auto;">
                        <thead>
                        <tr id="tag-analysis-row">
                            <th style="min-width:200px; max-width: 200px">{{ __("Phrase") }}</th>
                            <th style="min-width:200px; max-width: 200px">title</th>
                            <th style="min-width:200px; max-width: 200px">H1</th>
                            <th style="min-width:200px; max-width: 200px">H2</th>
                            <th style="min-width:200px; max-width: 200px">H3</th>
                            <th style="min-width:200px; max-width: 200px">H4</th>
                            <th style="min-width:200px; max-width: 200px">H5</th>
                            <th style="min-width:200px; max-width: 200px">H6</th>
                            <th style="min-width:200px; max-width: 200px">description</th>
                        </tr>
                        </thead>
                        <tbody id="tag-analysis-tbody">
                        </tbody>
                    </table>
                </div>

                <div class="modal fade" id="recommendationModal" data-backdrop="static" data-keyboard="false"
                     tabindex="-1" aria-labelledby="recommendationModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="recommendationModalLabel"></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="d-flex flex-row">
                                    <div class="w-50 pr-3" id="dualbox-phrases-block">
                                    </div>
                                    <div class="w-50 pl-3">
                                        <h3>Выберите теги</h3>
                                        <select multiple="multiple" size="10" name="duallistbox_tags"
                                                id="duallistbox_tags">
                                            <option value="h1" class="duallist-default">h1</option>
                                            <option value="h2" class="duallist-default">h2</option>
                                            <option value="h3" class="duallist-default">h3</option>
                                            <option value="h4" class="duallist-default">h4</option>
                                            <option value="h5" class="duallist-default">h5</option>
                                            <option value="h6" class="duallist-default">h6</option>
                                            <option value="title" class="duallist-default">title</option>
                                            <option value="description" class="duallist-default">description
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-end">
                                <button class="btn btn-secondary" id="getRecommendations" data-dismiss="modal">Получить
                                    рекомендации
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="recommendations-block" class="mt-5" style="display: none">
                    <h3>Рекомендации</h3>
                    <table class="table table-bordered table-striped dataTable dtr-inline" id="recommendations-table">
                        <thead id="recommendations-head">
                        </thead>
                        <tbody id="recommendations-tbody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @slot('js')
        <script src="{{ asset('plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.js') }}"></script>
        <script src="{{ asset('plugins/competitor-analysis/js/render-top-sites-table.js') }}"></script>
        <script src="{{ asset('plugins/competitor-analysis/js/render-nesting-table.js') }}"></script>
        <script src="{{ asset('plugins/competitor-analysis/js/render-site-positions-table.js') }}"></script>
        <script src="{{ asset('plugins/competitor-analysis/js/render-tags-table.js') }}"></script>
        <script src="{{ asset('plugins/competitor-analysis/js/render-urls-table.js') }}"></script>
        <script src="{{ asset('plugins/competitor-analysis/js/refresh-all.js') }}"></script>
        <script src="{{ asset('plugins/competitor-analysis/js/duallbox-block.js') }}"></script>
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
        <script>
            String.prototype.shuffle = function () {
                var a = this.split(""),
                    n = a.length;

                for (var i = n - 1; i > 0; i--) {
                    var j = Math.floor(Math.random() * (i + 1));
                    var tmp = a[i];
                    a[i] = a[j];
                    a[j] = tmp;
                }
                return a.join("").replaceAll(" ", "");
            }
            window.session = String(new Date()).shuffle();
            localStorage.setItem("sessionCompetitors", window.session);
            onStorage = function (e) {
                if (e.key === 'sessionCompetitors' && e.newValue !== window.session)
                    localStorage.setItem("multitab", window.session);
                if (e.key === "multitab" && e.newValue && e.newValue !== window.session) {
                    window.removeEventListener("storage", onStorage);
                    localStorage.setItem("sessionCompetitors", localStorage.getItem("multitab"));
                    localStorage.removeItem("multitab");
                }
            };
            window.addEventListener('storage', onStorage);

            $('.btn.btn-secondary.pull-left').click(() => {
                let phrases = $.trim($('.form-control.phrases').val())
                let count = $('.custom-select.rounded-0.count').val()
                let interval = null
                let token = $('meta[name="csrf-token"]').attr('content')
                if (phrases) {

                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('start.competitor.progress') }}",
                        data: {
                            _token: token,
                            pageHash: window.session,
                        },
                    });

                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('analysis.sites') }}",
                        data: {
                            _token: token,
                            phrases: phrases,
                            count: count,
                            region: $('.custom-select.rounded-0.region').val(),
                            pageHash: window.session,
                        },
                        beforeSend: function () {
                            refreshAll()
                            interval = setInterval(() => {
                                getProgressPercent(token)
                            }, 500)
                        },
                        success: async function (response) {
                            setProgressBarStyles(100)
                            setTimeout(() => {
                                $("#progress-bar").hide(300)
                                $('.btn.btn-secondary.pull-left').prop('disabled', false);
                                $('#render-bar').show(300)
                            }, 1000)
                            removeProgressPercent(token)
                            clearInterval(interval)

                            if (response.code === 415) {
                                getBrokenScriptMessage(false, response.message)
                            }
                            await renderTopSites(response.result.analysedSites)
                            await renderTopSitesV2(response.result.analysedSites)
                            await renderNestingTable(response.result.pagesCounter)
                            await renderSitePositionsTable(response.result.domainsPosition, {{ $config->positions_length }})
                            await renderTagsTable(response.result.totalMetaTags)
                            await renderUrlsTable(response.result.urls, {{ $config->urls_length }})
                            await duallboxBlockRender(response.result.totalMetaTags)

                        },
                        error: function () {
                            getBrokenScriptMessage(interval)
                        }
                    });
                } else {
                    getBrokenScriptMessage(interval, "{{ __('The list of keywords should not be empty') }}")
                }
            });

            function getProgressPercent(token) {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('get.competitor.progress') }}",
                    data: {
                        _token: token,
                        pageHash: window.session,
                    },
                    success: function (response) {
                        setProgressBarStyles(response.percent.percent)
                    }
                });
            }

            function removeProgressPercent(token) {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('remove.competitor.progress') }}",
                    data: {
                        _token: token,
                        pageHash: window.session,
                    },
                });
            }

            function getBrokenScriptMessage(interval, message = false) {
                if (!interval) {
                    setProgressBarStyles(100)
                    setTimeout(() => {
                        $("#progress-bar").hide(300)
                        $('.btn.btn-secondary.pull-left').prop('disabled', false);
                        $('#render-bar').hide(300)
                    }, 2000)
                    clearInterval(interval)
                }

                $('.toast-top-right.broken-script-message').show(300)
                if (message !== false) {
                    $('.toast-message').html(message)
                } else {
                    $('.toast-message').html("{{ __('Something went wrong, if the error repeats, report it to the administrator') }}")
                }
                setTimeout(() => {
                    $('.toast-top-right.broken-script-message').hide(300)
                }, 10000)

                refreshAll()
            }

            function getErrorMessage() {
                return "{{ __('The site is protected from information collection, we recommend analyzing it manually') }}"
            }

            function getStringDomain() {
                return "{{ __('Domain') }}"
            }

            function getStringPercent() {
                return "{{ __('Percentage of getting into the top') }}"
            }

            function getStringPosition() {
                return "{{ __('Middle position') }}"
            }

            function setProgressBarStyles(percent) {
                $('.progress-bar').css({
                    width: percent + '%'
                })
                $('.progress-bar').text(percent + '%')
            }

            function getXMLMessage() {
                return "{{ __('Processing the XML service response') }}"
            }

            function stringGoToPage() {
                return "{{ __('Go to the landing page') }}"
            }

            function stringGoToSite() {
                return "{{ __('Go to site') }}"
            }

            function stringGoToAnalyse() {
                return "{{ __('Analyze the text') }}"
            }
        </script>
    @endslot
@endcomponent
