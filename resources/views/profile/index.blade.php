@extends('layouts.app')
<title>
    {{ __('Profile') }}
</title>

@section('css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
@stop

@section('content')

    <div class="row">
        <div class="col-lg-3 col-6">
            <!-- small card -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ __('Balance') }}</h3>

                    <p>{{ __('Your balance:') }} {{ $user->balance }}</p>
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
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <!-- ./col -->
        @endif
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
    </div>
    <div class="card card-primary w-50">
        <div class="card-header">
            <h3 class="card-title">{{ __('Telegram bot') }}</h3>
        </div>
        @if(!$user->telegram_bot_active)
            <div class="card-body">
                <div>{{ __("This is your special token, don't show it to anyone!") }}<br>
                    <div class="text-info">
                        <input type="text" value="{{ $user->telegram_token }}" id="special-token" class="form form-control w-75 d-inline">
                        <button class="btn btn-default ml-2" id="saveInBufferButton">
                            <i class="fa fa-copy"></i>
                        </button>
                    </div>
                </div>
                <p>{{ __('send it to our telegram bot') }}
                    <span>
                    <a href="https://t.me/RedboxNotificationBot" target="_blank">
                        @RedboxNotificationBot
                    </a>
                </span>
                    {{ __('in order to receive notifications') }}</p>
            </div>
            <div class="card-footer">
                <form action="{{ route('verification.token', $user->telegram_token)}}"
                      method="get">
                    @csrf
                    <button class="btn btn-secondary" type="submit">
                        {{ __('I sent the token to the bot') }}
                    </button>
                </form>
            </div>
        @else
            <div class="card-body">
                <p>{{ __('You have set up receiving notifications from the bot') }}</p>
                <p>
                    {{ __('Want to') }}
                    <a href="{{ route('reset.notification', $user->telegram_token) }}">
                        {{ __('stop receiving notifications') }}
                    </a>
                    ?</p>
            </div>
        @endif
    </div>
@stop

@section('js')
    <!-- Select2 -->
    <script src="{{ asset('plugins/select2/js/select2.js') }}"></script>
    <script src="{{ asset('plugins/select2/js/profile.js') }}"></script>

    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script>
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

