@extends('layouts.app')

@section('title', __('Users'))

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.css') }}">
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Users') }}</h3>

            <div class="card-tools">
                <div class="btn-group">
                    <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown" data-offset="-200">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="dropdown-menu" role="menu">
                        <a href="{{ route('get.verified.users', 'xls') }}" class="dropdown-item">Excel</a>
                        <a href="{{ route('get.verified.users', 'csv') }}" class="dropdown-item">CSV</a>
                        <a type="button" class="dropdown-item" data-toggle="modal" data-target="#exportModal">Фильтр выгрузки</a>
                        <a type="button" class="dropdown-item" data-toggle="modal" data-target="#assignTariffModal">{{ __('Assign tariff')  }}</a>
                        <a href="{{ route('users.statistics') }}" class="dropdown-item">{{ __('General statistics of visits') }}</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-striped projects" id="service-users">
                <thead>
                <tr>
                    <th>{{ __('ID') }}</th>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Tariff') }}</th>
                    <th>{{__('Created')}}</th>
                    <th class="text-center">{{ __('Roles') }}</th>
                    <th>{{__('Was online')}}</th>
                    <th></th>
                </tr>
                </thead>

                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>
                            {{ $user->name }} {{ $user->last_name }}
                        </td>
                        <td>
                            {{ $user->email }}<br>
                            @if($user->email_verified_at)
                                <span class="badge bg-success">{{ __('VERIFIED') }}</span><br>
                            @endif
                            @if($user->read_letter)
                                <span class="badge bg-primary">{{ __('The letter has been read') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($pay = $user->pay->where('status', true)->first())
                                <span class="badge badge-warning">{{ $user->tariff()->name() }}</span><br>
                                @if($pay)
                                    <small>Активность до:</small><br>
                                    <small>{{ $pay->active_to->format('d.m.Y H:i') }}</small><br>
                                    <small>{{ $pay->active_to->diffForHumans() }}</small>
                                @endif
                            @endif
                        </td>
                        <td data-order="{{ $user->id }}">
                            {{ $user->created_at->format('d.m.Y H:i:s') }}
                            <br>
                            <small>{{ $user->created_at->diffForHumans() }}</small>
                        </td>
                        <td class="project-state">
                            @foreach($user->getRoleNames() as $role)
                                <span class="badge badge-success">{{ __($role) }}</span><br>
                            @endforeach
                        </td>
                        <td data-order="{{ strtotime($user->last_online_at) }}">
                            {{ $user->last_online_at->format('d.m.Y H:i:s') }}
                            <br>
                            <small>{{ $user->last_online_at->diffForHumans() }}</small>
                        </td>
                        <td class="project-actions text-right">

                            <a class="btn btn-info btn-sm" href="{{ route('users.login', $user->id) }}" title="{{ __('Login') }}">
                                <i class="fas fa-user-alt"></i>
                            </a>
                            <a class="btn btn-info btn-sm" href="{{ route('users.edit', $user->id) }}" title="{{ __('Edit') }}">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <a class="btn btn-info btn-sm" href="{{ route('visit.statistics', $user->id) }}" target="_blank" title="Статистика посещений">
                                <i class="fas fa-chart-pie"></i>
                            </a>

                            @if(isset($user->metrics))
                                <a class="btn btn-info btn-sm"
                                   title="{{ __('utm metrics') }}"
                                   data-toggle="collapse"
                                   href="#collapseExample{{ $user->id }}"
                                   role="button"
                                   aria-expanded="false"
                                   aria-controls="collapseExample{{ $user->id }}">
                                    <i class="fa fa-share-alt"></i>
                                </a>
                            @endif

                            {!! Form::open(['onSubmit' => 'agreeUser(event)', 'class' => 'd-inline', 'method' => 'DELETE', 'route' => ['users.destroy', $user->id]]) !!}
                            {!! Form::button( '<i class="fas fa-trash"></i> ', ['type' => 'submit', 'class' => 'btn btn-danger btn-sm', 'title' => __('Delete')]) !!}
                            {!! Form::close() !!}

                            <div class="mt-2">
                                @if(isset($user->metrics))
                                    <div class="collapse text-left mt-3" id="collapseExample{{ $user->id }}">
                                        @if(is_array($user->metrics))
                                            @foreach($user->metrics as $key => $value)
                                                <div><b>{{ $key }}</b>: {{ urldecode($value) }}</div>
                                            @endforeach
                                        @elseif(strlen($user->metrics) > 2 && $user->metrics != 'null')
                                            <div>
                                                {{ $user->metrics }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @include('users.modal.index', ['id' => 'exportModal', 'action' => route('filter.exports.users'), 'title' => __('User Upload Filter')])
    @include('users.modal.index', ['id' => 'assignTariffModal', 'action' => route('users.tariff'), 'title' => __('Assign tariff')])
@stop

@section('js')
    <!-- Toastr -->
    <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('plugins/select2/js/select2.js') }}"></script>
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function () {

            $('.btn').tooltip({
                animation: false,
                trigger: 'hover',
            });

            $('#service-users').DataTable({
                lengthMenu: [10, 25, 50, 100],
                pageLength: 100,
                language: {
                    lengthMenu: "_MENU_",
                    search: "_INPUT_",
                    searchPlaceholder: "{{ __('Search') }}",
                    paginate: {
                        "first": "«",
                        "last": "»",
                        "next": "»",
                        "previous": "«"
                    },
                },
                order: [
                    [0, 'asc'],
                ],
                columnDefs: [
                    {width: "200px", targets: 1},
                    { orderable: true, targets: [0, 1, 2, 3, 4, 5, 6] },
                    { orderable: false, targets: '_all' },
                ],
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

        function agreeUser(event) {
            if (window.confirm("Do you really want to delete?")) {
                return true;
            } else {
                event.preventDefault();
            }
        }
    </script>
@endsection
