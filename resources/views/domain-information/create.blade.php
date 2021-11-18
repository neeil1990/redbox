@component('component.card', ['title' => __('Add a monitored domain')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
    @endslot
    {!! Form::open(['action' =>'DomainInformationController@store', 'method' => 'POST'])!!}
    <div class='col-md-6 mt-3'>
        <div class='form-group required'>
            {!! Form::label(__('Domain')) !!}
            {!! Form::text('domain', null, ['class' => 'form-control', 'required']) !!}
        </div>
        <div class="form-group required">
            {!! Form::label(__('Check DNS')) !!}
            {!! Form::select('check_dns', [
                '1' => __('yes'),
                '0' => __('no'),
                ], null, ['class' => 'form-control custom-select rounded-0']) !!}
        </div>
        <div class="form-group required">
            {!! Form::label(__('check Registration date')) !!}
            {!! Form::select('check_registration_date', [
                '1' => __('yes'),
                '0' => __('no'),
            ], null, ['class' => 'form-control custom-select rounded-0 monitoring']) !!}
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
            <a href='{{ route('domain.information') }}' class='btn btn-default'>{{ __('To my projects') }}</a>
        </div>
    </div>
    {!! Form::close() !!}
@endcomponent
