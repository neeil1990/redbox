@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Profile') }}</h3>
                </div>

                {!! Form::model($user, ['method' => 'POST', 'enctype' => 'multipart/form-data', 'route' => ['profile.update']]) !!}
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
                        {!! Form::label('image', __('Image')) !!}
                        <div class="input-group">
                            <div class="custom-file">
                                {!! Form::file('image', ['class' => 'custom-file-input', 'id' => 'customFile', 'accept' => '.jpg, .jpeg, .png']) !!}
                                {!! Form::label('image', __('Choose file'), ['class' => 'custom-file-label', 'for' => 'customFile']) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    {!! Form::submit(__('Edit'), ['class' => 'btn btn-primary float-right']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Password edit') }}</h3>
                </div>

                {!! Form::model($user, ['method' => 'PATCH', 'enctype' => 'multipart/form-data', 'route' => ['profile.password']]) !!}
                <div class="card-body">
                    <div class="form-group">
                        {!! Form::label('password', __('New password')) !!}
                        <div class="input-group">
                            {!! Form::password('password', ['id' => 'password', 'class' => 'form-control' . ($errors->has('password') ? ' is-invalid' : ''), 'placeholder' => __('New password')]) !!}
                            <div class="input-group-append">
                                <span class="input-group-text" id="generate">{{ __('Generate password') }}</span>
                            </div>
                            @error('password') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                    </div>

                    <div class="form-group">
                        {!! Form::label('password_confirmation', __('Confirm password')) !!}
                        <div class="input-group">
                            {!! Form::password('password_confirmation', ['id' => 'password_confirmation', 'class' => 'form-control' . ($errors->has('password_confirmation') ? ' is-invalid' : ''), 'placeholder' => __('Confirm password')]) !!}
                            @error('password_confirmation') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    {!! Form::submit(__('Save'), ['class' => 'btn btn-primary float-right']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script>
        $(function () {
            bsCustomFileInput.init();

            $('#generate').click(function(){
                var password = generator.generate({
                    length: 12,
                    numbers: true,
                    symbols: true,
                });

                $('#password').val(password);
                $('#password_confirmation').val(password);
            });
        });
    </script>
@endsection

