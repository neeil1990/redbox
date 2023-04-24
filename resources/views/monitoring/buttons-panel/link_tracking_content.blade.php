@if($backLinks['total'])
    <h3>{{ $backLinks['total'] }}</h3>
    <p class="mb-0">Работающих: {{ $backLinks['work'] }}</p>
    <p class="mb-0">Неработающих: {{ $backLinks['broken'] }}</p>
@else
    <p>Вы не отслеживаете ссылки</p>
@endif
