@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Projects</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped projects">
                <thead>
                <tr>
                    <th style="width: 1%">
                        ID
                    </th>
                    <th style="width: 20%">
                        Name
                    </th>
                    <th style="width: 30%">
                        Email
                    </th>
                    <th>{{__('Created')}}</th>
                    <th style="width: 8%" class="text-center">
                        Status
                    </th>
                    <th style="width: 20%">
                    </th>
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
                                        {{ $user->session->agent->platform() }} - {{ $user->session->agent->browser() }}
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
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_at->format('m.d.Y H:m:s') }}</td>
                    <td class="project-state">
                        @foreach($user->getRoleNames() as $role)
                            <span class="badge badge-success">{{ $role }}</span>
                        @endforeach
                    </td>
                    <td class="project-actions text-right">
                        <a class="btn btn-info btn-sm" href="{{ route('users.edit', $user->id) }}">
                            <i class="fas fa-pencil-alt">
                            </i>
                            {{ __('Edit') }}
                        </a>
                        {!! Form::open(['class' => 'd-inline', 'method' => 'DELETE', 'route' => ['users.destroy', $user->id]]) !!}
                            {!! Form::button( '<i class="fas fa-trash"></i> ' . __('Delete'), ['type' => 'submit', 'class' => 'btn btn-danger btn-sm']) !!}
                        {!! Form::close() !!}
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
@stop
