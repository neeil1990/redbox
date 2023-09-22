@component('mail::message')
# Новый проект

Вам дали доступ к проекту, для подтверждения перейдите по ссылке 

@component('mail::button', ['url' => config('app.url') . '/monitoring'])
Подтвердить
@endcomponent

@endcomponent
