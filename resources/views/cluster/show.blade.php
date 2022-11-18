@php use Illuminate\Support\Str; @endphp
@component('component.card', ['title' =>  __('Analysis results') ])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <style>
            #clusters-table > tbody > tr > td > table > thead:hover {
                background: transparent !important;
            }

            .centered-text {
                text-align: center;
                vertical-align: inherit;
            }

            .ui_tooltip_content {
                width: 325px;
            }

            .dataTables_info, .hidden-result-table_filter {
                display: none;
            }

            .bg-cluster-warning {
                background: rgba(245, 226, 170, 0.5);
            }

            i:hover {
                cursor: pointer;
                color: black;
            }

            #scroll_top {
                display: none;
                position: fixed;
                bottom: 80px;
                right: 30px;
                z-index: 1000;
                width: 32px;
                height: 32px;
                background: url(https://snipp.ru/img/scroll_top.png) 50% 50% no-repeat;
                border-radius: 50%;
                opacity: 0.5;
            }

            #scroll_bottom {
                display: none;
                position: fixed;
                bottom: 30px;
                right: 30px;
                z-index: 1000;
                width: 32px;
                height: 32px;
                background: url(https://snipp.ru/img/scroll_top.png) 50% 50% no-repeat;
                border-radius: 50%;
                opacity: 0.5;
                transform: rotate(180deg);
            }

            #scroll_top:hover, #scroll_bottom:hover {
                opacity: 1;
            }
        </style>
    @endslot

    <div id="toast-container" class="toast-top-right success-message">
        <div class="toast toast-success" aria-live="polite" style="display:none;">
            <div class="toast-message success-msg"></div>
        </div>
    </div>

    <div id="toast-container" class="toast-top-right error-message">
        <div class="toast toast-error" aria-live="assertive" style="display:none;">
            <div
                class="toast-message error-msg">{{ __('An unexpected error has occurred, please contact the administrator') }}</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex p-0">
            <ul class="nav nav-pills p-2">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('cluster') }}">{{ __('Analyzer') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link admin-link"
                       href="{{ route('cluster.projects') }}">{{ __('My projects') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link admin-link active"
                       href="{{ route('cluster.configuration') }}">{{ __('My project') }}</a>
                </li>
                @if($admin)
                    <li>
                        <a class="nav-link admin-link" href="{{ route('cluster.configuration') }}">
                            {{ __('Module administration') }}
                        </a>
                    </li>
                @endif
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <div id="progress-bar" style="display: none">
                        <div class="progress-bar mt-3 mb-3" role="progressbar"></div>
                        <img src="/img/1485.gif" alt="preloader_gif" width="20">
                    </div>

                    <div id="params">
                        <span class="d-flex w-100 justify-content-between" style="margin-top: -1px;">
                            <span>
                                Уровень кластеризации: {{ $cluster['request']['clusteringLevel'] }}
                            </span>
                            <span>
                                Версия: {{ $cluster['request']['engineVersion'] }}
                            </span>
                            <span>
                                Количетсво фраз: {{ $cluster['count_phrases'] }}
                            </span>
                            <span>
                                Количетсво кластеров: {{ $cluster['count_clusters'] }}
                            </span>
                            <span>
                                Фразы:
                                <span class="__helper-link ui_tooltip_w">
                                    <i class="fa fa-paperclip"></i>
                                    <span class="ui_tooltip __bottom">
                                        <span class="ui_tooltip_content" style="width: 450px !important;">
                                            @foreach(explode("\n", $cluster['request']['phrases']) as $phrase)
                                                <div>{{ $phrase }}</div>
                                            @endforeach
                                        </span>
                                    </span>
                                </span>
                                <i class="fa fa-copy" id="copyUsedPhrases"></i>
                                <textarea name="usedPhrases" id="usedPhrases"
                                          style="display: none">{!! $cluster['request']['phrases'] !!}</textarea>
                            </span>
                            <span>
                                Регион: {{ \App\Cluster::getRegionName($cluster['request']['region']) }}
                            </span>
                        </span>
                    </div>
                </div>

                <div id="block-for-downloads-files" style="display: none">
                    <h3>{{ __('Cluster table') }}</h3>
                    <table id="hidden-result-table" style="display: none">
                        <thead>
                        <tr>
                            <th colspan="4"></th>
                            <th class="centered-text" colspan="3">{{ __('Frequency') }}</th>
                        </tr>
                        <tr>
                            <th>{{ __('Serial number') }}</th>
                            <th>{{ __('Sequence number in the cluster') }}</th>
                            <th>{{ __('Key query') }}</th>
                            <th>{{ __('Group') }}</th>
                            <th>{{ __('Base') }}</th>
                            <th>"{{ __('Phrasal') }}"</th>
                            <th>"!{{ __('Target') }}"</th>
                        </tr>
                        </thead>
                        <tbody id="hidden-table-tbody">
                        </tbody>
                    </table>
                    <div style="width: 100%; overflow-x: scroll;">
                        <table id="clusters-table" class="table table-bordered dtr-inline">
                            <thead>
                            <tr>
                                <th>{{ __('Clusters') }}</th>
                                <th style="min-width: 333px;">{{ __('Competitors') }}</th>
                            </tr>
                            </thead>
                            <tbody id="clusters-table-tbody">
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end">
                        <div>
                            Количетсво кластеров: {{ $cluster['count_clusters'] }}
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary mt-5" data-toggle="modal" data-target="#fastScan">
                        Пересобрать
                    </button>
                    <div class="brutForce mt-3 d-flex">
                        <div id="clusters-table-default" class="col-6" style="display:none;">
                            <h3>Изначальный вариант</h3>
                            <table id="default-hidden" style="display: none">
                                <thead>
                                <tr>
                                    <th class="border">порядковый номер</th>
                                    <th class="border">порядковый номер в кластере</th>
                                    <th class="border">Ключевой запрос</th>
                                </tr>
                                </thead>
                                <tbody id="clusters-result-hidden">
                                @php($total = 1)
                                @foreach (json_decode($cluster['result'], true) as $items)
                                    @php($iterator = 1)
                                    @foreach ($items as $phrase => $item)
                                        @if ($phrase !== 'finallyResult')
                                            <tr class="border">
                                                <td>{{ $total }}</td>
                                                <td>{{ $iterator }}</td>
                                                <td>{{ $phrase }}</td>
                                            </tr>
                                        @endif
                                        @php($iterator++)
                                    @endforeach
                                    @php($total++)
                                @endforeach
                                </tbody>
                            </table>
                            <table class="table table-bordered dtr-inline">
                                <thead>
                                <tr>
                                    <th>{{ __('Clusters') }}</th>
                                </tr>
                                </thead>
                                <tbody id="clusters-result-hidden">
                                @php($total = 1)
                                @foreach (json_decode($cluster['result'], true) as $items)
                                    <tr>
                                        <td class="p-0">
                                            <table class="default-cluster table table-hover text-nowrap no-footer mb-0"
                                                   id="{{ Str::random() }}">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>##</th>
                                                    <th>Ключевой запрос</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @php($iterator = 1)
                                                @foreach ($items as $phrase => $item)
                                                    @if ($phrase !== 'finallyResult')
                                                        <tr>
                                                            <td>{{ $total }}</td>
                                                            <td>{{ $iterator }}</td>
                                                            <td>
                                                                <div class="d-flex">
                                                                    <div class="mr-2">
                                                                        {{ $phrase }}
                                                                    </div>
                                                                    <div>
                                                                        <i class="fa fa-copy copy-full-urls"
                                                                           data-target="1"
                                                                           title="{{ __('Copy') }}"></i>
                                                                        <div style="display: none"
                                                                             id="hidden-urls-block-{{ $phrase }}">
                                                                            @foreach($item['sites'] as $site)
                                                                                {{ parse_url($site)['host'] . "\n" }}
                                                                            @endforeach
                                                                        </div>
                                                                        <span class="__helper-link ui_tooltip_w">
                                                                        <i class="fa fa-paperclip"></i>
                                                                        <span class="ui_tooltip __bottom"
                                                                              style="min-width: 250px;">
                                                                            <span class="ui_tooltip_content">
                                                                                @foreach($item['sites'] as $site)
                                                                                    <div>
                                                                                        <a href="{{$site}}"
                                                                                           target="_blank">
                                                                                            {{ parse_url($site)['host'] }}
                                                                                        </a>
                                                                                    </div>
                                                                                @endforeach
                                                                            </span>
                                                                        </span>
                                                                    </span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @php($iterator++)
                                                        @php($total++)
                                                    @endif
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end">
                                <div>
                                    Количетсво кластеров: <span>{{ $cluster['count_clusters'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div id="clusters-table-fast" class="col-6" style="display:none;">
                            <h3>Пересобранный вариант</h3>
                            <table id="hidden-result-fast" style="display:none;">
                                <thead>
                                <tr>
                                    <th>{{ __('Serial number') }}</th>
                                    <th>{{ __('Sequence number in the cluster') }}</th>
                                    <th>{{ __('Key query') }}</th>
                                </tr>
                                </thead>
                                <tbody id="hidden-fast-table-tbody">
                                </tbody>
                            </table>
                            <table class="table table-bordered dtr-inline">
                                <thead>
                                <tr>
                                    <th>{{ __('Clusters') }}</th>
                                </tr>
                                </thead>
                                <tbody id="clusters-fast-table-tbody">
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end">
                                <div>
                                    Количетсво кластеров: <span id="placeForCountClusters"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="fastScan" tabindex="-1" aria-labelledby="fastScanLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fastScanLabel"> пересобрать кластер на основе ранее
                        полученных данных</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group required">
                        <label>{{ __('clustering level') }}</label>
                        {!! Form::select('clusteringLevel', [
                            'light' => 'light - 40%',
                            'soft' => 'soft - 50%',
                            'pre-hard' => 'pre-hard - 60%',
                            'hard' => 'hard - 70%',
                            ], null, ['class' => 'custom-select rounded-0', 'id' => 'clusteringLevelFast']) !!}
                    </div>
                    <div class="form-group required">
                        <label>{{ __('Merging Clusters') }}</label>
                        {!! Form::select('engineVersion', [
                                'old' => __('Formation based on the first available phrase (old)'),
                                'new' => __('Forming a cluster based on an array of links (new)'),
                                'latest' => 'Дополнительная переборка (latest)',
                        ], null, ['class' => 'custom-select rounded-0', 'id' => 'engineVersionFast']) !!}
                    </div>
                    <div class="d-none">
                        {!! Form::select('count', array_unique([
                            $cluster['request']['count'] => $cluster['request']['count']
                        ]), null, ['class' => 'custom-select rounded-0', 'id' => 'countFast']) !!}
                    </div>
                    <div class="form-group required d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary mr-2"
                                data-dismiss="modal"
                                id="brutForceFast">
                            Пересобрать
                        </button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            {{ __('Close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <a href="#" id="scroll_top"></a>
    <a href="#" id="scroll_button"></a>
    <a href="#" id="scroll_bottom"></a>
    <textarea name="hiddenForCopy" id="hiddenForCopy" style="display: none"></textarea>
    <input type="hidden" id="progressId">
    @slot('js')
        <script>
            function successCopiedMessage() {
                $('.toast.toast-success').show(300)
                $('.toast-message.success-msg').html("{{ __('Successfully copied') }}")
                setTimeout(() => {
                    $('.toast.toast-success').hide(300)
                }, 3000)
            }

            $(function () {
                $(window).scroll(function () {
                    if ($(window).scrollTop() > 100) {
                        $('#scroll_top').show();
                        $('#scroll_bottom').show();
                    } else {
                        $('#scroll_top').hide();
                        $('#scroll_bottom').hide();
                    }
                });

                $("#scroll_button").on("click", function () {
                    $("html, body").animate({scrollTop: $('#brutForceFast').offset().top}, {duration: 600,});
                    return false;
                });

                $('#scroll_top').click(function () {
                    $('html, body').animate({scrollTop: 0}, 600);
                    return false;
                });

                $('#scroll_bottom').click(function () {
                    $('html, body').animate({scrollTop: $(document).height() - $(window).height()}, 600);
                    return false;
                });
            });
        </script>
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('/plugins/cluster/js/render-hidden-table.js') }}"></script>
        <script src="{{ asset('/plugins/cluster/js/render-result-table.js') }}"></script>
        <script src="{{ asset('/plugins/cluster/js/render-result-fast-table.js') }}"></script>
        <script src="{{ asset('/plugins/cluster/js/render-hidden-fast.js') }}"></script>
        <script src="{{ asset('/plugins/cluster/js/common.js') }}"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
        <script>
            $(document).ready(function () {
                $('#app > div > div > div.card-header').append($('#params').html())
                $('#params').remove()
                renderHiddenTable({!! $cluster['result'] !!})
                renderResultTable({!! $cluster['result'] !!})

                $('#default-hidden').dataTable({
                    'order': [[0, "asc"]],
                    'bPaginate': false,
                    'dom': 'lBfrtip',
                    'buttons': [
                        'copy', 'csv', 'excel'
                    ]
                })
                $('.dt-button').addClass('btn btn-secondary')
                $('.dt-buttons').addClass('pb-3')
                $('#default-hidden_filter').remove()

                // $.each($('.default-cluster'), function (key, value) {
                //     $('#' + $(this).attr('id')).dataTable({
                //         'order': [[0, "asc"]],
                //         'bPaginate': false,
                //         'orderCellsTop': true,
                //         'sDom': '<"top"i>rt<"bottom"lp><"clear">'
                //     })
                // })

                $('#copyUsedPhrases').click(function () {
                    successCopiedMessage()
                    $('#usedPhrases').css('display', 'block')

                    let text = document.getElementById("usedPhrases");
                    text.select();
                    document.execCommand("copy");
                    $('#usedPhrases').css('display', 'none')
                })

                $('#brutForceFast').on('click', function () {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('fast.scan.clusters') }}",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            count: $('#countFast').val(),
                            clusteringLevel: $('#clusteringLevelFast').val(),
                            engineVersion: $('#engineVersionFast').val(),
                            resultId: {{ $cluster['id'] }}
                        },
                        success: function (response) {
                            $('#clusters-table-default').show()
                            $('#hidden-result-fast').dataTable().fnDestroy()
                            $('.fast-render').remove()
                            $.each($('.render-table-fast'), function (key, value) {
                                $('#' + $(this).attr('id')).dataTable().fnDestroy()
                                $('#' + $(this).attr('id')).remove()
                            })

                            renderResultTableFast(response['sites'], response['count'])
                            renderHiddenFast(response['sites'])
                        },
                    });
                })
            })

            let progressId
            let interval

            $('#start-analysis').click(function () {
                if ($('#phrases').val() !== '') {
                    $(this).attr('disabled', true)
                    $.ajax({
                        type: "GET",
                        url: "{{ route('start.cluster.progress') }}",
                        success: function (response) {
                            progressId = response.id
                            $('#progress-bar').show()
                            $('#progressId').val(progressId)
                            refreshAll()
                            startAnalysis()

                            interval = setInterval(() => {
                                getProgressPercent(response.id)
                            }, 1000)
                        }
                    })
                }
            });

            function refreshAll() {
                $.each($('.render-table'), function (key, value) {
                    $('#' + $(this).attr('id')).dataTable().fnDestroy()
                })

                $('.render').remove()
                $('#hidden-result-table').dataTable().fnDestroy()
                $('#block-for-downloads-files').hide()
                $('.render-table').remove()
            }

            function getProgressPercent(id) {
                $.ajax({
                    type: "GET",
                    url: `/get-cluster-progress/${id}`,
                    success: function (response) {
                        setProgressBarStyles(response.percent)
                    }
                })
            }

            function startAnalysis() {
                $.ajax({
                    type: "POST",
                    url: "{{ route('analysis.cluster') }}",
                    data: getData(),
                    success: function (response) {
                        destroyProgress(progressId, interval)
                        $('#start-analysis').attr('disabled', false)
                        renderHiddenTable(response['result'])
                        renderResultTable(response['result'])
                    },
                    error: function (error) {
                        destroyProgress(progressId, interval)
                        $('#start-analysis').attr('disabled', false)
                        $('.toast.toast-error').show(300)
                        setTimeout(function () {
                            $('.toast.toast-error').hide(300)
                        }, 5000)
                    }
                });
            }

            function destroyProgress(progressId, interval) {
                clearInterval(interval)
                setTimeout(() => {
                    setProgressBarStyles(0)
                    $('#progress-bar').hide(300)
                }, 3000)
            }
        </script>
    @endslot
@endcomponent
