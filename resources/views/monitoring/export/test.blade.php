<table style="width: 100%">
    <thead>
    <tr>
        @for($i = 0; $i < 50; $i++)
        <th>{{ $i }}</th>
        @endfor
    </tr>
    </thead>
    <tbody>

        <tr>
            @for($i = 0; $i < 50; $i++)
            <td style="background-color: green">{{ $i }}</td>
            @endfor
        </tr>

    </tbody>
</table>
