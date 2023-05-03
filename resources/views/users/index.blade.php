@extends('layouts.app')

@section('title', __('Users'))

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
@endsection

@section('content')
    <div class="card">
        <div class="p-3 border-bottom d-flex justify-content-between w-100 align-items-center">
            <h3 class="card-title">{{ __('Users') }}</h3>
            <div>
                <a href="{{ route('get.verified.users', 'xls') }}" class="btn btn-secondary">Excel</a>
                <a href="{{ route('get.verified.users', 'csv') }}" class="btn btn-secondary">CSV</a>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#exportModal">
                    Фильтр выгрузки
                </button>
                <a href="{{ route('users.statistics') }}" class="btn btn-secondary">{{ __('General statistics of visits') }}</a>
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
                            <span class="badge badge-warning">{{ $user->tariff()->name() }}</span><br>
                            @if($pay = $user->pay->where('status', true)->first())
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

    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">{{ __('User Upload Filter') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('filter.exports.users') }}" class="modal-body">
                    @csrf
                    <div class="group group-required">
                        <label for="countDays">{{ __('The day of the last online') }}</label>
                        <input class="form form-control" type="datetime-local" name="lastOnline" required>
                    </div>

                    <div class="group group-required mt-3">
                        <label for="verify">{{ __('File Type') }}</label>
                        <select name="fileType" id="fileType" class="custom custom-select">
                            <option value="xls">excel</option>
                            <option value="csv">csv</option>
                        </select>
                    </div>

                    <div class="group group-required mt-3">
                        <label for="verify">{{ __('Verified user') }}</label>
                        <input type="checkbox" name="verify" checked>
                    </div>

                    <div class="pt-3 d-flex justify-content-end">
                        <button type="button" class="btn btn-default mr-1" data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-secondary">{{ __('Export') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <!-- Toastr -->
    <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
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
