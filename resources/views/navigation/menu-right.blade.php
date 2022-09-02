<li class="nav-item">
    <a class="nav-link" href="{{ route('balance.index') }}" role="button">
        {{ __('Your balance') }}: {{ $user->balance }}
    </a>
</li>

@if($name)
    <li class="nav-item">
        <a class="nav-link" href="{{ route('tariff.index') }}" role="button">
            {{ __('Your tariff') }}: {{ $name }}
        </a>
    </li>
@endif

<div class="dropdown p-0 m-0 nav-item">
    <span class="dropdown-toggle nav-link" role="button" data-toggle="dropdown" aria-expanded="false">
        Ваши лимиты
    </span>

    <div class="dropdown-menu p-0 m-0" style="width: 410px">
        <table class="table table-bordered p-0 m-0">
            <thead>
            <tr>
                <th>Модуль</th>
                <th>Лимиты</th>
                <th>Исчерпано</th>
            </tr>
            </thead>
            <tbody>
            @foreach($tariffs as $key => $tariff)
                @if($key != 'price')
                    <tr>
                        <td>{{ $tariff['name'] }}</td>
                        <td>{{ $tariff['value'] }}</td>
                        <td>
                            <div class="progress progress-xs">
                                <div class="progress-bar progress-bar-danger"
                                     style="width: {{ $tariff['percent'] }}%"></div>
                            </div>
                            Осталось {{ $tariff['value'] - $tariff['used'] }}
                        </td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
</div>

