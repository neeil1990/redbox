@component('component.card', ['title' =>  __('Cluster') ])
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

            .text-primary {
                color: #007bff !important;
            }
        </style>
    @endslot

    <div id="toast-container" class="toast-top-right success-message">
        <div class="toast toast-success" aria-live="polite" style="display:none;">
            <div class="toast-message success-msg"></div>
        </div>
    </div>

    <div id="toast-container" class="toast-top-right error-message" style="z-index: 99999 !important;">
        <div class="toast toast-error" aria-live="assertive" style="display:none;">
            <div class="toast-message error-msg">
                {{ __('An unexpected error has occurred, please contact the administrator') }}
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex p-0">
            <ul class="nav nav-pills p-2">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('cluster') }}">{{ __('Analyzer') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link admin-link"
                       href="{{ route('cluster.projects') }}">{{ __('My projects') }}</a>
                </li>
                @if($admin)
                    <li class="nav-item">
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
                    <p>
                        <button class="btn btn-secondary" id="classicMode">
                            {{ __('Classic mode') }}
                        </button>

                        <button class="btn btn-secondary" id="ProfessionalMode">
                            {{ __('Pro mode') }}
                        </button>
                    </p>
                    <div class="w-50 pb-3">
                        @include('cluster.layouts.form')
                    </div>

                    <div id="progress-bar" class="w-25" style="display: none">
                        <span id="progress-bar-state"></span>
                        <img src="/img/1485.gif" alt="preloader_gif" width="20">
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
                                <th>{{ __('Relevant urls') }}</th>
                                <th>{{ __('Base') }}</th>
                                <th>"{{ __('Phrasal') }}"</th>
                                <th>"!{{ __('Target') }}"</th>
                            </tr>
                            </thead>
                            <tbody id="hidden-table-tbody">
                            </tbody>
                        </table>
                        <div style='width: 100%; overflow-x: scroll;'>
                            <table id="clusters-table" class="table table-bordered dtr-inline">
                                <thead>
                                <tr>
                                    <th>{{ __('Clusters') }}</th>
                                    <th style="min-width: 400px;">{{ __('Competitors') }}</th>
                                </tr>
                                </thead>
                                <tbody id="clusters-table-tbody">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <textarea name="hiddenForCopy" id="hiddenForCopy" style="display: none"></textarea>

                    <input type="hidden" id="progressId">
                </div>
            </div>
        </div>
    </div>
    @slot('js')
        <script>
            $('#tab_1 > div.w-50.pb-3 > div:nth-child(4)').hide()
            $('#tab_1 > div.w-50.pb-3 > div:nth-child(6)').hide()

            function successCopiedMessage() {
                $('.toast.toast-success').show(300)
                $('.toast-message.success-msg').html("{{ __('Successfully copied') }}")
                setTimeout(() => {
                    $('.toast.toast-success').hide(300)
                }, 3000)
            }
        </script>
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('/plugins/cluster/js/render-hidden-table.js') }}"></script>
        <script src="{{ asset('/plugins/cluster/js/render-result-table.js') }}"></script>
        <script src="{{ asset('/plugins/cluster/js/common.js') }}"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
        <script>
            let progressId
            let interval

            $('#start-analyse').click(function () {
                if ($('#phrases').val() !== '') {
                    $(this).attr('disabled', true)
                    $.ajax({
                        type: "GET",
                        url: "{{ route('start.cluster.progress') }}",
                        success: function (response) {
                            progressId = response.id
                            $('#progress-bar').show()
                            $('#progressId').val(progressId)
                            interval = setInterval(() => {
                                getProgressPercent(response.id, interval)
                            }, 5000)

                            startClusterAnalyse(interval)
                        }
                    })
                }
            });

            function refreshAll() {
                $('#block-for-downloads-files').hide()

                $.each($('.render-table'), function (key, value) {
                    $('#' + $(this).attr('id')).dataTable().fnDestroy()
                })
                $('#hidden-result-table').dataTable().fnDestroy()

                $('.render-table').remove()
                $('.render').remove()

                $('#start-analyse').attr('disabled', false)
            }

            function getProgressPercent(id, interval) {
                let table
                $.ajax({
                    type: "GET",
                    url: `/get-cluster-progress/${id}`,
                    success: function (response) {
                        setProgressBarStyles(response.count)

                        if ('result' in response) {
                            refreshAll()
                            table = renderHiddenTable(response['result'])
                            renderResultTable(response['result'])
                            destroyProgress(interval)

                            $('.save-relevance-url').unbind().on('click', function () {
                                let phrase = $(this).attr('data-order')
                                let select = $('#' + phrase.replaceAll(' ', '-'))
                                let targetRow = Number(select.parent().parent().parent().children('td').eq(0).html()) - 1

                                $.ajax({
                                    type: "POST",
                                    url: "{{ route('set.cluster.relevance.url') }}",
                                    data: {
                                        _token: $('meta[name="csrf-token"]').attr('content'),
                                        phrase: $(this).attr('data-order'),
                                        url: select.val(),
                                        projectId: response['objectId'],
                                    },
                                    success: function () {
                                        select.parent().html('<a href="' + select.val() + '" target="_blank">' + select.val() + '</a>')
                                        table.cell(targetRow, 4).data(select.val())
                                        table.draw()
                                    },
                                    error: function (response) {
                                    }
                                });

                                $('#progress-bar-state').html("{{ __('Parse xml') }}")
                            })
                        }
                    },
                    error: function () {
                        clearInterval(interval)
                        $('#start-analyse').attr('disabled', false)
                    }
                })
            }

            function startClusterAnalyse(interval) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('analysis.cluster') }}",
                    data: getData(),
                    success: function (response) {
                        if ($('#save').val() === '1') {
                            $('.history-notification').show(300)
                            setTimeout(() => {
                                $('.history-notification').hide(300)
                            }, 15000)
                        }
                    },
                    error: function (response) {
                        destroyProgress(interval)
                        let values = [];

                        $('#start-analyse').attr('disabled', false)
                        $('.toast.toast-error').show(300)
                        $.each(response.responseJSON.errors, function (key, value) {
                            values.push(value)
                        })

                        $('.error-msg').html(values + "")

                        setTimeout(() => {
                            $('.toast.toast-error').hide(300)
                        }, 10000)
                    },
                });
            }

            function destroyProgress(interval) {
                clearInterval(interval)
                setTimeout(() => {
                    setProgressBarStyles(0)
                    $('#progress-bar').hide(300)
                }, 3000)
            }

            $('#classicMode').on('click', function () {
                $('#start-analyse').attr('data-target', 'classic')
                $('#tab_1 > div.w-50.pb-3 > div:nth-child(4)').hide(300)
                $('#tab_1 > div.w-50.pb-3 > div:nth-child(6)').hide(300)
            })

            $('#ProfessionalMode').on('click', function () {
                $('#start-analyse').attr('data-target', 'professional')
                $('#tab_1 > div.w-50.pb-3 > div:nth-child(4)').show(300)
                $('#tab_1 > div.w-50.pb-3 > div:nth-child(6)').show(300)
            })
        </script>
    @endslot
@endcomponent
