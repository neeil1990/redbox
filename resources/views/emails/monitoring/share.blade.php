@component('mail::message')
# {{ __('New project') }}

{{ __('You have been given access to the project') }} {{ $project['name'] }}

@component('mail::button', ['url' => config('app.url') . '/monitoring'])
Посмотреть в кабинете
@endcomponent

@endcomponent
