<div class="row">
    <div class="col-md-12">
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>ID REGION</th>
                    <th>Источник</th>
                    <th>Дата обновления</th>
                    <th>Средняя позиция</th>
                    <th>ТОП-1</th>
                    <th>ТОП-3</th>
                    <th>ТОП-5</th>
                    <th>ТОП-10</th>
                    <th>ТОП-20</th>
                    <th>ТОП-50</th>
                    <th>ТОП-100</th>
                </tr>
            </thead>
            <tbody>
                @foreach($engines as $engine)
                <tr>
                    <td>[{{ $engine->id }}] {{ $engine->lr }}</td>
                    <td>{{ ucfirst($engine->engine) }}, {{ $engine->location->name }}</td>
                    <td>{{ $engine->latest_created }}</td>
                    <td>{{ $engine->middle_position }}</td>
                    <td>{{$engine->top_1}}</td>
                    <td>{{$engine->top_3}}</td>
                    <td>{{$engine->top_5}}</td>
                    <td>{{$engine->top_10}}</td>
                    <td>{{$engine->top_20}}</td>
                    <td>{{$engine->top_50}}</td>
                    <td>{{$engine->top_100}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
