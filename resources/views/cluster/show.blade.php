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
                position: fixed;
                bottom: 100px;
                right: 30px;
                z-index: 1000;
                width: 32px;
                height: 32px;
                background: url(https://snipp.ru/img/scroll_top.png) 50% 50% no-repeat;
                border-radius: 50%;
                opacity: 0.5;
            }

            #scroll_button {
                position: fixed;
                bottom: 65px;
                right: 30px;
                z-index: 1000;
                width: 32px;
                height: 32px;
                background: #6c757d;
                border-radius: 50%;
                opacity: 0.5;
                transform: rotate(180deg);
            }

            #scroll_bottom {
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

            #scroll_top:hover, #scroll_bottom:hover, #scroll_button:hover {
                opacity: 1;
            }

            .Clusters {
                background: oldlace;
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

    <div class="modal fade" id="saveUrlsModal" tabindex="-1" aria-labelledby="saveUrlsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="saveUrlsModalLabel">{{ __('Select the url that will be saved for each phrase of this cluster') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <select name="relevanceUrls" id="relevanceUrls" class="select custom-select"></select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="save-cluster-url-button"
                            data-dismiss="modal">{{ __('Save') }}</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex p-0">
            <ul class="nav nav-pills p-2">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('cluster') }}">{{ __('Analyzer') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       href="{{ route('cluster.projects') }}">{{ __('My projects') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('show.cluster.result', $cluster['id']) }}">
                        {{ __('My project') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('edit.clusters', $cluster['id']) }}">
                        {{ __('Hands editor') }}
                    </a>
                </li>
                @if($admin)
                    <li>
                        <a class="nav-link text-primary" href="{{ route('cluster.configuration') }}">
                            {{ __('Module administration') }}
                        </a>
                    </li>
                @endif
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">

                    <div id="params" style="display: none">
                        <div class="d-flex w-100 justify-content-between" style="margin-top: 40px;">
                            <div>
                                {{ __('Number of phrases') }}: {{ $cluster['count_phrases'] }}
                            </div>
                            <div>
                                {{ __('Number of clusters') }}: {{ $cluster['count_clusters'] }}
                            </div>
                            <div>
                                {{ __('Phrases') }}:
                                <span class="__helper-link ui_tooltip_w" id="show-all-phrases">
                                    <i class="fa fa-paperclip"></i>
                                    <span class="ui_tooltip __bottom">
                                        <span class="ui_tooltip_content" style="width: 450px !important;"
                                              id="all-phrases"></span>
                                    </span>
                                </span>
                                <i class="fa fa-copy" id="copyUsedPhrases"></i>
                                <textarea name="usedPhrases" id="usedPhrases"
                                          style="display: none"></textarea>
                            </div>
                            <div>
                                {{ __('Region') }}: {{ \App\Common::getRegionName($cluster['request']['region']) }}
                            </div>
                            <div>
                                {{ __('Search Engine') }}: {{ $cluster['request']['searchEngine'] ?? 'yandex' }}
                            </div>
                            <div>
                                {{ __('Top') }}: {{ $cluster['request']['count'] }}
                            </div>
                            @if(isset($cluster['request']['mode']) && $cluster['request']['mode'] === 'professional' || \App\User::isUserAdmin())
                                <div>
                                    {{ __('Clustering level') }}: {{ $cluster['request']['clusteringLevel'] }}
                                </div>
                                <div>
                                    {{ __('Version') }}: {{ $cluster['request']['engineVersion'] }}
                                </div>
                                <div type="button" class="btn btn-secondary"
                                     id="fastScanButton"
                                     data-toggle="modal"
                                     data-target="#fastScan">
                                    {{ __('Rebuild') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div id="loader-block" class="text-center">
                        <img src="/img/1485.gif" alt="preloader_gif" width="40" height="40">
                        <div>
                            <span id="rendered-clusters">0</span>
                            /
                            <span>{{ $cluster['count_phrases'] }}</span>
                        </div>
                        <div>{{ __('Render data') }}</div>
                    </div>
                </div>

                <a class="btn btn-secondary mb-2 click_tracking" data-click="Download csv"
                   href="/download-cluster-result/{{ $cluster['id'] }}/csv"
                   target="_blank">{{ __('Download csv') }}</a>
                <a class="btn btn-secondary mb-2 click_tracking" data-click="Download xls"
                   href="/download-cluster-result/{{ $cluster['id'] }}/xls"
                   target="_blank">{{ __('Download xls') }}</a>

                <div id="block-for-downloads-files" style="display: none">
                    <div>
                        <table id="clusters-table" class="table table-bordered dtr-inline">
                            <thead>
                            <tr>
                                <th>{{ __('Clusters') }}</th>
                                <th style="min-width: 333px;"></th>
                            </tr>
                            </thead>
                            <tbody id="clusters-table-tbody">
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end">
                        <div>
                            {{ __('Number of clusters') }}: {{ $cluster['count_clusters'] }}
                        </div>
                    </div>
                    @if(isset($cluster['request']['mode']) && $cluster['request']['mode'] === 'professional' || \App\User::isUserAdmin())
                        <button class="btn btn-secondary mt-5"
                                type="button"
                                id="fastScanButton"
                                data-toggle="modal"
                                data-target="#fastScan">
                            {{ __('Rebuild') }}
                        </button>
                        <div class="modal fade" id="fastScan" tabindex="-1" aria-labelledby="fastScanLabel"
                             aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="fastScanLabel">
                                            {{ __('Rebuild the cluster based on previously received data') }}
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group required">
                                            <label>{{ __('clustering level') }}</label>
                                            {!! Form::select('clusteringLevel', [
                                                'light' => 'light',
                                                'soft' => 'soft',
                                                'pre-hard' => 'pre-hard',
                                                'hard' => 'hard',
                                            ], null, ['class' => 'custom-select rounded-0', 'id' => 'clusteringLevelFast']) !!}
                                        </div>
                                        <div class="form-group required">
                                            <label>{{ __('Merging Clusters') }}</label>
                                            {!! Form::select('engineVersion', [
                                                    'max_phrases' => 'Фразовый перебор и поиск максимального (13.01)',
                                                    '1501' => 'Фразовый перебор и поиск максимального (15.01)',
                                            ], null, ['class' => 'custom-select rounded-0', 'id' => 'engineVersionFast']) !!}
                                        </div>

                                        <div class="form-group required">
                                            <label for="ignoredDomains">{{ __('Ignored domains') }}</label>
                                            <textarea class="form form-control" name="ignoredDomains"
                                                      id="ignoredDomains" cols="8"
                                                      rows="8">{{ $cluster['request']['ignoredDomains'] ?? '' }}</textarea>
                                        </div>

                                        <div id="ignoredWordsBlock">
                                            <div class="form-group required">
                                                <label for="ignoredWords">{{ __('Ignored words') }}</label>
                                                <textarea class="form form-control" name="ignoredWords"
                                                          id="ignoredWords" cols="8"
                                                          rows="8">{{ $cluster['request']['ignoredWords'] ?? '' }}</textarea>
                                            </div>
                                        </div>

                                        <div class="form-group required">
                                            <label for="brutForce">{{ __('Additional bulkhead') }}</label>
                                            <input type="checkbox" name=" brutForce" id="brutForce">
                                            <span class="__helper-link ui_tooltip_w">
                                                <i class="fa fa-question-circle" style="color: grey"></i>
                                                <span class="ui_tooltip __right">
                                                    <span class="ui_tooltip_content" style="width: 300px">
                                                        {{ __('Phrases that, after clustering, did not get into the cluster will be further revised with a reduced entry threshold.') }} <br><br>
                                                        {{ __('If the clustering level is "pre-hard", then the entry threshold for phrases will be reduced to "soft",') }}
                                                        {{ __('if the phrase still doesnt get anywhere, then the threshold will be reduced to "light".') }}
                                                    </span>
                                                </span>
                                            </span>
                                        </div>
                                        <div class="form-group required" id="brutForceCountBlock" style="display: none">
                                            <div class="form-group required">
                                                <label for="gainFactor">{{ __('Gain factor(%)') }}</label>
                                                <input class="form form-control" type="number" id="gainFactor"
                                                       name="gainFactor"
                                                       value="{{ $cluster['request']['gainFactor'] ?? 40 ?? 10 }}"
                                                       placeholder="default 10">
                                            </div>

                                            <div class="form-group required">
                                                <label for="brutForceCount">
                                                    {{ __("Minimum cluster size for re-bulkhead") }}
                                                </label>
                                                <input type="number" name="brutForceCount" id="brutForceCount"
                                                       class="form form-control" value="1">
                                            </div>
                                            <div>
                                                <label for="reductionRatio">
                                                    {{ __('Minimum multiplier') }}
                                                </label>
                                                <select name="reductionRatio" id="reductionRatio"
                                                        class="select custom-select">
                                                    <option value="pre-hard">pre-hard</option>
                                                    <option value="soft">soft</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group required d-flex justify-content-end">
                                            <button type="button" class="btn btn-secondary mr-2 click_tracking"
                                                    data-click="Rebuild"
                                                    data-dismiss="modal"
                                                    id="brutForceFast">
                                                {{ __('Rebuild') }}
                                            </button>
                                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                                {{ __('Close') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="brutForce mt-3 d-flex">
                        <div id="clusters-table-default" class="col-6"
                             style="display:none;">
                            <h3>{{ __('The original version') }}</h3>
                            <table id="default-hidden" style="display: none">
                                <thead>
                                <tr>
                                    <th class="border">{{ __('Sequence number') }}</th>
                                    <th class="border">{{ __('Sequence number in the cluster') }}</th>
                                    <th class="border">{{ __('Key query') }}</th>
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
                                            <table class="default-cluster table table-hover no-footer mb-0"
                                                   id="{{ Str::random() }}">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>##</th>
                                                    <th>{{ __('Key query') }}</th>
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
                                                                {{ $phrase }}
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
                                    {{ __('Number of clusters') }}: <span>{{ $cluster['count_clusters'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div id="clusters-table-fast" class="col-6" style="display:none;">
                            <h3>{{ __('Reassembled version') }}</h3>
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
                                    {{ __('Number of clusters') }}: <span id="placeForCountClusters"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a href="#" id="scroll_top"></a>
    <a href="#brutForceFast" id="scroll_button"></a>
    <a href="#" id="scroll_bottom"></a>
    <textarea name="hiddenForCopy" id="hiddenForCopy" style="display: none"></textarea>
    <input type="hidden" id="progressId">

    @slot('js')
        <script>
            $('#app > div > div > div.card-header').append($('#params').html())
            $('#params').remove()

            $(function () {
                $("#scroll_button").click(function () {
                    $("html, body").animate({scrollTop: $('#clusters-table-default').offset().top}, {duration: 600,});
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
        <script src="{{ asset('/plugins/cluster/js/common_v2.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('/plugins/cluster/js/render-result-table_v2.min.js') }}"></script>
        <script src="{{ asset('/plugins/cluster/js/render-result-fast-table.min.js') }}"></script>
        <script src="{{ asset('/plugins/cluster/js/render-hidden-fast.min.js') }}"></script>
        <script src="{{ asset('plugins/common/js/common.js') }}"></script>
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/buttons/buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/buttons/jszip.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/buttons/vfs_fonts.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/buttons/html5.min.js') }}"></script>
        <script>
            const cluster_tracking_project_id = "{{ request()->route()->parameter('statistic_project_id') }}";

            $(document).on('click', '.click_tracking', function () {
                $.ajax({
                    type: 'post',
                    url: "{{ route('click.tracking') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        button_text: $(this).attr('data-click'),
                        url: location.href,
                        project_id: cluster_tracking_project_id
                    },
                    success: function (response) {},
                    error: function () {}
                })
            });

            function successCopiedMessage() {
                $('.toast.toast-success').show(300)
                $('.toast-message.success-msg').html("{{ __('Successfully copied') }}")
                setTimeout(() => {
                    $('.toast.toast-success').hide(300)
                }, 3000)
            }

            $(document).ready(function () {
                renderResultTable_v2({!! $cluster['result'] !!}, {!! $cluster['id'] !!})

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

                $('#copyUsedPhrases').click(function () {
                    let object = $('#usedPhrases')
                    if (object.html() === '') {
                        $.ajax({
                            type: "POST",
                            url: "/download-cluster-phrases",
                            dataType: 'json',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                projectId: {{ $cluster['id'] }},
                            },
                            success: function (response) {
                                let phrases = response.phrases
                                object.html(' ')
                                object.html(phrases.join("\n"))
                                object.css('display', 'block')
                                let text = document.getElementById("usedPhrases");
                                text.select();
                                document.execCommand("copy");
                                object.css('display', 'none')
                                successCopiedMessage()
                            },
                            error: function (response) {
                            }
                        });
                    } else {
                        object.css('display', 'block')
                        let text = document.getElementById("usedPhrases");
                        text.select();
                        document.execCommand("copy");
                        object.css('display', 'none')
                        successCopiedMessage()
                    }
                })

                let oldValue = 1
                $('#brutForce').on('click', function () {
                    if ($(this).is(':checked')) {
                        $('#brutForceCount').val(oldValue)
                        $('#brutForceCountBlock').show(300)
                    } else {
                        $('#brutForceCountBlock').hide(300)
                        oldValue = $('#brutForceCount').val()
                        $('#brutForceCount').val(1)
                    }
                })

                $('#brutForceFast').on('click', function () {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('fast.scan.clusters') }}",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            count: {{ $cluster['request']['count'] ?? 40 }},
                            clusteringLevel: $('#clusteringLevelFast').val(),
                            engineVersion: $('#engineVersionFast').val(),
                            resultId: {{ $cluster['id'] }},
                            brutForce: $('#brutForce').is(':checked'),
                            mode: 'professional',
                            brutForceCount: $('#brutForceCount').val(),
                            reductionRatio: $('#reductionRatio').val(),
                            ignoredDomains: $('#ignoredDomains').val(),
                            gainFactor: $('#gainFactor').val(),
                            ignoredWords: $('#ignoredWords').val(),
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
                            $("html, body").animate({scrollTop: $('#clusters-table-default').offset().top}, {duration: 600,});

                            $('#scroll_button').trigger('click')
                        },
                    });
                })

                $('.save-relevance-url').unbind().on('click', function () {
                    let phrase = $(this).attr('data-order')
                    let select = $('#' + phrase.replaceAll(' ', '-'))

                    $.ajax({
                        type: "POST",
                        url: "{{ route('set.cluster.relevance.url') }}",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            phrase: $(this).attr('data-order'),
                            url: select.val(),
                            projectId: {{ $cluster['id'] }},
                        },
                        success: function () {
                            select.parent().parent().html('<a href="' + select.val() + '" target="_blank">' + select.val() + '</a>')
                        },
                        error: function (response) {
                        }
                    });
                })

                saveAllUrls({{ $cluster['id'] }})

                $('.copy-full-urls').unbind().on('click', function () {
                    let target = $(this).attr('data-action')
                    downloadSites({{ $cluster['id'] }}, target, 'copy')
                })

                $('.fa.fa-paperclip').hover(function () {
                    let target = $(this).attr('data-action')
                    downloadSites({{ $cluster['id'] }}, target, 'download')
                });

                $('.all-competitors').unbind().on('click', function () {
                    downloadAllCompetitors({{ $cluster['id'] }}, $(this).attr('data-action'))
                })

                $("#show-all-phrases").unbind().hover(function () {
                    if ($('#all-phrases').html() === '') {
                        $.ajax({
                            type: "POST",
                            url: "/download-cluster-phrases",
                            dataType: 'json',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                projectId: {{ $cluster['id'] }},
                            },
                            success: function (response) {
                                let phrases = response.phrases
                                $('#all-phrases').html(' ')
                                $('#all-phrases').html(phrases.join("<br>"))
                            },
                            error: function (response) {
                            }
                        });
                    }
                })
            })
        </script>
    @endslot
@endcomponent
