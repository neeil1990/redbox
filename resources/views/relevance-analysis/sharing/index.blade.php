@component('component.card', ['title' =>  __('Share your projects') ])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/jqcloud/css/jqcloud.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css') }}"/>
        <style>
            .RelevanceAnalysis {
                background: oldlace;
            }

            .dataTables_length > label {
                display: flex;
            }

            .dataTables_length > label > select {
                margin: 0 5px;
            }

            .row {
                margin: 0 !important;
            }

        </style>
    @endslot

    <div id="toast-container" class="toast-top-right success-message" style="display:none;">
        <div class="toast toast-success" aria-live="polite">
            <div class="toast-message" id="toast-message"></div>
        </div>
    </div>

    <div id="toast-container" class="toast-top-right error-message" style="display:none;">
        <div class="toast toast-error" aria-live="polite">
            <div class="toast-message error-message" id="toast-message"></div>
        </div>
    </div>

    <div class="card">
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
                    <a href="{{ route('sharing.view') }}"
                       class="nav-link active">{{ __('Share your projects') }}</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('access.project') }}"
                       class="nav-link">{{ __('Projects available to you') }}</a>
                </li>
                @if($admin)
                    <li class="nav-item">
                        <a class="nav-link admin-link"
                           href="{{ route('all.relevance.projects') }}">{{ __('Statistics') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link admin-link"
                           href="{{ route('show.config') }}">{{ __('Module administration') }}</a>
                    </li>
                @endif
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <button type="button" class="btn btn-secondary mb-3" data-toggle="modal"
                            data-target="#accessModal">
                        {{ __('Granting access') }}
                    </button>
                    <button type="button" class="btn btn-secondary mb-3" data-toggle="modal"
                            data-target="#offAccessModal">
                        {{ __('Take access rights') }}
                    </button>

                    <table id="my-projects-table"
                           class="table table-bordered table-hover dataTable dtr-inline mb-3">
                        <thead>
                        <tr>
                            <th>{{ __('Project name') }}</th>
                            <th class="table-header">{{ __('Tags') }}</th>
                            <th>{{ __('Users who have access to the project') }}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($projects as $item)
                            <tr id="story-id-{{ $item->id }}">
                                <td>
                                    <span class="project_name" style="cursor: pointer"
                                          data-order="{{ $item->id }}">{{ $item->name }}</span>
                                </td>
                                <td id="project-{{ $item->id }}">
                                    @foreach($item->relevanceTags as $tag)
                                        <div style="color: {{ $tag->color }}">
                                            {{ $tag->name }}
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    <table class="table table-hover">
                                        <tbody>
                                        <tr data-widget="expandable-table" aria-expanded="true">
                                            <td>
                                                <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                                            </td>
                                        </tr>
                                        <tr class="expandable-body">
                                            <td>
                                                <div class="p-0" style="display: none;">
                                                    <table class="table table-hover">
                                                        <tbody class="project-{{ $item->id }}">
                                                        @foreach($item->sharing as $share)
                                                            <tr data-widget="expandable-table" aria-expanded="false"
                                                                class="share-{{ $share->id }}">
                                                                <td>
                                                                    <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                                                                    {{ $share->user->email }}
                                                                    <span class="text-muted">
                                                                        {{ $share->user->name .' '. $share->user->last_name }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr class="expandable-body d-none share-{{ $share->id }}">
                                                                <td>
                                                                    <div
                                                                        class="p-2 d-flex flex-row justify-content-between"
                                                                        style="display: none;">
                                                                        <select name="access"
                                                                                class="form form-control access-select"
                                                                                style="width: 350px"
                                                                                data-target="{{ $share->id }}">
                                                                            @if($share->access == 1)
                                                                                <option value="1">
                                                                                    {{ __('Viewing only') }}
                                                                                </option>
                                                                                <option value="2">
                                                                                    {{ __('Viewing and launching a re-analysis') }}
                                                                                </option>
                                                                            @elseif($share->access == 2)
                                                                                <option value="2">
                                                                                    {{ __('Viewing and launching a re-analysis') }}
                                                                                </option>
                                                                                <option value="1"
                                                                                >{{ __('Viewing only') }}
                                                                                </option>
                                                                            @endif
                                                                        </select>

                                                                        <button
                                                                            class="btn btn-secondary removeAccess"
                                                                            data-target="{{ $share->id }}">
                                                                            {{ __('Remove access') }}
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td class="col-2">
                                    <a href="{{ route('share.project.conf', $item->id) }}"
                                       class="btn btn-secondary">
                                        {{ __('More') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="accessModal" tabindex="-1" aria-labelledby="accessModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="accessModalLabel">{{ __('Granting access') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <label for="email">{{ __('Email of the user you want to give access to') }}</label>
                            <input type="email" class="form form-control" id="access-email" name="access-email">
                        </div>
                        <div>
                            <label for="access">{{ __('Access level') }}</label>
                            <select name="access" id="access" class="form form-control">
                                <option value="1">{{ __('Viewing only') }}</option>
                                <option value="2">{{ __('Viewing and the ability to run a re-analysis') }}</option>
                            </select>
                        </div>
                        <div>
                            <select multiple="multiple" class="form form-control" size="10"
                                    name="duallistbox_access">
                                @foreach($projects as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="set-access-button btn btn-secondary click_tracking" data-click="Give access">
                            {{ __('Give access') }}
                        </button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            {{ __('Close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="offAccessModal" tabindex="-1" aria-labelledby="offAccessModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="offAccessModalLabel">{{ __('Take access rights') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <label
                                for="email">{{ __('The mail of the user from whom you want to take access') }}</label>
                            <input type="email" class="form form-control" id="off-email" name="off-email">
                        </div>
                        <div>
                            <select multiple="multiple" class="form form-control" size="10"
                                    name="duallistbox_off_access">
                                @foreach($projects as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="off-access-button btn btn-secondary click_tracking"
                                data-click="Take access rights">
                            {{ __('Take access rights') }}
                        </button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            {{ __('Close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @slot('js')
        <script src="{{ asset('plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-editor/js/datatables_editor.min.js') }}"></script>
        <script>
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
                $('#my-projects-table').DataTable({
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

                $('#my-projects-table').wrap('<div style="width: 100%; overflow: auto"></div>')

                $('select[name="duallistbox_access"]').bootstrapDualListbox({
                    selectedListLabel: '{{ __('Projects you want to give access to') }}',
                    nonSelectedListLabel: '{{ __('Your projects') }}',
                    preserveSelectionOnMove: '{{ __('Moved') }}',
                    moveAllLabel: '{{ __('Move all') }}',
                    removeAllLabel: '{{ __('Move all') }}'
                });

                $('select[name="duallistbox_off_access"]').bootstrapDualListbox({
                    selectedListLabel: '{{ __('Projects from which you want to take away access') }}',
                    nonSelectedListLabel: '{{ __('Your projects') }}',
                    preserveSelectionOnMove: '{{ __('Moved') }}',
                    moveAllLabel: '{{ __('Move all') }}',
                    removeAllLabel: '{{ __('Move all') }}'
                });

                setInterval(() => {
                    refreshAllMethods()
                }, 500)
            })

            function refreshAllMethods() {
                $('.set-access-button').unbind().on('click', function () {
                    let ids = [];
                    $.each($('#bootstrap-duallistbox-selected-list_duallistbox_access').children(), function (key, value) {
                        ids.push($(this).attr('value'))
                    })
                    if (ids.length > 0) {
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "{{ route('get.multiply.access.to.my.project') }}",
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                ids: ids,
                                email: $('#access-email').val(),
                                access: $('#access').val(),
                            },
                            success: function (response) {
                                if (response.code === 201) {
                                    $('.toast-top-right.success-message').show(300)
                                    $('.toast-message').html(response.message)
                                    setTimeout(() => {
                                        $('.toast-top-right.success-message').hide(300)
                                    }, 3000)
                                    if (response.objects.length > 0) {
                                        $.each(response.objects, function (key, value) {
                                            let options
                                            if (value['access'] === '1') {
                                                options =
                                                    '<option value="1">{{ __('Viewing only')}}</option> ' +
                                                    '<option value="2">Просмотр и запуск повторного анализа</option>'
                                            } else {
                                                options =
                                                    '<option value="2">Просмотр и запуск повторного анализа</option>' +
                                                    '<option value="1">{{ __('Viewing only') }}</option> '

                                            }
                                            $('.project-' + value['project_id']).append(
                                                '<tr data-widget="expandable-table" aria-expanded="false"' +
                                                ' class="share-' + value['id'] + '"> ' +
                                                '<td> ' +
                                                '<i class="expandable-table-caret fas fa-caret-right fa-fw"></i> '
                                                + response.user.email + ' ' +
                                                '<span class="text-muted"> ' +
                                                response.user.name + ' ' + response.user.last_name +
                                                '</span> ' +
                                                '</td> ' +
                                                '</tr>' +
                                                '<tr class="expandable-body d-none share-' + value['id'] + '"> ' +
                                                '   <td> ' +
                                                '      <div class="p-2 d-flex flex-row justify-content-between" style="display: none;">' +
                                                '           <select name="access"' +
                                                '            class="form form-control access-select" style="width: 350px"' +
                                                '            data-target="' + value['id'] + '"> ' + options + '</select>' +
                                                '         <button class="btn btn-secondary removeAccess"' +
                                                '          data-target="' + value['id'] + '">Убрать доступ до проекта' +
                                                '         </button> ' +
                                                '      </div>' +
                                                '   </td>' +
                                                '</tr>'
                                            )
                                        })
                                    }
                                } else if (response.code === 415) {
                                    $('.toast-top-right.error-message').show(300)
                                    $('.toast-message.error-message').html(response.message)
                                    setTimeout(() => {
                                        $('.toast-top-right.error-message').hide(300)
                                    }, 3000)
                                }
                            },
                        });
                    }
                })

                $('.removeAccess').unbind().on('click', function () {
                    let button = $(this)
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('remove.access.to.my.project') }}",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            id: button.attr('data-target'),
                        },
                        success: function (response) {
                            if (response.code === 201) {
                                $('.toast-top-right.success-message').show(300)
                                $('.toast-message').html(response.message)
                                setTimeout(() => {
                                    $('.toast-top-right.success-message').hide(300)
                                }, 3000)

                                $('.share-' + button.attr('data-target')).hide(300)
                                setTimeout(() => {
                                    $('.share-' + button.attr('data-target')).remove()
                                }, 1000)
                            } else if (response.code === 415) {
                                $('.toast-top-right.error-message').show(300)
                                $('.toast-message.error-message').html(response.message)
                                setTimeout(() => {
                                    $('.toast-top-right.error-message').hide(300)
                                }, 3000)
                            }
                        },
                    });
                });

                $('.off-access-button').unbind().on('click', function () {
                    let ids = [];
                    $.each($('#bootstrap-duallistbox-selected-list_duallistbox_off_access').children(), function (key, value) {
                        ids.push($(this).attr('value'))
                    })
                    if (ids.length > 0) {
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "{{ route('remove.multiply.access') }}",
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                ids: ids,
                                email: $('#off-email').val(),
                            },
                            success: function (response) {
                                if (response.code === 200) {
                                    $('.toast-top-right.success-message').show(300)
                                    $('.toast-message').html(response.message)
                                    setTimeout(() => {
                                        $('.toast-top-right.success-message').hide(300)
                                    }, 3000)
                                    $.each(response.objects, function (key, value) {
                                        $('.share-' + value).remove()
                                    })
                                } else if (response.code === 415) {
                                    $('.toast-top-right.error-message').show(300)
                                    $('.toast-message.error-message').html(response.message)
                                    setTimeout(() => {
                                        $('.toast-top-right.error-message').hide(300)
                                    }, 3000)
                                }
                            },
                        });
                    }

                });

                $('.access-select').unbind().on('change', function () {
                    let elem = $('.access-select')
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('change.access.to.my.project') }}",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            access: elem.val(),
                            id: elem.attr('data-target'),
                        },
                        success: function (response) {
                            if (response.code === 201) {
                                $('.toast-top-right.success-message').show(300)
                                $('.toast-message').html(response.message)
                                setTimeout(() => {
                                    $('.toast-top-right.success-message').hide(300)
                                }, 3000)
                            } else if (response.code === 415) {
                                $('.toast-top-right.error-message').show(300)
                                $('.toast-message.error-message').html(response.message)
                                setTimeout(() => {
                                    $('.toast-top-right.error-message').hide(300)
                                }, 3000)
                            }
                        },
                    });
                })
            }
        </script>
    @endslot
@endcomponent
