<table class="table table-bordered table-hover table-sm">
    <thead>
    <tr>
        <th rowspan="2" class="align-middle">{{ __('Project') }}</th>

        <th colspan="3" class="text-center">{{ \Carbon\Carbon::now()->monthName }}</th>

        @foreach($periods as $date)
            <th colspan="3" class="text-center">{{ $date->monthName }}</th>
        @endforeach
    </tr>
    <tr>
        <th>{{ __('TOP 10') }}</th>
        <th>{{ __('Mastered') }}</th>
        <th>{{ __('Words') }}</th>

        @for($i = 0; $i < count($periods); $i++)
            <th>{{ __('TOP 10') }}</th>
            <th>{{ __('Mastered') }}</th>
            <th>{{ __('Words') }}</th>
        @endfor
    </tr>
    </thead>

    <tbody>
        @foreach($data as $tr)
        <tr>
            <td>{{ $tr['name'] }}</td>

            @foreach($tr['data'] as $td)
                <td>{{ $td['top10'] }}</td>
                <td>{{ $td['mastered'] }}</td>
                <td>{{ $td['words'] }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
