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

    <div class="modal fade" id="saveUrlsModal" tabindex="-1" aria-labelledby="saveUrlsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="saveUrlsModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label
                        for="relevanceUrls">{{ __('Select the url that will be saved for each phrase of this cluster') }}</label>
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

                        <button class="btn btn-outline-secondary" id="ProfessionalMode">
                            {{ __('Pro mode') }}
                        </button>
                    </p>
                    <div class="w-50 pb-3">
                        @include('cluster.layouts.form')
                    </div>

                    <div id="progress-bar" class="w-25 pt-3 pb-3" style="display: none">
                        <span id="progress-bar-state"></span><span id="total-phrases"></span>
                        <img src="/img/1485.gif" alt="preloader_gif" width="20">
                    </div>

                    <div id="block-for-downloads-files" style="display: none">
                        <h3>{{ __('Cluster table') }}</h3>
                        <div id="files-downloads"></div>
                    </div>
                    <div id="result-table" style='width: 100%; overflow-x: scroll; display: none'>
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
        <script src="{{ asset('/plugins/cluster/js/render-result-table.min.js') }}"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
        <script>

            let progressId
            let interval

            $(document).ready(function () {
                console.clear()
                $('#pro').hide()
                $('#classic').show()

                isSearchRelevance();
            })

            function saveAllUrls(id) {
                let button = $(this)
                $('.save-all-urls').unbind().on('click', function () {
                    button = $(this)
                    $('#relevanceUrls').html('')
                    $.each($(this).attr('data-urls').split(','), function (key, value) {
                        $('#relevanceUrls').append($('<option>', {
                            value: value,
                            text: value
                        }));
                    })
                })

                $('#save-cluster-url-button').unbind().on('click', function () {
                    let phrases = []
                    $.each(button.parent().parent().parent().parent().children('td').eq(0).children('div').eq(0).children('table').eq(0).children('tbody').children('tr'), function (key, value) {
                        let thisElem = $(this)
                        if (thisElem.children('td').eq(4).children('a').eq(0).length === 0) {
                            if (thisElem.children('td').eq(2).attr('title') !== undefined) {
                                let phrase = thisElem.children('td').eq(2).attr('title')
                                phrase = phrase.replace('Ваша фраза "', '')
                                phrase = phrase.replace('" была изменена', '')
                                phrases.push(phrase)
                            } else {
                                phrases.push(thisElem.children('td').eq(2).children('div').eq(0).children('div').eq(0).html())
                            }
                            thisElem.children('td').eq(4).html('<a href="' + $('#relevanceUrls').val() + '" target="_blank">' + $('#relevanceUrls').val() + '</a>')
                        }
                    })

                    $.ajax({
                        type: "POST",
                        url: "{{ route('set.cluster.relevance.urls') }}",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            phrases: phrases,
                            url: $('#relevanceUrls').val(),
                            projectId: id,
                        },
                        success: function () {

                        },
                        error: function (response) {
                        }
                    });
                })
            }

            $('#start-analyse').click(function () {
                if ($(this).attr('data-target') === 'classic' && $('#phrases_classic').val() === '') {
                    return;
                }
                if ($(this).attr('data-target') !== 'classic' && $('#phrases').val() === '') {
                    return;
                }
                $(this).attr('disabled', true)
                $.ajax({
                    type: "GET",
                    url: "{{ route('start.cluster.progress') }}",
                    success: function (response) {
                        progressId = response.id
                        $('#progress-bar').show()
                        $('#progressId').val(progressId)
                        setProgressBarStyles(0)
                        interval = setInterval(() => {
                            getProgressPercent(response.id, interval)
                        }, 5000)

                        startClusterAnalyse(interval)
                    }
                })
            });

            function refreshAll() {
                $('#block-for-downloads-files').hide()
                $('#result-table').hide()

                $.each($('.render-table'), function (key, value) {
                    $('#' + $(this).attr('id')).dataTable().fnDestroy()
                })
                $('#hidden-result-table').dataTable().fnDestroy()

                $('.render-table').remove()
                $('.render').remove()

                $('#start-analyse').attr('disabled', false)
            }

            function getProgressPercent(id, interval) {
                $.ajax({
                    type: "GET",
                    url: `/get-cluster-progress/${id}`,
                    success: function (response) {
                        setProgressBarStyles(response.count)

                        if ('result' in response) {
                            refreshAll()
                            renderResultTable(response['result'])
                            destroyProgress(interval)

                            $('#files-downloads').html(
                                '<a class="btn btn-secondary mb-2" href="/download-cluster-result/' + response['objectId'] + '/csv" target="_blank">{{ __('Download csv') }}</a>' +
                                ' <a class="btn btn-secondary mb-2" href="/download-cluster-result/' + response['objectId'] + '/xls" target="_blank">{{ __('Download xls') }}</a>'
                            );

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
                                        projectId: response['objectId'],
                                    },
                                    success: function () {
                                        select.parent().html('<a href="' + select.val() + '" target="_blank">' + select.val() + '</a>')
                                    },
                                    error: function (response) {
                                    }
                                });

                                $('#progress-bar-state').html("{{ __('Parse xml') }}")
                            })

                            saveAllUrls(response['objectId'])

                            $('.copy-full-urls').unbind().on('click', function () {
                                let target = $(this).attr('data-action')
                                downloadSites(response['objectId'], target, 'copy')
                            })

                            $('.fa.fa-paperclip').hover(function () {
                                let target = $(this).attr('data-action')
                                downloadSites(response['objectId'], target, 'download')
                            });

                            $('.all-competitors').unbind().on('click', function () {
                                downloadAllCompetitors(response['objectId'], $(this).attr('data-action'))
                            })


                            setTimeout(() => {
                                $('#result-table').show()
                                $('#block-for-downloads-files').show()
                            }, 1000)
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
                        $('#total-phrases').html(response.totalPhrases)
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
                $('#progress-bar').hide(300)
                setProgressBarStyles(0)
            }

            let classicEngine = $('#engineVersion').val()
            let classicCount = $('#count').val()
            let classicLevel = $('#clusteringLevel').val()

            let proEngine = '{{ $config->engine_version }}'
            let proCount = '{{ $config->count }}'
            let proLevel = '{{ $config->clustering_level }}'

            $('#classicMode').on('click', function () {
                $('#start-analyse').attr('data-target', 'classic')
                $('#pro').hide()
                $('#classic').show(300)

                proEngine = $('#engineVersion').val()
                proCount = $('#count').val()
                proLevel = $('#clusteringLevel').val()

                $('#engineVersion').val(classicEngine)
                $('#count').val(classicCount)
                $('#clusteringLevel').val(classicLevel)

                $('#classicMode').attr('class', 'btn btn-secondary')
                $('#ProfessionalMode').attr('class', 'btn btn-outline-secondary')
            })

            $('#ProfessionalMode').on('click', function () {
                $('#start-analyse').attr('data-target', 'professional')
                $('#classic').hide()
                $('#pro').show(300)

                $('#engineVersion').val(proEngine)
                $('#count').val(proCount)
                $('#clusteringLevel').val(proLevel)

                $('#classicMode').attr('class', 'btn btn-outline-secondary')
                $('#ProfessionalMode').attr('class', 'btn btn-secondary')
            })
        </script>
    @endslot
@endcomponent
