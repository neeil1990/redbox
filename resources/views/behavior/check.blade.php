@extends('layouts.auth')
@slot('css')
    <style>
        .behavior {
            background: oldlace;
        }
    </style>
@endslot
@section('content')

    <div class="login-box">
        @if (session('applied'))
            <div class="alert alert-success" role="alert">
                {{ session('applied') }}
            </div>
        @endif

        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="#" class="h1"><b>{{ __('Enter your exercise') }}</b></a>
            </div>
            <div class="card-body">
                {!! $behavior->description !!}
                <p class="login-box-msg">
                    <span class="text-lg">{{ __('Search request') }}: {{ $phrases->phrase }}</span><br/>
                    <span class="text-lg">{{ __('Domain') }}: {{ $domain }}</span>
                </p>

                <form action="{{ route('behavior.verify') }}" method="POST">
                    @csrf
                    <input type="hidden" name="domain" value="{{$behavior->domain}}">
                    <div class="form-group">
                        <label>{{ __('Enter the site visit code') }}</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control @error('code') is-invalid @enderror" name="code"
                                   value="{{ old('code') }}" placeholder="{{ __('Promo code') }}" autocomplete="email"
                                   autofocus>
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
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">{{ __('Send code') }}</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
