@extends('layouts.app')

@section('title', __('Users'))

@slot('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-editor/css/editor.bootstrap4.min.css') }}">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.css') }}">

    <style>
        .project-actions {
            min-width: 200px;
        }
    </style>
@endslot

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Users') }}</h3>

            <div class="card-tools">
                <div class="btn-group">
                    <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown"
                            data-offset="-200" aria-expanded="false">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="dropdown-menu" role="menu">
                        <a href="{{ route('get.verified.users', 'xls') }}" class="dropdown-item">Excel</a>
                        <a href="{{ route('get.verified.users', 'csv') }}" class="dropdown-item">CSV</a>
                        <a type="button" class="dropdown-item" data-toggle="modal" data-target="#exportModal">Фильтр
                            выгрузки</a>
                        <a type="button" class="dropdown-item" data-toggle="modal"
                           data-target="#assignTariffModal">{{ __('Assign tariff')  }}</a>
                        <a href="{{ route('users.statistics') }}"
                           class="dropdown-item">{{ __('General statistics of visits') }}</a>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-striped projects" id="service-users"></table>
    </div>

    @include('users.modal.index', ['id' => 'exportModal', 'action' => route('filter.exports.users'), 'title' => __('User Upload Filter')])
    @include('users.modal.index', ['id' => 'assignTariffModal', 'action' => route('users.tariff'), 'title' => __('Assign tariff')])
@stop

@section('js')
    <!-- Toastr -->
    <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('plugins/select2/js/select2.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-editor/js/datatables_editor.min.js') }}"></script>
    <script>
        $(document).ready(function () {

            $('#service-users').DataTable({
                dom: '<"card-header"<"card-title"l><"card-tools"f>><"card-body p-0"rt><"card-footer clearfix"p><"clear">',
                autoWidth: true,
                lengthMenu: [10, 25, 50, 100],
                pageLength: 50,
                pagingType: "simple_numbers",
                language: {
                    lengthMenu: "_MENU_",
                    search: "_INPUT_",
                    searchPlaceholder: "Email",
                    paginate: {
                        "first": "«",
                        "last": "»",
                        "next": "»",
                        "previous": "«"
                    },
                    processing: '<img src="/img/1485.gif" style="width: 50px; height: 50px;">',
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('users.index') }}",
                    type: 'GET',
                },
                order: [
                    [0, 'asc'],
                ],
                createdRow: function (row, data) {
                    $(row).find('td:nth-child(5)').attr('data-order', data.id);
                    $(row).find('td:nth-child(7)').attr('data-order', data.last_online_strtotime);
                },
                columnDefs: [
                    {orderable: true, targets: [0, 1, 2, 4, 6]},
                    {orderable: false, targets: '_all'},
                ],
                columns: [
                    {
                        name: 'id',
                        title: '{{ __('ID') }}',
                        data: 'id',
                    },
                    {
                        name: 'name',
                        title: '{{ __('Name') }}',
                        data: function (row) {
                            return row.name + ' ' + row.last_name;
                        },
                    },
                    {
                        name: 'email',
                        title: '{{ __('Email') }}',
                        data: function (row) {
                            let content = row.email + '<br>';

                            if (row.email_verified_at) {
                                content += '<span class="badge bg-success">{{ __('VERIFIED') }}</span><br>';
                            }

                            if (row.read_letter) {
                                content += '<span class="badge bg-primary">{{ __('The letter has been read') }}</span>'
                            }

                            return content;
                        },

                    },
                    {
                        title: '{{ __('Tariff') }}',
                        data: function (row) {
                            let content = '';
                            let tariff = row.tariff;

                            if (Object.keys(tariff).length > 0) {
                                content += '<span class="badge badge-warning">' + tariff.name + '</span><br>';
                                content += '<small>Активность до:</small><br>';
                                content += '<small>' + tariff.active_to + '</small><br>';
                                content += '<small>' + tariff.active_to_diffForHumans + '</small>';
                            }

                            return content;
                        },
                    },
                    {
                        name: 'created_at',
                        title: '{{ __('Created') }}',
                        data: function (row) {
                            let content = row.created + '<br>';

                            content += '<small>' + row.created_diffForHumans + '</small>';

                            return content;
                        },
                    },
                    {
                        title: '{{ __('Roles') }}',
                        className: 'project-state',
                        data: function (row) {
                            let content = '';
                            let roles = row.roles;

                            if (roles.length > 0) {
                                $.each(roles, function (i, el) {
                                    content += '<span class="badge badge-success">' + el.name + '</span><br>';
                                });
                            }

                            return content;
                        },
                    },
                    {
                        name: 'last_online_at',
                        title: '{{ __('Was online') }}',
                        data: function (row) {
                            let content = row.last_online + '<br>';

                            content += '<small>' + row.last_online_diffForHumans + '</small>';

                            return content;
                        },
                    },
                    {
                        className: 'project-actions text-right',
                        data: (row) => {
                            let content = '';
                            let btnClass = 'btn btn-info btn-sm mr-1';

                            content += '<a class="' + btnClass + '" href="/users/' + row.id + '/login" title="{{ __('Login') }}"><i class="fas fa-user-alt"></i></a>';
                            content += '<a class="' + btnClass + '" href="/users/' + row.id + '/edit" title="{{ __('Edit') }}"><i class="fas fa-pencil-alt"></i></a>';
                            content += '<a class="' + btnClass + '" href="/visit-statistics/' + row.id + '" title="{{ __('User statistic') }}"><i class="fas fa-chart-pie"></i></a>';

                            if (
                                Object.prototype.toString.call(row.metrics) === '[object Array]' ||
                                typeof row.metrics === 'object' && !Array.isArray(row.metrics) && row.metrics !== null ||
                                typeof row.metrics === 'string'
                            ) {
                                content += '<a class="' + btnClass + '" data-toggle="collapse" href="#collapseExample' + row.id + '" title="{{ __('utm metrics') }}"><i class="fas fa-share-alt"></i></a>';
                            }

                            content += '<a class="btn btn-danger btn-sm" onclick="deleteUser(' + row.id + ')" title="{{ __('Delete') }}"><i class="fas fa-trash"></i></a>';

                            if (row.metrics)
                                content += metricsTemp(row);

                            return content;
                        },
                    },
                ],
                initComplete: () => {
                    $('.btn').tooltip({
                        animation: false,
                        trigger: 'hover',
                    });
                },
            });

            $('#service-users_length').css({
                'padding-left': '15px',
                'padding-top': '15px',
            });

            $('#service-users_info ').css({
                'padding-left': '15px',
                'padding-top': '15px',
            });

            $('#service-users_filter ').css({
                'padding-right': '15px',
                'padding-top': '15px',
            });

            $('#service-users_paginate ').css({
                'margin-right': '15px',
                'margin-top': '15px',
                'margin-bottom': '15px',
            });

            $("#select-users").select2({
                theme: 'bootstrap4',
            });
        });

        function deleteUser(id) {
            if (window.confirm("{{ __('Do you really want to delete?') }}")) {
                axios.post('/users/' + id, {_method: 'DELETE'}).then(() => {
                    window.location.reload();
                });

                return true;
            }

            return false;
        }

        function metricsTemp(user) {
            let container = $('<div />', {
                class: 'mt-2'
            });

            let collapse = $('<div />', {
                class: 'collapse text-left mt-3',
                id: 'collapseExample' + user.id,
            });

            try {
                if (typeof user.metrics == 'string') {
                    let info = JSON.parse(user.metrics)
                    $.each(info, function (k, v) {
                        collapse.append($('<div />').html('<b>' + k + '</b>: ' + decodeURI(v)));
                    });
                } else {
                    $.each(user.metrics, function (k, v) {
                        collapse.append($('<div />').html('<b>' + k + '</b>: ' + decodeURI(v)));
                    });
                }
            } catch (e) {
            }

            container.append(collapse);

            return container[0].outerHTML;
        }
    </script>
    <script>
        $(document).ready(function () {
            $('#app > div > div.card > div.card-header > div > div > button').trigger('click')
        })
    </script>
@endsection
