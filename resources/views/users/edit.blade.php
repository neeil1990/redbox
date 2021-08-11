@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Edit user') }}</h3>
                </div>

                {!! Form::model($user, ['method' => 'PATCH', 'route' => ['users.update', $user->id]]) !!}
                    <div class="card-body">
                        <div class="form-group">
                            {!! Form::label('name', __('Name')) !!}
                            {!! Form::text('name', null, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => __('Name')]) !!}
                            @error('name') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            {!! Form::label('last_name', __('Last name')) !!}
                            {!! Form::text('last_name', null, ['class' => 'form-control' . ($errors->has('last_name') ? ' is-invalid' : ''), 'placeholder' => __('Last name')]) !!}
                            @error('last_name') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            {!! Form::label('email', __('Email')) !!}
                            {!! Form::email('email', null, ['class' => 'form-control' . ($errors->has('email') ? ' is-invalid' : ''), 'placeholder' => __('Email')]) !!}
                            @error('email') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            {!! Form::label('role', __('Roles')) !!}
                            {!! Form::select('role[]', $role, null, ['class' => 'custom-select' . ($errors->has('role') ? ' is-invalid' : ''), 'multiple']) !!}
                            @error('role') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            {!! Form::label('lang', __('Lang')) !!}
                            {!! Form::select('lang', $lang, null, ['class' => 'custom-select' . ($errors->has('lang') ? ' is-invalid' : '')]) !!}
                            @error('lang') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                    </div>
                    <div class="card-footer">
                        {!! Form::submit(__('Edit'), ['class' => 'btn btn-primary float-right']) !!}
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop
