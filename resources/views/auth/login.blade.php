@extends('layouts.auth')

@section('title', __('Login page'))

@section('content')
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <h1><b id="auth-header">{{ __('Log in to the system') }}</b></h1>
            </div>
            <div class="card-body">
                <form action="{{ route('login') }}" method="POST">
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
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="{{ __('E-Mail') }}" autocomplete="email" autofocus>
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
                    <div class="input-group mb-3">
                        <input id="password" type="password"
                               class="form-control @error('password') is-invalid @enderror" name="password"
                               placeholder="{{ __('Password') }}" autocomplete="current-password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>

                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="input-group mb-3">
                        <div class="icheck-primary">
                            <input type="checkbox" name="remember"
                                   id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember" id="remember-me-label">
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                    </div>

                    <div class="input-group">
                        <button type="submit" class="btn btn-primary btn-block" id="login-button">{{ __('Login') }}</button>
                    </div>
                </form>

                <div class="social-auth-links text-center mt-2 mb-3">
                    <div class="row">
                        @if (Route::has('password.request'))
                            <div class="col-6">
                                <a href="{{ route('password.request') }}" class="btn btn-block btn-danger">
                                    <i class="fas fa-key mr-2"></i> {{ __('Forgot Your Password?') }}
                                </a>
                            </div>
                        @endif

                        @if (Route::has('register'))
                            <div class="col-6">
                                <a href="{{ route('register') }}" class="btn btn-block btn-primary">
                                    <i class="fas fa-registered mr-2"></i> {{ __('Register') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

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
                    setEngLanguage()
                } else {
                    setRuLanguage()
                }
            })

            function setEngLanguage() {
                $('#password').attr('placeholder', 'Password')
                $('#remember-me-label').html('Remember me')
                $('#login-button').html('Login')
                $('body > div > div > div.card-body > div > a.btn.btn-block.btn-primary').html('<i class="fas fa-key mr-2"></i> Forgot your password?')
                $('body > div > div > div.card-body > div > a.btn.btn-block.btn-danger').html('<i class="fas fa-registered mr-2"></i> Register a new user')
                $('#auth-header').html('Log in to the system')
            }

            function setRuLanguage() {
                $('#password').attr('placeholder', 'Пароль')
                $('#remember-me-label').html('Запомнить меня')
                $('#login-button').html('Войти')
                $('body > div > div > div.card-body > div > a.btn.btn-block.btn-primary').html('<i class="fas fa-key mr-2"></i> Забыли пароль?')
                $('body > div > div > div.card-body > div > a.btn.btn-block.btn-danger').html('<i class="fas fa-registered mr-2"></i> Зарегистрировать нового пользователя')
                $('#auth-header').html('Вход в систему')
            }
        })
    </script>
    <script>
        if (localStorage.getItem('_user_metrics_redbox') != '' && localStorage.getItem('_user_metrics_redbox') != undefined) {
            let registerButtons = $("a[href='{{ route('register') }}']");

            $.each(registerButtons, function (k, element) {
                element.href += localStorage.getItem('_user_metrics_redbox');
            })

        } else if (new URL(window.location.href)['search'] != '' && new URL(window.location.href)['search'] != undefined) {
            let registerButtons = $("a[href='{{ route('register') }}']");

            $.each(registerButtons, function (k, element) {
                element.href += new URL(window.location.href)['search'];
            })
        }
    </script>
@endsection

