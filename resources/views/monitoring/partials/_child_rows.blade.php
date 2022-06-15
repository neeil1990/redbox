<div class="row">
    <div class="col-md-6">
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Источник</th>
                    <th>Дата</th>
                </tr>
            </thead>
            <tbody>
                @foreach($engines as $engine)
                <tr>
                    <td>{{ $engine->engine->location->name }}</td>
                    <td>{{ $engine->created_at }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
