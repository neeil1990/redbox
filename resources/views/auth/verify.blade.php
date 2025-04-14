@extends('layouts.auth')

@section('title', __('Verify page'))

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="login-box">
        @if (session('resent'))
            <div class="alert alert-success" role="alert">
                {{ __('A fresh verification link has been sent to your email address.') }}
            </div>
        @endif

        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="#" class="h1"><b>{{ __('Verify Your Email Address') }}</b></a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">
                    {{ __('Before proceeding, please check your email for a verification link.') }}
                </p>

                <form action="{{ route('verification.code') }}" method="POST">
                    @csrf

                    <div class="input-group mb-3">
                        <input type="text" class="form-control @error('code') is-invalid @enderror" name="code"
                               value="{{ old('code') }}" placeholder="{{ __('Code') }}" autocomplete="email" autofocus
                               required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-key"></span>
                            </div>
                        </div>
                        @error('code')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block"
                                    onclick="ym(89500732,'reachGoal','verifikacija_po_majlu_1628'); _tmr.push({ type: 'reachGoal', id: 3340935, goal: 'Verifikacija170523'});">
                                {{ __('Send') }}
                            </button>
                        </div>
                    </div>
                </form>
                <p class="mt-2">
                    <form method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-block btn-outline-secondary btn-sm">{{ __('click here to request another') }}</button>
                    </form>
                </p>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>

    </script>
@endsection
