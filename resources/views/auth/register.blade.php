@extends('layouts.auth')

@section('title', __('Register page'))

@section('content')

<div class="register-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="#" class="h1"><b>{{ __('Register') }}</b></a>
            </div>

            <div class="card-body">

                <form action="{{ route('register') }}" method="POST">
                    <p class="login-box-msg">{{ __('Select your language') }}</p>

                    <div class="input-group mb-3">
                        <select name="lang" class="custom-select flags @error('lang') is-invalid @enderror">
                            @foreach($lang as $l)
                            <option value="{{ $l }}">{{ __($l) }}</option>
                            @endforeach
                        </select>
                        @error('lang')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <p class="login-box-msg">{{ __('Register a new membership') }}</p>
                    @csrf

                    <div class="input-group mb-3">
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="{{ __('Name') }}" autocomplete="name" autofocus required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="input-group mb-3">
                        <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" placeholder="{{ __('Last name') }}" autocomplete="last_name" autofocus required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                        @error('last_name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="input-group mb-3">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="{{ __('E-Mail Address') }}" autocomplete="email" required>
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
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="{{ __('Password') }}" autocomplete="new-password" required>
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
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="{{ __('Confirm Password') }}" autocomplete="new-password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="icheck-primary">
                                <input type="checkbox" id="agreeTerms" name="terms" value="agree" required>
                                <label for="agreeTerms">
                                    {{ __('I give my consent to the processing') }} <a href="{{ __('rule agree link one') }}" target="_blank">{{ __('personal data') }}</a> {{ __('and agree to the terms') }}
                                    <a href="{{ __('rule agree link two') }}" target="_blank">{{ __('privacy policy') }}</a>
                                </label>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block" onclick="ym(89500732,'reachGoal','novaja_registracija_1231')">
                                <i class="fas fa-user-plus"></i> {{ __('Register') }}
                            </button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

                <div class="social-auth-links text-center">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="btn btn-block btn-primary">
                            <i class="fas fa-key mr-2"></i> {{ __('Forgot Your Password?') }}
                        </a>
                    @endif
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="btn btn-block btn-danger">
                            <i class="fas fa-user mr-2"></i> {{ __('Login membership') }}
                        </a>
                    @endif
                </div>
            </div>
            <!-- /.form-box -->
        </div><!-- /.card -->
    </div>
@endsection

@section('js')
    <script>
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
@endsection
