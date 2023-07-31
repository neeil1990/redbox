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
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="#" class="h1"><b>{{ __('End tasks') }}</b></a>
            </div>
            <div class="card-body text-center">
                <p>Задания закончились, обратитесь к администратору.</p>
            </div>
        </div>
    </div>
@endsection
