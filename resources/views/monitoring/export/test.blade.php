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
                @foreach($data['columns'] as $col)
                    <th>{!! strip_tags($col) !!}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data['data'] as $query)
                <tr>
                    @foreach($query as $field)
                        <td>{!! strip_tags($field) !!}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
