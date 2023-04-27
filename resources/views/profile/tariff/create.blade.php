@extends('layouts.app')

@section('css')

@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary" id="tariff-settings">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Tariff settings') }}</h3>
                </div>
                {!! Form::open(['method' => 'POST', 'route' => ['user-tariff.store']]) !!}
                <div class="card-body">
                    @include('profile.tariff.partials._form')
                    <div class="fields"></div>
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

    <script>
        let el = $('#settings');
        el.change(function(){
            let self = $(this);
            let settingId = self.val();

            axios.get('/profile/user-tariff/' + settingId)
                .then(function (response) {
                    // handle success
                    $('#tariff-settings').find('.fields').html(response.data);
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                });
        });

        el.trigger('change');
    </script>

@endsection
