@component('component.card', ['title' => __('Добавить отслеживаемый домен')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
    @endslot
    {!! Form::open(['action' =>'DomainMonitoringController@store', 'method' => 'POST'])!!}
    <div class='col-md-6 mt-3'>
        <div class='form-group required'>
            {!! Form::label(__('Project name')) !!}
            {!! Form::text('project_name', null, ['class' => 'form form-control','required']) !!}
        </div>
        <div class='form-group required'>
            {!! Form::label(__('Link')) !!}
            {!! Form::text('link', null, ['class' => 'form-control', 'required']) !!}
        </div>
        <div class="form-group required">
            {!! Form::label(__('Frequency of checks')) !!}
            {!! Form::select('timing', [
                '1' => __('once a minute'),
                '5' => __('every 5 minutes'),
                '10' => __('every 10 minutes'),
                '15' => __('every 15 minutes'),
                ], null, ['class' => 'form-control custom-select rounded-0']) !!}
        </div>
        <div class="form-group required">
            {!! Form::label(__('Response waiting time in seconds')) !!}
            {!! Form::select('waiting_time', [
            '1' => 1,
            '2' => 2,
            '3' => 3,
            '4' => 4,
            '5' => 5,
            '6' => 6,
            '7' => 7,
            '8' => 8,
            '9' => 9,
            '10' => 10,
            ],
            6,
            ['class' => 'form-control custom-select rounded-0 monitoring']) !!}
        </div>
        <div id="searchPhrase">
            <div class="form-group required d-flex flex-column">
                {!! Form::label(__('Keyword Search')) !!}
                {!! Form::checkbox(null, null, true, ['class' => 'checkbox']); !!}
            </div>
            <div class="form-group required keyword-phrase">
                {!! Form::label(__('Keyword')) !!}
                {!! Form::text('phrase', null, ['class' => 'form form-control', 'id' => 'phrase', 'required']) !!}
            </div>
        </div>
        <div id="notification" style="display:none;">
            <span
                class="text-info">{{ __('If the phrase is not selected, the server will wait for the 200 response code') }}</span>
        </div>
        @if(!\Illuminate\Support\Facades\Auth::user()->telegram_bot_active)
            <span>
            {{ __('Want to') }}
                <a href="{{ route('profile.index') }}" target="_blank">
                    {{ __('receive notifications from our telegram bot') }}
                </a> ?
            </span>
        @endif
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
