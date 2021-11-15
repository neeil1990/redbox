<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    </head>
<body style="font-family: firefly, DejaVu Sans, sans-serif;font-size: 11px;">

    <h1>{{ $project['name'] }}</h1>

    <table border="1" cellspacing="0" cellpadding="15">
        @foreach($project['data'] as $link => $td)
            <tr><td colspan="{{ count($td) }}">{{ $link }}</td></tr>
            <tr>
                @foreach($td as $tag => $val)
                    <td>
                        @if(is_array($val))
                            @if(count($val) > 10)
                                {{ count($val) }} шт.
                            @else
                                {!! implode('<br/>', $val) !!}
                            @endif
                        @elseif($val)
                            {{ $val }}
                        @else
                            Тег не найден
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
    </table>

</body>
</html>
