@extends('layouts.auth')

@section('title', __('Reset page'))

@section('content')
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <h1><b id="reset-header">{{ __('Reset Password') }}</b></h1>
            </div>
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form action="{{ route('password.email') }}" method="POST">
                    @csrf

                    <div class="input-group mb-3">
                        <select id="select-language" name="lang"
                                class="custom-select flags @error('lang') is-invalid @enderror">
                            @foreach($lang as $l)
                                <option value="{{ $l }}">
                                    @if($l == 'ru')
                                        Русский
                                    @else
                                        English
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('lang')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="input-group mb-3">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}" placeholder="{{ __('E-Mail') }}"
                               autocomplete="email" autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary btn-block">
                            {{ __('Send Password Reset Link') }}
                        </button>
                    </div>

                    <div class="mt-2">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-block btn-danger">
                                <i class="fas fa-registered mr-2"></i> {{ __('Register a new membership') }}
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    @section('js')
        <script>
            if (navigator.language === 'en') {
                $('#select-language').val('en')
            } else {
                $('#select-language').val('ru')
            }

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
        </script>

        <script>
            $(document).ready(function () {
                $('#select-language').on('change', function () {
                    if ($(this).val() === 'en') {
                        $('#reset-header').html('Reset Password')
                        $('body > div > div > div.card-body > form > div:nth-child(4) > button').html('Send Password Reset Link')
                        $('body > div > div > div.card-body > form > div.mt-2 > a.btn.btn-block.btn-danger').html('<i class="fas fa-registered mr-2"></i> Register a new membership')
                    } else {
                        $('#reset-header').html('Сброс пароля')
                        $('body > div > div > div.card-body > form > div:nth-child(4) > button').html('Отправить ссылку для сброса пароля')
                        $('body > div > div > div.card-body > form > div.mt-2 > a.btn.btn-block.btn-danger').html('<i class="fas fa-registered mr-2"></i> Зарегистрировать нового пользователя')
                    }
                })
            })
        </script>
    @endsection
@endsection
