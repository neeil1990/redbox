@component('mail::message')
# Новый проект

Пользователь {{ $user['name'] }} {{ $user['last_name'] }} подтвердил получение проекта {{ $project['name'] }}.

@endcomponent
