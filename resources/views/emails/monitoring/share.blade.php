@component('mail::message')
# {{ __('New project') }}

{{ __('You have been given access to the project') }} {{ $project['name'] }}, {{ __('To confirm, follow the link') }} 

@component('mail::button', ['url' => config('app.url') . '/monitoring'])
{{ __('Confirm in your account') }}
@endcomponent

@endcomponent
