@component('component.card', ['title' => __('Добавить отслеживаемый домен')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
    @endslot
    {!! Form::open(['action' =>'DomainMonitoringController@store', 'method' => 'POST'])!!}
    <div class='col-md-6 mt-3'>
        <div class='form-group required'>
            {!! Form::label(__('Название проекта')) !!}
            {!! Form::text('project_name', null, ['class' => 'form form-control','required']) !!}
        </div>
        <div class='form-group required'>
            {!! Form::label(__('Ссылка')) !!}
            {!! Form::text('link', null, ['class' => 'form-control', 'required']) !!}
        </div>
        <div class="form-group required">
            {!! Form::label(__('Частота проверок')) !!}
            {!! Form::select('timing', [
                '1' => 'раз в минуту',
                '5' => 'каждые 5 минут',
                '10' => 'каждые 10 минут',
                '15' => 'каждые 15 минут',
                ], null, ['class' => 'form-control custom-select rounded-0']) !!}
        </div>
        <div id="searchPhrase">
            <div class="form-group required d-flex flex-column">
                {!! Form::label(__('Поиск ключевой фразы')) !!}
                {!! Form::checkbox(null, null, true, ['class' => 'checkbox']); !!}
            </div>
            <div class="form-group required keyword-phrase">
                {!! Form::label(__('Ключевая фраза')) !!}
                {!! Form::text('phrase', null, ['class' => 'form form-control', 'id' => 'phrase', 'required']) !!}
            </div>
        </div>
        <div class='pt-3'>
            <button class='btn btn-secondary mr-2' type='submit'>{{ __('Add to Tracking') }}</button>
            <a href='{{ route('domain.monitoring') }}' class='btn btn-default'>{{ __('To my projects') }}</a>
        </div>
    </div>
    {!! Form::close() !!}
    @slot('js')
        <script src="{{ asset('plugins/domain-monitoring/js/domain-monitoring.js') }}"></script>
    @endslot
@endcomponent
