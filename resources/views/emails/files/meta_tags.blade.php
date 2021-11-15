@component('mail::message')

@component('mail::panel')
    {{ $message }}
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
