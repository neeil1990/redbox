@component('component.card', ['title' => __('Project') . ' ' .  $project->name ])
    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/common.css') }}"/>
        <!-- daterange picker -->
        <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">

        <style>
            .exist-position {
                color: #28a745 !important;
                font-weight: bold;
            }
        </style>
    @endslot

    <div id="toast-container" class="toast-top-right error-message" style="display:none;">
        <div class="toast toast-error" aria-live="polite">
            <div class="toast-message"></div>
        </div>
    </div>

    <div class="row">
        @foreach($navigations as $navigation)
            <div class="col-lg-2 col-6">
                <a href="{{ $navigation['href'] }}" class="small-box {{ $navigation['bg'] }}" style="min-height: 137px">
                    <div class="inner">
                        @if($navigation['h3'])
                            <h3 class="mb-0">{{ $navigation['h3'] }}</h3>
                        @endif

                        {!! $navigation['content'] !!}

                        @isset($navigation['small'])
                            <small>{{ $navigation['small'] }}</small>
                        @endisset
                    </div>
                    <div class="icon">
                        <i class="{{ $navigation['icon'] }}"></i>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="d-flex flex-row mt-3">
        <a class="btn btn-outline-secondary mr-2" href="{{ route('monitoring.competitors.positions', $project->id) }}">
            {{ __('Comparison with competitors') }}
        </a>
    </div>

    <div id="dateRange" class="mt-5">
        <h3 class="mt-3">{{ __('Project') . ' ' .  $project->name }}</h3>
        <h3>{{ __('Changes by top and date') }}</h3>
        <div class="card mt-3">
            <div class="card-header d-flex flex-row justify-content-start align-items-center">
                <div class="input-group col-8 pl-0 ml-0">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control" id="date-range">
                    <select name="region" class="custom-select" id="searchEngines">
                        @foreach($searchEngines as $search)
                            @if($search->id == request('region'))
                                <option value="{{ $search->id }}"
                                        selected>{{ strtoupper($search->engine) }} {{ $search->location->name }}
                                    [{{$search->lr}}]
                                </option>
                            @else
                                <option
                                    value="{{ $search->id }}">{{ strtoupper($search->engine) }} {{ $search->location->name }}
                                    [{{$search->lr}}]
                                </option>
                            @endif
                        @endforeach
                    </select>
                    <button id="competitors-history-positions" class="btn btn-default"
                            style="border-top-left-radius: 0; border-bottom-left-radius: 0">
                        {{ __('Analyse') }}
                    </button>
                </div>
            </div>
            <div class="card-body" id="history-block">
                <table class="table table-bordered w-50">
                    <thead>
                    <tr>
                        <th>{{ __('Date range') }}</th>
                        <th>{{ __('Region') }}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="changeDatesTbody">
                    @if(count($project->dates) > 0)
                        @foreach($project->dates as $result)
                            <tr @if($result['state'] === 'in queue' || $result['state'] === 'in process') class="need-check"
                                data-id="{{ $result['id'] }}"
                                id="analyse-in-queue-{{ $result['id'] }}" @endif>
                                <td>{{ $result['range'] }}</td>
                                <td>
                                    @foreach($searchEngines as $engine)
                                        @if($engine['id'] == json_decode($result['request'], true)['region'])
                                            {{ strtoupper($engine['engine']) }}, {{ $engine['location']['name'] }}
                                            [{{ $engine['location']['lr'] }}]
                                            @break
                                        @endif
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    @if($result['state'] === 'ready')
                                        <a class="btn btn-default"
                                           href="{{ route('monitoring.changes.dates.result', $result['id']) }}"
                                           target="_blank">{{ __('show') }}</a>
                                        <button class="btn btn-default remove-error-results"
                                                data-id="{{ $result['id'] }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @elseif($result['state'] === 'in queue')
                                        {{ __("In queue") }}
                                        <img src="/img/1485.gif" style="width: 20px; height: 20px;">
                                    @elseif($result['state'] === 'in process')
                                        {{ __("In process") }}
                                        <img src="/img/1485.gif" style="width: 20px; height: 20px;">
                                    @else
                                        {{ __('Fail') }}
                                        <button class="btn btn-default remove-error-results"
                                                data-id="{{ $result['id'] }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr id="empty-row">
                            <td class="text-center" colspan="3">{{ __('Empty') }}</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @slot('js')
        <!-- InputMask -->
        <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
        <script src="{{ asset('plugins/inputmask/jquery.inputmask.min.js') }}"></script>
        <!-- date-range-picker -->
        <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
        <script>
            const PROJECT_ID = {{ $project->id }};
            const REGION_ID = '{{ request('region', null) }}';

            let range = $('#date-range');
            range.daterangepicker({
                opens: 'left',
                startDate: moment().subtract(30, 'days'),
                endDate: moment(),
                ranges: {
                    'Последние 7 дней': [moment().subtract(6, 'days'), moment()],
                    'Последние 30 дней': [moment().subtract(29, 'days'), moment()],
                    'Последние 60 дней': [moment().subtract(59, 'days'), moment()],
                },
                alwaysShowCalendars: true,
                showCustomRangeLabel: false,
                locale: {
                    format: 'DD-MM-YYYY',
                    daysOfWeek: [
                        "Вс",
                        "Пн",
                        "Вт",
                        "Ср",
                        "Чт",
                        "Пт",
                        "Сб"
                    ],
                    monthNames: [
                        "Январь",
                        "Февраль",
                        "Март",
                        "Апрель",
                        "Май",
                        "Июнь",
                        "Июль",
                        "Август",
                        "Сентябрь",
                        "Октябрь",
                        "Ноябрь",
                        "Декабрь"
                    ],
                    firstDay: 1,
                }
            });

            range.on('updateCalendar.daterangepicker', function (ev, picker) {

                let container = picker.container;

                let leftCalendarEl = container.find('.drp-calendar.left tbody tr');
                let rightCalendarEl = container.find('.drp-calendar.right tbody tr');

                let leftCalendarData = picker.leftCalendar.calendar;
                let rightCalendarData = picker.rightCalendar.calendar;

                let showDates = [];

                for (let rows = 0; rows < leftCalendarData.length; rows++) {

                    let leftCalendarRowEl = $(leftCalendarEl[rows]);
                    $.each(leftCalendarData[rows], function (i, item) {

                        let leftCalendarDaysEl = $(leftCalendarRowEl.find('td').get(i));
                        if (!leftCalendarDaysEl.hasClass('off')) {

                            showDates.push({
                                date: item.format('YYYY-MM-DD'),
                                el: leftCalendarDaysEl,
                            });
                        }
                    });

                    let rightCalendarRowEl = $(rightCalendarEl[rows]);
                    $.each(rightCalendarData[rows], function (i, item) {

                        let rightCalendarDaysEl = $(rightCalendarRowEl.find('td').get(i));
                        if (!rightCalendarDaysEl.hasClass('off')) {

                            showDates.push({
                                date: item.format('YYYY-MM-DD'),
                                el: rightCalendarDaysEl,
                            });
                        }
                    });
                }

                axios.post('/monitoring/projects/get-positions-for-calendars', {
                    projectId: PROJECT_ID,
                    regionId: REGION_ID,
                    dates: showDates,
                }).then(function (response) {
                    $.each(response.data, function (i, item) {

                        let found = showDates.find(function (elem) {
                            if (elem.date === item.dateOnly)
                                return true;
                        });

                        if (!found.el.hasClass('exist-position'))
                            found.el.addClass('exist-position');
                    });
                })
            });

            $('#competitors-history-positions').unbind().on('click', function () {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('monitoring.competitors.history.positions') }}",
                    data: {
                        'projectId': PROJECT_ID,
                        'region': $('#searchEngines').val(),
                        'dateRange': $('#date-range').val(),
                    },
                    success: function (response) {
                        $('#empty-row').remove()
                        $('#changeDatesTbody').append(
                            '<tr id="analyse-in-queue-' + response.analyseId + '">' +
                            '   <td>' + $('#date-range').val() + '</td>' +
                            '   <td>' + String($('#searchEngines option:selected').text()).trim() + '</td>' +
                            '   <td class="text-center">' + "{{ __('In queue') }}" + ' <img src="/img/1485.gif" style="width: 20px; height: 20px;"></td>' +
                            '</tr>')
                        waitFinishAnalyse(response.analyseId)
                    },
                })
            })

            removeErrorResults()
            needCheck()

            function removeErrorResults() {
                $('.remove-error-results').unbind().on('click', function () {
                    let bool = confirm('Подтвердите удаление результатов')
                    if (bool) {
                        let $elem = $(this)
                        $.ajax({
                            type: "POST",
                            url: "{{ route('monitoring.changes.dates.remove') }}",
                            data: {
                                'id': $(this).attr('data-id'),
                            },
                            success: function () {
                                $elem.parents().eq(1).remove()
                            }
                        })
                    }
                })
            }

            function needCheck() {
                $.each($('.need-check'), function (k, v) {
                    waitFinishAnalyse($(this).attr('data-id'))
                })
            }

            function waitFinishAnalyse(recordId) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('monitoring.changes.dates.check') }}",
                    data: {
                        'id': recordId,
                    },
                    success: function (response) {
                        if (response.state === 'ready') {
                            $('#analyse-in-queue-' + recordId).children('td').eq(2).html(
                                '<a class="btn btn-default" href="/monitoring/competitors/result-analyse/' + recordId + '" target="_blank">{{ __('show') }}</a>' +
                                ' <button class="btn btn-default remove-error-results" data-id="' + recordId + '">' +
                                '    <i class="fa fa-trash"></i>' +
                                '</button>'
                            )

                            removeErrorResults()
                        } else if (response.state === 'in process') {
                            $('#analyse-in-queue-' + recordId).children('td').eq(2).html("{{ __('In process') }}" + ' <img src="/img/1485.gif" style="width: 20px; height: 20px;">')
                            setTimeout(() => {
                                waitFinishAnalyse(recordId)
                            }, 10000)
                        } else if (response.state === 'fail') {
                            $('#analyse-in-queue-' + recordId).children('td').eq(2).html("{{ __('Fail') }}" +
                                '<button class="btn btn-default remove-error-results" data-id="' + recordId + '">' +
                                '    <i class="fa fa-trash"></i>' +
                                '</button>')
                            removeErrorResults()
                        } else {
                            setTimeout(() => {
                                waitFinishAnalyse(recordId)
                            }, 10000)
                        }
                    },
                })
            }
        </script>
    @endslot()
@endcomponent
