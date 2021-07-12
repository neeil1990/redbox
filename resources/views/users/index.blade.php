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
                    <th>
                        Project Progress
                    </th>
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
                        <br/>
                        <small>
                            Created {{ $user->created_at->locale('ru_RU')->diffForHumans() }}
                        </small>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td class="project_progress">
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-green" role="progressbar" aria-valuenow="47" aria-valuemin="0" aria-valuemax="100" style="width: 47%">
                            </div>
                        </div>
                        <small>
                            47% Complete
                        </small>
                    </td>
                    <td class="project-state">
                        <span class="badge badge-success">Success</span>
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
