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
                    <a class="nav-link admin-link active" href="#">{{ __('My project') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link admin-link"
                       href="#">{{ __('Module administration') }}</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <div id="progress-bar" style="display: none">
                        <div class="progress-bar mt-3 mb-3" role="progressbar"></div>
                        <img src="/img/1485.gif" alt="preloader_gif" width="20">
                    </div>

                    <div id="block-for-downloads-files" style="display: none">
                        <h3>Таблица кластеров</h3>
                        <table id="hidden-result-table" style="display: none">
                            <thead>
                            <tr>
                                <th colspan="4"></th>
                                <th class="centered-text" colspan="3">Частотность</th>
                            </tr>
                            <tr>
                                <th>Порядковый номер</th>
                                <th>Порядковый номер в кластере</th>
                                <th>Ключевой запрос</th>
                                <th>Группа</th>
                                <th>Базовая</th>
                                <th>"Фразовая"</th>
                                <th>"!Точная"</th>
                            </tr>
                            </thead>
                            <tbody id="hidden-table-tbody">
                            </tbody>
                        </table>
                        <div style='width: 100%; overflow-x: scroll;'>
                            <table id="clusters-table" class="table table-bordered dtr-inline">
                                <thead>
                                <tr>
                                    <th>Кластеры</th>
                                    <th style="min-width: 400px;">Конкуренты</th>
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
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('/plugins/cluster/js/render-hidden-table.js') }}"></script>
        <script src="{{ asset('/plugins/cluster/js/render-result-table.js') }}"></script>
        <script src="{{ asset('/plugins/cluster/js/common.js') }}"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
        <script>
            $(document).ready(function () {
                renderHiddenTable({!! $cluster['result'] !!})
                renderResultTable({!! $cluster['result'] !!})
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