@component('component.card', ['title' =>  "{{ __('Share your projects') }}" ])
@section('content')
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/jqcloud/css/jqcloud.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/relevance-analysis/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css') }}"/>
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
                    <a href="{{ route('sharing.view') }}" class="nav-link active">{{ __('Share your projects') }}</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('access.project') }}" class="nav-link">{{ __('Projects available to you') }}</a>
                </li>
                @if($admin)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('show.config') }}" >{{ __('Module administration') }}</a>
                    </li>
                @endif
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <table id="my-projects-table" class="table table-bordered table-hover dataTable dtr-inline mb-3">
                        <thead>
                        <tr>
                            <th>{{ __('Project name') }}</th>
                            <th>{{ __('Tags') }}</th>
                            <th>Пользователи которым доступен проект</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($projects as $item)
                            <tr>
                                <td class="project_name" style="cursor:pointer;"
                                    data-order="{{ $item->id }}">
                                    <a href="#history_table_{{ $item->name }}">
                                        {{ $item->name }}
                                    </a>
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
                                        <tr data-widget="expandable-table" aria-expanded="false">
                                            <td>
                                                <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                                            </td>
                                        </tr>
                                        <tr class="expandable-body d-none">
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
                                                                                <option value="1">Только просмотр
                                                                                </option>
                                                                                <option value="2">Просмотр и запуск
                                                                                    повторного анализа
                                                                                </option>
                                                                            @elseif($share->access == 2)
                                                                                <option value="2">Просмотр и запуск
                                                                                    повторного анализа
                                                                                </option>
                                                                                <option value="1">Только просмотр
                                                                                </option>
                                                                            @endif
                                                                        </select>

                                                                        <button class="btn btn-secondary removeAccess"
                                                                                data-target="{{ $share->id }}">
                                                                            Убрать доступ до проекта
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
                                        Подробности
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#accessModal">
                Выдача доступов
            </button>
            <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#offAccessModal">
                Забрать доступы
            </button>
        </div>

        <div class="modal fade" id="accessModal" tabindex="-1" aria-labelledby="accessModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="accessModalLabel">Выдача доступов</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <label for="email">Почта пользователя которому вы хотите дать доступ</label>
                            <input type="email" class="form form-control" id="access-email" name="access-email">
                        </div>
                        <div>
                            <label for="access">Уровень доступа</label>
                            <select name="access" id="access" class="form form-control">
                                <option value="1">Только просмотр</option>
                                <option value="2">Просмотр и возможность запуска повторного анализа</option>
                            </select>
                        </div>
                        <div>
                            <select multiple="multiple" class="form form-control" size="10" name="duallistbox_access">
                                @foreach($projects as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                        <button class="set-access-button btn btn-secondary">Дать доступ</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="offAccessModal" tabindex="-1" aria-labelledby="offAccessModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="offAccessModalLabel">Забрать доступы</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <label for="email">Почта пользователя у которого вы хотите забрать доступ</label>
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
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                        <button class="off-access-button btn btn-secondary">Забрать доступ</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @slot('js')
        <script src="{{ asset('plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script>
            $(document).ready(function () {
                $('#my-projects-table').DataTable();

                $('select[name="duallistbox_access"]').bootstrapDualListbox({
                    selectedListLabel: 'Проекты к которым вы хотите дать доступ',
                    nonSelectedListLabel: 'Ваши проекты',
                    preserveSelectionOnMove: 'moved',
                    moveAllLabel: 'Move all',
                    removeAllLabel: 'Remove all'
                });

                $('select[name="duallistbox_off_access"]').bootstrapDualListbox({
                    selectedListLabel: 'Проекты для которых вы хотите забрать доступ',
                    nonSelectedListLabel: 'Ваши проекты',
                    preserveSelectionOnMove: 'moved',
                    moveAllLabel: 'Move all',
                    removeAllLabel: 'Remove all'
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
                                                    '<option value="1">Только просмотр</option> ' +
                                                    '<option value="2">Просмотр и запуск повторного анализа</option>'
                                            } else {
                                                options =
                                                    '<option value="2">Просмотр и запуск повторного анализа</option>' +
                                                    '<option value="1">Только просмотр</option> '

                                            }
                                            $('.project-' + value['project_id']).append(
                                                '<tr data-widget="expandable-table" aria-expanded="false"' +
                                                ' class="share-' + value['id'] + '"> ' +
                                                '<td> ' +
                                                '<i class="expandable-table-caret fas fa-caret-right fa-fw"></i> ' +
                                                +response.user.email + ' ' +
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
@endsection
@endcomponent
