<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

    <table style="width: 100%;table-layout: fixed;">
        <thead>
            <tr>
                @for($i = 0; $i < 250; $i++)
                    <th>{{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            <tr>
                @for($i = 0; $i < 250; $i++)
                    <td style="background-color: green">{{ $i }}</td>
                @endfor
            </tr>
        </tbody>
    </table>

</body>
</html>
