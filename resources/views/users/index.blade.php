@extends('layouts.app')

@section('title', __('Users'))

@section('content')
    <div class="card">
        <div class="p-3 border-bottom d-flex justify-content-between w-100 align-items-center">
            <h3 class="card-title">{{ __('Users') }}</h3>
            <div>
                <a href="{{ route('get.verified.users', 'xls') }}" class="btn btn-secondary">Excel</a>
                <a href="{{ route('get.verified.users', 'csv') }}" class="btn btn-secondary">CSV</a>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-striped projects">
                <thead>
                <tr>
                    <th style="width: 1%">
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
                    <th style="width: 20%"></th>
                </tr>
                </thead>

                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>
                            <a>{{ $user->name }} {{ $user->last_name }}</a>
                            @if($user->session)
                                <br/>
                                <small>
                                    <div class="d-flex flex-row align-items-center">
                                        <div>
                                            @if ($user->session->agent->isDesktop())
                                                <i class="fas fa-desktop fa-lg"></i>
                                            @else
                                                <i class="fas fa-mobile fa-lg"></i>
                                            @endif
                                        </div>

                                        <div class="ml-2">
                                            <div class="text-sm text-gray-600">
                                                {{ $user->session->agent->platform() }}
                                                - {{ $user->session->agent->browser() }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $user->session->ip_address }},

                                                @if ($user->session->is_current_device)
                                                    <span class="text-green">{{ __('This device') }}</span>
                                                @else
                                                    {{ __('Last active') }} {{ $user->session->last_active }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </small>
                            @endif
                        </td>
                        <td>
                            {{ $user->email }}
                            @if($user->email_verified_at)
                                <span class="badge bg-success">{{ __('VERIFIED') }}</span>
                            @endif
                        </td>
                        <td>
                            {{ $user->created_at->format('d.m.Y H:m:s') }}
                            <br/>
                            <small>{{ $user->created_at->diffForHumans() }}</small>
                        </td>
                        <td class="project-state">
                            @foreach($user->getRoleNames() as $role)
                                <span class="badge badge-success">{{ __($role) }}</span>
                            @endforeach
                        </td>
                        <td>
                            {{ $user->last_online_at->format('d.m.Y H:m:s') }}
                            <br>
                            <small>{{ $user->last_online_at->diffForHumans() }}</small>
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
    <div class="pb-5">
        {{ $users->links() }}
    </div>
@stop

@section('js')
    <script>

        $('.page-item').children().css({
            'font-size': '30px',
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
