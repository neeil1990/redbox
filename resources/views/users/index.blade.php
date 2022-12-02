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
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-striped projects" id="service-users">
                <thead>
                <tr>
                    <th style="width: 55px">
                        {{ __('ID') }}
                    </th>
                    <th style="width: 20%">
                        {{ __('Name') }}
                    </th>
                    <th style="width: 30%">
                        {{ __('Email') }}
                    </th>
                    <th>{{__('Created')}}</th>
                    <th style="width: 8%" class="text-center">
                        {{ __('Roles') }}
                    </th>
                    <th>
                        {{__('Was online')}}
                    </th>
                    <th>
                        {{__('Метрики')}}
                    </th>
                    <th style="width: 20%"></th>
                </tr>
                </thead>

                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td data-target="{{ $user->name }}">
                            {{ $user->name }} {{ $user->last_name }}
                            {{--                            @if($user->session)--}}
                            {{--                                <br/>--}}
                            {{--                                <small>--}}
                            {{--                                    <div class="d-flex flex-row align-items-center">--}}
                            {{--                                        <div>--}}
                            {{--                                            @if ($user->session->agent->isDesktop())--}}
                            {{--                                                <i class="fas fa-desktop fa-lg"></i>--}}
                            {{--                                            @else--}}
                            {{--                                                <i class="fas fa-mobile fa-lg"></i>--}}
                            {{--                                            @endif--}}
                            {{--                                        </div>--}}

                            {{--                                        <div class="ml-2">--}}
                            {{--                                            <div class="text-sm text-gray-600">--}}
                            {{--                                                {{ $user->session->agent->platform() }}--}}
                            {{--                                                - {{ $user->session->agent->browser() }}--}}
                            {{--                                            </div>--}}
                            {{--                                            <div class="text-xs text-gray-500">--}}
                            {{--                                                {{ $user->session->ip_address }},--}}

                            {{--                                                @if ($user->session->is_current_device)--}}
                            {{--                                                    <span class="text-green">{{ __('This device') }}</span>--}}
                            {{--                                                @else--}}
                            {{--                                                    {{ __('Last active') }} {{ $user->session->last_active }}--}}
                            {{--                                                @endif--}}
                            {{--                                            </div>--}}
                            {{--                                        </div>--}}
                            {{--                                    </div>--}}
                            {{--                                </small>--}}
                            {{--                            @endif--}}
                        </td>
                        <td data-target="{{ $user->email }}">
                            {{ $user->email }}
                            @if($user->email_verified_at)
                                <span class="badge bg-success">{{ __('VERIFIED') }}</span>
                            @endif
                        </td>
                        <td data-target="{{ $user->created_at->format('d.m.Y') }}">
                            {{ $user->created_at->format('d.m.Y H:m:s') }}
                            <br/>
                            <small>{{ $user->created_at->diffForHumans() }}</small>
                        </td>
                        <td class="project-state">
                            @foreach($user->getRoleNames() as $role)
                                <span class="badge badge-success">{{ __($role) }}</span>
                            @endforeach
                        </td>
                        <td data-target="{{ $user->last_online_at->format('d.m.Y') }}">
                            {{ $user->last_online_at->format('d.m.Y H:m:s') }}
                            <br>
                            <small>{{ $user->last_online_at->diffForHumans() }}</small>
                        </td>
                        <td style="max-width: 350px">
                            @if(is_array($user->metrics))
                                @foreach($user->metrics as $key => $value)
                                    <div><b>{{ $key }}</b>: {{ urldecode($value) }}</div>
                                @endforeach
                            @elseif($user->metrics !== "")
                                <div>
                                    {{ $user->metrics }}
                                </div>
                            @endif
                        </td>
                        <td class="project-actions text-right">
                            <a class="btn btn-info btn-sm" href="{{ route('users.login', $user->id) }}">
                                <i class="fas fa-user-alt">
                                </i>
                                {{ __('Login') }}
                            </a>
                            <a class="btn btn-info btn-sm" href="{{ route('users.edit', $user->id) }}">
                                <i class="fas fa-pencil-alt">
                                </i>
                                {{ __('Edit') }}
                            </a>
                            {!! Form::open(['onSubmit' => 'agreeUser(event)', 'class' => 'd-inline', 'method' => 'DELETE', 'route' => ['users.destroy', $user->id]]) !!}
                            {!! Form::button( '<i class="fas fa-trash"></i> ' . __('Delete'), ['type' => 'submit', 'class' => 'btn btn-danger btn-sm']) !!}
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
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
