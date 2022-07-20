@extends('layouts.auth')

@section('title', __('Reset page'))

@section('content')
<div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="#" class="h1"><b>{{ __('Reset Password') }}</b></a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">{{ __('You forgot your password? Here you can easily retrieve a new password.') }}</p>
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form action="{{ route('password.email') }}" method="POST">
                    @csrf

                    <div class="input-group mb-3">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="{{ __('E-Mail Address') }}" autocomplete="email" autofocus>
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
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">{{ __('Send Password Reset Link') }}</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
                <div class="social-auth-links text-center mt-2 mb-3">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-block btn-danger">
                            <i class="fas fa-registered mr-2"></i> {{ __('Register a new membership') }}
                        </a>
                    @endif
                </div>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
@endsection
