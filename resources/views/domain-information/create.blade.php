@component('component.card', ['title' => __('Add tracking the domain registration period')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/common.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>

    @endslot
    {!! Form::open(['action' =>'DomainInformationController@store', 'method' => 'POST', 'class' => 'single'])!!}
    <div class='col-md-6'>
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
            {!! Form::label(__('Check registration Date')) !!}
            {!! Form::select('check_registration_date', [
                '1' => __('yes'),
                '0' => __('no'),
            ], null, ['class' => 'form-control custom-select rounded-0 monitoring']) !!}
        </div>
        <div class="pt-1 pb-1 list">
            <a href="#">Добавить домены списком</a>
        </div>
        <div class='pt-3'>
            <button class='btn btn-secondary mr-2' type='submit'>{{ __('Add to Tracking') }}</button>
            <a href='{{ route('domain.information') }}' class='btn btn-default'>{{ __('To my projects') }}</a>
        </div>
    </div>
    {!! Form::close() !!}
    <div class="much col-md-6" style="display: none">
        {!! Form::open(['action' =>'DomainInformationController@store', 'method' => 'POST'])!!}
        <div class='form-group required'>
            {!! Form::label(__('Domains')) !!}
            <span class="__helper-link ui_tooltip_w">
            <i class="fa fa-question-circle"></i>
                <span class="ui_tooltip __right __l">
                    <span class="ui_tooltip_content" style="width: 600px">
                        <p>domain.ru:1:0</p>
                        domain.ru - {{ __('Domain') }}<br>
                        1 - {{ __('Show DNS information') }}<br>
                        0 - {{ __('Show information about the registration date') }}<br><br>
                        {{ __('The domain must not contain the protocol, slash, and characters after the slash') }} <br>
                        {{ __('Invalid domains (domain.com/my-page, exmpl.com/check/time.php) will not be saved') }} <br>
                        {{ __('Separate the lines using Shift + Enter') }}
                    </span>
                </span>
            </span>
            {!! Form::textarea('domains', null, ['class' => 'form-control', 'required',
'placeholder' => 'domain.com:1:1
domain2.com:0:1 '. __('etc..')]) !!}
        </div>
        <div class="pt-1 pb-1 multi">
            <a href="#">Добавить один домен</a>
        </div>
        <div class='pt-3'>
            <button class='btn btn-secondary mr-2' type='submit'>{{ __('Add to Tracking') }}</button>
            <a href='{{ route('domain.information') }}' class='btn btn-default'>{{ __('To my projects') }}</a>
        </div>
        {!! Form::close() !!}
    </div>
    @if(!\Illuminate\Support\Facades\Auth::user()->telegram_bot_active)
        <div class="col-md-6 mt-2">
            {{ __('Want to') }}
            <a href="{{ route('profile.index') }}" target="_blank">
                {{ __('receive notifications from our telegram bot') }}
            </a> ?
        </div>
    @endif
    @slot('js')
        <script>
            $('.list').click(function () {
                $('.much').show(300)
                $('.single').hide(300)
            });
            $('.multi').click(function () {
                $('.much').hide(300)
                $('.single').show(300)
            });
        </script>
    @endslot
@endcomponent
