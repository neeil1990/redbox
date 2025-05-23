@extends('layouts.app')

@slot('css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
@endslot

@section('content')

    <div class="row">
        <div class="col-lg-3 col-6">
            <!-- small card -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ __('Balance') }}</h3>

                    <p>{{ __('Your balance') }}: {{ $user->balance }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <a href="{{ route('balance.index') }}" class="small-box-footer">
                    {{ __('More info') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <!-- ./col -->
        @if($name)
        <div class="col-lg-3 col-6">
            <!-- small card -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ __('Tariff') }}</h3>

                    <p>{{ $name }}</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
                <a href="{{ route('tariff.index') }}" class="small-box-footer">
                    {{ __('More info') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <!-- ./col -->
        @endif
        <div class="col-lg-3 col-6">
            <div class="small-box bg-cyan">
                <div class="inner">
                    <h3>{{ __('Setting menu') }}</h3>
                    <p>{{ __('Setting up menu items') }}</p>
                </div>
                <div class="icon">
                    <i class="fa fa-folder"></i>
                </div>
                <a href="{{ route('menu.config') }}" class="small-box-footer">
                    {{ __('Setting up menu items') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div id="toast-container" class="toast-top-right success-message" style="display: none">
            <div class="toast toast-success" aria-live="polite">
                <div class="toast-message">{{ __('The token was copied to the clipboard') }}</div>
            </div>
        </div>
    </div>

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
                        {!! Form::label('lang', __('Lang')) !!}
                        {!! Form::select('lang', $lang, null, ['class' => 'custom-select flags' . ($errors->has('lang') ? ' is-invalid' : '')]) !!}
                        @error('lang') <span class="error invalid-feedback">{{ $message }}</span> @enderror
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
                            @error('password_confirmation') <span
                                class="error invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    {!! Form::submit(__('Save'), ['class' => 'btn btn-primary float-right']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>

        @hasanyrole('Super Admin|admin')
        <div class="col-md-6">
            @include('profile._tariff')
        </div>
        @endhasanyrole

        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Telegram bot') }}</h3>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-info">
                            {{ session('status') }}
                        </div>
                    @endif

                    <a href="https://t.me/RedBoxServiceBot?start={{ base64_encode($user->email) }}" target="_blank"><i class="fab fa-telegram-plane"></i> Подписаться на уведомления</a>
                </div>
                @if ($user->chat_id)
                    <div class="card-footer">
                        <a href="{{ route('profile.test-telegram-notify') }}" class="btn btn-block btn-info btn-sm">Отправить тестовое уведомление</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop

@section('js')
    <!-- Select2 -->
    <script src="{{ asset('plugins/select2/js/select2.js') }}"></script>
    <script src="{{ asset('plugins/select2/js/profile.js') }}"></script>

    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script>
        document.title = "{{ __('Profile') }}";

        $(function () {
            bsCustomFileInput.init();

            $('#generate').click(function () {
                var password = generator.generate({
                    length: 12,
                    numbers: true,
                    symbols: true,
                });

                $('#password').attr('type', 'text').val(password);
                $('#password_confirmation').attr('type', 'text').val(password);
            });

            $(".flags").select2({
                theme: 'bootstrap4',
                minimumResultsForSearch: Infinity,
                templateResult: function (state) {
                    if (!state.id) {
                        return state.text;
                    }
                    var baseUrl = "/img/flags";
                    var $state = $(
                        '<span><img src="' + baseUrl + '/' + state.element.value.toLowerCase() + '.png" class="img-flag" /> ' + state.text + '</span>'
                    );
                    return $state;
                }
            });
        });
    </script>
@endsection

