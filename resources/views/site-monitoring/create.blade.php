@php use Illuminate\Support\Facades\Auth; @endphp
@component('component.card', ['title' => __('Add a monitored domain')])
    @slot('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/site-monitoring/css/site-monitoring.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
        <style>
            .domainMonitoringProject {
                background: oldlace;
            }
        </style>
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
                '5' => __('every 5 minutes'),
                '10' => __('every 10 minutes'),
                '15' => __('every 15 minutes'),
                '20' => __('every 20 minutes'),
                '30' => __('every 30 minutes'),
                '60' => __('every 60 minutes'),
                ], null, ['class' => 'form-control custom-select rounded-0']) !!}
        </div>
        <div class="form-group required">
            {!! Form::label(__('Response waiting time')) !!}
            {!! Form::select('waiting_time', [
            '10' => '10 ' . __("sec"),
            '15' => '15 ' . __("sec"),
            '20' => '20 ' . __("sec"),
            ], 10, ['class' => 'form-control custom-select rounded-0 monitoring']) !!}
        </div>
        <div id="searchPhrase">
            <div class="form-group required flex-column">
                {!! Form::label(__('Keyword Search')) !!}
                {!! Form::checkbox(null, null, true, ['class' => 'checkbox']); !!}
            </div>
            <div class="form-group required keyword-phrase">
                {!! Form::label(__('Keyword')) !!}
                <span class="__helper-link ui_tooltip_w">
                <i class="fa fa-question-circle" style="color: grey"></i>
                    <span class="ui_tooltip __bottom">
                        <span class="ui_tooltip_content" style="width: 300px">
                           {{ __('It is better to take the keyword from the page code') }}
                        </span>
                    </span>
                </span>
                {!! Form::text('phrase', null, ['class' => 'form form-control', 'id' => 'phrase', 'required']) !!}
            </div>
        </div>
        <div id="notification" style="display:none;" class="mt-3 mb-3">
            <span class="text-info">{{ __('If the phrase is not selected, the server will wait for the 200 response code') }}</span>
        </div>
        @if(!Auth::user()->telegram_bot_active)
            <span>
            {{ __('Want to') }}
                <a href="{{ route('profile.index') }}" target="_blank">
                    {{ __('receive notifications from our telegram bot') }}
                </a> ?
            </span>
        @endif
        <div class='pt-3'>
            <button class='btn btn-secondary mr-2' type='submit'>{{ __('Add to Tracking') }}</button>
            <a href='{{ route('site.monitoring') }}' class='btn btn-default'>{{ __('To my projects') }}</a>
        </div>
    </div>
    {!! Form::close() !!}
    @slot('js')
        <script src="{{ asset('plugins/site-monitoring/js/site-monitoring.js') }}"></script>
    @endslot
@endcomponent
