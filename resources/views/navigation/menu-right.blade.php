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

@if($tariffs != [])
    <div class="dropdown p-0 m-0 nav-item">
        <span class="dropdown-toggle nav-link" role="button" data-toggle="dropdown" aria-expanded="false">
            {{ __('Your limits') }}
        </span>
        <div class="dropdown-menu p-0 m-0" style="width: 410px">
            <table class="table table-bordered p-0 m-0">
                <thead>
                <tr>
                    <th>{{ __('Module') }}</th>
                    <th>{{ __('Limits') }}</th>
                    <th>{{ __('Left') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tariffs as $key => $tariff)
                    @if($key != 'price')
                        <tr class="{{ $key }}">
                            <td>{{ $tariff['name'] }}</td>
                            <td>
                                @if($tariff['value'] === 1000000)
                                    {{ __('No restrictions') }}
                                @else
                                    {{ $tariff['value'] }}
                                @endif
                            </td>
                            <td>
                                @if(gettype($tariff['used']) == 'integer')
                                    {{ $tariff['value'] - $tariff['used'] }}
                                @else
                                    {{ __('No restrictions') }}
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <li class="nav-item">
        <div class="nav-link">
            <span id="userModuleUsed"></span>
            <span id="userModuleLimit"></span>
        </div>
    </li>

@endif

<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script>
    $(document).ready(function () {
        $.each($('#header-nav-bar > ul.navbar-nav.ml-auto > div > div > table > tbody > tr'), function (key, value) {
            if ($(this).css('background-color') === 'rgb(253, 245, 230)') {
                if (($(this).children('td').eq(1).html()).trim() === 'Без ограничений') {
                    $('#userModuleLimit').html('Без ограничений')
                } else {
                    $('#userModuleLimit').html("из " + $(this).children('td').eq(1).html())
                    $('#userModuleUsed').html("Осталось " + $(this).children('td').eq(2).html())
                }
                return;
            }
        });
    })
</script>
