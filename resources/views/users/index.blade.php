@extends('layouts.app')

@section('title', __('Users'))

@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
    <div class="card">
        <div class="p-3 border-bottom d-flex justify-content-between w-100 align-items-center">
            <h3 class="card-title">{{ __('Users') }}</h3>
            <div>
                <a href="{{ route('get.verified.users', 'xls') }}" class="btn btn-secondary">Excel</a>
                <a href="{{ route('get.verified.users', 'csv') }}" class="btn btn-secondary">CSV</a>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#exportModal">
                    Фильтр выгрузки
                </button>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-striped projects" id="service-users">
                <thead>
                <tr>
                    <th style="width: 6%">
                        {{ __('ID') }}
                    </th>
                    <th style="width: 20%">
                        {{ __('Name') }}
                    </th>
                    <th>
                        {{ __('Email') }}
                    </th>
                    <th>{{__('Created')}}</th>
                    <th class="text-center">
                        {{ __('Roles') }}
                    </th>
                    <th>
                        {{__('Was online')}}
                    </th>
                    <th style="width: 372px"></th>
                </tr>
                </thead>

                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td data-target="{{ $user->name }}">
                            {{ $user->name }} {{ $user->last_name }}
                        </td>
                        <td data-target="{{ $user->email }}">
                            {{ $user->email }}
                            @if($user->email_verified_at)
                                <span class="badge bg-success">{{ __('VERIFIED') }}</span>
                            @endif
                            @if($user->read_letter)
                                <span class="badge bg-primary">{{ __('The letter has been read') }}</span>
                            @endif
                        </td>
                        <td data-target="{{ $user->id }}">
                            {{ $user->created_at->format('d.m.Y H:m:s') }}
                            <br>
                            <small>{{ $user->created_at->diffForHumans() }}</small>
                        </td>
                        <td class="project-state">
                            @foreach($user->getRoleNames() as $role)
                                <span class="badge badge-success">{{ __($role) }}</span>
                            @endforeach
                        </td>
                        <td data-target="{{ (int) strtotime($user->last_online_at) }}">
                            {{ $user->last_online_at->format('d.m.Y H:m:s') }}
                            <br>
                            <small>{{ $user->last_online_at->diffForHumans() }}</small>
                        </td>
                        <td class="project-actions @empty($user->metrics) text-right @endempty">
                            <a class="btn btn-info btn-sm" href="{{ route('users.login', $user->id) }}">
                                <i class="fas fa-user-alt"></i>
                                {{ __('Login') }}
                            </a>
                            <a class="btn btn-info btn-sm" href="{{ route('users.edit', $user->id) }}">
                                <i class="fas fa-pencil-alt"></i>
                                {{ __('Edit') }}
                            </a>
                            <a class="btn btn-info btn-sm" href="{{ route('visit.statistics', $user->id) }}"
                               target="_blank">
                                <i class="fas fa-chart-pie"></i>
                                Статистика посещений
                            </a>

                            <div class="mt-2">
                                @if(isset($user->metrics))
                                    <a class="btn btn-info btn-sm" data-toggle="collapse"
                                       href="#collapseExample{{ $user->id }}"
                                       role="button" aria-expanded="false"
                                       aria-controls="collapseExample{{ $user->id }}">
                                        <i class="fa fa-share-alt"></i>
                                        {{ __('utm metrics') }}
                                    </a>
                                @endif

                                {!! Form::open(['onSubmit' => 'agreeUser(event)', 'class' => 'd-inline', 'method' => 'DELETE', 'route' => ['users.destroy', $user->id]]) !!}
                                {!! Form::button( '<i class="fas fa-trash"></i> ' . __('Delete'), ['type' => 'submit', 'class' => 'btn btn-danger btn-sm']) !!}
                                {!! Form::close() !!}

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
                    <h5 class="modal-title" id="exportModalLabel">Фильтр выгрузки пользователей</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('filter.exports.users') }}" class="modal-body">
                    @csrf
                    <div class="group group-required">
                        <label for="countDays">День последнего онлайна</label>
                        <input class="form form-control" type="datetime-local" name="lastOnline" required>
                    </div>

                    <div class="group group-required mt-3">
                        <label for="verify">Тип файла</label>
                        <select name="fileType" id="fileType" class="custom custom-select">
                            <option value="xls">excel</option>
                            <option value="csv">csv</option>
                        </select>
                    </div>

                    <div class="group group-required mt-3">
                        <label for="verify">Верифицированный пользователь</label>
                        <input type="checkbox" name="verify" checked>
                    </div>

                    <div class="pt-3 d-flex justify-content-end">
                        <button type="button" class="btn btn-default mr-1" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Export</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#service-users').DataTable({
                "pageLength": 100,
            })

            $('#service-users_length').css({
                'padding-left': '15px',
                'padding-top': '15px',
            })

            $('#service-users_info ').css({
                'padding-left': '15px',
                'padding-top': '15px',
            })

            $('#service-users_filter ').css({
                'padding-right': '15px',
                'padding-top': '15px',
            })

            $('#service-users_paginate ').css({
                'margin-right': '15px',
                'margin-top': '15px',
                'margin-bottom': '15px',
            })
        })

        function agreeUser(event) {
            if (window.confirm("Do you really want to delete?")) {
                return true;
            } else {
                event.preventDefault();
            }
        }
    </script>
@endsection
