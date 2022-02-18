@extends('layouts.app')

@section('css')

@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Tariff settings') }}</h3>
                </div>

                {!! Form::open(['method' => 'POST', 'route' => ['tariff-setting-values.store']]) !!}

                {!! Form::hidden('tariff_setting_id', request('id')) !!}

                <div class="card-body">
                    @include('tariff-setting-values.partials._form')
                </div>
                <div class="card-footer">
                    {!! Form::submit('Сохранить', ['class' => 'btn btn-primary float-right']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop

@section('js')


@endsection
