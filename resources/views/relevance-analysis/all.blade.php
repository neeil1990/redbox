@component('component.card', ['title' =>  __('Statistics')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/jqcloud/css/jqcloud.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/relevance-analysis/css/style.css') }}"/>
        <style>
            i:hover {
                opacity: 1 !important;
                transition: .3s;
            }

            .empty-td {
                background: #dee2e6;
                width: 0;
                padding: 0 !important;
                border: none;
            }

            .fixed-width {
                max-width: 50px !important;
            }

            .RelevanceAnalysis {
                background: oldlace;
            }
        </style>
    @endslot

    <div id="toast-container" class="toast-top-right success-message" style="display:none;">
        <div class="toast toast-success" aria-live="polite">
            <div class="toast-message" id="message-info"></div>
        </div>
    </div>

    <div id="toast-container" class="toast-top-right error-message" style="display:none;">
        <div class="toast toast-error" aria-live="polite">
            <div class="toast-message" id="message-error-info"></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex p-0">
            <div class="card-header d-flex p-0">
                <ul class="nav nav-pills p-2">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('relevance-analysis') }}">{{ __('Analyzer') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('create.queue.view') }}">
                            {{ __('Create page analysis tasks') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('relevance.history') }}">{{ __('History') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('sharing.view') }}" class="nav-link">{{ __('Share your projects') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('access.project') }}"
                           class="nav-link">{{ __('Projects available to you') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active"
                           href="{{ route('all.relevance.projects') }}">{{ __('Statistics') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link admin-link"
                           href="{{ route('show.config') }}">{{ __('Module administration') }}</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <div class="d-flex w-100">
                        <div class="w-50 pr-3">
                            <h3 style="padding-bottom: 44px">{{ __('General statistics of the module') }}</h3>
                            <table style="margin: 0 0 35px 0 !important;" id="statistics_table"
                                   class="table table-bordered table-hover dataTable dtr-inline mb-5">
                                <tbody>
                                <tr>
                                    <td class="col-10">
                                        <b>{{ __('Number of checks for the current day') }}</b>
                                    </td>
                                    <td> {{ $statistics['toDay']['count_checks'] ?? 0 }} </td>
                                </tr>
                                <tr>
                                    <td class="col-10">
                                        <b>{{ __('Number of checks for the current month') }}</b>
                                    </td>
                                    <td> {{ $statistics['month']}} </td>
                                </tr>
                                <tr>
                                    <td class="col-10">
                                        <b>{{ __('Number of errors for the current day') }}</b>
                                    </td>
                                    <td> {{ $statistics['toDay']['count_fails'] ?? 0 }} </td>
                                </tr>
                                <tr>
                                    <td class="col-10">
                                        <b>{{ __('Number of projects') }}</b>
                                    </td>
                                    <td> {{ $statistics['countProjects']}} </td>
                                </tr>
                                <tr>
                                    <td class="col-10">
                                        <b>{{ __('Number of saved scan results') }}</b>
                                    </td>
                                    <td> {{ $statistics['countSavedResults']}} </td>
                                </tr>
                                <tr>
                                    <td class="col-10">
                                        <b>{{ __('Number of unique landing pages') }}</b>
                                    </td>
                                    <td> {{ $statistics['pages'] }} </td>
                                </tr>
                                <tr>
                                    <td class="col-10">
                                        <b>{{ __('Number of unique landing domains') }}</b>
                                    </td>
                                    <td> {{ $statistics['domains'] }} </td>
                                </tr>
                                <tr>
                                    <td class="col-10">
                                        <b>{{ __('Total number of unique analyzed domains') }}</b>
                                    </td>
                                    <td> {{ $statistics['allDomains'] }} </td>
                                </tr>
                                <tr>
                                    <td class="col-10">
                                        <b>{{ __('Total number of unique analyzed sites') }}</b>
                                    </td>
                                    <td> {{ $statistics['allPages'] }} </td>
                                </tr>
                                <tr>
                                    <td class="col-10">
                                        <b>{{ __('Number of tasks in the queue') }}</b>
                                    </td>
                                    <td id="countJobs"> {{ $statistics['countJobs'] }} </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="w-50 ">
                            <h3>{{ __('Users and their tasks') }}</h3>
                            <table style="margin: 0 0 35px 0 !important;" id="user_jobs_table"
                                   class="table table-bordered table-hover dataTable dtr-inline mb-5">
                                <thead>
                                <tr>
                                    <th>
                                        {{ __('user') }}
                                    </th>
                                    <th>
                                        {{ __('Number of tasks in the queue') }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody id="user_jobs_table_body">
                                @foreach($usersJobs as $job)
                                    <tr class="job-row">
                                        <td>
                                            {{ $job->user->email }}
                                            <div class="text-muted">
                                                {{ $job->user->name }}
                                                {{ $job->user->last_name }}
                                            </div>
                                        </td>
                                        <td>
                                            {{ $job->count_jobs }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <h3>{{ __('All user projects') }}</h3>
                    <table id="users_projects" class="table table-bordered table-hover dataTable dtr-inline mb-3">
                        <thead>
                        <tr>
                            <th class="table-header">{{ __('Project name') }}</th>
                            <th class="table-header">{{ __('Tags') }}</th>
                            <th class="table-header">{{ __('Owner') }}</th>
                            <th class="table-header">{{ __('Number of analyzed pages') }}</th>
                            <th class="table-header">{{ __('Number of saved scans') }}</th>
                            <th class="table-header">{{ __('Total score') }}</th>
                            <th class="table-header">{{ __('Avg position') }}</th>
                            <th class="table-header">{{ __('End-to-end analysis') }}</th>
                            <th class="table-header">{{ __('Last check') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                    <div id="block-for-modals">

                    </div>

                    <div style="display:none;" class="history">
                        <div class="modal fade" id="staticBackdrop" data-backdrop="static" tabindex="-1" role="dialog"
                             aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"
                                            id="staticBackdropLabel">{{ __('Repeat the analysis') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="col-12">
                                            <div class="form-group required">
                                                <label>{{ __('Your landing page') }}</label>
                                                {!! Form::text("link", null ,["class" => "form-control link", "required"]) !!}
                                            </div>

                                            <div id="site-list">
                                                <div class="form-group required">
                                                    <label>{{ __('List of sites') }}</label>
                                                    {!! Form::textarea("siteList", null ,["class" => "form-control", 'id'=>'siteList'] ) !!}
                                                </div>
                                            </div>

                                            <div class="form-group required">
                                                <label>{{ __('Keyword') }}</label>
                                                {!! Form::text("phrase", null ,["class" => "form-control phrase", "required"]) !!}
                                            </div>

                                            <div class="form-group required">
                                                <label>{{ __('Region') }}</label>
                                                {!! Form::select('region', array_unique([
                                                       $config->region => $config->region,
                                                       '213' => __('Moscow'),
                                                       '1' => __('Moscow and the area'),
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
                                                       '10649' => __('Stary Oskol'),
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

                                            <div id="key-phrase">

                                                <div class="form-group required">
                                                    <label>{{ __('Top 10/20') }}</label>
                                                    <select name="count" id="count"
                                                            class="custom-select rounded-0 count">
                                                        <option value="10">10</option>
                                                        <option value="20">20</option>
                                                    </select>

                                                </div>

                                                <div class="form-group required" id="ignoredDomainsBlock">
                                                    <label id="ignoredDomains">{{ __('Ignored domains') }}</label>
                                                    {!! Form::textarea("ignoredDomains", null,["class" => "form-control ignoredDomains"] ) !!}
                                                </div>
                                            </div>

                                            <div class="form-group required d-flex align-items-center">
                                                <span>{{ __('Cut the words shorter') }}</span>
                                                <input type="number" class="form form-control col-2 ml-1 mr-1"
                                                       name="separator"
                                                       id="separator">
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
                                                                   name="noIndex">
                                                            <label class="custom-control-label"
                                                                   for="switchNoindex"></label>
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
                                                                   name="hiddenText">
                                                            <label class="custom-control-label"
                                                                   for="switchAltAndTitle"></label>
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
                                                                   name="conjunctionsPrepositionsPronouns">
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
                                                                   name="switchMyListWords">
                                                            <label class="custom-control-label"
                                                                   for="switchMyListWords"></label>
                                                        </div>
                                                    </div>
                                                    <span>{{ __('Exclude') }}<span
                                                            class="text-muted">{{ __('(your own list of words)') }}</span></span>
                                                </div>
                                                <div class="form-group required list-words mt-1"
                                                     @if($config->remove_my_list_words == 'no') style="display:none;" @endif >
                                                    {!! Form::textarea('listWords', $config->my_list_words,['class' => 'form-control listWords', 'cols' => 8, 'rows' => 5]) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="hiddenId">
                                    <input type="hidden" id="type">
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default"
                                                data-dismiss="modal">{{ __('Close') }}
                                        </button>
                                        <button type="button" class="btn btn-secondary" id="relevance-repeat-scan"
                                                data-dismiss="modal">
                                            {{ __('Repeat the analysis') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h3>{{ __("Recent checks") }}</h3>
                        <table id="history_table" class="table table-bordered table-hover dataTable dtr-inline w-100">
                            <thead>
                            @include('relevance-analysis.layouts.table-rows')
                            </thead>
                            <tbody id="historyTbody">
                            </tbody>
                        </table>
                    </div>

                    <h3 style="display: none" id="history-list-subject">{{ __('Scan history (list of phrases)') }}</h3>
                    <table class="table table-bordered table-hover dtr-inline no-footer" id="list-history"
                           style="display: none">
                        <thead>
                        <tr>
                            <th style="position: inherit;"></th>
                            <th style="position: inherit;">
                                <input class="w-100 form form-control" type="date" name="dateMinList" id="dateMinList"
                                       value="{{ Carbon\Carbon::parse('2022-03-01')->toDateString() }}">
                                <input class="w-100 form form-control" type="date" name="dateMaxList" id="dateMaxList"
                                       value="{{ Carbon\Carbon::now()->toDateString() }}">
                            </th>
                            <th style="position: inherit;">
                                <input class="w-100 form form-control search-input" type="text"
                                       name="phraseSearchList" id="phraseSearchList" placeholder="phrase">
                            </th>
                            <th style="position: inherit;">
                                <input class="w-100 form form-control search-input" type="text"
                                       name="regionSearchList" id="regionSearchList" placeholder="region">
                            </th>
                            <th style="position: inherit;">
                                <input class="w-100 form form-control search-input" type="text"
                                       name="mainPageSearchList" id="mainPageSearchList" placeholder="link">
                            </th>
                            <th style="position: inherit;">
                                <input class="w-100 form form-control search-input" type="number"
                                       name="minPositionList" id="minPositionList" placeholder="min">
                                <input class="w-100 form form-control search-input" type="number"
                                       name="maxPositionList" id="maxPositionList" placeholder="max">
                            </th>
                            <th style="position: inherit;">
                                <input class="w-100 form form-control search-input" type="number"
                                       name="minPointsList" id="minPointsList" placeholder="min">
                                <input class="w-100 form form-control search-input" type="number"
                                       name="maxPointsList" id="maxPointsList" placeholder="max">
                            </th>
                            <th style="position: inherit;">
                                <input class="w-100 form form-control search-input" type="number"
                                       name="minCoverageList" id="minCoverageList" placeholder="min">
                                <input class="w-100 form form-control search-input" type="number"
                                       name="maxCoverageList" id="maxCoverageList" placeholder="max">
                            </th>
                            <th style="position: inherit;">
                                <input class="w-100 form form-control search-input" type="number"
                                       name="minCoverageTfList" id="minCoverageTfList" placeholder="min">
                                <input class="w-100 form form-control search-input" type="number"
                                       name="maxCoverageTfList" id="maxCoverageTfList" placeholder="max">
                            </th>
                            <th style="position: inherit;">
                                <input class="w-100 form form-control search-input" type="number" name="minWidthList"
                                       id="minWidthList" placeholder="min">
                                <input class="w-100 form form-control search-input" type="number"
                                       name="maxWidthList" id="maxWidthList" placeholder="max">
                            </th>
                            <th style="position: inherit;">
                                <input class="w-100 form form-control search-input" type="number"
                                       name="minDensityList" id="minDensityList" placeholder="min">
                                <input class="w-100 form form-control search-input" type="number"
                                       name="maxDensityList" id="maxDensityList" placeholder="max">
                            </th>
                        </tr>
                        <tr>
                            <th class="table-header" style="position: inherit;"></th>
                            <th class="table-header" style="position: inherit;">{{ __('Date of last check') }}</th>
                            <th class="table-header" style="position: inherit;">
                                {{ __('Phrase') }}
                            </th>
                            <th class="table-header" style="position: inherit;">
                                {{ __('Region') }}
                            </th>
                            <th class="table-header" style="position: inherit;">
                                {{ __('Landing page') }}
                            </th>
                            <th class="table-header" style="position: inherit;">
                                {{ __('Position in the top') }}
                            </th>
                            <th class="table-header" style="position: inherit;">
                                {{ __('Scores') }}
                            </th>
                            <th class="table-header" style="position: inherit;">
                                {{ __('Coverage of important words') }}
                            </th>
                            <th class="table-header" style="position: inherit;">
                                {{ __('TF coverage') }}
                            </th>
                            <th class="table-header" style="position: inherit;">
                                {{ __('Width') }}
                            </th>
                            <th class="table-header" style="position: inherit;">
                                {{ __('Density') }}
                            </th>
                        </tr>
                        </thead>
                        <tbody id="list-history-body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @slot('js')
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/history/mainHistoryTable.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/history/childHistoryTable.js') }}"></script>
        <script src="{{ asset('plugins/relevance-analysis/history/common.js') }}"></script>
        <script>
            let usersProjects
            let gettedProjects
            let words = {
                search: "{{ __('Search') }}",
                show: "{{ __('show') }}",
                records: "{{ __('records') }}",
                noRecords: "{{ __('No records') }}",
                showing: "{{ __('Showing') }}",
                from: "{{ __('from') }}",
                to: "{{ __('to') }}",
                of: "{{ __('of') }}",
                entries: "{{ __('entries') }}"
            };

            $(document).ready(function () {
                $('input#switchMyListWords').click(function () {
                    if ($(this).is(':checked')) {
                        $('.form-group.required.list-words.mt-1').show(300)
                    } else {
                        $('.form-group.required.list-words.mt-1').hide(300)
                    }
                })

                $('#user_jobs_table').dataTable({
                    "order": [[0, "desc"]],
                    "pageLength": 10,
                    "searching": true,
                    language: {
                        paginate: {
                            "first": "«",
                            "last": "»",
                            "next": "»",
                            "previous": "«"
                        },
                    },
                    "oLanguage": {
                        "sSearch": words.search + ":",
                        "sLengthMenu": words.show + " _MENU_ " + words.records,
                        "sEmptyTable": words.noRecords,
                        "sInfo": words.showing + " " + words.from + "  _START_ " + words.to + " _END_ " + words.of + " _TOTAL_ " + words.entries,
                    },
                    drawCallback: function () {
                        $('#changeAllState, #changeAllStateList').unbind().on('change', function () {
                            let state = $(this).is(':checked')
                            $.each($('.custom-control-input.switch'), function () {
                                if (state !== $(this).is(':checked')) {
                                    $(this).trigger('click');
                                }
                            });
                        });

                        $('.history-comment').unbind().change(function () {
                            $.ajax({
                                type: "POST",
                                dataType: "json",
                                url: "/edit-history-comment",
                                data: {
                                    id: $(this).attr('data-target'),
                                    comment: $(this).val()
                                },
                                success: function () {
                                    $('#toast-container').show(300)
                                    $('#message-info').html("{{ __('Successfully changed') }}")
                                    setInterval(function () {
                                        $('#toast-container').hide(300)
                                    }, 3000)
                                },
                            });
                        });

                        $('.project_name').unbind().click(function () {
                            let thisElem = $(this)
                            let thisElementClass = $(this).attr('class')
                            hideListHistory()
                            hideTableHistory()

                            let storyId = $(this).attr('data-order')
                            $.ajax({
                                type: "POST",
                                dataType: "json",
                                url: "/get-stories",
                                data: {
                                    history_id: storyId,
                                },
                                beforeSend: function () {
                                    thisElem.attr('class', 'fa fa-clock')
                                },
                                async: true,
                                success: function (response) {
                                    thisElem.attr('class', thisElementClass)
                                    if (response.code === 415) {
                                        getErrorMessage(response.message)
                                    } else {
                                        $('#changeAllState').prop('checked', false);
                                        $('#changeAllStateList').prop('checked', false);
                                        $('.search-input').val('')
                                        $('.history').show()
                                        let tbody = $('#historyTbody')

                                        $.each(response.stories, function (key, val) {
                                            let checked = val.calculate ? 'checked' : ''
                                            let state

                                            if (val.state === 1) {
                                                state =
                                                    '<button type="button" class="btn btn-secondary get-history-info" data-order="' + val.id + '" data-toggle="modal" data-target="#staticBackdrop">' +
                                                    "{{ __('Repeat the analysis') }}" +
                                                    '</button>'
                                                    +
                                                    "<a href='/show-history/" + val.id + "' target='_blank' class='btn btn-secondary mt-3'>" + '{{ __("Detailed information") }}' + "</a>"

                                            } else if (val.state === 0) {
                                                state =
                                                    '<p>' + "{{ __('Processed..') }}" + '</p>' +
                                                    '<div class="text-center" id="preloaderBlock">' +
                                                    '        <div class="three col">' +
                                                    '            <div class="loader" id="loader-1"></div>' +
                                                    '        </div>' +
                                                    '</div>'
                                                checkAnalyseProgress(val.id)
                                            } else if (val.state === -1) {
                                                state =
                                                    '<button type="button" class="btn btn-secondary get-history-info" data-order="' + val.id + '" data-toggle="modal" data-target="#staticBackdrop">' +
                                                    '{{ __("Processed..") }}' +
                                                    '</button>' +
                                                    "<span class='text-muted'>" + '{{ __("An error has occurred, try again or contact the administrator") }}' + "</span>"
                                            }

                                            let position = val.position

                                            if (val.position === 0) {
                                                position = "{{ __('Did not get into the top 100') }}"
                                            }

                                            let phrase = val.phrase

                                            if (phrase == null) {
                                                phrase = "{{ __('An analysis without a keyword was used') }}"
                                            }

                                            let newRow

                                            if (val.average_values == null) {
                                                newRow = "<tr class='render'>" +
                                                    "   <td>" + val.last_check + "</td>" +
                                                    "   <td>" +
                                                    "      <textarea style='height: 160px;' data-target='" + val.id + "' class='history-comment form form-control' >" + val.comment + "</textarea>" +
                                                    "   </td>" +
                                                    "   <td>" + phrase + "</td>" +
                                                    "   <td>" + getRegionName(val.region) + "</td>" +
                                                    "   <td>" + val.main_link + "</td>" +
                                                    "   <td>" + position + "</td>" +
                                                    "   <td>" + val.points + "</td>" +
                                                    "   <td>" + val.coverage + "</td>" +
                                                    "   <td>" + val.coverage_tf + "</td>" +
                                                    "   <td>" + val.width + "</td>" +
                                                    "   <td>" + val.density + "</td>" +
                                                    "   <td>" +
                                                    "      <div class='d-flex justify-content-center'> " +
                                                    "          <div class='__helper-link ui_tooltip_w'> " +
                                                    "              <div class='custom-control custom-switch custom-switch-off-danger custom-switch-on-success'>" +
                                                    "                  <input onclick='changeState($(this))' type='checkbox' class='custom-control-input switch' id='calculate-project-" + val.id + "' name='noIndex' data-target='" + val.id + "' " + checked + ">" +
                                                    "                  <label class='custom-control-label' for='calculate-project-" + val.id + "'></label>" +
                                                    "              </div>" +
                                                    "          </div>" +
                                                    "      </div>" +
                                                    "   </td>" +
                                                    "   <td id='history-state-" + val.id + "'>" +
                                                    state +
                                                    "   </td>" +
                                                    "</tr>"
                                            } else {
                                                newRow = "<tr class='render'>" +
                                                    "   <td>" + val.last_check + "</td>" +
                                                    "   <td>" +
                                                    "      <textarea style='height: 160px;' data-target='" + val.id + "' class='history-comment form form-control' >" + val.comment + "</textarea>" +
                                                    "   </td>" +
                                                    "   <td>" + phrase + "</td>" +
                                                    "   <td>" + getRegionName(val.region) + "</td>" +
                                                    "   <td>" + val.main_link + "</td>" +
                                                    "   <td>" + position + "</td>" +
                                                    "   <td style='background: " + getColor(val.points, Math.round(val.average_values.points)) + "'>" + getTextResult(val.points, Math.round(val.average_values.points)) + "</td>" +
                                                    "   <td style='background: " + getColor(val.coverage, Math.round(val.average_values.coverage)) + "'>" + getTextResult(val.coverage, Math.round(val.average_values.coverage)) + "</td>" +
                                                    "   <td style='background: " + getColor(val.coverage_tf, Math.round(val.average_values.coverageTf)) + "'>" + getTextResult(val.coverage_tf, Math.round(val.average_values.coverageTf)) + "</td>" +
                                                    "   <td style='background: " + getColor(val.width, Math.round(val.average_values.width)) + "'>" + getTextResult(val.width, Math.round(val.average_values.width)) + "</td>" +
                                                    "   <td style='background: " + getColor(val.density, Math.round(val.average_values.densityPercent)) + "'>" + getTextResult(val.density, Math.round(val.average_values.densityPercent)) + "</td>" +
                                                    "   <td>" +
                                                    "      <div class='d-flex justify-content-center'> " +
                                                    "          <div class='__helper-link ui_tooltip_w'> " +
                                                    "              <div class='custom-control custom-switch custom-switch-off-danger custom-switch-on-success'>" +
                                                    "                  <input onclick='changeState($(this))' type='checkbox' class='custom-control-input switch' id='calculate-project-" + val.id + "' name='noIndex' data-target='" + val.id + "' " + checked + ">" +
                                                    "                  <label class='custom-control-label' for='calculate-project-" + val.id + "'></label>" +
                                                    "              </div>" +
                                                    "          </div>" +
                                                    "      </div>" +
                                                    "   </td>" +
                                                    "   <td id='history-state-" + val.id + "'>" +
                                                    state +
                                                    "   </td>" +
                                                    "</tr>"
                                            }

                                            tbody.append(newRow)
                                        })

                                        $(document).ready(function () {
                                            if ($.fn.DataTable.fnIsDataTable($('#history_table'))) {
                                                $('#history_table').dataTable().fnDestroy();
                                            }

                                            let historyTable = $('#history_table').DataTable({
                                                "order": [[0, "desc"]],
                                                "pageLength": 25,
                                                "searching": true,
                                                language: {
                                                    paginate: {
                                                        "first": "«",
                                                        "last": "»",
                                                        "next": "»",
                                                        "previous": "«"
                                                    },
                                                },
                                                "oLanguage": {
                                                    "sSearch": words.search + ":",
                                                    "sLengthMenu": words.show + " _MENU_ " + words.records,
                                                    "sEmptyTable": words.noRecords,
                                                    "sInfo": words.showing + " " + words.from + "  _START_ " + words.to + " _END_ " + words.of + " _TOTAL_ " + words.entries,
                                                },
                                                drawCallback: function () {
                                                    getHistoryInfo()
                                                }
                                            });

                                            $('#history_table').wrap("<div style='width: 100%; overflow-x: scroll;'></div>")

                                            $('#history_table_length').before(
                                                "<span>" +
                                                "<a href='/get-file/" + storyId + "/csv' class='btn btn-secondary ml-1'>CSV</a>" +
                                                "<a href='/get-file/" + storyId + "/xls' class='btn btn-secondary ml-1'>Excel</a>" +
                                                "</span>"
                                            )

                                            $(".dt-button").addClass('btn btn-secondary')

                                            $('#history_table_filter').hide()

                                            scrollTo('#tab_1 > div.history > h3')

                                            repeatScan()

                                            customHistoryFilters('history_table', historyTable)
                                        });
                                    }
                                },
                                error: function () {
                                    thisElem.attr('class', thisElementClass)
                                }
                            });
                        });

                        $('.project_name_v2').unbind().click(function () {
                            let thisElem = $(this)
                            let thisElementClass = $(this).attr('class')
                            hideListHistory()
                            hideTableHistory()

                            $.ajax({
                                type: "POST",
                                dataType: "json",
                                url: "/get-stories-v2",
                                async: true,
                                data: {
                                    historyId: $(this).attr('data-order'),
                                },
                                beforeSend: function () {
                                    thisElem.attr('class', 'fa fa-clock')
                                },
                                success: function (response) {
                                    thisElem.attr('class', thisElementClass)

                                    if (response.code === 415) {
                                        getErrorMessage(response.message)
                                    } else {
                                        $('#history-list-subject').show()
                                        $('#list-history').show()
                                        object = response.object
                                        $.each(response.object, function (key, value) {
                                            let position = value[0]['position']
                                            if (position == 0) {
                                                position = 'Не попал в топ 100'
                                            }
                                            $('#list-history-body').append(
                                                '<tr class="render">' +
                                                '   <td data-target="' + key + '" class="col-1" style="text-align: center; vertical-align: inherit; width: 50px"></td>' +
                                                '   <td>' + value[0]['created_at'] + '</td>' +
                                                '   <td>' + key + '</td>' +
                                                '   <td>' + getRegionName(value[0]['region']) + '</td>' +
                                                '   <td>' + value[0]['main_link'] + '</td>' +
                                                '   <td>' + position + '</td>' +
                                                '   <td>' + value[0]['points'] + '</td>' +
                                                '   <td>' + value[0]['coverage'] + '</td>' +
                                                '   <td>' + value[0]['coverage_tf'] + '</td>' +
                                                '   <td>' + value[0]['width'] + '</td>' +
                                                '   <td>' + value[0]['density'] + '</td>' +
                                                '</tr>'
                                            )
                                        })
                                        $(document).ready(function () {
                                            $('.dataTables_wrapper.no-footer').css({
                                                width: '100%'
                                            })

                                            $('#list-history-body > tr.render > td.col-1').append('<i class="fa fa-eye"></i>')

                                            if ($.fn.DataTable.fnIsDataTable($('#list-history'))) {
                                                $('#list-history').dataTable().fnDestroy();
                                            }

                                            let listTable = $('#list-history').DataTable({
                                                columns: [
                                                    {
                                                        className: 'dt-control',
                                                        orderable: false,
                                                    },
                                                    {data: 'date'},
                                                    {data: 'phrase'},
                                                    {data: 'region'},
                                                    {data: 'link'},
                                                    {data: 'position'},
                                                    {data: 'points'},
                                                    {data: 'coverage'},
                                                    {data: 'coverage_tf'},
                                                    {data: 'width'},
                                                    {data: 'density'},
                                                ],
                                                order: [[1, 'desc']],
                                                destroy: true,
                                                language: {
                                                    paginate: {
                                                        "first": "«",
                                                        "last": "»",
                                                        "next": "»",
                                                        "previous": "«"
                                                    },
                                                },
                                                "oLanguage": {
                                                    "sSearch": words.search + ":",
                                                    "sLengthMenu": words.show + " _MENU_ " + words.records,
                                                    "sEmptyTable": words.noRecords,
                                                    "sInfo": words.showing + " " + words.from + "  _START_ " + words.to + " _END_ " + words.of + " _TOTAL_ " + words.entries,
                                                }
                                            });

                                            scrollTo('#history-list-subject')

                                            customFiltersWithoutComment('list-history', listTable, 'List', 1)
                                            $('#list-history').wrap("<div style='width: 100%; overflow-x: scroll;'></div>")

                                            $('#list-history').unbind().on('click', 'td.dt-control', function () {
                                                let tr = $(this).closest('tr');
                                                let row = listTable.row(tr);

                                                if (row.child.isShown()) {
                                                    row.child.hide();
                                                    tr.removeClass('shown');
                                                    $('#' + $(this).attr('data-target').replace(' ', '-')).dataTable().fnDestroy()
                                                } else {
                                                    row.child(format($(this).attr('data-target'))).show();
                                                    tr.addClass('shown');
                                                    let target = $(this).attr('data-target').replace(' ', '-')
                                                    let table = $('#' + target).DataTable({
                                                        order: [[0, 'desc']],
                                                        destroy: true,
                                                        language: {
                                                            lengthMenu: "_MENU_",
                                                            search: "_INPUT_",
                                                            paginate: {
                                                                "first": "«",
                                                                "last": "»",
                                                                "next": "»",
                                                                "previous": "«"
                                                            },
                                                        },
                                                    })
                                                    customFilters(target, table, target)
                                                }
                                            });
                                        })
                                        repeatScan()
                                    }
                                },
                                error: function () {
                                    thisElem.attr('class', thisElementClass)
                                }
                            });
                        })

                        $('.fa.fa-plus.show-stories').unbind().on('click', function () {
                            let target = $(this).attr('data-target');
                            $("td[data-order='" + target + "']").show()

                            $(this).attr('class', 'fa fa-minus hide-stories')
                        });

                        $('.fa.fa-minus.hide-stories').unbind().on('click', function () {
                            let target = $(this).attr('data-target');
                            $("td[data-order='" + target + "']").hide()

                            $(this).attr('class', 'fa fa-plus show-stories')
                        });
                    }
                })

                setInterval(() => {
                    $.ajax({
                        type: "get",
                        dataType: "json",
                        url: "{{ route('get.queue.count') }}",
                        success: function (response) {
                            $('#countJobs').html(response.count)
                        },
                    });

                    $.ajax({
                        type: "get",
                        dataType: "json",
                        url: "{{ route('get.user.jobs') }}",
                        success: function (response) {
                            $('#user_jobs_table').DataTable().rows().remove();
                            $.each(response.jobs, function (key, value) {
                                $('#user_jobs_table').DataTable().row.add({
                                    0: '<td class="sorting_1">' +
                                        value['user']['email'] +
                                        '   <div class="text-muted">' +
                                        value['user']['name'] + ' ' +
                                        value['user']['last_name'] +
                                        '   </div>' +
                                        '</td>',
                                    1: value['count_jobs'],
                                });
                            });

                            $('#user_jobs_table').DataTable().draw()
                        },
                    });
                }, 10000)

                $('#user_jobs_table_length').append('<button class="btn btn-secondary ml-3" id="reset-statistic">Очистить статистику</button>')

                $('#reset-statistic').on('click', function () {
                    $.ajax({
                        type: "get",
                        dataType: "json",
                        url: "{{ route('remove.user.jobs') }}",
                        success: function (response) {
                            $('#user_jobs_table').DataTable().rows().remove().draw();
                        },
                    });
                })

                usersProjects = $('#users_projects').DataTable({
                    pageLength: 10,
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('get.all.relevance.projects') }}",
                    columns: columns,
                    order: [[8, 'desc']],
                    aoColumnDefs: [
                        {
                            bSortable: false,
                            aTargets: [1, 2, 7]
                        }
                    ],
                    language: {
                        paginate: {
                            "first": "«",
                            "last": "»",
                            "next": "»",
                            "previous": "«"
                        },
                    },
                    oLanguage: {
                        "sSearch": words.search + ":",
                        "sLengthMenu": words.show + " _MENU_ " + words.records,
                        "sEmptyTable": words.noRecords,
                        "sInfo": words.showing + " " + words.from + "  _START_ " + words.to + " _END_ " + words.of + " _TOTAL_ " + words.entries,
                    },
                    drawCallback: function () {
                        $('#users_projects').css({
                            width: '100%'
                        })

                        let api = this.api();
                        let currentPageData = api.rows({page: 'current'}).data();
                        let newModals = ''
                        $.each(currentPageData, function (key, value) {
                            let select = getSelect(value.id)

                            newModals += ' <div class="modal fade" id="removeModal' + value.id + '" tabindex="-1"' +
                                '      aria-labelledby="removeModalLabel" aria-hidden="true">' +
                                '     <div class="modal-dialog">' +
                                '         <div class="modal-content">' +
                                '             <div class="modal-header">' +
                                '                 <h5 class="modal-title" id="removeModalLabel">' +
                                '                     {{ __('Deleting results from a project') }} ' + value.name +
                                '                 </h5>' +
                                '                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                                '                     <span aria-hidden="true">&times;</span>' +
                                '                 </button>' +
                                '             </div>' +
                                '             <div class="modal-body">' +
                                '             <span class="__helper-link ui_tooltip_w">' +
                                '                 {{ __('How it works') }}' +
                                '                 <i class="fa fa-question-circle" style="color: grey"></i>' +
                                '                 <span class="ui_tooltip __right" style="width: 350px">' +
                                '                     <span class="ui_tooltip_content">' +
                                '                         {{ __('All scan results that have no comment will be deleted.') }} <br>' +
                                '                         {{ __('But the most recent and unique (by fields: phrase, region, link) will not be deleted.') }}' +
                                '                     </span>' +
                                '                 </span>' +
                                '             </span>' +
                                '                 <p>' +
                                '                     <b>{{ __('You will not be able to recover the data.') }}</b>' +
                                '                 </p>' +
                                '             </div>' +
                                '             <div class="modal-footer">' +
                                '                 <button type="button" class="btn btn-secondary remove-empty-results"' +
                                '                         data-target="' + value.id + '" data-dismiss="modal">' +
                                '                     {{ __('Remove') }}' +
                                '                 </button>' +
                                '                 <button type="button" class="btn btn-default" data-dismiss="modal">' +
                                '                     {{ __('Do not delete') }}' +
                                '                 </button>' +
                                '             </div>' +
                                '         </div>' +
                                '     </div>' +
                                ' </div>' +
                                ' <div class="modal fade" id="startThroughScan' + value.id + '" tabindex="-1" ' +
                                '    aria-labelledby="repeatUniqueScan' + value.id + '" aria-hidden="true">' +
                                '        <div class="modal-dialog">' +
                                '        <div class="modal-content">' +
                                '        <div class="modal-header">' +
                                '        <h5 class="modal-title">' +
                                '        {{ __('Run an analysis of the end-to-end results of the project') }} ' + value.name +
                                '        </h5>' +
                                '    <button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                                '        <span aria-hidden="true">&times;</span>' +
                                '    </button>' +
                                '</div>' +
                                '    <div class="modal-body">' +
                                '        {{ __('The analysis of end-to-end words will be performed at') }}' +
                                '        <b>' + value.count_sites + '</b>' +
                                '        {{ __('unique pages, are you sure?') }}' +
                                '    </div>' +
                                '    <div class="modal-footer">' +
                                '        <button data-target="' + value.id + '" type="button"' +
                                '                class="btn btn-secondary start-through-analyse click_tracking"' +
                                '                data-dismiss="modal"' +
                                '                data-click="Start through scan">{{ __('Start') }}</button>' +
                                '        <button type="button" class="btn btn-default"' +
                                '                data-dismiss="modal">{{ __('Close') }}</button>' +
                                '    </div>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '        <div class="modal fade" id="repeatUniqueScan' + value.id + '" tabindex="-1"' +
                                '    aria-labelledby="repeatUniqueScan' + value.id + '" aria-hidden="true">' +
                                '        <div class="modal-dialog">' +
                                '        <div class="modal-content">' +
                                '        <div class="modal-header">' +
                                '        <h5 class="modal-title">{{ __('restart analyzed pages') }} ' + value.name + '</h5>' +
                                '    <button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                                '        <span aria-hidden="true">&times;</span>' +
                                '    </button>' +
                                '</div>' +
                                '    <div class="modal-body">' +
                                '        {{ __('Are you going to restart the scan') }}' +
                                '        <b>' + value.count_sites + '</b>' +
                                '        {{ __('unique pages, are you sure?') }}' +
                                '    </div>' +
                                '    <div class="modal-footer">' +
                                '        <button data-target="' + value.id + '" type="button"' +
                                '                class="btn btn-secondary repeat-scan-unique-sites"' +
                                '                data-dismiss="modal">{{ __('Start') }}</button>' +
                                '        <button type="button" class="btn btn-default"' +
                                '                data-dismiss="modal">{{ __('Close') }}</button>' +
                                '    </div>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '<div class="modal fade" id="removeWithFiltersModal' + value.id + '" tabindex="-1"' +
                                '     aria-labelledby="removeWithFiltersModalLabel" aria-hidden="true">' +
                                '         <div class="modal-dialog">' +
                                '         <div class="modal-content">' +
                                '         <div class="modal-header">' +
                                '         <h5 class="modal-title"' +
                                '     id="removeWithFiltersModalLabel">' +
                                '         {{ __('Deleting results from a project') }} ' + value.name +
                                '         </h5>' +
                                '     <button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                                '         <span aria-hidden="true">&times;</span>' +
                                '     </button>' +
                                ' </div>' +
                                '     <div class="modal-body">' +

                                '         <div class="d-flex flex-row">' +
                                '             <div>' +
                                '                 <label>{{ __('Scans performed after (inclusive)') }}</label>' +
                                '                 <input class="form form-control" type="date"' +
                                '                        id="date-filter-after-' + value.id + '">' +
                                '             </div>' +

                                '             <div>' +
                                '                 <label>{{ __('Scans performed before (inclusive)') }}</label>' +
                                '                 <input class="form form-control" type="date"' +
                                '                        id="date-filter-before-' + value.id + '">' +
                                '             </div>' +
                                '         </div>' +

                                '         <label class="mt-3">{{ __('Comment') }}</label>' +
                                '         <input type="text" class="form form-control" name="comment-filter"' +
                                '                id="comment-filter-' + value.id + '">' +

                                '             <label class="mt-3">{{ __('Phrase') }}</label>' +
                                '             <input type="text" class="form form-control" name="phrase-filter"' +
                                '                    id="phrase-filter-' + value.id + '">' +

                                '                 <label class="mt-3">{{ __('Region') }}</label>'
                                + select +
                                '                 <label class="mt-3">{{ __('Link') }}</label>' +
                                '                 <input type="text" class="form form-control"' +
                                '                        name="link-filter"' +
                                '                        id="link-filter-' + value.id + '">' +

                                '                     <div class="d-flex flex-row mt-3 mb-3">' +
                                '                         <div>' +
                                '                             <label>{{ __('Position from (inclusive)') }}</label>' +
                                '                             <input class="form form-control" type="number"' +
                                '                                    id="position-filter-after-' + value.id + '"' +
                                '                                    placeholder="{{ __('0 - did not get into the top 100') }}">' +
                                '                         </div>' +
                                '                         <div>' +
                                '                             <label>{{ __('Position up to (inclusive)') }}</label>' +
                                '                             <input class="form form-control" type="number"' +
                                '                                    id="position-filter-before-' + value.id + '"' +
                                '                                    placeholder="{{ __('0 - did not get into the top 100') }}">' +
                                '                         </div>' +
                                '                     </div>' +
                                '                     <span class="__helper-link ui_tooltip_w">' +
                                '                             {{ __('How it works') }}' +
                                '                             <i class="fa fa-question-circle" style="color: grey"></i>' +
                                '                             <span class="ui_tooltip __right" style="width: 350px">' +
                                '                                 <span class="ui_tooltip_content">' +
                                '                                    {{ __('According to your project') }} ' + value.name + ' {{ __('the results of the scans will be searched by the filter that you will generate.') }} <br>' +
                                '                                     {{ __('All matches found will be deleted.') }} <br>' +
                                '                                     {{ __("If you don't want to search by any parameter, then leave the field empty.") }}' +
                                '                                 </span>' +
                                '                             </span>' +
                                '                         </span>' +
                                '                     <div class="text-danger mt-3 mb-3">' +
                                '                         {{ __('You can delete all the results associated with the project') }}' + value.name +
                                '                         , {{ __('if you leave all fields empty, be careful') }}' +
                                '                     </div>' +
                                '     </div>' +
                                '     <div class="modal-footer">' +
                                '         <button type="button" class="btn btn-secondary remove-with-filters"' +
                                '                 data-dismiss="modal" data-target="' + value.id + '">' +
                                '             {{ __('Remove') }}' +
                                '         </button>' +
                                '         <button type="button" class="btn btn-default"' +
                                '                 data-dismiss="modal">{{ __('Do not delete') }}</button>' +
                                '     </div>' +
                                ' </div>' +
                                ' </div>' +
                                ' </div>'

                        })

                        $('#block-for-modals').html(newModals)

                        $('.repeat-scan-unique-sites').on('click', function () {
                            $.ajax({
                                type: "POST",
                                dataType: "json",
                                url: "/repeat-scan-unique-sites",
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                    id: $(this).attr('data-target'),
                                },
                                success: function (response) {
                                    if (response.code === 200) {
                                        getSuccessMessage(response.message)
                                        $.each(response.object, function (key, value) {
                                            $('#history-state-' + value).html(
                                                '<p>Обрабатывается..</p>' +
                                                '<div class="text-center" id="preloaderBlock">' +
                                                '        <div class="three col">' +
                                                '            <div class="loader" id="loader-1"></div>' +
                                                '        </div>' +
                                                '</div>'
                                            )
                                        })

                                    } else if (response.code === 415) {
                                        getErrorMessage(response.message)
                                    }
                                },
                            });
                        })

                        $('.start-through-analyse').on('click', function () {
                            $.ajax({
                                type: "POST",
                                dataType: "json",
                                url: "/start-through-analyse",
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                    id: $(this).attr('data-target'),
                                },
                                success: function (response) {
                                    if (response.code === 200) {
                                        getSuccessMessage(response.message, 5000)
                                    } else if (response.code === 415) {
                                        getErrorMessage(response.message, 15000)
                                    }
                                },
                            });
                        })

                        $('.remove-empty-results').on('click', function () {
                            $.ajax({
                                type: "POST",
                                dataType: "json",
                                url: "/remove-scan-results",
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                    id: $(this).attr('data-target'),
                                },
                                success: function (response) {
                                    if (response.code === 200) {
                                        getSuccessMessage(response.message)
                                        setValues(response)
                                        usersProjects.draw()
                                    } else if (response.code === 415) {
                                        getErrorMessage(response.message)
                                    }
                                },
                            });
                        })

                        $('.remove-with-filters').on('click', function () {
                            let id = $(this).attr('data-target');

                            if ($('#comment-filter-' + id).val() === '' &&
                                $('#phrase-filter-' + id).val() === '' &&
                                $('#region-filter-' + id).val() === 'none' &&
                                $('#link-filter-' + id).val() === '' &&
                                $('#date-filter-before-' + id).val() === '' &&
                                $('#date-filter-after-' + id).val() === '' &&
                                $('#position-filter-after-' + id).val() === '' &&
                                $('#position-filter-before-' + id).val() === ''
                            ) {
                                if (!confirm('У вас будут удалены ВСЕ результаты проекта.')) {
                                    getSuccessMessage('Удаление было отменено')
                                    return;
                                }
                            }
                            $.ajax({
                                type: "POST",
                                dataType: "json",
                                url: "/remove-scan-results-with-filters",
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                    id: id,
                                    comment: $('#comment-filter-' + id).val(),
                                    phrase: $('#phrase-filter-' + id).val(),
                                    region: $('#region-filter-' + id).val(),
                                    link: $('#link-filter-' + id).val(),
                                    before: $('#date-filter-before-' + id).val(),
                                    after: $('#date-filter-after-' + id).val(),
                                    positionAfter: $('#position-filter-after-' + id).val(),
                                    positionBefore: $('#position-filter-before-' + id).val()
                                },
                                success: function (response) {
                                    if (response.code === 200) {
                                        getSuccessMessage(response.message)
                                        usersProjects.draw()
                                    } else if (response.code === 415) {
                                        getErrorMessage(response.message)
                                    }
                                },
                            });
                        })
                    }
                });
            });

            function getRegionName(id) {
                switch (id) {
                    case '1' :
                        return "{{ __('Moscow') }}";
                    case '20' :
                        return "{{ __('Arkhangelsk') }}";
                    case '37' :
                        return "{{ __('Astrakhan') }}";
                    case '197' :
                        return "{{ __('Barnaul') }}";
                    case '4' :
                        return "{{ __('Belgorod') }}";
                    case '77' :
                        return "{{ __('Blagoveshchensk') }}";
                    case '191' :
                        return "{{ __('Bryansk') }}";
                    case '24' :
                        return "{{ __('Veliky Novgorod') }}";
                    case '75' :
                        return "{{ __('Vladivostok') }}";
                    case '33' :
                        return "{{ __('Vladikavkaz') }}";
                    case '192' :
                        return "{{ __('Vladimir') }}";
                    case '38' :
                        return "{{ __('Volgograd') }}";
                    case '21' :
                        return "{{ __('Vologda') }}";
                    case '193' :
                        return "{{ __('Voronezh') }}";
                    case '1106' :
                        return "{{ __('Grozny') }}";
                    case '54' :
                        return "{{ __('Ekaterinburg') }}";
                    case '5' :
                        return "{{ __('Ivanovo') }}";
                    case '63' :
                        return "{{ __('Irkutsk') }}";
                    case '41' :
                        return "{{ __('Yoshkar-ola') }}";
                    case '43' :
                        return "{{ __('Kazan') }}";
                    case '22' :
                        return "{{ __('Kaliningrad') }}";
                    case '64' :
                        return "{{ __('Kemerovo') }}";
                    case '7' :
                        return "{{ __('Kostroma') }}";
                    case '35' :
                        return "{{ __('Krasnodar') }}";
                    case '62' :
                        return "{{ __('Krasnoyarsk') }}";
                    case '53' :
                        return "{{ __('Kurgan') }}";
                    case '8' :
                        return "{{ __('Kursk') }}";
                    case '9' :
                        return "{{ __('Lipetsk') }}";
                    case '28' :
                        return "{{ __('Makhachkala') }}";
                    case '213' :
                        return "{{ __('Moscow') }}";
                    case '23' :
                        return "{{ __('Murmansk') }}";
                    case '1092' :
                        return "{{ __('Nazran') }}";
                    case '30' :
                        return "{{ __('Nalchik') }}";
                    case '47' :
                        return "{{ __('Nizhniy Novgorod') }}";
                    case '65' :
                        return "{{ __('Novosibirsk') }}";
                    case '66' :
                        return "{{ __('Omsk') }}";
                    case '10' :
                        return "{{ __('Eagle') }}";
                    case '48' :
                        return "{{ __('Orenburg') }}";
                    case '49' :
                        return "{{ __('Penza') }}";
                    case '50' :
                        return "{{ __('Perm') }}";
                    case '25' :
                        return "{{ __('Pskov') }}";
                    case '39' :
                        return "{{ __('Rostov-on') }}";
                    case '11' :
                        return "{{ __('Ryazan') }}";
                    case '51' :
                        return "{{ __('Samara') }}";
                    case '42' :
                        return "{{ __('Saransk') }}";
                    case '2' :
                        return "{{ __('Saint-Petersburg') }}";
                    case '12' :
                        return "{{ __('Smolensk') }}";
                    case '239' :
                        return "{{ __('Sochi') }}";
                    case '36' :
                        return "{{ __('Stavropol') }}";
                    case '973' :
                        return "{{ __('Surgut') }}";
                    case '13' :
                        return "{{ __('Tambov') }}";
                    case '14' :
                        return "{{ __('Tver') }}";
                    case '67' :
                        return "{{ __('Tomsk') }}";
                    case '15' :
                        return "{{ __('Tula') }}";
                    case '195' :
                        return "{{ __('Ulyanovsk') }}";
                    case '172' :
                        return "{{ __('Ufa') }}";
                    case '76' :
                        return "{{ __('Khabarovsk') }}";
                    case '45' :
                        return "{{ __('Cheboksary') }}";
                    case '56' :
                        return "{{ __('Chelyabinsk') }}";
                    case '1104' :
                        return "{{ __('Cherkessk') }}";
                    case '16' :
                        return "{{ __('Yaroslavl') }}";
                }
            }

            let columns = [
                {
                    name: 'id',
                    data: function (row) {
                        return '<span>' +
                            '    <span class="project_name" style="cursor: pointer"' +
                            '          data-order="' + row.id + '">' + row.name + '</span>' +
                            '    <i class="fa fa-table project_name"' +
                            '       data-order="' + row.id + '" style="opacity: 0.6; cursor:pointer;"></i>' +
                            '    <i class="fa fa-list project_name_v2"' +
                            '       data-order="' + row.id + '"' +
                            '       style="opacity: 0.6; cursor:pointer;"></i>' +
                            '</span>' +
                            '<div class="dropdown" style="display: inline">' +
                            '    <i class="fa fa-cogs" id="dropdownMenuButton" data-toggle="dropdown"' +
                            '       aria-expanded="false" style="opacity: 0.6; cursor: pointer"></i>' +
                            '    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">' +
                            '                   <span class="dropdown-item project_name"' +
                            '  style="cursor:pointer;"' +
                            '  data-order="' + row.id + '">' +
                            '<i class="fa fa-table"></i>' +
                            '{{ __('Show the results of the analysis') }}' +
                            '                   </span>' +
                            '        <span class="dropdown-item project_name_v2"' +
                            '              style="cursor:pointer;"' +
                            '              data-order="' + row.id + '">' +
                            '<i class="fa fa-list"></i>' +
                            '{{ __('View the results in a list') }}' +
                            '                   </span>' +
                            '        <span class="dropdown-item"' +
                            '              style="cursor:pointer;"' +
                            '              data-toggle="modal" data-target="#removeModal' + row.id + '">' +
                            '<i class="fa fa-trash"></i>' +
                            '{{ __('Delete results without comments') }}' +
                            '                   </span>' +
                            '        <span class="dropdown-item"' +
                            '              style="cursor:pointer;"' +
                            '              data-toggle="modal"' +
                            '              data-target="#removeWithFiltersModal' + row.id + '">' +
                            '<i class="fa fa-trash"></i>' +
                            '{{ __('Delete using filters') }}' +
                            '                   </span>' +
                            '    </div>' +
                            '</div>'
                    }
                },
                {
                    name: 'relevanceTags',
                    data: function (row) {
                        let content = ''

                        $.each(row.relevanceTags, function (key, value) {
                            content +=
                                '<div class="d-flex justify-content-between align-items-center" style="color: ' + value.color + '" id="tag-' + value.id + '-item-' + row.id + '">' +
                                value.name +
                                '    <i class="fa fa-trash"' +
                                '       style="opacity: 0.5; cursor: pointer"' +
                                '       data-toggle="modal"' +
                                '       data-target="#removeTagModal' + value.id + '' + row.id + '">' +
                                '    </i>' +
                                '</div>'
                        })

                        return content;
                    },
                },
                {
                    name: 'owner',
                    data: function (row) {
                        return row.owner.email + '<br><span class="text-muted">' + row.owner.name + '' + row.owner.last_name + '</span>'
                    },
                },
                {
                    name: 'count_sites',
                    data: function (row) {
                        return '<span class="count-sites-' + row.id + '">' + row.count_sites + '</span>' +
                            '<i class="fa fa-repeat" style="opacity: 0.6; cursor: pointer"' +
                            '   data-target="#repeatUniqueScan' + row.id + '"' +
                            '   data-toggle="modal" data-placement="top"' +
                            '   title="{{ __('restart analyzed pages') }}"></i>'
                    },

                },
                {
                    name: 'count_checks',
                    data: 'count_checks',
                },
                {
                    name: 'total_points',
                    data: 'total_points',
                },
                {
                    name: 'avg_position',
                    data: 'avg_position',
                },
                {
                    name: 'though',
                    data: function (row) {
                        if (row.though.id) {
                            return '<div id="though' + row.id + '" class="mt-2 mb-2">' +
                                '    <a href="/show-though/' + row.though.id + '" target="_blank">' +
                                '        {{ __('Results of end-to-end analysis') }}' +
                                '    </a>' +
                                '    <div class="text-muted">' +
                                '        {{ __('Last analysis') }} ' + '' + row.though.updated_at +
                                '    </div>' +
                                '</div>'
                        } else {
                            return '<button data-target="#startThroughScan' + row.id + '" ' +
                                ' data-toggle="modal"' +
                                ' data-placement="top" class="btn btn-secondary">' +
                                ' Сквозной анализ' +
                                '</button>'
                        }
                    },
                },
                {
                    name: 'last_check',
                    data: 'last_check',
                },
            ];

            $(".dt-button").addClass('btn btn-secondary')

            function getSuccessMessage(message, time = 3000) {
                $('.toast-top-right.success-message').show(300)
                $('#message-info').html(message)
                setTimeout(() => {
                    $('.toast-top-right.success-message').hide(300)
                }, time)
            }

            function getErrorMessage(message, time = 3000) {
                $('.toast-top-right.error-message').show(300)
                $('#message-error-info').html(message)
                setTimeout(() => {
                    $('.toast-top-right.error-message').hide(300)
                }, time)
            }

            function customFiltersWithoutComment(tableID, table, prefix = '', index = 0) {
                $.fn.dataTable.ext.search.push(function (settings, data) {
                    let target = String(data[index]);
                    return isDateValid(target, settings, tableID, prefix)
                });
                $('#dateMin' + prefix).change(function () {
                    table.draw();
                });
                $('#dateMax' + prefix).change(function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    let phraseSearch = String($('#phraseSearch' + prefix).val()).toLowerCase();
                    let target = String(data[index + 1]).toLowerCase();
                    return isIncludes(target, phraseSearch, settings, tableID)
                });
                $('#phraseSearch' + prefix).keyup(function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    let regionSearch = String($('#regionSearch' + prefix).val()).toLowerCase();
                    let target = String(data[index + 2]).toLowerCase();
                    return isIncludes(target, regionSearch, settings, tableID)
                });
                $('#regionSearch' + prefix).keyup(function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    let mainPageSearch = String($('#mainPageSearch' + prefix).val()).toLowerCase();
                    let target = String(data[index + 3]).toLowerCase();
                    return isIncludes(target, mainPageSearch, settings, tableID)
                });
                $('#mainPageSearch' + prefix).keyup(function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    let maxPosition = parseFloat($('#maxPosition' + prefix).val());
                    let minPosition = parseFloat($('#minPosition' + prefix).val());
                    let target = parseFloat(data[index + 4]);
                    return isValidate(minPosition, maxPosition, target, settings, tableID)
                });
                let pos = '#minPosition' + prefix + ', #maxPosition' + prefix
                $(pos).keyup(function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    let maxPoints = parseFloat($('#maxPoints' + prefix).val());
                    let minPoints = parseFloat($('#minPoints' + prefix).val());
                    let target = parseFloat(data[index + 5]);
                    return isValidate(minPoints, maxPoints, target, settings, tableID)
                });
                let points = '#minPoints' + prefix + ', #maxPoints' + prefix
                $(points).keyup(function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    let maxCoverage = parseFloat($('#maxCoverage' + prefix).val());
                    let minCoverage = parseFloat($('#minCoverage' + prefix).val());
                    let target = parseFloat(data[index + 6]);
                    return isValidate(minCoverage, maxCoverage, target, settings, tableID)
                });
                let coverage = '#minCoverage' + prefix + ', #maxCoverage' + prefix
                $(coverage).keyup(function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    let maxCoverageTf = parseFloat($('#maxCoverageTf' + prefix).val());
                    let minCoverageTf = parseFloat($('#minCoverageTf' + prefix).val());
                    let target = parseFloat(data[index + 7]);
                    return isValidate(minCoverageTf, maxCoverageTf, target, settings, tableID)
                });
                let covTf = '#minCoverageTf' + prefix + ', #maxCoverageTf' + prefix
                $(covTf).keyup(function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    let maxWidth = parseFloat($('#maxWidth' + prefix).val());
                    let minWidth = parseFloat($('#minWidth' + prefix).val());
                    let target = parseFloat(data[index + 8]);
                    return isValidate(minWidth, maxWidth, target, settings, tableID)
                });
                let width = '#minWidth' + prefix + ', #maxWidth' + prefix
                $(width).keyup(function () {
                    table.draw();
                });

                $.fn.dataTable.ext.search.push(function (settings, data) {
                    let maxDensity = parseFloat($('#maxDensity' + prefix).val());
                    let minDensity = parseFloat($('#minDensity' + prefix).val());
                    let target = parseFloat(data[index + 9]);
                    return isValidate(minDensity, maxDensity, target, settings, tableID)
                });
                let density = '#minDensity' + prefix + ', #maxDensity' + prefix
                $(density).keyup(function () {
                    table.draw();
                });
            }

            function getTextResult(result, ideal) {
                return "{{ __('The landing page received') }}" + ' <b>' + result + '</b>.<br> ' + ' {{ __('Recommended value')}}' + ' <b>' + ideal + '.</b>';
            }

            function getSelect(id) {
                return '<select class="custom-select rounded-0 region" id="region-filter-' + id + '">' +
                    '    <option value="none">' + "{{ __("Don't search for matches by region") }}" + '</option>' +
                    '    <option value="all">Любой регион</option>' +
                    '    <option value="213">' + "{{ __('Moscow') }}" + '</option>' +
                    '    <option value="1">' + "{{ __('Moscow and the area') }}" + '</option>' +
                    '    <option value="20">' + "{{ __('Arkhangelsk') }}" + '</option>' +
                    '    <option value="37">' + "{{ __('Astrakhan') }}" + '</option>' +
                    '    <option value="197">' + "{{ __('Barnaul') }}" + '</option>' +
                    '    <option value="4">' + "{{ __('Belgorod') }}" + '</option>' +
                    '    <option value="77">' + "{{ __('Blagoveshchensk') }}" + '</option>' +
                    '    <option value="191">' + "{{ __('Bryansk') }}" + '</option>' +
                    '    <option value="24">' + "{{ __('Veliky Novgorod') }}" + '</option>' +
                    '    <option value="75">' + "{{ __('Vladivostok') }}" + '</option>' +
                    '    <option value="33">' + "{{ __('Vladikavkaz') }}" + '</option>' +
                    '    <option value="192">' + "{{ __('Vladimir') }}" + '</option>' +
                    '    <option value="38">' + "{{ __('Volgograd') }}" + '</option>' +
                    '    <option value="21">' + "{{ __('Vologda') }}" + '</option>' +
                    '    <option value="193">' + "{{ __('Voronezh') }}" + '</option>' +
                    '    <option value="1106">' + "{{ __('Grozny') }}" + '</option>' +
                    '    <option value="54">' + "{{ __('Ekaterinburg') }}" + '</option>' +
                    '    <option value="5">' + "{{ __('Ivanovo') }}" + '</option>' +
                    '    <option value="63">' + "{{ __('Irkutsk') }}" + '</option>' +
                    '    <option value="41">' + "{{ __('Yoshkar-ola') }}" + '</option>' +
                    '    <option value="43">' + "{{ __('Kazan') }}" + '</option>' +
                    '    <option value="22">' + "{{ __('Kaliningrad') }}" + '</option>' +
                    '    <option value="64">' + "{{ __('Kemerovo') }}" + '</option>' +
                    '    <option value="7">' + "{{ __('Kostroma') }}" + '</option>' +
                    '    <option value="35">' + "{{ __('Krasnodar') }}" + '</option>' +
                    '    <option value="62">' + "{{ __('Krasnoyarsk') }}" + '</option>' +
                    '    <option value="53">' + "{{ __('Kurgan') }}" + '</option>' +
                    '    <option value="8">' + "{{ __('Kursk') }}" + '</option>' +
                    '    <option value="9">' + "{{ __('Lipetsk') }}" + '</option>' +
                    '    <option value="28">' + "{{ __('Makhachkala') }}" + '</option>' +
                    '    <option value="23">' + "{{ __('Murmansk') }}" + '</option>' +
                    '    <option value="1092">' + "{{ __('Nazran') }}" + '</option>' +
                    '    <option value="30">' + "{{ __('Nalchik') }}" + '</option>' +
                    '    <option value="47">' + "{{ __('Nizhniy Novgorod') }}" + '</option>' +
                    '    <option value="65">' + "{{ __('Novosibirsk') }}" + '</option>' +
                    '    <option value="66">' + "{{ __('Omsk') }}" + '</option>' +
                    '    <option value="10">' + "{{ __('Eagle') }}" + '</option>' +
                    '    <option value="48">' + "{{ __('Orenburg') }}" + '</option>' +
                    '    <option value="49">' + "{{ __('Penza') }}" + '</option>' +
                    '    <option value="50">' + "{{ __('Perm') }}" + '</option>' +
                    '    <option value="25">' + "{{ __('Pskov') }}" + '</option>' +
                    '    <option value="39">' + "{{ __('Rostov-on-Don') }}" + '</option>' +
                    '    <option value="11">' + "{{ __('Ryazan') }}" + '</option>' +
                    '    <option value="51">' + "{{ __('Samara') }}" + '</option>' +
                    '    <option value="42">' + "{{ __('Saransk') }}" + '</option>' +
                    '    <option value="2">' + "{{ __('Saint-Petersburg') }}" + '</option>' +
                    '    <option value="12">' + "{{ __('Smolensk') }}" + '</option>' +
                    '    <option value="239">' + "{{ __('Sochi') }}" + '</option>' +
                    '    <option value="36">' + "{{ __('Stavropol') }}" + '</option>' +
                    '    <option value="10649">' + "{{ __('Stary Oskol') }}" + '</option>' +
                    '    <option value="973">' + "{{ __('Surgut') }}" + '</option>' +
                    '    <option value="13">' + "{{ __('Tambov') }}" + '</option>' +
                    '    <option value="14">' + "{{ __('Tver') }}" + '</option>' +
                    '    <option value="67">' + "{{ __('Tomsk') }}" + '</option>' +
                    '    <option value="15">' + "{{ __('Tula') }}" + '</option>' +
                    '    <option value="195">' + "{{ __('Ulyanovsk') }}" + '</option>' +
                    '    <option value="172">' + "{{ __('Ufa') }}" + '</option>' +
                    '    <option value="76">' + "{{ __('Khabarovsk') }}" + '</option>' +
                    '    <option value="45">' + "{{ __('Cheboksary') }}" + '</option>' +
                    '    <option value="56">' + "{{ __('Chelyabinsk') }}" + '</option>' +
                    '    <option value="1104">' + "{{ __('Cherkessk') }}" + '</option>' +
                    '    <option value="16">' + "{{ __('Yaroslavl') }}" + '</option>' +
                    '</select>'
            }

            function setValues(response) {
                $('.count-sites-' + response.objectId).html(response.countSites)
                $('.total-points-' + response.objectId).html(response.points)
                $('.count-checks-' + response.objectId).html(response.countChecks)
                $('.total-positions-' + response.objectId).html(response.avgPosition)
                if (response.removed == 1) {
                    $('#story-id-' + response.objectId).remove()
                } else {
                    $('a[data-order="' + response.objectId + '"]').trigger('click')
                }
            }
        </script>
    @endslot
@endcomponent
