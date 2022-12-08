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
                            <button type="submit" class="btn btn-primary btn-block" id="verifyCodeButton"
                                    onclick="ym(89500732,'reachGoal','verifikacija_po_majlu_1628')"
                                    style="display: none">{{ __('Send') }}</button>
                            <button type="submit" class="btn btn-primary btn-block" id="fakeButton"
                                    disabled>{{ __('Send') }}</button>
                        </div>
                    </div>
                </form>
                <p class="mt-2">{{ __('If you did not receive the email') }},
                    <a href="{{ route('verification.resend') }}">{{ __('click here to request another') }}</a>.
                </p>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $('body > div.login-box > div > div.card-body > form > div.input-group.mb-3 > input').on('keyup', function () {
            if ($(this).val().length > 0) {
                $.ajax({
                    method: "post",
                    dataType: "json",
                    data: {
                        code: $(this).val(),
                    },
                    url: "{{ route('validate.verify.code') }}",
                    error: function (response) {
                        $('#fakeButton').show()
                        $('#verifyCodeButton').hide()
                    },
                    success: function (response) {
                        $('#fakeButton').hide()
                        $('#verifyCodeButton').show()
                    }
                })
            }
        })
    </script>
@endsection
