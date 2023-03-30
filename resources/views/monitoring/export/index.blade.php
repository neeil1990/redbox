<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <table style="width: 100%">
        <thead>
            <tr>
                <th colspan="{{ (count($data['columns']) + 1) }}"></th>
            </tr>
            <tr>
                <th>№</th>
                @foreach($data['columns'] as $col)
                    <th>{!! trim(strip_tags($col)) !!}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data['data'] as $ek => $query)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    @foreach($query as $fk => $field)
                        @if(is_array($field))
                            <td style="background-color: {{ $field['color'] }}">
                                @if(count($field) > 2) {{ $field[0] }} [{{ $field[1] }}] @else {{ $field[0] }} @endif
                            </td>
                        @else
                            <td>{{ $field }}</td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
