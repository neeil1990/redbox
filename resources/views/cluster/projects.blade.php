@component('component.card', ['title' =>  __('My projects') ])
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

            a.paginate_button.current {
                background: #ebf0f5 !important;
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
            <div class="toast-message error-msg"></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex p-0">
            <ul class="nav nav-pills p-2">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('cluster') }}">{{ __('Analyzer') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link admin-link active"
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
                    <table id="my-cluster-projects" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>{{ __('Analysis date') }}</th>
                            <th>{{ __('Domain') }}</th>
                            <th>{{ __('Comment') }}</th>
                            <th>{{ __('Number of phrases') }}</th>
                            <th>{{ __('Number of groups') }}</th>
                            <th>{{ __('TOP') }}</th>
                            <th>{{ __('Mode') }}</th>
                            <th>{{ __('Region') }}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($projects as $project)
                            <tr>
                                <td>{{ $project->created_at }}</td>
                                <td>
                                    <textarea data-target="{{ $project->id }}" name="domain"
                                              class="action-edit project-domain form-control"
                                              rows="7">{{ $project->domain }}</textarea>
                                </td>
                                <td>
                                    <textarea data-target="{{ $project->id }}" name="comment"
                                              class="action-edit project-comment form-control"
                                              rows="7">{{ $project->comment }}</textarea>
                                </td>
                                <td>{{ $project->count_phrases }}</td>
                                <td>{{ $project->count_clusters }}</td>
                                <td>{{ $project->top }}</td>
                                <td>{{ $project->clustering_level }} / {{ $project->request['engineVersion'] }}</td>
                                <td class="project-region">{{ $project->region }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <button type="button"
                                                data-toggle="modal"
                                                data-target="#repeat-scan"
                                                data-order="{{ $project->id }}"
                                                class="btn btn-secondary mb-2 repeat-scan">
                                            {{ __('Repeat analysis') }}
                                        </button>
                                        <a class="btn btn-secondary mb-2"
                                           href="{{ route('show.cluster.result', $project->id) }}" target="_blank">
                                            {{ __('View results') }}
                                        </a>
                                        <a class="btn btn-secondary mb-2"
                                           href="/download-cluster-result/{{$project->id}}/csv"
                                           target="_blank">{{ __('Download csv') }}</a>
                                        <a class="btn btn-secondary mb-2"
                                           href="/download-cluster-result/{{$project->id}}/xls"
                                           target="_blank">{{ __('Download xls') }}</a>
                                        @if($project->count_phrases >= $config->warning_limit)
                                            <span class="text-info">{{ __('A page can weigh a lot') }}<br> {{ __('and work slowly') }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="modal fade" id="repeat-scan" tabindex="-1" aria-labelledby="repeat-scanLabel"
                         aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="repeat-scanLabel"></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    @include('cluster.layouts.form')
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="progressId">
                </div>
            </div>
        </div>
    </div>
    @slot('js')
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script>
            var progressId
            var interval

            $(document).ready(function () {
                refreshAll()
                $('#saveResultBlock').remove()
                $('#my-cluster-projects').dataTable({
                    "order": [[0, "desc"]],
                    "pageLength": 25,
                    "searching": true,
                })
                $('.dt-button.buttons-copy.buttons-html5').addClass('ml-2')
                $('.dt-button').addClass('btn btn-secondary')

            })

            function successMessage(message = "{{ __('Text was successfully change') }}") {
                $('.toast.toast-success').show(300)
                $('.toast-message.success-msg').html(message)

                setTimeout(() => {
                    $('.toast.toast-success').hide(300)
                }, 6000)
            }

            function errorMessage(message) {
                $('.toast.toast-error').show(300)
                $('.toast-message.error-msg').html(message)

                setTimeout(() => {
                    $('.toast.toast-error').hide(300)
                }, 5000)
            }

            function startClusterAnalyse(progressId) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('analysis.cluster') }}",
                    data: getData(true, progressId),
                    success: function () {
                        successMessage("{{ __('The analysis has been successfully launched, the results will be automatically added to the table') }}")
                    },
                    error: function (response) {
                        errorMessage(response.responseJSON.message)
                        clearInterval(interval)
                    }
                });
            }

            function refreshAll() {
                $('.repeat-scan').unbind().on('click', function () {
                    $.ajax({
                        type: "post",
                        url: "{{ route('get.cluster.request') }}",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            id: $(this).attr('data-order'),
                        },
                        success: function (response) {
                            let request = response.request

                            console.log(request)
                            $('#repeat-scanLabel').html(response.created_at)
                            $('#region').val(request.region)
                            $('#count').val(request.count)
                            $('#phrases').val(request.phrases)
                            $('#clusteringLevel').val(request.clusteringLevel)
                            $('#engineVersion').val(request.engineVersion)
                            $('#domain-textarea').html(request.domain)
                            $('#comment-textarea').html(request.comment)
                            $('#save').val(request.save)

                            if ('searchEngine' in request) {
                                $('#searchEngine').val(request.searchEngine)
                            } else {
                                $('#searchEngine').val('yandex')
                            }

                            if ('ignoredWords' in request) {
                                $('#ignoredWords').val(request.ignoredWords)
                            } else {
                                $('#ignoredDomains').val('')
                            }

                            if ('ignoredDomains' in request) {
                                $('#ignoredDomains').val(request.ignoredDomains)
                            } else {
                                $('#ignoredDomains').val('')
                            }

                            if ('gainFactor' in request) {
                                $('#gainFactor').val(request.gainFactor)
                            } else {
                                $('#gainFactor').val(10)
                            }

                            if (request.engineVersion === 'max_phrases') {
                                $('#ignoredWordsBlock').show(300)
                            } else {
                                $('#ignoredWordsBlock').hide(300)
                            }

                            if (request.searchPhrases === 'true') {
                                $('#searchPhrases').prop('checked', true);
                            } else {
                                $('#searchPhrases').prop('checked', false);
                            }
                            if (request.brutForce === 'true') {
                                $('#brutForce').prop('checked', true);
                                $('.brut-force').show(300)

                            } else {
                                $('#brutForce').prop('checked', false);
                                $('.brut-force').hide(300)
                            }

                            if (request.searchRelevance === 'true') {
                                $('#searchRelevance').prop('checked', true);
                            } else {
                                $('#searchRelevance').prop('checked', false);
                            }

                            if (request.searchTarget === 'true') {
                                $('#searchTarget').prop('checked', true);
                            } else {
                                $('#searchTarget').prop('checked', false);
                            }

                            if (request.searchBase === 'true') {
                                $('#searchBase').prop('checked', true);
                            } else {
                                $('#searchBase').prop('checked', false);
                            }


                            if (request.mode === 'professional') {
                                $('#start-analyse').attr('data-target', 'professional')
                                $('#repeat-scan > div > div > div.modal-body > div:nth-child(4)').show()
                                $('#repeat-scan > div > div > div.modal-body > div:nth-child(6)').show()
                            } else {
                                $('#start-analyse').attr('data-target', 'classic')
                                $('#repeat-scan > div > div > div.modal-body > div:nth-child(4)').hide()
                                $('#repeat-scan > div > div > div.modal-body > div:nth-child(6)').hide()
                            }
                        },
                        error: function (error) {
                            errorMessage(error.responseJSON.message)
                        }
                    });
                })

                $('#start-analyse').unbind().on('click', function () {
                    if ($('#phrases').val() !== '') {
                        $.ajax({
                            type: "GET",
                            url: "/start-cluster-progress",
                            success: function (response) {
                                $('#progressId').val(response.id)
                                interval = setInterval(() => {
                                    getProgressPercent(response.id, interval)
                                }, 5000)

                                startClusterAnalyse(response.id, interval)
                            }
                        })
                    }
                });

                $('.action-edit').unbind().on('change', function () {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('cluster.edit') }}",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            id: $(this).attr('data-target'),
                            option: $(this).attr('name'),
                            value: $(this).val(),
                        },
                        success: function (response) {
                            successMessage()
                        },
                        error: function (error) {
                        }
                    });
                })

                function getProgressPercent(id, interval) {
                    $.ajax({
                        type: "GET",
                        url: `/get-cluster-progress/${id}/modify`,
                        success: function (response) {
                            if ('cluster' in response) {
                                let cluster = response['cluster']
                                let domain = cluster['domain'] === null ? '' : cluster['domain']
                                let comment = cluster['comment'] === null ? '' : cluster['comment']
                                let table = $('#my-cluster-projects').DataTable();
                                table.row.add({
                                    0: cluster['created_at'],
                                    1: '<textarea data-target="' + cluster['id'] + '" name="domain" rows="7" class="action-edit project-domain form-control">' + domain + '</textarea>',
                                    2: '<textarea data-target="' + cluster['id'] + '" name="comment" rows="7" class="action-edit project-comment form-control">' + comment + '</textarea>',
                                    3: cluster['count_phrases'],
                                    4: cluster['count_clusters'],
                                    5: cluster['top'],
                                    6: cluster['clustering_level'] + ' / ' + cluster['request']['engineVersion'],
                                    7: cluster['region'],
                                    8: '<div class="d-flex flex-column">' +
                                        '<button type="button" data-toggle="modal" data-target="#repeat-scan" data-order="' + cluster['id'] + '" class="btn btn-secondary mb-2 repeat-scan">{{ __('Repeat the analysis') }}</button> ' +
                                        '<a href="/show-cluster-result/' + cluster['id'] + '" target="_blank" class="btn btn-secondary mb-2">{{ __('View results') }}</a> ' +
                                        '<button class="btn btn-secondary mb-2">{{ __('Download csv') }}</button>' +
                                        '<button class="btn btn-secondary">{{ __('Download xls') }}</button></div>'
                                });
                                table.draw()
                                refreshAll()
                                clearInterval(interval)
                            }
                        }
                    })
                }
            }
        </script>
    @endslot
@endcomponent
