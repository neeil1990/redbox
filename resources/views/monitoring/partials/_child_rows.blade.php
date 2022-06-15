<div class="row">
    <div class="col-md-6">
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Источник</th>
                    <th>Дата</th>
                    <th>Средняя позиция</th>
                </tr>
            </thead>
            <tbody>
                @foreach($engines as $engine)
                <tr>
                    <td>{{ $engine->location->name }}</td>
                    <td>{{ $engine->latest_position }}</td>
                    <td>{{ $engine->middle_position }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
